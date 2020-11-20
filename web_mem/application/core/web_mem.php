<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class web_mem extends web_base {
	public $ver=1;
	public $langx_code=1;
	public $libc;
	public $lib_ac;
	public $adata;
	public $info=null;
	public $MemLog;
	public $LP;
	public $lib_sess;
	function __construct(){
		header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
		header("Access-Control-Allow-Origin: ".@$_SERVER['HTTP_ORIGIN']);
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Headers: Last-Event-Id, Origin, X-Requested-With, Content-Type, Accept, Authorization");
		parent::__construct();
		$this->LP           = $this->man_lib("language_pack");
		$this->gdata["ver"] = $this->ver;
		$this->libc         = $this->get_lib("lib_codes");
		$this->lib_ac       = $this->get_lib("lib_acc_mem");
		$this->lib_sess     = $this->get_lib("lib_sess");
		$this->lib_ac->skey = get_class();
	}
	public function WebInclude($type = null,$data = array(),$Html = true){
		if($type==null){return false;}
		return $this->LP->GetView($type,$data,$Html);
		//return $this->parser->parse("main/".$type,$data,$Html);
	}
	public function defaultms($code=null){
		// if($code==null){return false;}
		require_once('../web_conf/default/zh_cn.inc');
		$getdefaultcode = getdefaultcode();
		return $getdefaultcode;
	}
	//print data
	public function pr($obj, $isfag = false) {
		if ($isfag) {
			echo '<pre>'.htmlspecialchars(print_r($obj, true)).'</pre>';
		} else {
			echo '<pre>'.print_r($obj, true).'</pre>';
		}
	}
}