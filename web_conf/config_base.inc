<?php
/// 先用檔案判斷，期望使用SetEnv
if (!defined('UUID')) {
    if (isset($_SERVER['__UUID'])) {
        define('UUID', $_SERVER['__UUID']);
    }
    if (isset($_SERVER['__TESTMODE'])) {
        define('TESTMODE', $_SERVER['__TESTMODE']);
    }
    if (!defined('UUID')) {
        require_once WEBROOT_CUSTOM.'UUID';
    }
}

if (!defined('TESTMODE')) {
    define('TESTMODE', 0); // 上正式請設為0
}
require_once WEBROOT_CUSTOM.'web_conf/UUID/'.UUID.'/uuid_config.inc';
uuid_config::init();
function get_testmode_base() {
    return TESTMODE;
}
function get_base(){
	$conf=array();
	$conf['upl_dir'] = '/home/web_gs_pb/fun/'.uuid_config::$set['webroot'].'/web_file/upload/';
	$conf["is_test"] = TESTMODE;
	$conf['defaultLang'] = 'zh_cn';
    $conf['game_bo_domain'] = uuid_config::$set['game_bo_domain'];
    $conf['country_code'] = uuid_config::$set['country_code'];
	$conf['version']     = 14;
    $conf['fileAssets']  = 'assets/';
	$dom = explode(".",$_SERVER['HTTP_HOST']);
	$FileSubdomain = "cfile";
    switch(count($dom)){
        case 2:{
            $num = 0;
            break;
        }
        case 3:case 4:default:{
            $num = 1;
            break;
        };

    }
	$now_dom = '';
	for($i = $num; $i < count($dom); $i++){
		$now_dom  = $now_dom .".".$dom[$i];
	}
	$now_dom = substr($now_dom,1,strlen($now_dom));
	$conf['webDomain'] = $now_dom;
	$httpProtected = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');

	$conf['web_file'] = $httpProtected . $FileSubdomain . '.'.$now_dom.'/';

	if (isset($_SERVER['HTTP_ORIGINALHOST'])) {
        $conf['web_url'] = $httpProtected.$_SERVER['HTTP_ORIGINALHOST'].'/';
    } else {
        $conf['web_url'] = $httpProtected.$_SERVER['HTTP_HOST'].'/';
    }

    $doChgFile = true;
    $chgFileUrl = array(
        'url' => array(),
        'web' => array()
    );
    // if ($doChgFile) {
    //     if (isset($chgFileUrl['web'][$now_dom]) && isset($chgFileUrl['url'][$chgFileUrl['web'][$now_dom]])) {
    //         $conf['web_file'] = $chgFileUrl['url'][$chgFileUrl['web'][$now_dom]];
    //     }else{
    //         $conf['web_file'] = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? $conf['web_file'] : $httpProtected."gfile.dwmyxx.com/";
    //     }
    // }

	$conf['ag_url'] = 'http://ag.'.$now_dom.'/';

	/// 設定檔 web_conf/UUID/網站資料夾/uuid_config.inc
    $conf['dbs'] = uuid_config::$set['dbs'];

    $conf["redis"]= uuid_config::$set['redis'];

    return $conf;
}
function get_db_base($set){
	/// 設定檔 web_conf/UUID/網站資料夾/uuid_config.inc
    $db = uuid_config::$set['db'];
    return $db;
}
?>
