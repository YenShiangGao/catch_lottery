<?php
/**
 * �`�� LOG ��k
 * NOTE: 2017-06-15 �إߡA�C�C��U���|�Ψ쪺���� LOG FUNCTION ��z�ܦ�
 */
require_once('ext/lib_base'.EXT);
class logs extends lib_base {
    public function __construct() {
        parent::__construct();
    }
    public function api_rec ($gameType=null, $url='', $postdata=array(), $descr=array()){
        if(is_array($postdata)) $postdata = json_encode($postdata);
        if(is_array($descr)) $descr = json_encode($descr);
        switch($gameType){
            case null:
                $gameType = "unknow";
                break;
        }
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : array();
        $data = array(
			"url"      => $url,
			"gameType" => $gameType,
			"post"     => $postdata,
			"descr"    => $descr,
			"ua"       => json_encode($ua),
			"itime"    => date("Y-m-d H:i:s")
        );
        $this->mod->add_by("LT_api_rec", $data);
    }
}
