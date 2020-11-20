<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
class sys extends lib_base{
	function __construct(){
		parent::__construct();
	}
	public function SendMessage($Phone,$Content){
		$Act = "it.bcad8@gmail.com";
		$Pwd = "f1629798";
		$Url = "http://www.smsgo.com.tw/sms_gw/sendsms.aspx?";
		$Url .= "&username=".urlencode($Act);
		$Url .= "&password=".urlencode($Pwd);
		$Url .= "&dstaddr=".urlencode($Phone);
		$Url .= "&smbody=".urlencode($Content);
		//echo $Url;
		$Re = file_get_contents($Url);
		//echo $Re;
		$ReAry = array();
		/*$Re ="msgid=1511260211383527
statuscode=0
statusstr=OK
point=1";*/
		$Re = explode("\n", trim($Re));
		foreach($Re as $v){
			$a = explode("=", $v);
			if(isset($a[1])){
				$ReAry[$a[0]] = $a[1];
			}
		}
		/*$nowdatetime = date("Y-m-d H:i:s");
		$add = $this->mod->add_by("tb_send_messages_secord",array(
			"phone"			=> $Phone,
			"content"		=> $Content,
			"msgid"			=> isset($ReAry['msgid']) ? $ReAry['msgid'] : "-",
			"re_code"		=> isset($ReAry['statuscode']) ? $ReAry['statuscode'] : "-",
			"re_str"		=> isset($ReAry['statusstr']) ? $ReAry['statusstr'] : "-",
			"point"			=> isset($ReAry['point']) ? $ReAry['point'] : "0",
			"SetupTime"		=> $nowdatetime
		));*/
		if($ReAry["statuscode"]=="0"){
			return true;
		}else{
			return false;
		}
	}
	public function SendEmail($Email,$Title,$Content){
		$this->ci->load->library('email');
		$config = array('mailtype' => 'html');
		$this->ci->email->initialize($config);
		$SendEmail = 'av533av@163.com';
		$SendUser = '擎磊系統';
		$Title = $Title;
		$ToEmail = $Email;
		$Content = $Content;
		
		$this->ci->email->from($SendEmail,$SendUser);
		$this->ci->email->to($ToEmail);
		$this->ci->email->subject($Title);
		$this->ci->email->message($Content);
		if($this->ci->email->send()){
			return true;
		}else{
			$this->ci->email->print_debugger();
			//$this->ci->obj["code"] = "403";
			return false;
		}
	}
}