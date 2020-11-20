<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*山東11選5*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class sd11x5 extends lib_base {
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
				case 'sdsyydj_nine':
				case 'sd11x5_ocapi':
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
		if (!is_array($pusharray) && preg_match('/<center><h1>403 Forbidden<\/h1><\/center>/', $pusharray))
			return false;

		switch ($api_name) {
			case 'sd11x5':
				$data_array = array();

				preg_match_all('/new Array\((\'\d+\'),(\'\d+\'),(\'\d+\'),(\'\d+\'),(\'\d+\'),(\'\d+\')/', $pusharray, $code);

				// 去除單引號
				foreach ($code as $key => $val) {
					foreach ($val as $k => $v) {
						$code[$key][$k] = str_replace('\'', '', $v);
					}
				}

				// $code[1]第1個號碼, $code[2]第2個號碼, $code[3]第3個號碼, $code[4]第4個號碼, $code[5]第5個號碼, $code[6]期數
				foreach ($code[6] as $key => $val) {
					$temp = array(
						'period' => $val,
						'code'   => $code[1][$key] . ',' .  $code[2][$key] . ',' .  $code[3][$key] . ',' .  $code[4][$key] . ',' .  $code[5][$key]
					);
					$data_array[] = $temp;
				}

				return $data_array;
				break;
			case 'sdsyydj_nine':
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
							$date_array["code"] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
							$date_array[3] = substr($result_array01[$key]['dateline'], 0, 10);
							$date_array[2] = $result_array01[$key]['dateline'];
							array_push($data_array_total, $date_array);
						}
					}
				}
				return $data_array_total;
				break;
			case 'sd11x5a01':
				$result_array01 = array(); //中獎期數
				$result_array02 = array(); //中獎號碼
				$result_array03 = array(); //合併資料

				preg_match('/<tbody>(.*)<\/tbody>/smi', $pusharray, $htmlTbodys);
				if (!$this->ci->man_lib("lt_global")->isMatch($htmlTbodys))
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tbody內容');

				//中獎期數
				preg_match_all('/<td class="c_fbf5e3 bd_rt_a">(\d*?)<\/td>/smi', $htmlTbodys[1], $result_array01);
				if (!$this->ci->man_lib("lt_global")->isMatch($result_array01))
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td.bd_rt_a內容');

				//中獎號碼
				preg_match_all('/<i class="t_c_d10000">(\d*?)<\/i>/smi', $htmlTbodys[1], $result_array02);
				if (!$this->ci->man_lib("lt_global")->isMatch($result_array02))
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析i.t_c_d10000內容');

				//把各期數與號碼放入陣列3
				foreach ($result_array01[1] as $key => $value) {
					$result_array03[$key]['period'] = $value;
					$start = $key * 5;
					$temp = array();
					for ($i=$start; $i < $start + 5; $i++) {
						$temp[] = $result_array02[1][$i];
					}
					$result_array03[$key]['code']   = implode($temp, ',');
				}
				return $result_array03;
				break;
			case 'sd11x5a02':
				$result_array01 = array(); //中獎期數
					$result_array02 = array(); //中獎號碼
					$result_array03 = array(); //合併資料

					preg_match('/<tbody *?id="pagedata">(.*)<\/tbody>/smi', $pusharray, $htmlTbodys);
					if (!$this->ci->man_lib("lt_global")->isMatch($htmlTbodys)) {
						$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tbody內容');
					}

					//中獎期數
					preg_match_all('/<td[\w\W]*? class="bg_02">([\w\W]*?)<\/td>/smi', $htmlTbodys[1], $result_array01);
					if (!$this->ci->man_lib("lt_global")->isMatch($result_array01)) {
						$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td.bg_02內容');
					}

					//中獎號碼
					preg_match_all('/<span[\w\W]*? class="c_red">([\w\W]*?)<\/span>/smi', $htmlTbodys[1], $result_array02);
					if (!$this->ci->man_lib("lt_global")->isMatch($result_array02)) {
						$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析span.c_red內容');
					}

					//把各期數與號碼放入陣列3
					foreach ($result_array01[1] as $key => $value) {
						$result_array03[$key]['period'] = $result_array01[1][$key];
						$result_array03[$key]['code']   = isset($result_array02[1][$key]) ? $result_array02[1][$key] : '';
					}
					return $result_array03;
					break;
			case 'sd11x5_ocapi':
				$res = array();
				$result = array();
				try {
					if(is_array($pusharray)){
						$res['data'] = array();
						foreach($pusharray as $st){
							$d = json_decode($st ,true);
							if(isset($d['data'])) $res['data'] = array_merge($res['data'], $d['data']);
						}
					} else {
						$res = json_decode($pusharray ,true);
					}

				} catch (Exception $e) {
					$this->ci->lib_func->output(504);
				}
				if(!isset($res['data'])) $this->ci->lib_func->output(502);

				foreach($res['data'] as $key=> $v){
					$date = date('Y-m-d', $v['opentimestamp']);
					$peroid = $v['expect'];
					$code = $v['opencode'];

					array_push($result, array('period'=>$peroid, 'code'=>$code, 'date'=>$date, 'time'=>$v['opentime']));
				}
				return $result;
				break;
			case 'sd11x5a05':
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
            case 'sd11x5a_mcai':
                $result = array();
                $res = json_decode($pusharray, true);
				if ($res == null)
					return false;
					
                if ( isset($res['status']) ) {
                    $this->ci->lib_func->outerr(403, '違規' . $res['error']);
                } else {
                    foreach ($res as $k => $v) {
                        $result[] = array(
                                'period'   => $v['issue'],
                                'code' => $v['code'],
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
			case 'sd11x5':
				foreach ($data_array as $key => $value) {
					$expect = date("Y").substr($value["period"],2,4).substr("00".substr($value["period"],-2),-3);
					$url_data["expect"]   = $expect; 						//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");			//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
			case 'sdsyydj_nine':
			case 'sd11x5_ocapi':
			case 'sd11x5a05':
				foreach ($data_array as $key => $value) {
					$expect = substr($value["period"],0,8).substr("00".substr($value["period"],-2),-3);
					$url_data["expect"]   = $expect;	//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]);		//開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");				//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
        		break;
			case 'sd11x5a02':
				foreach ($data_array as $key => $value) {
					$expect = date("Y").substr($value["period"],2,4).substr("00".substr($value["period"],-2),-3);
					$url_data["expect"]   = $expect; 						//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");			//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
			case 'sd11x5a01':
				foreach ($data_array as $key => $value) {
					$expect = date("Y").substr($value["period"],0,4).substr("00".substr($value["period"],-2),-3);
					$url_data["expect"]   = $expect; 		//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]); 		//開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s"); 	//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
		    case 'sd11x5a_mcai':
                foreach ($data_array as $key => $value) {
                    $expect = date("Y").substr($value["period"],2,4).substr("00".substr($value["period"],-2),-3);
                    $url_data["expect"]   = $expect;        //期數編碼
                    $url_data["opencode"] = $this->zeroDel($value["code"]); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        }
		return true;
	}
}