<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
class lib_functionPage extends lib_base
{
    function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
    }
    public function gameList() {
        /**************遊戲列表**************/
        $sql     = "SELECT id,cname FROM LT_game WHERE enable = ?";
        $LT_game = $this->mod->select($sql, array(0));

        $gameGroup = array();
        foreach ($LT_game as $key => $value) {
            $gameGroup[$LT_game[$key]["id"]]["id"]   = $LT_game[$key]["id"];
            $gameGroup[$LT_game[$key]["id"]]["name"] = $LT_game[$key]["cname"];
        }
        /************************************/
        $this->obj["code"]   = 100;
        $this->obj["data"]   = $gameGroup;
        $this->output();
    }
    public function insertPeriods() {
        if (empty($_POST['gameID']) || empty($_POST['date']) || $_POST['date'] == 'ex.20200311') {
            $this->obj["code"]   = 403;
            $this->obj["msg"]   = '內容錯誤';
            $this->output();
            return;
        }
        
        $gameID = $_POST['gameID'];
        $date = $_POST['date'];
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/lotteryapi/autocreatdata/' .$gameID. '/' .$date;

        $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($ch);
        
        
        $data = $_POST;
        $data['host'] = $_SERVER['HTTP_HOST'];

	    $this->obj["code"]   = 100;
        $this->obj["data"]   = $data;
        $this->obj["msg"]   = $url;
        $this->output();

        curl_close($ch);
        
    }
}