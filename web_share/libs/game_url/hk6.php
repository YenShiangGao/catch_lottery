<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*六合彩*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class hk6 extends lib_base {
	public function __construct() {
		$this->ci = &get_instance();
		parent::__construct();
	}
	//處理網址
	public function getlottery_url($sql_data = null){
		$date1    = date("Ymd", strtotime("now")); //500彩票網用 (當天日期)
		$date2    = date("Ymd", strtotime("-1 day")); //500彩票網用 (前一天日期)
		$date3    = date("Y-m-d", strtotime("now"));
		$date4    = date("Y-m-d", strtotime("-1 day"));

		$url_data = array();

		if ($sql_data) {
			array_push($url_data, $sql_data["url"]);
		}

		return $url_data;
	}
	//資料正規化
	public function getlotterycycle_url($pusharray = null, $api_name = null){
		if (!is_array($pusharray) && preg_match('/<center><h1>403 Forbidden<\/h1><\/center>/', $pusharray))
			return false;

		switch ($api_name) {
			case 'hk6':
                $pusharray = gzdecode($pusharray);

                $result = array();
                //存放期數以及開獎號碼 表格
                preg_match('/<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #E5E5E5">(.*)<\/table>/smi', $pusharray, $htmlTable);
                preg_match('/<table width="100%" border="0" cellspacing="0" cellpadding="2">(.*)<\/table>/smi', $htmlTable[1], $htmlTable);
                // print_r($htmlTable);die();

				//取出期數
				preg_match_all('/<td class="content" style="padding-left:1px" valign="top">(.*)<\/td>/', $htmlTable[1], $htmltd);
				$period = explode('/',explode(':',$htmltd[1][0])[1]);
				$period = '20'.trim($period[0]).trim($period[1]);
				$result["period"] = $period;

				//取出開獎號碼
				preg_match_all('/<table border="0" cellspacing="0" cellpadding="0">(.*)<\/table>/', $htmlTable[1],$htmltable);
				$codeStr = $htmltable[1][0];
				$codeStr = explode('<img src="/marksix/info/images/icon/',$codeStr);
				$code = '';
				foreach ($codeStr as $k => $v) {
					if ($k == 0)
						continue;

					$value = explode('?CV=L209R1b',$v);
					$value = explode('_',$value[0]);
					$value = explode('.gif',$value[1]);

					if($code==='')
						$code = $value[0];
					else
						$code = $code.",".$value[0];
				}
				$result["code"] = $code;

				return $result;
				break;
			case 'hk6_nine':
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
							$date_array["expect"] = $key;
							$date_array["opencode"] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
							$date_array[3] = substr($result_array01[$key]['dateline'], 0, 10);
							$date_array[2] = $result_array01[$key]['dateline'];
							array_push($data_array_total, $date_array);
						}
					}
				}
				return $data_array_total;
				break;
			case 'hk6_ocapi':
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
			case 'hk602':
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
		    case 'hk6_mcai':
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
            case 'hk6':
                $url_data["expect"]   = $data_array["period"]; //期數編碼
                $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$data_array["code"]))); //開獎號碼
                $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                array_push($data_array_total,$url_data);
                break;
            case 'hk6_nine':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["expect"]; //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$value["opencode"]))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                break;
            case 'hk6_ocapi':
                foreach ($data_array as $key => $value) {
                    $opencode = str_replace('+', ',', $value["code"]);
                    $url_data["expect"]   = $value["period"]; //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$opencode))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                break;
            case 'hk602':
                foreach ($data_array as $key => $value) {
                    $opencode = str_replace('+', ',', $value["code"]);
                    $url_data["expect"]   = $value["period"]; //期數編碼
                    $url_data["opencode"] = $this->zeroFill($opencode); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                break;
            case 'hk6_mcai':
                foreach ($data_array as $k) {
                    $opencode = str_replace('+', ',', $k["opencode"]);
                    $url_data["expect"]   = date('Y') . explode('/', $k['expect'])[1];   //期數編碼
                    $url_data["opencode"] = implode(",",$this->zeroFill(explode(",",$opencode))); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                break;
        }

        foreach ($data_array_total as $value) {
            $poencode = $value['opencode'];
            $codeAry = explode(',', $poencode);
            foreach ($codeAry as $v) {
                if (strlen($v) == 1)
                    return false;
                    // return '其中號碼不為兩碼，不處理';
            }

        }
        return $data_array_total;
    }
}