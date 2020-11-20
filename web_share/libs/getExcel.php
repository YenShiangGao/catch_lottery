<?php
require_once('ext/lib_base'.EXT);
require_once 'cls_telcall.php';
require_once 'cls_chkUrlWx.php';
require_once 'google/cls_g_api.php';

class getExcel extends lib_base{
    function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
    }

    public function GetExcelCode($code){
        $ary = array();
        $ary["code"] = "1XXzycnijk0Qg5H0HnQdFAuxgXHlxHy4TsPsB20NY7HA";
        $ary["field"] = "'工作表1'!A2:G";
        return $ary;
    }

    public function GetExcelInfo($web="lot"){
        $linkinfo = $this->GetExcelCode($web);
        $obj_g = new cls_g_client();
        $obj_data = $obj_g->obj_sheet()->obj_get_by_rng($linkinfo["code"],$linkinfo["field"]);
        $ary_list = $obj_data["values"];
        $ary_chk = array();
        $today = date('Y-m-d');

        for($a = 0; $a < count($ary_list); $a++) {
            $v = $ary_list[$a];
            $t = array();

            // if ($v[1] === '開彩網') {
            //     $v[4] = str_replace(' ', '-', $v[4]);
            //     $date = explode('-', $v[4]);
            //     $date = '20'.$date[0].'-'.$date[1].'-'.$date[2];
            //     $v[6] =  (strtotime($date) - strtotime($today)) /3600 / 24;
            // }
            $t["name"] = $v[0]; //彩種
            $t["web"] = $v[1]; //API租用廠商
            $t["webadd"] = $v[2]; //站別
            $t["status"] = $v[3]; //狀態
            $t["enddate"] = $v[4]; //過期日期
            $t["remark"] = $v[5]; //備註
            $t["day"] = $v[6]; //倒數天數

            if ($t["status"] == '啟用')
                $ary_chk[] = $t;
        }

        return $ary_chk;
    }
}