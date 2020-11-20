<?php
class chkUrlWx{
        private $str_url_test;
        public $obj_result;
        private $str_ref="https://guanjia.qq.com/online_server/result.html";
        private $ary_wx_text=array("https://cgi.urlsec.qq.com/index.php?m=check&a=check&callback=jQuery172077612693898703_1530244552230&url=","&_=1530244552544");
        public function __construct($str_url) {
            $this->str_url_test=$str_url;
            $this->str_call();
        }
        private function str_call(){
            $str_url_test=urlencode($this->str_url_test);
            $str_url_send = $this->ary_wx_text[0].$str_url_test.$this->ary_wx_text[1];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $str_url_send);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($ch, CURLOPT_REFERER, $this->str_ref);
            $str_raw = curl_exec($ch);
            curl_close($ch);
            $obj_rt=array("code"=>403);
            $ary_raw=explode("({",$str_raw);
            if(count($ary_raw)==1){
                $this->obj_result=$obj_rt;
                return;
            }
            $ary_raw=explode("})",$ary_raw[1]);
            if(count($ary_raw)==1){
                $this->obj_result=$obj_rt;
                return;
            }
            $obj=json_decode("{".$ary_raw[0]."}",true);
            if($obj["data"]["results"]["whitetype"]==1){
                $obj_rt["code"]=100;
                $obj_rt["obj_result"]=$obj["data"]["results"];
            }else{
                $obj_rt["code"]=200;
                $obj_rt["obj_result"]=$obj["data"]["results"];
            }
            $this->obj_result=$obj_rt;
        }
    }
?>