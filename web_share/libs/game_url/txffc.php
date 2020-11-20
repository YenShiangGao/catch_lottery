<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*騰訊分分彩*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class Txffc extends lib_base{
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
            case 'txffc' :
                $result = array();
                // 解析 HTML 的 <body> 區段
                preg_match('/<div *? class="kj_tab">(.*)<\/div>/smi', $pusharray, $htmlTbodys);
                if(!$this->ci->man_lib("lt_global")->isMatch($htmlTbodys)){
                    $this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析table class="ssctab"內容');
                }

                preg_match_all('/<tr>(.*?)<\/tr>/smi', $htmlTbodys[1], $htmlEmployee);
                if(!$this->ci->man_lib("lt_global")->isMatch($htmlEmployee)){
                    $this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td class="gray03"內容');
                }

                foreach ($htmlEmployee[1] as $key => $value) {
                    preg_match_all('/<td>(.*?)<\/td>/smi', $value, $htmlEmploye);
                    if(!$this->ci->man_lib("lt_global")->isMatch($htmlEmploye)){
                        $this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td class="gray03"內容');
                    }

                    preg_match( '/(\d+)<\/i>\s(\d+)/', $htmlEmploye[1][1], $expectDom );
                    $expect = trim($expectDom[1]) . trim($expectDom[2]);
                    preg_match_all( '/<i>(\d+)<\/i>/', $htmlEmploye[1][2], $opencode );

                    $result[] = array(
                        'expect' => $expect,
                        'opencode' => implode(',', $opencode[1]),
                        'opentime' => $htmlEmploye[1][0],
                        );
                }
                return $result;
                break;
            case 'txffc_77tj':
                $result = array();
                $res = json_decode($pusharray, true);
                if ($res == null)
                    return false;
                    
                if ( isset($res['status']) ) {
                    $this->ci->lib_func->outerr(403, '違規' . $res['error']);
                } else {
                    foreach ($res as $k => $v) {
                        $onlinetime = $v['onlinetime'];
                        $peopleAry = $v['onlinenumber'];
                        $people = str_split($peopleAry);
                        $total = array_reduce($people, function ($v1, $v2) {
                                    return $v1 + $v2;
                                });
                        $opencode = substr($total, -1) . substr('000' . $peopleAry, -4);
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
                            'opencode' => implode(',', preg_split('//', $opencode, -1, PREG_SPLIT_NO_EMPTY)),
                            'opentime' => $onlinetime
                            );
                    }
                }
                return $result;
                break;
            case 'txffc_star':
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
                    $peroid = $v['expect'];
                    $code   = $v['opencode'];

                    array_push($result, array('expect' => $peroid, 'opencode' => $code, 'time' => $v['opentime']));
                }

                return $result;
                break;
            case 'txffc_mcai':
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
            case 'txffc_caipiaoapi':
                $result = array();
                $res = json_decode($pusharray, true);
                if ($res['errorCode'] == 0 ) {
                    if (!empty($res['result'])) {
                        foreach ($res['result']['data'] as $k => $v) {
                            $result[] = array(
                                'expect'   => $v['gid'],
                                'opencode' => $v['award'],
                                'opentime' => $v['time']
                                );
                        }
                    } else {
                        $this->ci->lib_func->outerr(502, 'error' . $res['message']);
                    }
                } else {
                    $this->ci->lib_func->outerr(504, 'error' . $res['message']);
                }
                return $result;
                break;
            case 'txffc_b1api':
                $result = array();
                $res = json_decode($pusharray, true);
                if ($res['row'] > 0 ) {
                    if (!empty($res['data'])) {
                        foreach ($res['data'] as $k => $v) {
                            $result[] = array(
                                'expect'   => $v['expect'],
                                'opencode' => $v['opencode'],
                                'opentime' => $v['opentime']
                                );
                        }
                    } else {
                        $this->ci->lib_func->outerr(502, 'error' . $res['message']);
                    }
                } else {
                    $this->ci->lib_func->outerr(504, 'error' . $res['message']);
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
            case 'txffc_77tj':
            case 'txffc':
            case 'txffc_star':
            case 'txffc_mcai':
            case 'txffc_caipiaoapi':
            case 'txffc_b1api':
                foreach ($data_array as $k) {
                    $url_data["expect"]   = $k['expect'];   //期數編碼
                    $url_data["opencode"] = $this->zeroDel($k['opencode']); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        }
        return true;
    }
}