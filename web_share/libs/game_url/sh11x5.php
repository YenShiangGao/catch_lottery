<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*上海11選5*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class sh11x5 extends lib_base {
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
				case 'shsyxw_nine':
				case 'sh11x5_ocapi':
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
			case 'sh11x5':
				$data_array = array();
				$data_array_total = array();

				preg_match_all('/<table id="tb_chart" .*?>(.*)<\/table>/smi',$pusharray, $result_array01);
				if( !$this->ci->man_lib("lt_global")->isMatch($result_array01) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析table#tb_chart內容');
				}
				$result_array01 = preg_replace('/<(th.*?)>(.*?)<(\/th.*?)>|<!--(.*?)-->|<td class="bg_5">|<td class="bg_4">|<td class="bg_[2-3].*?>(.*?)<(\/td.*?)>|<td class="spline" colspan="100"><\/td>|<\/tr>|<(td.*?)>(.*?)<(\/td.*?)>|预选区[1-5]<\/td>|[\s]/is', '', $result_array01[1]);
				$result_array01 = preg_replace('/<\/td>/si',",",$result_array01);
				$result_array01 = preg_split('/<tr>/smi', $result_array01[0]);
				foreach ($result_array01 as $key1 => $value) {
					if(strlen($result_array01[$key1])>=5){
						array_push($data_array,$result_array01[$key1]);
					}
				}
				foreach ($data_array as $key2 => $value) {
					if(!empty($data_array[$key2])) {
						$data_array_total[$key2]["period"] = substr($data_array[$key2],0,10);
						$data_array_total[$key2]["code"] = $this->ci->lib_func->convtoint(substr($data_array[$key2],11,14));

						$data_array_total[$key2][3] = substr($data_array[$key2],0,4)."-".substr($data_array[$key2],4,2)."-".substr($data_array[$key2],6,2);
						$data_array_total[$key2][2] = $this->ci->lib_func->convtoopentime($data_array_total[$key2]["period"], $data_array_total[$key2][3],2);
					}
				}
				return $data_array_total;
				break;
			case 'shsyxw_nine':
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
			case 'sh11x5_ocapi':
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
			case 'sg11x5a01':
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
			case 'sg11x5a04':
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
		    case 'sg11x5a_mcai':
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
			case 'sh11x5':
			case 'shsyxw_nine':
			case 'sh11x5_ocapi':
            case 'sg11x5a04':
				foreach ($data_array as $key => $value) {
					$url_data["expect"]   = $value["period"]; 				//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");			//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
			case 'sg11x5a01':
				foreach ($data_array as $key => $value) {
					$expect = date("Y").substr($value["period"],2,4).substr("00".substr($value["period"],-2),-2);
					$url_data["expect"]   = $expect; 						//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");			//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
            case 'sg11x5a_mcai':
                
                foreach ($data_array as $key => $value) {
                    $period = str_replace(substr($value["period"], 0, 8),'',$value["period"]);
                    $expect = substr($value["period"], 0, 8) . substr("00".$period,-2);
                    $url_data["expect"]   = $expect;    //期數編碼
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