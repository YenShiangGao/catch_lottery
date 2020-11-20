<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*上海時時樂*/
require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class jbzs extends lib_base{
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
				case 'shssl_nine':
				case 'shssl_ocapi':
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
		if (!is_array($pusharray) && preg_match('/<center><h1>403 Forbidden<\/h1><\/center>/', $pusharray))
			return false;
		switch ($api_name) {
			case 'jbzs':
				preg_match('/<div class="lm2block1">(.*)<\/div>/smi', $pusharray, $htmlBody);
				if( !$this->ci->man_lib("lt_global")->isMatch($htmlBody) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析body內容');
				}

				preg_match('/<div class="lm2con. fl">(.*)<\/div>/smi', $htmlBody[1], $htmlDiv);
				if( !$this->ci->man_lib("lt_global")->isMatch($htmlDiv) ){
					$this->ci->lib_func->outerr(401, 'CURL ERROR: 無法解析div內容');
				}

				// 期數 & 時間
				preg_match_all('/<div class="lm2con\d+.*?">(.*?)<\/div>/', $htmlDiv[0], $content);
				$result = array();
				for ($i=0; $i < count($content[1]) / 2 ; $i++) {
					$result[$i]['expect'] = str_replace('-', '0', $content[1][$i * 2]);
					$result[$i]['time'] = $content[1][$i * 2 + 1];
				}

				// 開獎號碼
				preg_match_all('/<p class="p\d+"><span class="num\d+">(.*?)<\/span><\/p>/', $htmlDiv[0], $content);
				for ($i=0; $i < count($content[1]) / 3 ; $i++) {
					$code = "";
					for ($j=0; $j < 3; $j++) {
						if($code=="")
							$code = $content[1][$i * 3 + $j];
						else
							$code = $code.",".$content[1][$i * 3 + $j];
					}
					$result[$i]['opencode'] = $code;
				}

				return $result;
				break;
			case 'shssl_nine':
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
							$date_array["period"] = $key;
							$date_array["code"] = $this->ci->lib_func->convtoint($result_array01[$key]['number']);
							$date_array[3] = substr($result_array01[$key]['dateline'],0,10);
							$date_array[2] = $result_array01[$key]['dateline'];
							array_push($data_array_total, $date_array);
						}
					}
				}
				return $data_array_total;
				break;
			case 'jbzsa02':
				$result = array();
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

				array_push($result,$Temparray_02,$Temparray_03);
				return $result;
				break;
			case 'shssl_ocapi':
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

					array_push($result, array('period'=>$peroid, 'code'=>$code, 'date'=>$date, 'time'=>$v['opentime']));
				}

				return $result;
				break;
			case 'jbzsa_mcai':
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
			case 'jbzsa04':
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
		$url_name = $sql_data["api_name"];	//url_name
		$url_datak = array();
		$send_data = array();
		$data_array_total = array();

		switch ($url_name) {
        	case 'jbzs':
				foreach ($data_array as $key => $value) {
                    $url_data["expect"]   = $value["expect"];   //期數編碼
                    $url_data["opencode"] = $this->zeroDel($value["opencode"]); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
                    array_push($data_array_total,$url_data);
                }
        		return $data_array_total;
				break;
            case 'jbzsa_mcai':
                foreach ($data_array as $key => $value) {
                    $period = str_replace(substr($value["expect"],0,8), '', $value["expect"]);
                    $expect = substr($value["expect"],0,8) . substr("00".$period,-3);
                    $url_data["expect"]   = $expect;//期數編碼
                    $url_data["opencode"] = $this->zeroDel($value["opencode"]); //開獎號碼
                    $url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
                    array_push($data_array_total,$url_data);
                }
                return $data_array_total;
                break;
        	case 'jbzsa02':
        		foreach ($data_array[0] as $k => $v) {
    				$url_data["expect"]   = substr($v,0,8).substr("00".substr($v,-2),-3);//期數編碼
    				$url_data["opencode"] = $this->transfer($data_array[1][$k]); //開獎號碼
    				$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間

    				array_push($data_array_total,$url_data);
    			}
        		return $data_array_total;
				break;
        	case 'shssl_nine':
        	case 'shssl_ocapi':
        	case 'jbzsa04':
				foreach ($data_array as $key => $value) {
					$expect = substr($value["period"],0,8).substr("00".substr($value["period"],-2),-3);
					$url_data["expect"]   = $expect;	//期數編碼
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