<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_api_base'.EXT);
class lib_api extends lib_api_base{
	public $api=null;
	public $parm;
	public function lib_api(){
		parent::__construct();
	}
}