<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class web_base extends CI_Controller {
	public $gdata = array();
	public $burl;
	public $furl;
	public $mod;
	public $tpl_p;
	public $libs = array();
	public $obj  = array();
	public $dbp  = array();
	public $modp = array();
	/**proxy*/
	public $util;
	public $logs;
	public $pxy;
	/********/
	public function __construct() {
		parent::__construct();
		$this->load->helper('cookie');
		$this->burl = $this->config->item('base_url');
		$this->furl = $this->config->item('furl');
		$this->country_code = $this->config->item('country_code');
		$this->game_bo_domain = $this->config->item('game_bo_domain');
		/**proxy*/
		$this->util = $this->man_lib('utils');
		$this->logs = $this->man_lib('logs');
		$this->pxy  = $this->man_lib('proxys');
		/********/

		$this->gdata["burl"] = $this->burl;
		$this->gdata["furl"] = $this->furl;

		$this->gdata["country_code"] = $this->country_code;
		$this->gdata["game_bo_domain"] = $this->game_bo_domain;

		$this->obj["code"] = "403";
		$this->load->library("parser");
		$this->init();
	}
	private function init() {
		$this->mod = $this->get_mod("mod_msys");
	}
	public function get_mod($mod) {
		$this->load->model($mod);
		return $this->$mod;
	}
	public function get_lib($lib) {
		$this->load->library($lib);
		return $this->$lib;
	}
	public function man_lib($lib, $dir = "libs") {
		if (array_key_exists($lib, $this->libs)) {
			return $this->libs[$lib];
		}
		require_once '../web_share/' . $dir . '/' . $lib . EXT;
		$str              = $lib;
		$this->libs[$lib] = new $str();
		return $this->libs[$lib];
	}
	public function url_lib($lib, $dir = "libs/game_url") {
		if (array_key_exists($lib, $this->libs)) {
			return $this->libs[$lib];
		}
		require_once '../web_share/' . $dir . '/' . $lib . EXT;
		$str              = $lib;
		$this->libs[$lib] = new $str();
		return $this->libs[$lib];

	}
	public function output($otype = "json") {
		$this->mod->CloseDB();
		switch ($otype) {
			case "json":
				$this->json_output();
				break;
			case "xml":
				$this->xml_output();
				break;
			case "html":
				$this->html_output();
				break;
		}
	}
	private function html_output() {
		$this->parser->parse($tpl_p, $this->gdata);
	}
	private function json_output() {
		exit(json_encode($this->obj));
	}
	private function xml_output() {
		exit(json_encode($this->obj));
	}
}