<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*幸運飛停*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class xyft extends lib_base {
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
			case 'xyft_nine':
			case 'xyft_ocapi':
					array_push($url_data, $sql_data["url"] . $date1, $sql_data["url"] . $date2);
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
			case 'xyft':
				$result = array();
				preg_match("/<table.*>(.*)<\/table>/smUi", $pusharray, $table);
				if( !$this->ci->man_lib("lt_global")->isMatch($table) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tbody內容');
				}

				preg_match_all("/<tr class.*>(.*)<\/tr>/smUi", $table[1], $tr);
				if( !$this->ci->man_lib("lt_global")->isMatch($tr) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tr內容');
				}

				foreach($tr[1] as $v){
					preg_match_all("/<td.*>(.*)<\/td>/smUi", $v, $td);
					if( !$this->ci->man_lib("lt_global")->isMatch($td) ){
						continue;
					}
					$td = $td[1];
					if(count($td)!=3 ) continue;

					preg_match_all("/<span.*>(.*)<\/span>/smUi", $td[2], $span);
					if( !$this->ci->man_lib("lt_global")->isMatch($span) ){
						continue;
					}
					$span = $span[1];
					if(count($span)!=10 ) continue;
					$codeAry = array();
					$invalid = false;
					foreach($span as $num){
						$num = (int)$num;
						if(!$num || $num > 10){
							$invalid = true;
							break;
						}
						$tempNum = str_pad($num,2,'0',STR_PAD_LEFT);
						array_push($codeAry, $tempNum);
					}
					if($invalid) continue;

					$code   = implode(',', $codeAry);
					$cdate  = $td[0];
					$period = $td[1];
					$dtime  = $this->ci->lib_func->convtoopentime($period, $cdate, '2');

					array_push($result, array('period'=>$period, 'code'=>$code, 'date'=>$cdate, 'time'=>$dtime));
				}
				return $result;
				break;
			case 'xyft_node':
				$result = array();
				foreach ($pusharray as $key => $val) {
					if (trim($key) != 'Installments') {
						if (trim($val['period']) != 'Installments') 
						array_push($result, array('period'=>preg_replace("/\t/","",$key), 'code'=>implode(',', $val['number']), 'date'=>$val['date']));
					}
				}
				return $result;
				break;
			case 'xyft_nine':
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
							} else if ($api_name == 'xyft_nine') {
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
			case 'xyft_ocapi':
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
					$date   = date('Y-m-d', $v['opentimestamp']);
					$peroid = $v['expect'];
					$code   = $v['opencode'];

					array_push($result, array('period' => $peroid, 'code' => $code, 'date' => $date, 'time' => $v['opentime']));
				}

				return $result;
				break;
			case 'xyft02':
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
		    case 'xyft_mcai':
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
        }
	}
	//將資料整理
	public function getloggery($sql_data = array(), $data_array = array()){
		$api_name = $sql_data["api_name"]; //url_name
		$url_data = array();
		$data_array_total = array();

		switch ($api_name) {
			case 'xyft':
			case 'xyft_ocapi':
			case 'xyft_nine':
			case 'xyft_node':
				foreach ($data_array as $key => $value) {
					$url_data["expect"]   = $value["period"]; //期數編碼
					$url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$value["code"]))); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
				}
        		return $data_array_total;
				break;
        	case 'xyft02':
				foreach ($data_array as $key => $value) {
					$opencode = str_replace('+', ',', $value["code"]);
					$url_data["expect"]   = $value["period"]; //期數編碼
					$url_data["opencode"] = $this->zeroFill($opencode); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
            case 'xyft_mcai':
                foreach ($data_array as $k) {
                    $url_data["expect"]   = $k['expect'];   //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$k['opencode']))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        }
		return true;
	}
}