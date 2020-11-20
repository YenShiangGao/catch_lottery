<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
require_once('ext/lib_pubfun.php');
class lt_global extends lib_base{
    function __construct(){
        parent::__construct();
        $this->ci =& get_instance();

        $this->pubfun = new lib_pubfun();
    }
    //將資料存入資料庫
    public function getloggery_insert($sql_data = array(), $result = array()){
        if(empty($sql_data))
            return false;
        if(empty($result))
            return false;

        $id         = $sql_data["id"];          //網址ID
        $game_id    = $sql_data["game_id"];     //遊戲ID
        $code_order = $sql_data["code_order"];  //權重
        $url_name   = $sql_data["url_name"];    //url_name

        /***************************************
            用game_id去抓出遊戲相關設定
            $Name["cname"],$Name["urlCheck"]
            $Name["period_num"],$Name["period_format"],$Name["repeat"],
        ****************************************/
        $sql  = "SELECT cname,urlCheck,period_num,period_format,`repeat` FROM LT_game WHERE id = ? ";
        $Name = array_shift($this->mod->select($sql, array($game_id)));

        /***************************************
            抓取telegram message要傳送到哪個群組
            $teleID["user_id"]
        ****************************************/
        $sql = "SELECT user_id FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? AND web = ? ";
        $noticeID = $this->mod->select($sql, array(1,'N', $this->gdata['country_code']));

        foreach ($result as $k => $v) {
            $expect   = (string) $v["expect"];  //期數編碼
            $opencode = $v["opencode"];         //開獎號碼
            $opentime = $v["opentime"];         //開獎時間
            if(DateTime::createFromFormat('Y-m-d G:i:s', $opentime) === false)
                $opentime = date("Y-m-d H:i:s");

            /*********************************************
                檢查期數是否已經有預先建立好了
                否：建立新的資料，並且發通知 empty($info)
            **********************************************/
            $sql = "
                SELECT
                    id,             -- LT_periods期數表ID
                    lottery,        -- 開獎號碼
                    lottery_status, -- 開獎狀態
                    checks,         -- 是否已經檢查，權重0是否已回傳
                    be_lottery_time -- 預計開獎時間
                FROM
                    LT_periods
                WHERE
                    game_id = ? AND
                    period_str = ?
                ORDER BY period_date DESC
            ";
            $info = array_shift($this->mod->select($sql, array($game_id,$expect)));

            $be_lottery_time = isset($info["be_lottery_time"]) ? $info["be_lottery_time"] : date('Y-m-d H:i:s');    //預計開獎時間
            $periodid        = isset($info["id"]) ? $info["id"] : NULL;//periods 的 ID
            $lottery_status  = isset($info["lottery_status"]) ? $info["lottery_status"] : 0;//periods 的 ID
            $checks          = isset($info["checks"]) ? $info["checks"] : 0;//periods 的 ID

            /**********************************************
                否：建立新的資料，並且發通知 empty($info)
            ***********************************************/
            if(empty($info)){
                /***************
                    存到通知表
                ***************/
                foreach ($noticeID as $ID) {
                    $msg = "[".$Name["cname"]."]，\n第".$expect."期";
                    $notice = array(
                        'noticeCode'=> 'periodNotInTab', //期數不存在表中
                        'game_id'   => $game_id,
                        'period_str'=> $expect,
                        'user_id'   => $ID["user_id"],
                        'msg'       => $msg,
                    );
                    $this->pubfun->noticeMsg($notice);
                }
                /********************************************
                    在periods新建資料，先抓出預計開獎時間
                ********************************************/
                $periods = substr($expect, "-".$Name["period_num"]);
                switch($Name["period_format"]){
                    case 'Ymd':
                    case 'ymd':
                        $sql = "SELECT PeriodsTime FROM tb_game_periods WHERE game_id = ? AND Periods = ? ";
                        $data = array_shift($this->mod->select($sql,array($game_id,$periods)));
                        $be_lottery_time = date('Y-m-d') . " " . $data["PeriodsTime"];
                        break;
                    default:
                        $be_lottery_time = date('Y-m-d H:i:s');
                        break;
                }

                $lid = $this->mod->add_by('LT_periods', array(
                            "game_id"         => $game_id,
                            "period_date"     => date('Y-m-d'),
                            "period_num"      => $Name["period_num"],
                            "period_str"      => $expect,
                            "be_lottery_time" => $be_lottery_time,
                            "lottery"         => $opencode,
                            "lottery_time"    => $opentime,
                            "url_id"          => $id
                        ));

                $periodid = $lid["lid"];
            }

            /**********************************************************
                取出history的所有相關號碼，進行檢查。
                若為空：直接新增
                不為空：一筆一筆檢查，號碼是否相同，不相同需在新增一次
            ***********************************************************/
            $sql = "SELECT lottery FROM LT_history WHERE game_id = ? AND period_str = ?";
            $info = $this->mod->select($sql, array($game_id, $expect));

            if (empty($info)) {
                $this->mod->add_by(
                    'LT_history',
                    array(
                        "game_id"      => $game_id,
                        "lottery_id"   => $periodid,
                        "period_str"   => $expect,
                        "lottery"      => $opencode,
                        "lottery_time" => $opentime,
                        "url_id"       => $id,
                        "proxy"        => json_encode($this->pxy->current),
                        "code_order"   => $code_order
                    )
                );
            } else {
                foreach ($info as $k => $v) {
                    /*****************************************
                        判斷，如果開獎號碼與資料庫號碼不一致
                        -->寫入history
                        -->寫入通知表，發送telegram通知
                    ******************************************/
                    if($opencode != $v["lottery"]) {
                        /***************
                            存到通知表
                        ***************/
                        foreach ($noticeID as $ID) {
                            $msg = "[".$Name["cname"]."]，\n第".$expect."期";
                            $notice = array(
                                'noticeCode'=> 'openNumError', //開獎號碼有誤
                                'game_id'   => $game_id,
                                'period_str'=> $expect,
                                'user_id'   => $ID["user_id"],
                                'msg'       => $msg,
                            );
                            $this->pubfun->noticeMsg($notice);
                        }
                        $errorPeriod = array(
                            'game_id'    => $game_id,
                            'lottery_id' => $periodid,
                            'lottery'    => $opencode
                        );
                        if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                            return false;
                        } 
                        
                    }
                }

                $sql = "SELECT id FROM LT_history WHERE game_id = ? AND period_str = ? AND url_id = ? AND lottery = ?";
                $data = $this->mod->select($sql, array($game_id, $expect, $id, $opencode));

                if(empty($data)){
                    $this->mod->add_by(
                        'LT_history',
                        array(
                            "game_id"      => $game_id,
                            "lottery_id"   => $periodid,
                            "period_str"   => $expect,
                            "lottery"      => $opencode,
                            "lottery_time" => $opentime,
                            "url_id"       => $id,
                            "proxy"        => json_encode($this->pxy->current),
                            "code_order"   => $code_order
                        )
                    );
                }
            }

            /***************************************************
                將號碼做判斷,是不是每一個號碼都有在標準內
                若回傳訊息1，則表示其中一個號碼不成立
                將此訊息記錄到telegram通知表
                $checkResult
            ****************************************************/
            $checkResult = $this->checkNumber($game_id,$opencode);
            if($checkResult === 1) {
                foreach ($noticeID as $ID) {
                    $msg = "[".$Name["cname"]."]，\n第".$expect."期";
                    $notice = array(
                        'noticeCode'=> 'openNumNotRange', //開獎號碼未落在號碼區間
                        'game_id'   => $game_id,
                        'period_str'=> $expect,
                        'user_id'   => $ID["user_id"],
                        'msg'       => $msg,
                    );
                    $this->pubfun->noticeMsg($notice);
                }
                $errorPeriod = array(
                    'game_id'    => $game_id,
                    'lottery_id' => $periodid,
                    'lottery'    => $opencode
                );
                if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                    return false;
                }
                return true;
            }

            /***************************************************
                25幸运飞艇 66六合彩 9北京赛车
                將號碼做判斷,是否有重複
                若回傳訊息1，則表示其中一個號碼有重複
                將此訊息記錄到telegram通知表
                $checkResult
            ****************************************************/
            if ($Name["repeat"] == 'N') {
                $checkResult = $this->checkNumberRepeat($opencode);
                if($checkResult === 1) {
                    foreach ($noticeID as $ID) {
                        $msg = "[".$Name["cname"]."]，\n第".$expect."期";
                        $notice = array(
                            'noticeCode'=> 'openNumRepeat', //開獎號碼重複
                            'game_id'   => $game_id,
                            'period_str'=> $expect,
                            'user_id'   => $ID["user_id"],
                            'msg'       => $msg,
                        );
                        $this->pubfun->noticeMsg($notice);
                    }
                    $errorPeriod = array(
                        'game_id'    => $game_id,
                        'lottery_id' => $periodid,
                        'lottery'    => $opencode
                    );
                    if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                        return false;
                    } 
                    return true;
                }
            }

            if($lottery_status == 0) {
                $urlConfidence = array(
                    'game_id'      => $game_id,
                    'period_str'   => $expect,
                    'lottery'      => $opencode,
                    'lottery_time' => $opentime,
                    'code_order'   => $code_order,
                    'periodid'     => $periodid,
                    'urlCheck'     => $Name["urlCheck"],
                    'url_id'       => $id
                );
                if($this->pubfun->urlConfidenceCheck($urlConfidence) > 0){
                    return false;
                }
            }
            /******************************************************
                檢查 開獎時間 是否 小於 預計開獎時間 =>提早開獎
            *******************************************************/
            if (strtotime($opentime) < strtotime($be_lottery_time))
                $this->timeCheck($game_id,$expect);

        }
        return true;
    }
    /*提早開獎 */
    public function timeCheck($game_id=null,$expect=null){
        //先抓出指定期數的 【開獎時間】 與 【預計開獎時間】 PS.當期 已經insert data了
        $sql  = "SELECT lottery_time,be_lottery_time FROM LT_periods WHERE game_id = ? AND period_str = ?";
        $data = array_shift($this->mod->select($sql, array($game_id,$expect)));
        //用game_id去抓出遊戲名稱
        $sql  = "SELECT cname FROM LT_game WHERE id = ?";
        $Name = array_shift($this->mod->select($sql, array($game_id)));
        //抓取要傳送到哪個群組
        $sql = "SELECT user_id FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? AND web = ? ";
        $noticeID = $this->mod->select($sql, array(1,'N', $this->gdata['country_code']));
        
        if(!empty($data)){
            $beLotteryTime = $data["be_lottery_time"];
            $opentime      = $data["lottery_time"];
            
            if (strtotime($opentime) < strtotime($beLotteryTime)){
                foreach ($noticeID as $ID) {
                    $msg = $Name["cname"]. "　第".$expect."期，\n預計開獎時間為".$beLotteryTime."\n實際開獎時間為".$opentime;
                    $notice = array(
                        'noticeCode'=> 'quickOpen', //提早開獎
                        'game_id'   => $game_id,
                        'period_str'=> $expect,
                        'user_id'   => $ID["user_id"],
                        'msg'       => $msg,
                    );
                    $this->pubfun->noticeMsg($notice);
                }
            }
        }
    }
    //更新LT_url表
    public function LTurlUpdate($sql_data = array()){
        $sql = "
            SELECT
                period_str,      -- 期數編碼
                lottery_time,    -- 開獎時間
                proxy            -- proxy
            FROM
                LT_history
            WHERE
                game_id = ? AND
                url_id  = ?
            ORDER BY
                id DESC
            LIMIT
                1

        ";
        $data = array_shift($this->mod->select($sql, array($sql_data["game_id"],$sql_data["id"])));

        $this->mod->modi_by(
            'LT_url',
            array('id' => $sql_data["id"]),
            array(
                "last_period" => $data["period_str"],
                "last_status" => 1,
                "last_time"   => date('Y-m-d H:i:s'),
                "last_proxy"  => $data["proxy"],
                "nowtime"     =>date('Y-m-d H:i:s')
            )
        );
    }
    /**
     * 檢查號碼是否有在區間內
     * @param  [type] $game_id [遊戲種類]
     * @param  [type] $data    [號碼]
     * @return [type]          [description]
     */
    public function checkNumber($game_id, $data){
        /**************************************
            抓取彩種的最大以及最小號碼
            用來檢查開獎號碼是否有存在此區間中
            $mNum("min_number"),$mNum("max_number")
        ***************************************/
        $sql  = "SELECT min_number,max_number FROM LT_game WHERE id = ? AND enable = ?";
        $mNum = array_shift($this->mod->select($sql, array($game_id,0)));

        $c = explode(',',$data);

        foreach ($c as $k => $v) {
            if((int)$v>$mNum["max_number"] || (int)$v<$mNum["min_number"])
                return 1;
        }
    }
    /**
     * 檢查號碼是否重複 9,25,66
     * @param  [type] $game_id [description]
     * @param  [type] $data    [description]
     * @return [type]          [description]
     */
    public function checkNumberRepeat($data){
        $dataAry = explode(',', $data);
        $dataRep = array_count_values($dataAry);
        foreach ($dataRep as $k => $v) {
            if((int)$v > 1)
                return 1;
        }
    }
    public function isMatch($preg_res=false){
        if(empty($preg_res) || !is_array($preg_res) || ( isset($preg_res[0]) && empty($preg_res[0]) ) ){
            return false;
        }
        return true;
    }
    public function ltUrlInfo($game_id = null, $api_name = null) {
        $sql = "SELECT id,api_name FROM `LT_url` WHERE `game_id` = ? AND `api_name` = ? ";
        $info = $this->mod->select($sql, array($game_id, $api_name));

        if (empty($info)) {
            $this->obj["code"] = 500;
            $this->obj["msg"]  = 'DB 沒有此資料';
        } else {
            $this->obj["code"] = 100;
            $this->obj["msg"]  = $info;
        }
        $this->output();
    }
}