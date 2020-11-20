<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class lib_acc_mem extends lib_acc{
	private $tb = "tb_member";
	private $acc = null;
	public function lib_acc_mem(){
		parent::__construct();
	}
}