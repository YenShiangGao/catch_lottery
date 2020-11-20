<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
class language_pack extends lib_base{
	public $PresetLanguage = "zh_cn";
	public $HaveLanguage = array("zh_cn","kr_ko");
	public $domain = "main";
	private $TextAry;
	public $Agent=null;
	function __construct(){
		parent::__construct();
		$this->ci =& get_instance();
	}
	public function SetAgentID($agent=null){
		$this->Agent = $agent;
	}
	public function GetLangStr($key = null,$lang = null,$exend=null){
		if($key==null) {return;}
		if($lang==null) {$lang = $this->NowLanguage();}
		//echo $key."<br>";
		if(!isset($this->TextAry[$lang])){
			$this->TextAry[$lang] = $this->GetLanguageText($lang);
		}
		$LangData = $this->TextAry[$lang];
		if(isset($LangData[$key])){
			$str = $LangData[$key];
			if($exend!=null){
				foreach($exend as $k=>$v){
					$str2 = '['.$k.']';
					if (false !== ($rst = strpos($str, $str2))) {
						$str = str_replace ($str2,$v,$str);
					}
				}
			}
			return $str;
		}else{
			return false;
		}
	}
	public function PullStrByLang($str = null,$lang = null){
		if($str==null) {return;}
		if($lang==null) {$lang = $this->NowLanguage();}
		if(!isset($this->TextAry[$lang])){
			$this->TextAry[$lang] = $this->GetLanguageText($lang);
		}
		$LangData = $this->TextAry[$lang];
		$key = array();
		$t = explode("[",$str);
		foreach($t as $k=>$v){
			$tm = explode("]",$v);
			$key[] = $tm[0];
		}
		if(!is_array($key)){
			$str2 = '['.$key.']';
			$val = $this->GetLangStr($key,$lang);
			if($val){
				if (false !== ($rst = strpos($str, $str2))) {
					$str = str_replace ($str2,$val,$str);
				}
			}
		}else{
			foreach($key as $k){
				$str2 = '['.$k.']';
				$val = $this->GetLangStr($k,$lang);
				if($val){
					if (false !== ($rst = strpos($str, $str2))) {
						$str = str_replace ($str2,$val,$str);
					}
				}
			}
		}
		return $str;
	}
	public function PushToJson($LangKey=null,$exend=array()){
		if($LangKey==null) {return;}
		$ary = array();
		$ary["language_key"] = $LangKey;
		$ary["exend"] = $exend;
		return json_encode($ary);
	}
	public function GetView($view=null,$data=array(),$return = false){
		$lang = $this->NowLanguage();
		$this->ci->obj["lang"] = $lang;
		if($view==null) {return;}
		if(!isset($this->TextAry[$lang])){
			$this->TextAry[$lang] = $this->GetLanguageText($lang);
		}
		$LangData = $this->TextAry[$lang];
		$LangData["language"] = $lang;

		if(is_file("../web_view/".$this->domain."/".$lang."/".$view)){
			$View = $this->ci->parser->parse($this->domain."/".$lang."/".$view,array_merge($data, $LangData),$return);
		}else{
			$View = $this->ci->parser->parse($this->domain."/"."zh_cn/".$view,array_merge($data, $LangData),$return);
		}

		return $View;
	}
	public function NowLanguage(){
		$Language = $this->PullLanguage();
		if($Language == ""){
			$this->PushLanguage($this->PresetLanguage);
			$Language = $this->PresetLanguage;
		}
		return $Language;
	}
	public function PushLanguage($v){
		$cookie = array('name'=> "Language",'value'=> $v,'expire' =>(60*60*24));
		set_cookie($cookie);
	}
	public function PullLanguage(){
		$sess = get_cookie("Language");
		return $sess;
	}
	public function GetAllLanguage($Enable = false){
		$Condition = '';
		if($Enable){
			$Condition = " And enable ='1'";
		}
		$LangxInf = $this->mod->select("SELECT * FROM tb_langx where 1 ".$Condition." order by id");
		return $LangxInf;
	}
	public function GetLanguageInfo($code){
		$raw = $this->mod->get_by("tb_langx",array("code"=> $code));
		if(count($raw)==0){return false;}
		return $raw[0];
	}
	public function GetJavascriptLanguagePack($lang=null){
		if($lang==null){
			$lang = $this->NowLanguage();
		}
		$LangData = $this->GetLanguageText($lang);
		$Ary = array();
		foreach($LangData as $k => $v){
			$temp = array();
			$temp["key"] = $k;
			$temp["val"] = $v;
			$Ary[] = $temp;
		}
		$data["Lang"] = $Ary;
		$View = $this->ci->parser->parse("Language.js",$data,true);
		return $View;
	}
	public function GetLanguageText($lang=null){
		if($lang==null){
			$lang = $this->NowLanguage();
		}
		$Data = array();
		switch($lang){
			case 'zh_cn':{
				require_once('../web_conf/languages/mem/'.$lang.'.inc');
				$Data = GetLanguageInf();
				if(isset($this->ci->lib_ac)){
					$agent_id = null;
					if($this->Agent!=null){
						$agent_id = $this->Agent;
					}else{
						$LoginInfo = $this->ci->lib_ac->pull("LoginInfo",true);
						if($LoginInfo){
							$code_info = explode("***",$this->ci->libc->aes_de($LoginInfo));
							$this->MemLog = new stdClass();
							$this->MemLog->mem_id = $code_info[0];
							$this->MemLog->mem_act = $code_info[1];
							$this->MemLog->log_id = $code_info[2];
							$this->MemLog->game_log_id = isset($code_info[3]) ? $code_info[3] : "0";
							$this->MemLog->agent_id = $code_info[4];
							$agent_id = $this->MemLog->agent_id;
							
						}
					}
					if($agent_id!="1"){
						require_once('../web_conf/languages/mem/agent_5/'.$lang.'.inc');
						$AG_Data = GetLanguageInf_5();
						$Data = array_merge($Data,$AG_Data);
					}
				}
				break;
			}
			case 'kr_ko':{
				require_once('../web_conf/languages/mem/'.$lang.'.inc');
				$Data = GetLanguageInf();
				break;
			}
			default:{
				require_once('../web_conf/languages/mem/zh_cn.inc');
				$Data = GetLanguageInf();
			}
		}
		return $Data;
	}
}