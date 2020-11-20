<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class web_ctl extends web_base {
	public $ver=1;
	public $libc;
	public $lib_ac;
	public $adata;
	function __construct(){
  	parent::__construct();
  	$this->gdata["ver"]=$this->ver;
  	$this->gdata["inc_file"]=$this->parser->parse("ctl/inc_file.html",$this->gdata,true);
  	$this->libc=$this->get_lib("lib_codes");
  	$this->lib_ac=$this->get_lib("lib_acc_ctl");
  	$this->lib_ac->skey=get_class();
  	//$this->ci->op_rec=$this->op_rec;
  }
  public function chklogin($cary){
  	$this->lib_ac->pass=$cary;
  	$chk=$this->lib_ac->chklog();
  	if(!$chk){
  		$this->output();
  	}
  }
  public function op_rec($type,$tb,$tbid,$ary){
  	$no=array("tb_acc_login"=> 1);
  	if(array_key_exists($tb,$no)){
  		return true;
  	}
  	$types=array("add"=>0,"mod"=>1,"del"=>2);
  	$type=$types[$type];
  	$this->mod->insert("INSERT INTO tb_op_rec (tb,tb_id,type,vals,acc_tb,acc_tb_id,itime)VALUES(?,?,?,?,?,?,?)",array(
  		$tb,$tbid,$type,json_encode($ary),"tb_acc_ctl",$this->lib_ac->kinfo("id"),Date("Y-m-d H:i:s")
  	));
  }
}