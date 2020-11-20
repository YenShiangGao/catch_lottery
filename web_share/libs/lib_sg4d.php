<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
require_once('ext/lib_pubfun.php');
class lib_sg4d extends lib_base{
    function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
        $this->gameID = 155;
        $this->str_ary = ['第一奖','第二奖','第三奖','入围奖','安慰奖'];
        $this->pubfun = new lib_pubfun();
        //抓取要傳送到哪個群組
        $sql = "SELECT user_id,last_name FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? AND web = ? ";
        $this->noticeID = $this->mod->select($sql, array(1, 'N', $this->gdata['country_code']));
    }
    public function openDate() {
        $today = getdate();
        $year = $today['year'];
        $mon = $today['mon'];
        $day = date('t', strtotime($year.'-'.$mon));
        
        for($m = $mon; $m <= $mon + 1; $m++) {
            if ($m == 2) {
                if (checkdate(2, 29, $year))
                    $day = 29;
                else 
                    $day = 28;   
            }
            
            $lottery_day = [];
            for ($i = 1; $i <= $day; $i++) {
                $d = date("w", strtotime($year.'-'.$m.'-'.$i));
                if ($d == 0 || $d == 6 || $d == 3) {
                    $lottery_day[] = $i;
                }
            }
            $exit = $this->mod->select('SELECT id FROM `LT_openset` WHERE `game_id` = ? AND `lottery_year` = ? AND `lottery_month` = ?', 
                array($this->gameID, $year, $m));

            if (empty($exit)) {
                $this->mod->add_by('LT_openset',
                    array(
                        'game_id' => $this->gameID,
                        'lottery_year' => $year,
                        'lottery_month' => $m,
                        'lottery_day' => implode(',', $lottery_day),
                        'enable' => 0
                    ));
            }
        }
        $this->obj['code'] = 100;
        $this->output();
    }
    //將資料存入資料庫
    public function getloggery_insert($sql_data = array(), $result = array()){
        if(empty($sql_data)) {
            return false;
        }
        if(empty($result)) {
            return false;
        }

        $id         = $sql_data["id"];          //網址ID
        $game_id    = $sql_data["game_id"];     //遊戲ID
        $code_order = $sql_data["code_order"];  //權重
        $url_name   = $sql_data["url_name"];    //url_name

        /***************************************
            用game_id去抓出遊戲相關設定
            $Name["cname"],$Name["urlCheck"]
            $Name["period_num"],$Name["period_format"],$Name["repeat"],
        ****************************************/
        $sql  = "SELECT cname,urlCheck,period_num,period_format,lottery_num,`repeat` FROM LT_game WHERE id = ? ";
        $Name = array_shift($this->mod->select($sql, array($game_id)));
        
        foreach ($result as $key => $value) {
            $expect   = (string) $value["expect"];  //期數編碼
            $opencode = $value["opencode"];         //開獎號碼
            $opentime = $value["opentime"];         //開獎時間
            if(DateTime::createFromFormat('Y-m-d G:i:s', $opentime) === false) {
                $opentime = date("Y-m-d H:i:s");
            }

            if ($this->IsNullOrEmptyExpect($expect)) {
                continue;
            }
            /***************************************
                檢查號碼數量，若不符合則表示開獎尚未完成，
                或是號碼有問題，一律不處理直接略過。
            ****************************************/
            if ($this->IsNotEqualToLotteryNumber($opencode, $Name["lottery_num"])) {
                continue;
            }

            if ($this->IsNullOpenCode($opencode)) {
                continue;
            }

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
                foreach ($this->noticeID as $ID) {
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
                            "lottery"         => json_encode($opencode),
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
                        "lottery"      => json_encode($opencode),
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
                    $openCodeCheckResult = $this->pubfun->checkOpenCodeResult($opencode, $v["lottery"]);
                    if(gettype($openCodeCheckResult) == 'array') {
                        foreach($openCodeCheckResult as $k => $v) {
                            $msg = "[".$Name["cname"]."]，\n第".$expect."期,".$this->str_ary[$k].'獎號:'.$v;
                        }
                        /***************
                            存到通知表
                        ***************/
                        foreach ($this->noticeID as $ID) {
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
                            'lottery'    => json_encode($opencode)
                        );
                        if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                            return false;
                        }
                    }
                }

                $sql = "SELECT id FROM LT_history WHERE game_id = ? AND period_str = ? AND url_id = ? AND lottery = ?";
                $data = $this->mod->select($sql, array($game_id, $expect, $id, json_encode($opencode)));

                if(empty($data)){
                    $this->mod->add_by(
                        'LT_history',
                        array(
                            "game_id"      => $game_id,
                            "lottery_id"   => $periodid,
                            "period_str"   => $expect,
                            "lottery"      => json_encode($opencode),
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

            $checkResult = $this->checkNumberRange($game_id,$opencode);
            if($checkResult === 1) {
                /***************
                    存到通知表
                ***************/
                foreach ($this->noticeID as $ID) {
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
                    'lottery'    => json_encode($opencode)
                );
                if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                    return false;
                }
            }
            /***************************************************
                將號碼做判斷,是否有重複
                若回傳訊息1，則表示其中一個號碼有重複
                將此訊息記錄到telegram通知表
                $checkResult
            ****************************************************/
            if ($Name["repeat"] == 'N') {
                $checkResult = $this->checkNumberRepeat($opencode);
                if($checkResult === 1) {
                    /***************
                        存到通知表
                    ***************/
                    foreach ($this->noticeID as $ID) {
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
                        'lottery'    => json_encode($opencode)
                    );
                    if($this->pubfun->errorPeriodRecord($errorPeriod) > 0){
                        return false;
                    }
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
                檢查 開獎時間 是否 大於 預計開獎時間
            *******************************************************/
            if (strtotime($opentime) < strtotime($be_lottery_time)) {
                $this->lotteryTimeCheck($game_id,$expect);
            }
        }
        return true;
    }
    
    /*檢查 開獎時間 預計開獎時間 */
    public function lotteryTimeCheck($game_id=null,$expect=null){
        //先抓出指定期數的 【開獎時間】 與 【預計開獎時間】 PS.當期 已經insert data了
        $sql  = "SELECT lottery_time,be_lottery_time FROM LT_periods WHERE game_id = ? AND period_str = ?";
        $data = array_shift($this->mod->select($sql, array($game_id,$expect)));
        //用game_id去抓出遊戲名稱
        $sql  = "SELECT cname FROM LT_game WHERE id = ?";
        $Name = array_shift($this->mod->select($sql, array($game_id)));
        
        if(!empty($data)){
            $beLotteryTime = $data["be_lottery_time"];
            $opentime      = $data["lottery_time"];
            if (strtotime($opentime) < strtotime($beLotteryTime)){
                /**存到通知表**/
                foreach ($this->noticeID as $ID) {
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
                /**************/
            }
        }
    }

    public function IsNullOrEmptyExpect($expect){
        return (!isset($expect) || trim($expect) === '');
    }

    public function IsNotEqualToLotteryNumber($opencode, $lotteryNumber){
        $countNum = 0;
        foreach ($opencode as $opencodeKey => $opencodeValue) {
            $countNum = count($opencodeValue) + $countNum;
        }
        if ($countNum != $lotteryNumber) {
            return 1;
        }
        return 0;
    }

    public function IsNullOpenCode($opencode){
        foreach($opencode as $level => $prizes){
            foreach($prizes as $codeIndex => $code){
                if(is_null($code)){
                    return 1;
                }
            }
        }
        return 0;
    }

    public function checkNumberRange($game_id, $openCodeGroup){
        /**************************************
            抓取彩種的最大以及最小號碼
            用來檢查開獎號碼是否有存在此區間中
        ***************************************/
        $sql  = "SELECT min_number,max_number FROM LT_game WHERE id = ? AND enable = ?";
        $openCodeRangeOfValues = array_shift($this->mod->select($sql, array($game_id,0)));

        foreach ($openCodeGroup as $openCodeLevel => $openCodePrizes) {
            foreach ($openCodePrizes as $openCodeKey => $openCode) {
                if((int)$openCode > $openCodeRangeOfValues["max_number"] || (int)$openCode < $openCodeRangeOfValues["min_number"]){
                    return 1;
                }
            }
        }
    }

    public function checkNumberRepeat($openCodeGroup){
        $openCodes = array();
        foreach ($openCodeGroup as $openCodeLevel => $openCodePrizes) {
            $openCodes = array_merge($openCodes, $openCodePrizes);
        }
        $openCodesRepeatValues = array_count_values($openCodes);
        foreach ($openCodesRepeatValues as $key => $value) {
            if((int)$value > 1)
                return 1;
        }
    }
}