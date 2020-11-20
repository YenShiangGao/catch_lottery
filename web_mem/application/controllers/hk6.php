<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

@ini_set('display_errors', 1);
/*設置錯誤信息的類別*/
class hk6 extends web_mem{
    public $delItem;
    public $proxy = false;
    public function __construct(){
        parent::__construct();
        $this->gameID = 66;
        $this->delItem = $this->defaultms();
        $this->load->library('curl');
        $this->load->library('lib_func');
        $this->pubfun = $this->man_lib("lib_pubfun", "libs/ext");

        //抓取要傳送到哪個群組
        $sql = "SELECT user_id,last_name FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? ";
        $this->noticeID = $this->mod->select($sql, array(3, 'N'));
    }
    /**
     * 六合彩開獎號碼通知
     * 呼叫fun 由node game_catch_check此資料夾內的main.js呼叫
     */
    public function hk6_openResult(){
        $sql = "SELECT lottery,period_date,period_str FROM LT_periods WHERE game_id = ? AND lottery_status = ? ORDER BY period_str Desc";
        $info = array_shift($this->mod->select($sql, array($this->gameID, 1)));
        $sql = "SELECT period_date,period_str FROM LT_periods WHERE game_id = ? AND lottery_status = ? ORDER BY period_str ASC";
        $info1 = array_shift($this->mod->select($sql, array($this->gameID, 0)));
        
        /**存到通知表**/
        foreach ($this->noticeID as $ID) {
            $msg = "開獎通知　" . $ID['last_name'] . "\n開獎日期　".$info["period_date"]."\n開獎期數　".$info["period_str"]."\n開獎號碼　".$info["lottery"]."\n下一期開獎資訊\n開獎日期　".$info1["period_date"]."\n開獎期數　".$info1["period_str"];
            $notice = array(
                'noticeCode'=> 'hk6Open', //六合彩開獎通知
                'game_id'   => $this->gameID,
                'period_str'=> $info["period_str"],
                'user_id'   => $ID['user_id'],
                'msg'       => $msg
            );

            if($this->pubfun->noticeMsg($notice) > 0){
                $this->obj["msg"] = "沒有更新資料";
            } else {
                $this->obj["msg"] = $msg;
            }
            
        }
        /**************/
        $this->obj["code"]         = 100;
        $this->output();
    }

    /**
     * 六合彩開獎日期檢查
     * 呼叫fun 由node game_catch_hk6此資料夾內的main.js呼叫
     */
    public function hk6_openDate() {
        $getData = json_decode(file_get_contents('php://input', 'r'), true);
        $returnData = [];

        $gameInfoSql = "SELECT param,period_num,param_2 FROM LT_game WHERE id = ?";
        $hk6Info  = array_shift($this->mod->select($gameInfoSql, array($this->gameID)));
        $openTime = 'Y-m-d '.$hk6Info['param_2'];

        foreach ($getData as $key => $val) {
            $year = explode('-', $key)[0];
            $month = explode('-', $key)[1];
            sort($val);
            $day = $val;

            $sql = "SELECT lottery_day FROM LT_openset WHERE game_id = ? AND lottery_year = ? AND lottery_month = ?";
            $data = $this->mod->select($sql, array($this->gameID, $year, $month));
            
            if ($data) {
                /********************************
                    將開獎日期做排序，由小到大
                *********************************/
                $sortdayStr = implode(",",$day);
                $sortOpenDate = explode(",", trim($data[0]['lottery_day']));
                $diffday = implode(array_diff($sortOpenDate, $day));
                sort($sortOpenDate);
                $sortOpenDate = implode(",",$sortOpenDate);
                if ($sortOpenDate != $sortdayStr) {
                    /**存到通知表**/
                    $periodCounter = null;
                    $period = date('Y-m-d');
                    foreach ($this->noticeID as $ID) {
                        $msg = $ID['last_name'] . "\n變更年月：".$key."\n原開獎日：".$sortOpenDate."\n新開獎日：".$sortdayStr;
                        $notice = array(
                            'noticeCode'=> 'hk6OpenDateUpd', //六合彩開獎日期異動
                            'game_id'   => $this->gameID,
                            'period_str'=> $period,
                            'user_id'   => $ID["user_id"],
                            'msg'       => $msg,
                        );
                        $this->pubfun->noticeMsg($notice);            
                    }
                    /***************/
                    if(strtotime(date('Y-m')) > strtotime($year.'-'.$month)){
                        array_push($returnData, $msg);
                        continue;
                    }

                    /**更改開獎日*/
                    $this->mod->modi_by('LT_openset', array(
                        "game_id"       => $this->gameID,
                        "lottery_year"  => $year,
                        "lottery_month" => $month,
                    ), array(
                        "lottery_day"   => $sortdayStr
                    ));
                    $this->mod->del_by('LT_periods', array(
                        "game_id"       => $this->gameID, 
                        "lottery_status"=> 0,
                        "period_date"   => $year . '-' . $month . '-' . $diffday
                    ));

                    //取得已開獎最新期數資料
                    $openLotterySql = 'SELECT period_str,period_date FROM `LT_periods` WHERE `game_id` = ? and `lottery_status` = ? order by period_str DESC';
                    $openLottery = $this->mod->select($openLotterySql, array($this->gameID, 1));
                    $latestOpenPeriodStr = $openLottery[0];
                    //取得所有未開獎資料
                    $unOpenLotterySql = 'SELECT id,period_str FROM `LT_periods` WHERE `game_id` = ? and `lottery_status` = ? order by period_str DESC';
                    $unOpenLottery = $this->mod->select($unOpenLotterySql, array($this->gameID, 0));
                    //刪除(未開獎期數編碼大於已開獎最新期數編碼)未開獎資料
                    foreach ($unOpenLottery as $lottery) {
                        if($lottery['period_str'] > $latestOpenPeriodStr['period_str']){
                            $this->mod->del_by_id('LT_periods', $lottery['id']);
                        }
                    }

                    //重新產生未開獎資料
                    $opensetSql = "SELECT lottery_year,lottery_month,lottery_day,id FROM LT_openset WHERE `enable` = ? AND game_id = ? order by lottery_year, lottery_month ASC";
                    $LTOpenset = $this->mod->select($opensetSql, array(1, $this->gameID));

                    $periodCounter = $latestOpenPeriodStr['period_str'];
                    $latestOpenYear = explode('-', $latestOpenPeriodStr['period_date'])[0];
                    $latestOpenMonth = explode('-', $latestOpenPeriodStr['period_date'])[1];

                    foreach ($LTOpenset as $set) {
                        if(strtotime($set['lottery_year'].'-'.$set['lottery_month']) >= strtotime($latestOpenYear.'-'.$latestOpenMonth)){
                            $lottery_day = explode(",", $set["lottery_day"]);
                            foreach ($lottery_day as $day) {
                                $dateStr = date("Y-m-d", strtotime($set['lottery_year'].'/'.$set['lottery_month'].'/'.$day));
                                $querySql = 'SELECT id,period_str FROM `LT_periods` WHERE `game_id` = ? and `period_date` = ? order by period_str DESC';
                                $result = array_shift($this->mod->select($querySql, array($this->gameID, $dateStr)));
                                if(empty($result)){
                                    $periodCounter++;
                                    $this->mod->add_by(
                                        'LT_periods',
                                        array(
                                            "game_id"         => $this->gameID,
                                            "period_date"     => $dateStr,
                                            "period_num"      => $hk6Info['period_num'],
                                            "period_str"      => $periodCounter,
                                            "be_lottery_time" => date($openTime, strtotime($dateStr))
                                        )
                                    );
                                }
                            }
                        }
                    }
                    $this->mod->modi_by_id('LT_game', $this->gameID, array('param' => $periodCounter));
                    array_push($returnData, $msg);
                } else {
                    $msg = $year . '/' . $month ."沒有問題";
                    array_push($returnData, $msg);
                }
            } else{
                $this->mod->add_by(
                    'LT_openset',
                    array(
                        "game_id"       => $this->gameID,
                        "lottery_year"  => $year,
                        "lottery_month" => $month,
                        "lottery_day"   => implode(",",$day),
                        "enable"        => 0
                    )
                );

                /**存到通知表**/
                $period = $year . '/' . $month;
                foreach ($this->noticeID as $ID) {
                    $msg = $ID['last_name'] . "\n" . $period;
                    $notice = array(
                        'noticeCode'=> 'hk6OpenDateCre', //六合彩開獎日期新增
                        'game_id'   => $this->gameID,
                        'period_str'=> $period,
                        'user_id'   => $ID["user_id"],
                        'msg'       => $msg,
                    );
                    $this->pubfun->noticeMsg($notice);     
                }
                /***************/

                $msg = "新增六合彩". $year . '/' . $month ."開獎日";
                array_push($returnData, $msg);
            }
        }
        $this->obj["code"]= 100;
        $this->obj["msg"]= $returnData;
        $this->output();
    }
}