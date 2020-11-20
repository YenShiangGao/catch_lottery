<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class cndssc extends lib_base {
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
			// if (preg_match('/<center><h1>403 Forbidden<\/h1><\/center>/', $pusharray)) {
				return false;
			// }
		}
		switch ($api_name) {
		case 'cndssc':{
				$data_array = array();
				$data_array_total = array();

				preg_match_all('/<td height="20" align="center" bgcolor="#FFFFFF">(.*)<tr bgcolor="#99cc99">/smi', $pusharray, $result_array01);
				if (!$this->ci->man_lib("lt_global")->isMatch($result_array01)) {
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td tr內容');
				}

				$result_array01 = preg_replace('/<strong>|<(\/?td.*?)>||[\s]|<(\/?span.*?)>|<tr>/smi', '', $result_array01[1]);
				$result_array01 = preg_split('/<\/tr>/smi', $result_array01[0]);

				foreach ($result_array01 as $key1 => $value) {
					if (strlen($result_array01[$key1])) {
						$result_array01[$key1] = preg_replace("/，/", ",", $result_array01[$key1]);
						array_push($data_array, substr($result_array01[$key1], 0, 22));
					}
				}

				foreach ($data_array as $key2 => $value) {
					$data_array_total[$key2][0] = substr($data_array[$key2], 0, 8);
					$data_array_total[$key2][1] = $this->ci->lib_func->convtoint(substr($data_array[$key2], 8, 14));

					$data_array_total[$key2][3] = "20" . substr($data_array_total[$key2][0], 0, 2) . "-" . substr($data_array_total[$key2][0], 2, 2) . "-" . substr($data_array_total[$key2][0], 4, 2);
					$data_array_total[$key2][2] = $this->ci->lib_func->convtoopentime($data_array_total[$key2][0], $data_array_total[$key2][3], '2');
				}
				return $data_array_total;
				break;
			}
		case 'cndssc_ocapi':{
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
			}
		}
	}
	//將資料整理
	public function getloggery($sql_data = array(), $data_array = array()){
		$api_name = $sql_data["api_name"]; //url_name
		$url_data = array();
		$data_array_total = array();

		switch ($api_name) {
			case 'cndssc':
				foreach ($data_array as $key => $value) {
					//將開獎號碼 做 加減
					$code = $this->getGameSolCode(
						explode(",",$value)[1].",".
						explode(",",$value)[2].",".
						explode(",",$value)[3].",".
						explode(",",$value)[4],$sql_data["game_id"]);

					$url_data["expect"]   = explode(",",$value)[0]; //期數編碼
					$url_data["opencode"] = $this->zeroDel($code); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
					// $this->ci->man_lib("lt_global")->getloggery_insert($sql_data,$url_data);
        		}	
        		return $data_array_total;
				break;
			case 'cndssc_ocapi':
				foreach ($data_array as $key => $value) {
					$url_data["expect"]   = $value["period"]; //期數編碼
					$url_data["opencode"] = $this->zeroDel($value["code"]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
					// $this->ci->man_lib("lt_global")->getloggery_insert($sql_data,$url_data);
        		}	
        		return $data_array_total;
				break;
		}
		return true;
	}
	//print data
	public function pr($obj, $isfag = false){
		if ($isfag) {
			echo '<pre>' . htmlspecialchars(print_r($obj, true)) . '</pre>';
		} else {
			echo '<pre>' . print_r($obj, true) . '</pre>';
		}
	}
}