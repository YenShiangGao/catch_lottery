<?php
/**
 * PROXY切換工具
 * -----------------------------------------------
 * CREATE DATE: 2017-06-19
 * UPDATE DATE: 2017-06-20
 */
require_once('ext/lib_base'.EXT);
class proxys extends lib_base {
    public $version = '1.0.0';
    public $enable = false;
    public $list = array();
    public $current = null;
    public function __construct() {
        parent::__construct();
    }
    public function init ($enable=false) {
        $this->set($enable);
    }
    /**
     * [init description]
     * @param  integer $index random:-1, index:0,1,2...
     * @return [type]       [description]
     */
    
    public function set ($index=0) {
        if($index===false || is_null($index)){
            $this->off();
        } else {
            if($index===true) $index = 0;
            $this->list = $data = $this->get_proxy_list();
            if(!empty($data)){
                switch ($index){
                    case -1:
                        shuffle($data);
                        $this->current = $data[0];
                        break;
                    default:
                        $data = (isset($data[$index])) ? $data[$index] : $data[0];
                        $this->current = $data;
                        break;
                }
                $this->enable = true;
            } else {
                $this->off();
            }
        }
    }
    public function off () {
        $this->list    = array();
        $this->current = null;
        $this->enable  =  false;
    }

    /**
     * 取得資料庫 proxy 設定
     * @return array
     */
    private function get_proxy_list (){
    	$sql = "SELECT IP, port, proxy_acc, proxy_pwd FROM LT_proxy WHERE enable=?";
		$result   = $this->mod->select($sql, array(0));

        return $result;
    }
}