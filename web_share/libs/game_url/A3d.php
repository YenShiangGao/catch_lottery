<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*福彩3D*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class A3d extends lib_base{
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
				case 'fc3d_ocapi':
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
			case '3d':
				// print_r($pusharray);
				preg_match_all("/employee.push\((.*)\)/Ui", $pusharray, $htmlBodyers);
				if( !$this->ci->man_lib("lt_global")->isMatch($htmlBodyers) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析body內容');
				}
				// 解析 <employee> 中的文字
				$htmlEmployee =$htmlBodyers[1];

				return $htmlEmployee;
				break;
			case 'sd_nine':
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
							$date_array[0] = $key;
							if($api_name == 'bjkl8_nine' || $api_name == 'xy28_nine'|| $api_name == 'bjssc_nine'){
								$date_array[1] = $result_array01[$key]['number'];
							}else if($api_name == 'xyft_nine'){
								$aryCode = explode(',', $result_array01[$key]['number']);
								$codeAry = array();
								foreach ($aryCode as $keyTemp => $valueTemp) {
									$tempNum = str_pad($valueTemp,2,'0',STR_PAD_LEFT);
									array_push($codeAry, $tempNum);
								}
								$date_array[1] = implode(',', $codeAry);
							}else{
								$date_array[1] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
							}
							$date_array[3] = substr($result_array01[$key]['dateline'],0,10);
							$date_array[2] = $result_array01[$key]['dateline'];
							array_push($data_array_total, $date_array);
						}
					}
				}
				return $data_array_total;
				break;
			case 'fc3d_ocapi':
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
			case 'fc3d':
				$result = array();
				preg_match("/<table.* class=\"hz\">(.*)<\/table>/smUi", $pusharray, $table);
				if( !$this->ci->man_lib("lt_global")->isMatch($table) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析table.hz內容');
				}
				preg_match_all("/<tr.*>(.*)<\/tr>/smUi", $table[1], $tr);
				if( !$this->ci->man_lib("lt_global")->isMatch($tr) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tr內容');
				}
				if(count($tr[1])<3){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 找不到tr內有效的開獎資料');
				}
				unset($tr[1][0]);
				unset($tr[1][1]);
				foreach($tr[1] as $v){
					preg_match_all("/<td.*>(.*)<\/td>/smUi", $v, $td);
					if( !$this->ci->man_lib("lt_global")->isMatch($td)){
						continue;
					}
					if(count($td[1])>=4){
						$num = array();
						for($i=1; $i<=3; $i++){
							$n = str_replace(array(' ',"\n"),'', strip_tags($td[1][$i]));
							if(is_numeric($n)) array_push($num, $n);
						}
						$period = $td[1][0];
						$datediff = (int)$period-2016127;
						$date = date("Y-m-d", strtotime('2016-05-13 '.$datediff.' day'));
						$dtime = $date.' 20:30:00';
						$code = implode(',', $num);
						array_push($result, array('expect'=>$period, 'opencode'=>$code, 'date'=>$date, 'time'=>$dtime));
					}
				}
				return $result;
				break;
			case 'fc3d02':
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
            case 'fc3d_mcai':
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
        	case '3d':
				foreach ($data_array as $k) {
					$k = str_replace("'",'"',$k);
					$k = json_decode($k,true);
					$url_data["expect"]   = $k['id'];	//期數編碼
					$url_data["opencode"] = $this->zeroDel($k['c']); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
				}
        		return $data_array_total;
				break;
        	case 'sd_nine':
				foreach ($data_array as $key => $value) {
					$url_data["expect"]   = $value[0];	//期數編碼
					$url_data["opencode"] = $this->zeroDel($value[1]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
        		break;
            case 'fc3d':
            case 'fc3d_ocapi':
            case 'fc3d_mcai':
				foreach ($data_array as $key => $value) {
					$url_data["expect"]   = $value["expect"];	//期數編碼
					$url_data["opencode"] = $this->zeroDel($value["opencode"]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
					array_push($data_array_total,$url_data);
				}
				return $data_array_total;
        		break;
        	case 'fc3d02':
				foreach ($data_array as $key => $value) {
					$opencode = str_replace('+', ',', $value["code"]);
					$url_data["expect"]   = $value["period"]; //期數編碼
					$url_data["opencode"] = $this->zeroDel($opencode); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
        }
        return true;
	}
}