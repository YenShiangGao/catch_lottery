<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
class lib_redis extends lib_base{
	public $redis;
	public $rtype;
	public $set;
	public $now_database;
	function __construct(){
		parent::__construct();
		require_once('../web_conf/config_mem.inc');
		$this->set = get_conf_mem();
		if(!$this->redis){
			$this->redis = new Redis();
			$this->LinkServer("game");
		}
	}
	public function LinkServer($rtype = "game"){
		$this->rtype = $rtype;
		$this->redis->connect($this->set["redis"][$this->rtype]["ip"],$this->set["redis"][$this->rtype]["port"]);
		$this->now_database = 0;
	}
	public function UseDatabase($to=0){
		if($to==$this->now_database){
			return;
		}
		$this->now_database = $to;
		$this->redis->select($to);
	}
	public function GetValue($key){
		$val = $this->redis->get($key);
		if($this->isJSON($val)){
			$val = json_decode($val,true);
		}
		return $val;
	}
	public function SetValue($key,$val,$seconds=0){
		if(is_array($val)){
			$val = json_encode($val);
		}
		if($seconds==0){
			$this->redis->set($key,$val);
		}else{
			$this->redis->setex($key,$seconds,$val);
		}
	}
	public function Delete($key=null,$key2=null){
		if($key==null && $key2==null){return;}
		if($key2==null){
			$this->redis->delete($key);
		}else{
			$this->redis->hdel($key,$key2);
		}
	}
	public function SetHash($key,$key2,$val){
		if(is_array($val)){
			$val = json_encode($val);
		}
		$this->redis->hmset($key,array($key2=>$val));
	}
	public function SetHashAry($key,$kav){
		if(!is_array($kav)){ return false;}
		foreach($kav as $k => $v){
			if(is_array($v)){
				$kav[$k] = json_encode($v);
			}
		}
		$this->redis->hmset($key,$kav);
	}
	public function GetHash($key,$key2){
		$val = $this->redis->hmGet($key,array($key2));
		$val = $val[$key2];
		if($this->isJSON($val)){
			$val = json_decode($val,true);
		}
		return $val;
	}
	public function GetHashAll($key){
		$ary = $this->redis->hgetall($key);
		if(!is_array($ary)){ return false;}
		foreach($ary as $k=>$v){
			if($this->isJSON($v)){
				$v = json_decode($v,true);
				$ary[$k] = $v;
			}
		}
		return $ary;
	}
	public function isJSON($string){
	   return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}