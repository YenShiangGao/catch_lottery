<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*天津時時彩*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class tjinssc extends lib_base{
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
				case 'tjssc_nine':
				case 'tjssc_ocapi':
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
			case 'tjinssc':
				$result_array01 = array(); //中獎期數
				$result_array02 = array(); //中獎號碼
				$data_array     = array(); //合併資料

				//中獎期數 中獎號碼
				if(preg_match_all('/table_add_one_tr(.*);/i', $pusharray, $result_array01)){
					if( !$this->ci->man_lib("lt_global")->isMatch($result_array01) ){
						$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析table_add_one_tr內容');
					}
				}
				$result_array01 = explode("; table_add_one_tr",$result_array01[1][0]);

				foreach ($result_array01 as $key => $value) {
					$result_array01[$key] = preg_replace("/[^0-9|:|]/", '', $result_array01[$key]);
					$result_array01[$key] = preg_replace("/[^0-9]/", ',', $result_array01[$key]);
					$data_array[$key]['expect'] = mb_substr($result_array01[$key], 0, 11, "UTF-8");
					$data_array[$key]['opencode'] = $this->ci->lib_func->convtoint(mb_substr($result_array01[$key], 11, 14, "UTF-8"));
					$data_array[$key]['date'] = date("Y-m-d" , strtotime(mb_substr($data_array[$key]['expect'], 0, 8, "UTF-8")));
					$data_array[$key]['time'] = $this->ci->lib_func->convtoopentime($data_array[$key]['expect'], $data_array[$key]['date'], '1');
				}
				return $data_array;
				break;
			case 'tjssc_nine':
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
							$date_array[1] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
							$date_array[3] = substr($result_array01[$key]['dateline'],0,10);
							$date_array[2] = $result_array01[$key]['dateline'];
							array_push($data_array_total, $date_array);
						}
					}
				}
				return $data_array_total;
				break;
			case 'tjinssca01':
				// 取得 <End_time> 中的文字  日期塞值
				preg_match('/<tbody *?id="pagedata">(.*)<\/tbody>/smi', $pusharray, $htmlTbodys);
				if( !$this->ci->man_lib("lt_global")->isMatch($htmlTbodys) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析tbody內容');
				}

				preg_match_all('/<td *?class=\'z_bg_05\'>(.*)<\/td>/smUi', $htmlTbodys[1], $Temparray_02);
				if( !$this->ci->man_lib("lt_global")->isMatch($Temparray_02) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析z_bg_05內容');
				}
				$Temparray_02 = $Temparray_02[1];

				//排除不是開獎期數的
				foreach ($Temparray_02 as $key => $value) {
					if(strlen($Temparray_02[$key]) <= 3) unset($Temparray_02[$key]);
				}
				//重排序列
				$Temparray_02 = array_values($Temparray_02);

				preg_match_all('/<td *?class=\'z_bg_13\'>(.*)<\/td>/smUi', $htmlTbodys[1], $Temparray_03);
				if( !$this->ci->man_lib("lt_global")->isMatch($Temparray_03) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析z_bg_13內容');
				}
				$Temparray_03 = $Temparray_03[1];
				$data = array(
					"date"=>$Temparray_02,
					"number"=>$Temparray_03
				);
				return $data;
				break;
			case 'tjssc_ocapi':
				$result = array();
				$res = array();
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
			case 'tjinssca04':
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
            case 'tjssc_mcai':
                $result = array();
                $res = json_decode($pusharray, true);
				if ($res == null)
					return false;
					
                if ( isset($res['status']) ) {
                    $this->ci->lib_func->outerr(403, '違規' . $res['error']);
                } else {
                    foreach ($res as $k => $v) {
                        $result[] = array(
                                'expect'   => $v['officialissue'],
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
        	case 'tjinssc':
            case 'tjssc_ocapi':
            case 'tjinssca04':
                foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["expect"];   //期數編碼
                    $url_data["opencode"] = $this->zeroDel($value["opencode"]); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
            case 'tjssc_mcai':
                foreach ($data_array as $key => $value) {
                    $expect = substr($value['expect'],0,8).substr("00".substr($value['expect'],-2),-3);
                    $url_data["expect"]   = $expect;   //期數編碼
                    $url_data["opencode"] = $this->zeroDel($value["opencode"]); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        	case 'tjssc_nine':
				foreach ($data_array as $key => $value) {
					$expect = substr($value[0],0,8).substr("00".substr($value[0],-2),-3);
					$url_data["expect"]   = $expect;	//期數編碼
					$url_data["opencode"] = $this->zeroDel($value[1]); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
        		break;
        	case 'tjinssca01':
				foreach ($data_array["date"] as $k => $v) {
					$url_data["expect"]   = $v;	//期數編碼
					$url_data["opencode"] = $this->zeroDel(implode(",",str_split($data_array["number"][$k],1))); //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

					array_push($data_array_total,$url_data);
        		}
        		return $data_array_total;
				break;
        }
        return true;
	}
}