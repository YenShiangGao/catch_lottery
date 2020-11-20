<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*新疆時時彩*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class xjssc extends lib_base{
	public function __construct(){
		$this->ci =& get_instance();
		parent::__construct();
	}
	//處理網址
	public function getlottery_url($sql_data = null){
		$date1 = date("Ymd" , strtotime("now"));		//500彩票網用 (當天日期)
		$date2 = date("Ymd" , strtotime("-1 day"));		//500彩票網用 (前一天日期)
		$url_data = array();

		if($sql_data){
			switch ($sql_data["api_name"]) {
				case 'xjssc_nine':
				case 'xjssc_ocapi':
					array_push($url_data, $sql_data["url"].$date1, $sql_data["url"].$date2);
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
			case 'xjssc':
                // 取得 <End_time> 中的文字  日期塞值
                preg_match('/<div class="kj_code_tab" id="kj_code_tab">(.*?)<\/div>/smi', $pusharray, $htmlTbodys);
                if( !$this->ci->man_lib("lt_global")->isMatch($htmlTbodys) ){
                    $this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tbody#pagedata內容');
                }

                // 開獎期數
                preg_match_all('/<td class="bold" *?>(\d*)<\/td>/smi', $htmlTbodys[1], $periods);
                if( !$this->ci->man_lib("lt_global")->isMatch($periods) ){
                    $this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td.bold內容');
                }

                // 開獎號碼
                preg_match_all('/<td class="kj_codes" *?>(\d,\d,\d,\d,\d*)<\/td>/smi',$htmlTbodys[1], $codes);
                if( !$this->ci->man_lib("lt_global")->isMatch($codes) ){
                    $this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td.kj_codes內容');
                }

                $data = array();
                foreach ($periods[1] as $key => $val) {
                    $data[] = array(
                        'expect'   => $val,
                        'opencode' => $codes[1][$key]
                    );
                }
                return $data;
                break;
			case 'xjssca01':
				// 取得 <End_time> 中的文字  日期塞值
				preg_match('/<tbody *?id="pagedata">(.*)<\/tbody>/smi',$pusharray, $htmlTbodys);
				if( !$this->ci->man_lib("lt_global")->isMatch($htmlTbodys) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tbody#pagedata內容');
				}

				preg_match_all('/<td *?class=\'z_bg_05\'>(.*)<\/td>/smi',$htmlTbodys[1], $Temparray_02);
				if( !$this->ci->man_lib("lt_global")->isMatch($Temparray_02) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td.z_bg_05內容');
				}

				$Temparray_02 = $Temparray_02[1];

				//排除不是開獎期數的
				foreach ($Temparray_02 as $key => $value) {
					if(strlen($Temparray_02[$key]) <= 3) {unset($Temparray_02[$key]);}
				}
				//重排序列
				$Temparray_02 = array_values($Temparray_02);
				preg_match_all('/<td *?class=\'z_bg_13\'>(.*)<\/td>/smi',$htmlTbodys[1], $Temparray_03);

				if( !$this->ci->man_lib("lt_global")->isMatch($Temparray_03) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析td.z_bg_13內容');
				}
				$Temparray_03 = $Temparray_03[1];
				$data = array(
					"date"=>$Temparray_02,
					"number"=>$Temparray_03
				);
				return $data;
				break;
			case 'xjssc_nine':
				$data_array_total = array();
				$result_array01 = json_decode($pusharray ,true);

				if(isset($result_array01['status'])) {
					$errcode = isset($result_array01['status']["code"]) ? $result_array01['status']["code"] : 0;
					$errmsg = isset($result_array01['status']["text"]) ? $result_array01['status']["text"] : 'UNKNOWN ERROR';
					$this->ci->lib_func->outerr(403, 'CURL ERROR: 租用線路ERROR:['.$errcode.']'.$errmsg);
				}

				if(is_array($result_array01)) {
					foreach ($result_array01 as $key => $value) {
						if(isset($result_array01[$key]['number']) && isset($result_array01[$key]['dateline']) && isset($result_array01[$key]['dateline'])) {
							$date_array["expect"] = $key;
							if($api_name == 'bjkl8_nine' || $api_name == 'xy28_nine'|| $api_name == 'bjssc_nine'){
								$date_array["opencode"] = $result_array01[$key]['number'];
							}else if($api_name == 'xyft_nine'){
								$aryCode = explode(',', $result_array01[$key]['number']);
								$codeAry = array();
								foreach ($aryCode as $keyTemp => $valueTemp) {
									$tempNum = str_pad($valueTemp,2,'0',STR_PAD_LEFT);
									array_push($codeAry, $tempNum);
								}
								$date_array["opencode"] = implode(',', $codeAry);
							}else{
								$date_array["opencode"] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
							}
							$date_array[3] = substr($result_array01[$key]['dateline'],0,10);
							$date_array[2] = $result_array01[$key]['dateline'];
							array_push($data_array_total, $date_array);
						}
					}
				}
				return $data_array_total;
				break;
			case 'xjssc_ocapi':
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

					array_push($result, array('expect'=>$peroid, 'opencode'=>$code, 'date'=>$date, 'time'=>$v['opentime']));
				}
				return $result;
				break;
			case 'xjssca03':
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
            case 'xjssc_mcai':
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
		$api_name = $sql_data["api_name"];	//url_name
		$url_data = array();
		$data_array_total = array();

        switch ($api_name) {
        	case 'xjssc':
            case 'xjssc_ocapi':
            case 'xjssc_nine':
            case 'xjssca03':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = substr($value['expect'],0,8).substr("00".substr($value['expect'],-2),-3); //期數編碼
                    $url_data["opencode"] = $this->zeroDel($value['opencode']); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        	case 'xjssca01':
				foreach ($data_array["date"] as $key => $value) {
					$url_data["expect"]   = $value;	//期數編碼
					$url_data["opencode"] = $this->zeroDel($this->transfer($data_array["number"][$key])); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
            case 'xjssc_mcai':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["expect"];   //期數編碼
                    $url_data["opencode"] = $this->zeroDel($value["opencode"]); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        }

        return true;
	}
}