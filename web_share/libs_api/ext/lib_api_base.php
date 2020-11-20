<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class lib_api_base{
	public $ci;
	public $gdata;
	public $obj;
	public $api_url;
	public $api_code;
	public $api_act;
	public function lib_api_base(){
		$this->ci =& get_instance();
		$this->ci->load->library("lib_codes");
		$this->gdata =& $this->ci->gdata;
		$this->obj =& $this->ci->obj;
		require_once('../web_conf/config_main_api.inc');
		$main_api = get_main_api();
		$this->api_url = $main_api["url"];
		$this->api_code = $main_api["code"];
		$this->api_act = $main_api["act"];
	}
	public function output($data="json"){
		return $this->ci->output($data);
	}
}