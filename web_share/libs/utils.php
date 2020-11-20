<?php
/**
 * 常用函式工具
 * -----------------------------------------------
 * CREATE DATE: 2017-06-19
 * UPDATE DATE: 2017-06-28
 */
require_once('ext/lib_base'.EXT);

class utils extends lib_base {
    public $version = '1.0.0';
    public $cookies = null;
    public function __construct() {
        parent::__construct();
    }
    public function test (){
        $this->pr("UTIL->".$this->version); die;
    }
    /** JSON decode extension */
    public function json_de($str='', $default=array()){
        $result = $default;
        if($str){
            try {
                $de = json_decode($str, true);
                if(!is_array($de)) $de = array();
            } catch (Exception $e) {
                $de = array(); 
            }
            $result = $de;
        }
        return $result;

    }
    /** 產生QRCODE */
    public function qrcode($data){
        require_once '../web_share/libs_3rd/phpqrcode/qrlib.php';
        $level = 'H';
        $size = 9;
        $url = $this->libc->aes_de($data);
        QRcode::png($url, false, $level, $size, 2);
    }
    
    /** 
     * 偵測手機裝置
     * @return boolean
     */
    public function detectMobile(){
        require_once '../web_share/libs_3rd/mobiledetect/Mobile_Detect.php';
        $detect = new Mobile_Detect;
        return  $detect->isMobile();
    }

    /** 取得使用者IP */
    public function get_user_ip(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
           $myip = $_SERVER['HTTP_CLIENT_IP'];
        }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
           $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
           $myip= $_SERVER['REMOTE_ADDR'];
        }
        return $myip;
    }

    /** 
     * 檢查IP位址 - IP黑名單、白名單、域名白名單、地域(鎖台灣)
     * @return boolean
     */
    public function chk_ip_loc() {
        require_once ('../web_conf/config_ip_loc.inc');
        if(ip_loc::dom_bypass()==true){
            return true;    //白名單網域
        }else if(ip_loc::$enable==0){
            return true;    //未啟用限制
        }
        $loc = ip_loc::loc_chk();
        $ip = true;
        if($loc!=true){
            $ip = ip_loc::ip_chk();
        }
        if(!$loc && !$ip){
            return false;
        }
        return true;
    }

    /**
     * 檢查IP是否在白名單
     * @return boolean
     */
    public function chk_ip_whitelist() {
        require_once ('../web_conf/config_ip_loc.inc');
        return ip_loc::ip_chk();
    }

    /** 
     * 通用 CURL POST
     * 
     * NOTE:
     * 預設 CURLOPT_SSL_VERIFYPEER 為 TRUE 代表要比對驗證伺服器憑證，與 CURL 程式本身所使用的 crt 憑證是否相符合。
     * 預設 CURLOPT_SSL_VERIFYHOST 為 2 代表除了要檢查 SSL 憑證內的 common name 是否存在外，也驗證是否符合伺服器的主機名稱。
     */
    public function curl ($gateway='', $postdata=array(), $opt=array(), $game='', $debug=false) {
        $data =(is_array($postdata)) ? http_build_query($postdata): $postdata;
        if(empty($gateway)) return false;

        $this->cookies = tempnam('/tmp', 'COOKIE');
      
        $defaults = array(
            "CONNECTTIMEOUT" => 30,
            "TIMEOUT"        => 60,
            "USERAGENT"      => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.72 Safari/537.36',
            "SSL_VERIFYHOST" => 2,
            "SSL_VERIFYPEER" => false,
            "HTTPHEADER"     => false,
        );
        $defaultHeader = array(
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.72 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.6,en;q=0.4',
            'Accept-Encoding: gzip,deflate,sdch',
            'Connection: keep-alive'
        );
		$ch      = curl_init();
		$opt     = array_merge($defaults, $opt);
		$curlopt = array(
			CURLOPT_SSL_VERIFYHOST => $opt['SSL_VERIFYHOST'],
			CURLOPT_SSL_VERIFYPEER => $opt['SSL_VERIFYPEER'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL            => $gateway,
			// CURLOPT_POSTFIELDS  => $data,
			CURLOPT_CONNECTTIMEOUT => $opt['CONNECTTIMEOUT'],
			CURLOPT_TIMEOUT        => $opt['TIMEOUT'],
			CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_COOKIEJAR      => $this->cookies,//取出對方給的cookie
            CURLOPT_COOKIEFILE     => $this->cookies,//送給對方的cookie
        );
        
        if (!empty($data)) {
        	$curlopt[CURLOPT_POSTFIELDS] = $data;
        }
        if($debug) {
            $curlopt[CURLINFO_HEADER_OUT] = true;
            $curlopt[CURLOPT_HEADER] = true;
        }
        //USERAGENT
        if($opt['USERAGENT']){
            $curlopt[CURLOPT_USERAGENT] = $opt['USERAGENT'];
        }
        //HTTPHEADER
        if(!empty($opt['HTTPHEADER'])){
            if($opt['HTTPHEADER']===true){
                $curlopt[CURLOPT_HTTPHEADER] = $defaultHeader;
            } elseif(is_array($opt['HTTPHEADER'])) {
                $curlopt[CURLOPT_HTTPHEADER] = $opt['HTTPHEADER'];
            }
        }
        $proxyLog = '';
        //PROXY
        if($this->pxy->enable){
            $curlopt[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $curlopt[CURLOPT_PROXY] = $this->pxy->current['IP'];
            $curlopt[CURLOPT_PROXYPORT] = $this->pxy->current['port'];
            $curlopt[CURLOPT_PROXYTYPE] = 'HTTP';
            $curlopt[CURLOPT_PROXYUSERPWD] = $this->pxy->current['proxy_acc'].':'.$this->pxy->current['proxy_pwd'];
            $proxyLog = json_encode($this->pxy->current);
        }
        curl_setopt_array($ch, $curlopt);

        $this->ci->benchmark->mark('curl_query');
        $resp = curl_exec($ch);
        $this->ci->benchmark->mark('curl_response');
        
        $err     = @curl_errno($ch); 
        $errmsg  = @curl_error($ch); 
        $header  = @curl_getinfo($ch); 
    
        $recDate = array(
            //'gateway'  => $gateway,
            //"proxy"    => $this->pxy->current['ip'],
            //"curlopt"  => $curlopt,
            //"response" => htmlentities($resp),
            "response" => $resp,
            "err"      => $err,
            "errmsg"   => $errmsg,
            "header"   => $header,
            "costtime" => $this->ci->benchmark->elapsed_time('curl_query','curl_response'),
            "proxy"    => $proxyLog
        );
        if($debug) $this->pr($recDate);
        /*2017-12-04 DB 負載太多暫時移除*/
        // $this->logs->api_rec($game, $gateway, $postdata, $recDate);
        // $fileName = date('Y-m-d H:i:s').".txt";
        // $myfile = fopen("../../log/".$fileName, "w");
        curl_close($ch);
        return $resp;
    }
    /** 
     * CURL 加 JSON decode
     * 回傳 JSON 必須帶 code 參數
     * 適合內部的常用 API 格式
     */
    public function curl_decode ($gateway='', $postdata=array(), $opt=array()){
        $resp = $this->curl($gateway, $postdata, $opt);
        try {
            $de = json_decode($resp, true);
            if(!is_array($de) || !isset($de['code'])){
                $de['code'] = 503;
                $de['resp'] = $resp;
            }
            $resp = $de;
        } catch (Exception $e) {
            $resp["code"] = 500;
            $resp["resp"] = $resp;
        }
        return $resp;
    }
    /** CURL DEBUG AND TEST */
    public function curl_debug ($gateway, $data=null, $opt=array()) {
        if(!$this->chk_ip_whitelist()) $this->outerr(503, 'INVALID CONNECTION.');
        if(is_array($data)) $data = http_build_query($data);
        return $this->curl($gateway, $data, $opt, true);
    }
    public function curl_test ($gateway, $data=null, $opt=array()) {
        return curl_debug($gateway, $data, $opt);
    }
}