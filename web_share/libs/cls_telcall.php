<?php
class telCall{
    	private $str_bot_id="174559560:AAH-m1F98n1yYujoKHrwJZ0_5VpENLmUHa4";
    	private $str_id="https://api.telegram.org/bot/sendMessage?chat_id=@wxurldet&text=shit%20my%20face";
    	public function __construct() {
    		return $this;
    	}
    	public function vod_call_ch($str_msg,$str_ch="wxurldet"){
    		$str_url="https://api.telegram.org/bot".$this->str_bot_id."/sendMessage?chat_id=@".$str_ch."&text=".$str_msg."&parse_mode=html";
    		echo $str_url;
    		$this->str_call($str_url);
    	}
    	private function str_call($str_url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $str_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($ch, CURLOPT_REFERER, $this->str_ref);
            $str_raw = curl_exec($ch);
            curl_close($ch);
            //echo $str_raw;
            return $ch;
    	}
    	
    }
?>