<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
require_once('ext/lib_pubfun.php');
class lib_hn extends lib_base
{
    function __construct(){
        parent::__construct();
        $this->ci =& get_instance();

        $this->pubfun = new lib_pubfun();
        //抓取要傳送到哪個群組
        $sql = "SELECT user_id,last_name FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? AND web = ? ";
        $this->noticeID = $this->mod->select($sql, array(1, 'N', $this->gdata['country_code']));
    }
    /**接收到從node傳送過來的json檔關於開獎號碼以及日期*/
    public function getloggery_insert($urlid = null, $lotteryData=null){
        $getData = $lotteryData === null ? json_decode(file_get_contents('php://input', 'r'), true) : $lotteryData;
        $sql = "SELECT id, urlCheck FROM `LT_game` WHERE ename = ?";
        $info = array_shift($this->mod->select($sql, array('hn')));
        $game_id = $info['id'];
        $urlCheck = $info["urlCheck"];
        
        $urlSql = "SELECT code_order FROM `LT_url` WHERE id = ?";
        $info = array_shift($this->mod->select($urlSql, array($urlid)));
        $code_order = isset($info['code_order']) ? $info['code_order'] : 1;//權重

        /***************************************
            第一階 日期迴圈
        ****************************************/
        // echo '<pre>' . print_r($getData, true) . '</pre>';
        foreach ($getData as $oneK => $oneV) {
            $date = str_replace('/', '', $oneK);
            $period_date = str_replace('/', '-', $oneK);
            $openWeekDayColumn = "w".date('w', strtotime(date($date)));
            
            /***************************************
                第二階 地區迴圈
            ****************************************/
            foreach ($oneV as $twoK => $twoV) {
                $city = $this->removeCitySpecialChar($twoK);
                krsort($twoV);
                /***************************************
                 計算號碼總數量
                 ****************************************/
                $countNum = 0;
                foreach ($twoV as $numAry) {
                    foreach($numAry as $val) {
                        if ($val != '')
                            $countNum++;
                    }
                }
                /***************************************
                    取出 地區 相關資訊
                    地區名稱各個網站都不同 因此需判斷
                    $infoCity['id']          = 地區的ID
                    $infoCity["PeriodsTime"] = 地區的開獎時間
                    $infoCity["lottery_num"] = 總開獎數
                    $infoCity["city_en"]     = 地區的英文名稱
                    $infoCity["city_ch"]     = 地區的中文名稱
                ****************************************/
                $sql = "SELECT id,city_en,city_ch,".$openWeekDayColumn.",lottery_num,PeriodsTime FROM hn_city WHERE cityAry like ? ";
                $infoCity = array_shift($this->mod->select($sql, array("%".$city."%")));
                if(empty($infoCity)){
                    /***************
                        存到通知表
                    ***************/
                    foreach ($this->noticeID as $ID) {
                        $msg = "開獎日期：".$date."\n開獎城市：".$city."\n網址ID：".$urlid;
                        $notice = array(
                            'noticeCode'=> 'hnCityNotInTab', //越南彩開獎城市不存在表中
                            'game_id'   => $game_id,
                            'period_str'=> $period_date,
                            'user_id'   => $ID["user_id"],
                            'msg'       => $msg,
                        );
                        $this->pubfun->noticeMsg($notice);
                    }
                    $this->mod->add_by('LT_api_rec', array(
                        "url"       => $urlid,
                        "gameType"  => $game_id,
                        "post"      => $msg,
                        "descr"     => json_encode($oneV),
                        "ua"        => $period_date
                    ));
                    continue;
                }
                $period_str      = $date.'_'.$infoCity['id'];
                $be_lottery_time = $period_date . " " . $infoCity["PeriodsTime"];
                /***************************************
                    檢查開獎城市開獎日是否正確，
                    若不符合不處理直接略過。
                ****************************************/
                if($infoCity[$openWeekDayColumn] == 'N'){
                    continue;
                }
                /***************************************
                    檢查號碼數量，若不符合則表示開獎尚未完成，
                    或是號碼有問題，一律不處理直接略過。
                ****************************************/
                if ($countNum != $infoCity["lottery_num"]) {
                    $msg = "countNum = " . $countNum . "　infoCity = " . $infoCity["lottery_num"];
                    
                    $this->mod->add_by('LT_api_rec', array(
                        "url"       => $urlid,
                        "gameType"  => $game_id,
                        "post"      => $msg,
                        "descr"     => json_encode($oneV),
                        "ua"        => $period_date
                    ));
                    continue;
                }

                /*********************************************
                    檢查期數與日期是否存在期數表
                    否：建立新的資料，並且發通知 empty($info)
                **********************************************/
                $sql = "
                    SELECT
                    id,             -- LT_periods期數表ID
                    lottery_status  -- 開獎狀態
                    FROM
                        `LT_periods`
                    WHERE
                        game_id = ? AND
                        period_date = ? AND
                        period_str = ?";
                $ext = array_shift($this->mod->select($sql, array($game_id, $period_date, $period_str)));

                $periodid        = isset($ext["id"]) ? $ext["id"] : NULL;//periods 的 ID
                $lottery_status  = isset($ext["lottery_status"]) ? $ext["lottery_status"] : 0;//periods 的 ID

                if (empty($ext)) {
                    /***************
                        存到通知表
                    ***************/
                    foreach ($this->noticeID as $ID) {
                        $msg = "[".$infoCity["city_ch"]."]，\n第".$period_str."期";
                        $notice = array(
                            'noticeCode'=> 'periodNotInTab', //期數不存在表中
                            'game_id'   => $game_id,
                            'period_str'=> $period_date,
                            'user_id'   => $ID["user_id"],
                            'msg'       => $msg,
                        );
                        $this->pubfun->noticeMsg($notice);
                    }
                    /********************************************
                        在periods新建資料，先抓出預計開獎時間
                    ********************************************/
                    $sql = "SELECT period_num FROM `LT_game` WHERE id = ?";
                    $info = array_shift($this->mod->select($sql, array($game_id)));
                    $lid = $this->mod->add_by('LT_periods', array(
                        "game_id"         => $game_id,
                        "period_date"     => $period_date,
                        "period_num"      => $info['period_num'],
                        "period_str"      => $period_str,
                        "be_lottery_time" => $be_lottery_time,
                        'lottery'         => json_encode($twoV),
                        'lottery_time'    => date("Y-m-d H:i:s"),
                        'url_id'          => $urlid,
                        'lottery_status'  => 1
                    ));
                    $periodid = $lid["lid"];
                }
                /**********************************************************
                    取出history的所有相關號碼，進行檢查。
                    若為空：直接新增
                    不為空：一筆一筆檢查，號碼是否相同，不相同需在新增一次
                ***********************************************************/
                $sql = "SELECT id,lottery,url_id FROM `LT_history` WHERE game_id = ? AND period_str = ?";
                $ext = $this->mod->select($sql, array($game_id, $period_str));
                if (empty($ext)) {
                    $this->mod->add_by(
                        'LT_history',
                        array(
                            "game_id"      => $game_id,
                            "lottery_id"   => $periodid,
                            "period_str"   => $period_str,
                            "lottery"      => json_encode($twoV),
                            "lottery_time" => date("Y-m-d H:i:s"),
                            "url_id"       => $urlid,
                            "code_order"   => $code_order
                        )
                    );
                }  else {
                    foreach ($ext as $k => $v) {
                        /*****************************************
                            判斷，如果開獎號碼與資料庫號碼不一致
                            -->寫入history
                            -->寫入通知表，發送telegram通知
                        ******************************************/
                        if(json_encode($twoV) != $v["lottery"]) {
                            /***************
                                存到通知表
                            ***************/
                            foreach ($this->noticeID as $ID) {
                                $msg = "[".$infoCity["city_en"]."]，\n第".$period_str."期";
                                $notice = array(
                                    'noticeCode'=> 'openNumError', //開獎號碼有誤
                                    'game_id'   => $game_id,
                                    'period_str'=> $period_str,
                                    'user_id'   => $ID["user_id"],
                                    'msg'       => $msg,
                                );
                                $this->pubfun->noticeMsg($notice);
                            }
                            
                            $errorPeriod = array(
                                'game_id'    => $game_id,
                                'lottery_id' => $periodid,
                                'lottery'    => json_encode($twoV)
                            );
                            if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                                $this->obj['code'] = 500;
                                $this->output();
                                return;
                            }
                        }
                    }

                    $sql = "SELECT id FROM LT_history WHERE game_id = ? AND period_str = ? AND url_id = ? AND lottery = ?";
                    $data = $this->mod->select($sql, array($game_id, $period_str, $urlid, json_encode($twoV)));

                    if(empty($data)){
                        $this->mod->add_by(
                            'LT_history',
                            array(
                                "game_id"      => $game_id,
                                "lottery_id"   => $periodid,
                                "period_str"   => $period_str,
                                "lottery"      => json_encode($twoV),
                                "lottery_time" => date("Y-m-d H:i:s"),
                                "url_id"       => $urlid,
                                "proxy"        => json_encode($this->pxy->current),
                                "code_order"   => $code_order
                            )
                        );
                    }
                }
                if($lottery_status == 0) {
                    $urlConfidence = array(
                        'game_id'      => $game_id,
                        'period_str'   => $period_str,
                        'lottery'      => json_encode($twoV),
                        'lottery_time' => date("Y-m-d H:i:s"),
                        'code_order'   => $code_order,
                        'periodid'     => $periodid,
                        'urlCheck'     => $urlCheck,
                        'url_id'       => $urlid
                    );
                    if($this->pubfun->urlConfidenceCheck($urlConfidence) > 0){
                        $this->obj['code'] = 500;
                        $this->output();
                        return;
                    }
                }
            }
        }
        $this->obj['code'] = 100;
        $this->obj['data'] = $getData;
        $this->output();
    }
    /**傳送越南彩各官方網址給node*/
    public function hnUrl() {
        $sql = "SELECT id,area,city,city_en,url,w1,w2,w3,w4,w5,w6,w0,PeriodsTime FROM hn_city";
        $info = $this->mod->select($sql);
        $sql = "SELECT area,url,url_id,url_name,w1,w2,w3,w4,w5,w6,w0 FROM hn_unofficial_url";
        $info2 = $this->mod->select($sql);
        $data = array(
            'office' => $info,
            'notOffice' => $info2
        );
        $this->obj['code'] = 100;
        $this->obj['data'] = $data;
        $this->output();
    }
    /**取出所有越南彩资讯*/
    public function hnData() {
        $sql     = "SELECT * FROM hn_city";
        $info = $this->mod->select($sql);
        if (empty($info)) {
            $this->obj["code"] = 101;
        } else {
            $this->obj["code"] = 100;
        }

        $this->obj["data"] = $info;
        $this->output();
    }
    /**取出所有越南彩资讯*/
    public function hnUrlList() {
        $sql = "SELECT * FROM hn_city WHERE 1";
        $where = array();

        if (isset($_POST["area"]) && (!empty($_POST["area"]))) {
            $sql .= " AND area = ?";
            $where[] = $_POST["area"];
        }

        if (isset($_POST["w"]) && !empty($_POST["w"])) {
            if ($_POST["w"] == 1)
                $sql .= " AND w1 = 'Y'";
            if ($_POST["w"] == 2)
                $sql .= " AND w2 = 'Y'";
            if ($_POST["w"] == 3)
                $sql .= " AND w3 = 'Y'";
            if ($_POST["w"] == 4)
                $sql .= " AND w4 = 'Y'";
            if ($_POST["w"] == 5)
                $sql .= " AND w5 = 'Y'";
            if ($_POST["w"] == 6)
                $sql .= " AND w6 = 'Y'";
            if ($_POST["w"] == 0)
                $sql .= " AND w0 = 'Y'";
        }

        $info = $this->mod->select($sql, $where);

        if (empty($info)) {
            $this->obj["code"] = 101;
        } else {
            foreach ($info as $key => $v) {
                $wAry = [];
                switch ($v['area']) {
                    case 'NAM':
                        $info[$key]["area"] = '南部';
                        break;
                    case 'TRUNG':
                        $info[$key]["area"] = '東部';
                        break;
                    case 'BAC':
                        $info[$key]["area"] = '北部';
                        break;
                }
                if ($v['w1'] == 'Y')
                    array_push($wAry, '星期一');
                if ($v['w2'] == 'Y')
                    array_push($wAry, '星期二');
                if ($v['w3'] == 'Y')
                    array_push($wAry, '星期三');
                if ($v['w4'] == 'Y')
                    array_push($wAry, '星期四');
                if ($v['w5'] == 'Y')
                    array_push($wAry, '星期五');
                if ($v['w6'] == 'Y')
                    array_push($wAry, '星期六');
                if ($v['w0'] == 'Y')
                    array_push($wAry, '星期日');
                $info[$key]["w"] = $wAry;
            }
            $this->obj["code"] = 100;
        }

        $this->obj["data"] = $info;
        $this->output();
    }
    /**
     * 移除越南彩城市名稱特殊字元
     * @param  String $city       城市名稱
     * @return String $filterCity 移除特殊字元的城市名稱
     */
    public function removeCitySpecialChar($city){
        $beRemovedStr = array("\n");
        $filterCity = str_replace($beRemovedStr,'',$city);
        return $filterCity;
    }
}