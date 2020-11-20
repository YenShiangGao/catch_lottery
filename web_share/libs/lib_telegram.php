<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/ext/lib_base'.EXT);
class lib_telegram extends lib_base{
    public $BotKey = "446407075:AAFL83YfP9rjX4b_gsqkZJbTGVbZkxNL2Ds";
    function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
    }
    public function ChangeKey($key=null){
        if($key==null) return false;
        $this->BotKey = $key;
        return true;
    }
    public function GetUserName($GroupID = 1){
        $path = "https://api.telegram.org/bot".$this->BotKey."/getUpdates";
        $UrlInfo = array();
        $re_inf = $this->CurlAPI($path,$UrlInfo);
        echo "<pre>";
        print_r($re_inf);
        echo "</pre>";

        if( isset($re_inf["error_code"]) ){
            return false;
        }else{
            $Peo = $re_inf["result"];
            $nowdatetime = date("Y-m-d H:i:s");
            foreach($Peo as $k => $v){
                $from       = $v["message"]["from"];
                $user_id    = $from["id"];
                $first_name = isset($from["first_name"]) ? $from["first_name"] : "";
                $last_name  = isset($from["last_name"]) ? $from["last_name"] : "";
                $username   = isset($from["username"]) ? $from["username"] : "";
                $info       = $this->mod->select("
                    SELECT *
                    FROM `tb_telegram_user`
                    where group_id = '".$GroupID."'
                    And user_id = '".$user_id."'
                    limit 0,1
                ",array(),true);
                if($info){
                    $info = $info[0];
                    if($first_name != $info["first_name"] || $last_name != $info["last_name"] || $username != $info["username"]){
                        $this->mod->modi_by("tb_telegram_user",array("id" => $info["id"]),array(
                            "first_name"    => $first_name,
                            "last_name"     => $last_name,
                            "username"      => $username
                        ));
                    }
                }else{
                    $add = $this->mod->add_by("tb_telegram_user",array(
                        "group_id"      => $GroupID,
                        "user_id"       => $user_id,
                        "first_name"    => $first_name,
                        "last_name"     => $last_name,
                        "username"      => $username,
                        "enable"        => "N",
                        "SetupTime"     => $nowdatetime,
                        "nowtime"       => $nowdatetime
                    ));
                }
            }
            return $re_inf;
        }
    }
    public function AddNoticeToGroup($GroupID,$Msg){
        $nowdatetime = date("Y-m-d H:i:s");
        $GroupInfo = $this->mod->select("
            SELECT *
            FROM `tb_telegram_group`
            where id = '".$GroupID."'
            limit 0 , 1
        ");
        if($GroupInfo){
            $GroupInfo = $GroupInfo[0];
            $UserInfo = $this->mod->select("
                SELECT *
                FROM `tb_telegram_user`
                where group_id = '".$GroupInfo["id"]."'
                And enable = 'Y'
            ");
            foreach($UserInfo as $k => $user){
                $add = $this->mod->add_by("tb_telegram_notice",array(
                    "tb_id"         => $user["id"],
                    "user_id"       => $user["user_id"],
                    "content"       => $Msg,
                    "notice"        => "N",
                    "SetupTime"     => $nowdatetime,
                    "nowtime"       => $nowdatetime
                ));
            }
            return true;
        }else{
            return false;
        }
    }
    
    public function SendNotice($Times = 10){
        $info = $this->mod->select("
            SELECT a.*,b.notice as tgNotice,b.cname
            FROM `tb_telegram_notice` as a
            LEFT JOIN LT_game as b ON a.game_id = b.id
            where a.notice = 'N'
            limit 0 , ".$Times."
        ");
        $info = $this->mod->select("
            SELECT a.*,b.notice as tgNotice,b.cname
            FROM `tb_telegram_notice` as a
            LEFT JOIN LT_game as b ON a.game_id = b.id
            where a.id = 629455
        ");
        
        if($info){
            foreach ($info as $k => $v) {
                if ($v['tgNotice'] == 'Y') {
                    switch ($v["type_id"]) {
                        case '2':
                            $v["content"] = $this->editEmergencyNotice($v, $v['cname']);
                            $this->SendToServer($v, '8');
                            break;
                        case '3':
                        case '4':
                        case '5':
                            $this->SendToServer($v, '8');
                            break;
                        case '9':
                        case '10':
                        case '11':
                            $this->SendToServer($v, '7');
                            break;
                        default:
                            switch($this->gdata['country_code']){
                                case 'CN':
                                    $this->SendToServer($v, '2');
                                    break;
                                case 'HK':
                                    $this->SendToServer($v, '3');
                                    break;
                            }
                            break;
                    }
                }

                $this->mod->modi_by('tb_telegram_notice', array('id' => $v["id"]), array('notice' => 'Y'));
            }
            return count($info);
        }else{
            return false;
        }
    }
    public function SendMessageToGroup($GroupID,$Msg){
        $GroupInfo = $this->mod->select("
            SELECT *
            FROM `tb_telegram_group`
            where id = '".$GroupID."'
            limit 0 , 1
        ");
        if($GroupInfo){
            $GroupInfo = $GroupInfo[0];
            $UserInfo = $this->mod->select("
                SELECT *
                FROM `tb_telegram_user`
                where group_id = '".$GroupInfo["id"]."'
                And enable = 'Y'
            ");
            foreach($UserInfo as $k => $user){
                $this->SendMessage($user["user_id"],$Msg);
            }
            return true;
        }else{
            return false;
        }
    }
    public function SendMessage($Send_ID, $msg = null, $game_id = null){
        $msg = isset($_POST["msg"]) ? $_POST["msg"] : $msg;
        $path = "https://api.telegram.org/bot".$this->BotKey."/sendMessage";
        $UrlInfo = array();
        $UrlInfo["text"] = $msg;
        $UrlInfo["chat_id"] = $Send_ID;
        $re_inf = $this->CurlAPI($path, $UrlInfo, $game_id);

        if( isset($re_inf["error_code"]) ){
            return false;
        }else{
            return true;
        }
    }
    private function CurlAPI($path, $UrlInfo, $game_id){
        $post = curl_init($path);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($post, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($post, CURLOPT_POST,1);
        curl_setopt($post, CURLOPT_POSTFIELDS,http_build_query($UrlInfo));
        $resp = curl_exec($post);
        $re_inf = json_decode($resp,true);
        return $re_inf;
    }
    //本來應該由telegram通知的 訊息 傳回到台灣主機
    private function SendToServer($Notice, $group = null){
        if ($group == null) return;
        $apiPath = "telegram/addNoticeToGroup";
        $path = $this->gdata['game_bo_domain'].$apiPath;
        $parm = array();
        $parm["msg"] = '';  
        $parm["groupId"] = $group;
        
        $parm["msg"] = "【官方】" . $Notice["content"];
        $this->util->curl($path , $parm);
    }

    /**
     * 編輯開獎錯誤Telegram訊息
     * @param  Array $notice 通知資訊
     * @param  string $gameName 遊戲名稱
     * @return string
     */
    private function editEmergencyNotice($notice, $gameName){
        $notice["content"] = "開獎號碼有誤\n".$gameName;
        $notice["content"] = $this->editEmergencyNoticeHostLocation($notice);
        switch ($notice['game_id']) {
            case 110:
                $notice["content"] = $this->editHnEmergencyNotice($notice);
                break;
            case 155:
                $notice["content"] = $this->editSg4dEmergencyNotice($notice);
                break;
            default:
                $notice["content"] = $this->editRegularEmergencyNotice($notice);
        }
        return $notice["content"];
    }

    /**
     * 編輯開獎錯誤地區Telegram訊息
     * @param  Array $notice 通知資訊
     * @return string
     */
    private function editEmergencyNoticeHostLocation($notice){
        if ($this->gdata['country_code'] == 'TEST') {
            return $notice["content"] = "【測試】 ".$notice["content"];
        }
        if ($this->gdata['country_code'] == 'CN') {
            $notice["content"] = "【大陸】 ".$notice["content"];
        } 
        if ($this->gdata['country_code'] == 'HK') {
            $notice["content"] = "【香港】 ".$notice["content"];
        }
        return $notice["content"];
    }

    /**
     * 編輯一般彩種錯誤Telegram訊息
     * @param  Array $notice 通知資訊
     * @return string
     */
    private function editRegularEmergencyNotice($notice){
        $lotteryOpenData = array_shift($this->mod->select("
            SELECT *
            FROM `LT_periods`
            where game_id = '".$notice['game_id']."'
            And period_str = '".$notice['period_str']."'
        "));
        $notice["content"] = $notice["content"]."\n◆開獎日期：".$lotteryOpenData["period_date"]."\n◆開獎期數：".$lotteryOpenData['period_str']."\n◆開獎號碼：".$lotteryOpenData["lottery"];
        $lotteryOpenHistory = $this->mod->select("
            SELECT *
            FROM `LT_history`
            where game_id = '".$notice['game_id']."'
            And period_str = '".$notice['period_str']."'
        ");
        $notice["content"] = $notice["content"]."\n◆抓獎紀錄：";
        foreach ($lotteryOpenHistory as $historyKey => $history) {
            $lotteryOpenUrl = array_shift($this->mod->select("
                SELECT *
                FROM `LT_url`
                where id = '".$history['url_id']."'
            "));
            $lotteryOpenUrl['code_order'] = $lotteryOpenUrl['code_order'] == 0 ? '(高)' : '(低)';
            $notice["content"] = $notice["content"]."\n".($historyKey+1).". ".$lotteryOpenUrl['url_name']."，權重 ".$lotteryOpenUrl['code_order']."，".$history['lottery'];
        }
        return $notice["content"];
    }

    /**
     * 編輯越南彩錯誤Telegram訊息
     * @param  Array $notice 通知資訊
     * @return string
     */
    private function editHnEmergencyNotice($notice){
        $cityId = explode("_",$notice['period_str'])[1]; 
        $prizeLevels = array("特獎", "一獎",  "二獎",  "三獎", "四獎", "五獎", "六獎", "七獎", "八獎" );
        $hnCity = array_shift($this->mod->select("
            SELECT *
            FROM `hn_city`
            where id = '".$cityId."'
        "));
        $notice["content"] = $notice["content"]."\n◆開獎地區：".$hnCity['city_ch']."(".$hnCity['city_en'].")";
        $lotteryOpenData = array_shift($this->mod->select("
            SELECT *
            FROM `LT_periods`
            where game_id = '".$notice['game_id']."'
            And period_str = '".$notice['period_str']."'
        "));
        $openCode = json_decode($lotteryOpenData["lottery"]);
        $prizeContext = "";
        foreach($openCode as $level => $prizes){
            $prizeContext = $prizeContext."\n".$prizeLevels[$level]." ".implode(", ",$prizes);
        }
        $notice["content"] = $notice["content"]."\n◆開獎日期：".$lotteryOpenData["period_date"]."\n◆開獎期數：".$lotteryOpenData['period_str']."\n◆開獎號碼：".$prizeContext;
        $lotteryOpenHistory = $this->mod->select("
            SELECT *
            FROM `LT_history`
            where game_id = '".$notice['game_id']."'
            And period_str = '".$notice['period_str']."'
        ");
        $notice["content"] = $notice["content"]."\n◆抓獎紀錄：";
        foreach ($lotteryOpenHistory as $historyKey => $history) {
            $lotteryOpenUrl = array_shift($this->mod->select("
                SELECT *
                FROM `LT_url`
                where id = '".$history['url_id']."'
            "));
            if (empty($lotteryOpenUrl)){
                $lotteryOpenUrl['url_name'] = "official";
                $lotteryOpenUrl['code_order'] = "(無)";
            } else {
                $lotteryOpenUrl['code_order'] = $lotteryOpenUrl['code_order'] == 0 ? '(高)' : '(低)';
            }
            $openCode = json_decode($history['lottery']);
            $prizeContext = "";
            foreach($openCode as $level => $prizes){
                $prizeContext = $prizeContext."\n".$prizeLevels[$level]." ".implode(", ",$prizes);
            }
            $notice["content"] = $notice["content"]."\n".($historyKey+1).". ".$lotteryOpenUrl['url_name']."，權重 ".$lotteryOpenUrl['code_order']."，".$prizeContext;
        }
        
        return $notice["content"];
    }

    /**
     * 編輯萬字票錯誤Telegram訊息
     * @param  Array $notice 通知資訊
     * @return string
     */
    private function editSg4dEmergencyNotice($notice){
        $prizeLevels = array("特獎", "一獎",  "二獎",  "三獎", "四獎");
        $lotteryOpenData = array_shift($this->mod->select("
            SELECT *
            FROM `LT_periods`
            where game_id = '".$notice['game_id']."'
            And period_str = '".$notice['period_str']."'
        "));
        $openCode = json_decode($lotteryOpenData["lottery"]);
        $prizeContext = "";
        foreach($openCode as $level => $prizes){
            $prizeContext = $prizeContext."\n".$prizeLevels[$level]." ".implode(", ",$prizes);
        }
        $notice["content"] = $notice["content"]."\n◆開獎日期：".$lotteryOpenData["period_date"]."\n◆開獎期數：".$lotteryOpenData['period_str']."\n◆開獎號碼：".$prizeContext;
        $lotteryOpenHistory = $this->mod->select("
            SELECT *
            FROM `LT_history`
            where game_id = '".$notice['game_id']."'
            And period_str = '".$notice['period_str']."'
        ");
        $notice["content"] = $notice["content"]."\n◆抓獎紀錄：";
        foreach ($lotteryOpenHistory as $historyKey => $history) {
            $lotteryOpenUrl = array_shift($this->mod->select("
                SELECT *
                FROM `LT_url`
                where id = '".$history['url_id']."'
            "));
            $lotteryOpenUrl['code_order'] = $lotteryOpenUrl['code_order'] == 0 ? '(高)' : '(低)';
            $openCode = json_decode($history['lottery']);
            $prizeContext = "";
            foreach($openCode as $level => $prizes){
                $prizeContext = $prizeContext."\n".$prizeLevels[$level]." ".implode(", ",$prizes);
            }
            $notice["content"] = $notice["content"]."\n".($historyKey+1).". ".$lotteryOpenUrl['url_name']."，權重 ".$lotteryOpenUrl['code_order']."，".$prizeContext;
        }
        return $notice["content"];
    }
}