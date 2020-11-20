<?php

require_once 'vendor/autoload.php';

class cls_g_client{
	public $obj_client=null;

	private $obj_sheet=null;

	public function __construct() {
        $client = new Google_Client();

        $client->setApplicationName('Google Sheets API Quickstart');

        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);

        $client->setAccessType('offline');

        $client->setAuthConfig(dirname(__FILE__).'/client_secret.json');

		$this->obj_client=$client;

  }

  public function obj_sheet(){

  	if(!$this->obj_sheet){

  		$this->obj_sheet = new clsSheet($this->obj_client);

  	}

  	return $this->obj_sheet;

  }

}

class clsSheet{
    private $cls_gsvr_sht=null;

    public function __construct($ref) {

         $this->cls_gsvr_sht = new Google_Service_Sheets($ref);

         return $this->cls_gsvr_sht;

	}

	public function obj_get_by_rng($str_sid,$str_rng){

		$obj = $this->cls_gsvr_sht->spreadsheets_values->get($str_sid, $str_rng, ['majorDimension' => 'ROWS']);

		return json_decode(json_encode($obj),true);

	}

}