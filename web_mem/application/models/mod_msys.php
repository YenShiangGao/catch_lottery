<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class mod_msys extends mod_ext {
	function __construct(){
		$this->dbw_str = "db_w";
		$this->dbr_str = "db_r";
		parent::__construct(); 
	}
	public function quick_db(){
		$this->dbw_str = "db_w";
		$this->dbr_str = "db_w";
		$this->db_con();
	}
}