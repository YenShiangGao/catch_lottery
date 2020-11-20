<?php
class uuid_config {
    public static $set = array();
    /// 正式環境
    public static function envPro() {
        $conf = array();

        $conf["dbs"]=array();
        $conf["dbs"]["dbw"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbw"]["pwd"]="edGB_12";
        $conf["dbs"]["dbw"]["adr"]="192.168.30.179";

        $conf["dbs"]["dbr"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbr"]["pwd"]="edGB_12";
        $conf["dbs"]["dbr"]["adr"]="192.168.30.180";

        $conf["dbs"]["dbw_2"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbw_2"]["pwd"]="edGB_12";
        $conf["dbs"]["dbw_2"]["adr"]="192.168.30.179";

        $conf["dbs"]["dbr_2"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbr_2"]["pwd"]="edGB_12";
        $conf["dbs"]["dbr_2"]["adr"]="192.168.30.180";

        $conf["redis"]["game"]["ip"]= "192.168.20.169";
        $conf["redis"]["game"]["port"]= "6379";

        $conf["game_bo_domain"] = "http://boolottery.com/";
        $conf["country_code"] = ""; // 大陸地區設定 CN， 香港地區設定 HK

        return $conf;
    }
    /// 測試環境
    public static function envTest() {
        $conf = array();
        $conf["dbs"]=array();
        $conf["dbs"]["dbw"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbw"]["pwd"]="edGB_12";
        $conf["dbs"]["dbw"]["adr"]="192.168.30.203";

        $conf["dbs"]["dbr"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbr"]["pwd"]="edGB_12";
        $conf["dbs"]["dbr"]["adr"]="192.168.30.204";

        $conf["dbs"]["dbw_2"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbw_2"]["pwd"]="edGB_12";
        $conf["dbs"]["dbw_2"]["adr"]="192.168.30.203";

        $conf["dbs"]["dbr_2"]["acc"]="lo_ball_op";
        $conf["dbs"]["dbr_2"]["pwd"]="edGB_12";
        $conf["dbs"]["dbr_2"]["adr"]="192.168.30.204";

        $conf["redis"]["game"]["ip"]= "192.168.20.6";
        $conf["redis"]["game"]["port"]= "6379";

        $conf["game_bo_domain"] = "http://www.bolottery.info/";
        $conf["country_code"] = "TEST";

        return $conf;
    }
    public static function init() {

        if (TESTMODE == '1') {
            $conf = self::envTest();
        } else {
            $conf = self::envPro();
        }
        $db=array();
        $db['db_w']['hostname'] = $conf["dbs"]["dbw"]["adr"];
        $db['db_w']['username'] = $conf["dbs"]["dbw"]["acc"];
        $db['db_w']['password'] = $conf["dbs"]["dbw"]["pwd"];
        $db['db_w']['database'] = "lt_game_catch";
        $db['db_w']['dbdriver'] = 'mysql';
        $db['db_w']['dbprefix'] = '';
        $db['db_w']['pconnect'] = FALSE;
        $db['db_w']['db_debug'] = true;
        $db['db_w']['cache_on'] = FALSE;
        $db['db_w']['cachedir'] = '';
        $db['db_w']['char_set'] = 'utf8';
        $db['db_w']['dbcollat'] = 'utf8_general_ci';
        $db['db_w']['swap_pre'] = '';
        $db['db_w']['autoinit'] = TRUE;
        $db['db_w']['stricton'] = FALSE;

        $db["db_r"]=$db["db_w"];
        $db['db_r']['hostname'] = $conf["dbs"]["dbr"]["adr"];
        $db['db_r']['username'] = $conf["dbs"]["dbr"]["acc"];
        $db['db_r']['password'] = $conf["dbs"]["dbr"]["pwd"];

        $conf['db'] = $db;

        /// webroot dir
        $conf['webroot'] = UUID;
        self::$set = $conf;
    }
}
