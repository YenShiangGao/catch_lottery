<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*幸運飛停*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class xyft1 extends lib_base {
	public function __construct() {
		$this->ci = &get_instance();
		parent::__construct();
	}
	//處理網址
	public function getlottery_url($sql_data = null){
		$date1    = date("Ymd", strtotime("now")); //500彩票網用 (當天日期)
		$date2    = date("Ymd", strtotime("-1 day")); //500彩票網用 (前一天日期)
		$url_data = array();

		if ($sql_data) {
			switch ($sql_data["api_name"]) {
                case 'xyft1_nine':
                    array_push($url_data, $sql_data["url"] . $date1, $sql_data["url"] . $date2);
                    break;
                case 'xyft1':
                    array_push($url_data, 'https://mairship.com/api/draw_history?size=20&page=1&_=1597717752269');
                    break;
                default:
                    array_push($url_data, $sql_data["url"]);
                    break;
			}
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
            case 'xyft1':
                $result = array();
                $data = json_decode($pusharray, true);
                if ($data['msg'] == 'ok') {
                    if(!isset($data['result']['data']))
                        return;
                    $d = $data['result']['data'];
                    foreach ($d as $k => $v) {
                        array_push($result, array(
                            'period'=>$v['issueNo'], 
                            'code'=>$v['openCode'], 
                            'opentime'=>$v['openTime']
                            )
                        );
                    }
                }
				return $result;
                break;
            case 'xyft1_mcai':
                $result = array();
                $res = json_decode($pusharray, true);
				if ($res == null)
					return false;
					
                if ( isset($res['status']) ) {
                    $this->ci->lib_func->outerr(403, '違規' . $res['error']);
                } else {
                    foreach ($res as $k => $v) {
                        $result[] = array(
                                'expect'   => $v['issue'],
                                'opencode' => $v['code'],
                                'opentime' => $v['opendate']
                                );  
                    }
                }
                return $result;
                break;
            case 'xyft1_nine':
                $data_array_total = array();
                $result_array01   = json_decode($pusharray, true);

                if (isset($result_array01['status'])) {
                    $errcode = isset($result_array01['status']["code"]) ? $result_array01['status']["code"] : 0;
                    $errmsg  = isset($result_array01['status']["text"]) ? $result_array01['status']["text"] : 'UNKNOWN ERROR';
                    $this->ci->lib_func->outerr(403, 'CURL ERROR: 租用線路ERROR:[' . $errcode . ']' . $errmsg);
                }

                if (is_array($result_array01)) {
                    foreach ($result_array01 as $key => $value) {
                        if (isset($result_array01[$key]['number']) && isset($result_array01[$key]['dateline']) && isset($result_array01[$key]['dateline'])) {
                            $date_array["period"] = $key;
                            if ($api_name == 'bjkl8_nine' || $api_name == 'xy28_nine' || $api_name == 'bjssc_nine') {
                                $date_array["code"] = $result_array01[$key]['number'];
                            } else if ($api_name == 'xyft1_nine') {
                                $aryCode = explode(',', $result_array01[$key]['number']);
                                $codeAry = array();
                                foreach ($aryCode as $keyTemp => $valueTemp) {
                                    $tempNum = str_pad($valueTemp, 2, '0', STR_PAD_LEFT);
                                    array_push($codeAry, $tempNum);
                                }
                                $date_array["code"] = implode(',', $codeAry);
                            } else {
                                $date_array["code"] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
                            }
                            $date_array["time"] = substr($result_array01[$key]['dateline'], 0, 10);
                            $date_array["time"] = $result_array01[$key]['dateline'];
                            array_push($data_array_total, $date_array);
                        }
                    }
                }
                return $data_array_total;
                break;
            case 'xyft1_star':
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

                    array_push($result, array('period' => $peroid, 'code' => $code, 'time' => $v['opentime']));
                }

                return $result;
                break;
        }
	}
	//將資料整理
	public function getloggery($sql_data = array(), $data_array = array()){
		$api_name = $sql_data["api_name"]; //url_name
		$url_data = array();
		$data_array_total = array();
		switch ($api_name) {
            case 'xyft1':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["period"]; //期數編碼
                    $url_data["opencode"] = implode(',', $this->zeroFill($value['code'])); //開獎號碼
					$url_data["opentime"] = date('Y-m-d H:i:s', $value["opentime"]);//開獎時間
                    
					array_push($data_array_total, $url_data);
				}
        		return $data_array_total;
                break;
            case 'xyft1_mcai':
                foreach ($data_array as $k) {
                    $url_data["expect"]   = explode('-', $k['expect'])[0] . explode('-', $k['expect'])[1];   //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$k['opencode']))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
            case 'xyft1_nine':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["period"]; //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$value["code"]))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
            case 'xyft1_star':
                foreach ($data_array as $key => $value) {
                    $opencode = str_replace('+', ',', $value["code"]);
                    $url_data["expect"]   = $value["period"]; //期數編碼
                    $url_data["opencode"] = $this->zeroFill($opencode); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;        }
		return true;
	}
}