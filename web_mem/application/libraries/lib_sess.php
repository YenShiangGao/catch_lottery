<?php
class lib_sess{
	public $ci;
	public $time;
	public $pass=array();
	public $info;
	public $libc;
	public $skey;
	public function lib_sess(){
		$this->ci =& get_instance();
		$this->ci->load->helper('cookie');
		$this->libc=$this->ci->get_lib("lib_codes");
		$this->time=60*60*4;
	}
	public function GetCookie($key=null){
		if($key==null){ return false; }
		return get_cookie($key);
	}	
	public function SetCookie($key=null,$name=null,$time=null){
		if($key==null || $name==null){ return false; }
		$time = $time==null ? $this->time : $time;
		$cookie = array(
			'name'		=> $key,
			'value'		=> $name,
			'expire' 	=> $time
		);
		set_cookie($cookie);
	}	
	public function set($set,$parms){
		foreach($set as $k => $v){
			$this->info[]= $parms[$set[$k]];
		}
		$this->set_sess();
	}
	public function chk(){
		$sess=get_cookie($this->skey);	
		if($sess==""){return false;}	
		$chk=$this->libc->aes_de($sess);
		$chk=explode("*",$chk);	
		if($chk < 1){return false;}
		$now=strtotime(DATE("YmdHis"));
		if($now-$chk[0] > $this->time){ return false;}
		array_shift($chk);
		$this->info=$chk;
		$this->set_sess();
		return true; 
	}
	public function logout(){
		delete_cookie($this->skey);
	}
	public function SetLogInfo($info){
		$info = implode("***",$info);
		$info = $this->libc->aes_en($info);
		$cookie = array(
			'name'		=> "LoginInfo",
			'value'		=> $info,
			'expire' 	=>(60*60)
		);
		set_cookie($cookie);
	}
	private function set_sess(){
		$code = $this->libc->aes_en(strtotime(DATE("YmdHis"))."*".implode("*",$this->info));
		$cookie = array('name'=> $this->skey,'value'=> $code,'expire' =>(60*60*24));
		set_cookie($cookie);
	}
	public function push($k,$v){
		$cookie = array('name'=> $this->skey."_".$k,'value'=> $v,'expire' =>(60*60*24));
		set_cookie($cookie);
	}
	public function pull($k,$d=false){
		if($d){
			$sess = get_cookie($k);
		}else{
			$sess = get_cookie($this->skey."_".$k);
		}
		return $sess;
	}
	public function destory(){
		$past = time() - 3600;
		foreach ( $_COOKIE as $k => $v ){
			setcookie( $k, "", $past,"/");
		}
	}
}