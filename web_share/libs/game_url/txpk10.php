<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*騰訊分分彩*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class Txpk10 extends lib_base{
    public function __construct(){
        $this->ci =& get_instance();
        parent::__construct();
    }
    //處理網址
    public function getlottery_url($sql_data = null){
        $date1 = date("Ymd" , strtotime("now"));        //500彩票網用 (當天日期)
        $date2 = date("Ymd" , strtotime("-1 day"));     //500彩票網用 (前一天日期)
        $url_data = array();

        if($sql_data){
            array_push($url_data, $sql_data["url"]);
        }

        return $url_data;
    }
    //資料正規化
    public function getlotterycycle_url($pusharray = null, $api_name = null){
        // 出現403 Forbidden時，回傳 false
        if (!is_array($pusharray) && preg_match('/<center><h1>403 Forbidden<\/h1><\/center>/', $pusharray)) {
            return false;
        }
        switch ($api_name) {
            case 'txpk10_mcai' :
                $result = array();
                $res = json_decode($pusharray, true);
                if ($res == null)
                    return false;
                    
                if ( isset($res['status']) ) {
                    $this->ci->lib_func->outerr(403, '違規' . $res['error']);
                } else {
                    foreach ($res as $k => $v) {
                        $expect = explode('-', $v['issue']);
                        $expect = $expect[0].$expect[1];

                        $result[] = array(
                                'expect'   => $expect,
                                'opencode' => $v['code'],
                                'opentime' => $v['opendate']
                                );
                    }
                }
                return $result;
                break;
            case 'txpk10_star':
                $result = array();
                $res    = array();
                try {
                    if (is_array($pusharray)) {
                        $res['data'] = array();
                        foreach ($pusharray as $st) {
                            $d = json_decode($st, true);
                            if (isset($d['data'])) {
                                $res['data'] = array_merge($res['data'], $d['data']);
                            }

                        }
                    } else {
                        $res = json_decode($pusharray, true);
                    }

                } catch (Exception $e) {
                    $this->ci->lib_func->output(504);
                }
                if (!isset($res['data'])) {
                    $this->ci->lib_func->output(502);
                }

                foreach ($res['data'] as $key => $v) {
                    $peroid = (int) $v['expect'] - 1 ;
                    $code   = $v['opencode'];

                    array_push($result, array('expect' => $peroid, 'opencode' => $code, 'time' => $v['opentime']));
                }
                return $result;
                break;
            case 'txpk10_77tj':
                $result = array();
                $res = json_decode($pusharray, true);
                if ($res == null)
                    return false;
                    
                if ( isset($res['status']) ) {
                    $this->ci->lib_func->outerr(403, '違規' . $res['error']);
                } else {
                    foreach ($res as $k => $v) {
                        $onlinetime = $v['onlinetime'];

                        $h = date('H', strtotime($onlinetime));
                        $i = date('i', strtotime($onlinetime));
                        if ($h == '00' && $i == '00') {
                            $h = 24;
                            $onlinetime = date('Y-m-d',strtotime($onlinetime.'-1 day'));
                        }

                        $periods = $h * 60 + $i;
                        $expect = date('Ymd', strtotime($onlinetime)) . substr('000' . $periods, -4);
                        $result[] = array(
                            'expect'   => $expect,
                            'opencode' => implode(',', $v['car']),
                            'opentime' => $onlinetime
                            );
                    }
                }
                return $result;
                break;
        }
    }
    //將資料整理
    public function getloggery($sql_data = array(), $data_array = array()){
        $api_name = $sql_data["api_name"];  //url_name
        $url_data = array();
        $data_array_total = array();

        switch ($api_name) {
            case 'txpk10_mcai':
            case 'txpk10_77tj':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["expect"];   //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$value["opencode"]))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
            case 'txpk10_star':
                foreach ($data_array as $k) {
                    $expect = substr($k['expect'],0,8).substr("000".substr($k['expect'],-3),-4);
                    $url_data["expect"]   = $expect;   //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$value["opencode"]))); //開獎號碼
                    $url_data["opentime"] = $k['time'];//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        }
        return true;
    }
}