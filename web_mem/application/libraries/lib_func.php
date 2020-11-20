<?php
class lib_func {
	var $ci;
	function lib_func(){
		$this->ci =& get_instance();
	}
	
	public function test(){
		die('success');
	}
	
	//迴圈把開獎號碼轉為數字
	public function convtoint($data=null){
		$data = explode(",", $data);
		foreach ($data as $key => $value) {
			$output[$key] = $data[$key]+0;
		}
		$output = implode(",", $output);
		return $output;

	}
	//傳遞的值字元逗號分隔
	public function getlotcode($in_code=null, $spbyte=1) {
		if(is_array($in_code)){
			foreach ($in_code as $key => $value) {
				$lines = str_split($in_code[$key],$spbyte);
				$in_code[$key] = implode(",", $lines); 
			}
			return $in_code;
		} else {
			$lines = str_split($in_code,$spbyte);
			$lines_value = implode(",", $lines);
			return $lines_value;
		}

	}
	
	//期數換算開獎時間
	public function convtoopentime($data=null,$date=null,$lottoclass=null){
		ini_set('date.timezone','Asia/Taipei');  
		switch ($lottoclass) {
			case '1'://9點開獎 每十分鐘一期 84期 取後三位
				$open_periods_minate = substr($data,-3)*60*10;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 08:53:00')+$open_periods_minate);
				break;
			case '2'://9點開獎 每十分鐘一期 取後2位
				$open_periods_minate = substr($data,-2)*60*10;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 08:50:00')+$open_periods_minate);
				break;
			case '3'://10點開獎 每十分鐘一期 120期 取後三位 重慶時時彩
			if(substr($data,-3)+0 <=96 ) {
				if(substr($data,-3)+0 <=23) {
					$open_periods_minate = substr($data,-3)*60*5;
					$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 00:00:00')+$open_periods_minate);
				} else {
					$open_periods_minate = (substr($data,-3)-24)*60*10;
					$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 10:00:00')+$open_periods_minate);
				}
			} else {
				$open_periods_minate = (substr($data,-3)-96)*60*5;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 22:00:00')+$open_periods_minate); 
			}
				break;
			case '4'://10點開獎 每10分鐘1期 78 取後三位 山東11選5  
				$open_periods_minate = substr($data,-3)*60*10;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 10:05:00')+$open_periods_minate);
				break;
			case '5'://10點開獎 30 23期 取後2位 紹上海時時樂
				$open_periods_minate = substr($data,-2)*60*30;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 10:00:00')+$open_periods_minate);
				break;
			case '6'://體彩排列3 每晚20:30開獎 一天一期
				$open_time = $date." 20:30:00";
				//$open_time =  $open_periods_minate." day";
				break;
			case '7'://安徽快3 8:40開獎 10分鐘一期
				$open_periods_minate = substr($data,-2)*60*10;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 8:40:00')+$open_periods_minate);
				break;
			case '8'://上海時時樂 10:30開獎 30分鐘一期
				$open_periods_minate = substr($data,-2)*60*30;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 10:20:00')+$open_periods_minate);
				break;
			case '9'://新疆時時彩 10:00開獎 10分鐘一期
				$open_periods_minate = substr($data,-2)*60*10;
				$open_time =  date("Y-m-d H:i:s" , strtotime($date.' 10:00:00')+$open_periods_minate);
				break;  
			case '16'://幸運飛艇
				/*
				NOTE:
				We open one prize in every five minutes, totally 180 times in one day. Please notice: our opening time is The last Sunday in March - The last Sunday in September 06:04AM to 09:09PM, another time is 07:04AM to 10:09PM; we are closed between in The last Sunday in March - The last Sunday in September 09:09PM to 06:04AM, another time is 10:09PM to 07:04AM.
				
				5分鐘1期 共180期
				3月最後一個禮拜天 - 9月最後一個禮拜天 12:09開獎 (官方時間 06:09) 
				其他時間 13:09開獎 (官方時間 07:09) 
				 */
				$period = (int)substr($data, -3);
				$datetime = strtotime(substr($data, 0, 8));
				$open_time =  date("Y-m-d H:i:s" , $datetime + ( 13*3600 + 9*60 )+( 5*60* ($period-1) ) ); 
				break;
			default:
				break;
		}
		return $open_time;
	}
	//删除空格、換行與前後空白
	public function delspace($data=''){
		if($data){
			$data = preg_replace("/[\t\r\n]+/", "", $data);
			$data = trim($data);
		}
		return $data;
	}

	/**
	 * 通用的輸出代碼結果
	 * @param  integer $code 結果代碼
	 * @param  array   $data 回傳的資料
	 * @param  boolean $rt   將結果回傳或直接輸出
	 * @return json or array 回傳結果陣列或直接輸出JSON
	 */
	public function output($code=100, $data=array(), $rt=false){
		if(is_bool($data)){
			$rt = $data;
			$data = array();
			$msg = $this->code2msg($code);
		}
		if(!is_array($data)){
			$msg = $data;
			$data = array();
		} else {
			$msg = $this->code2msg($code);
		}
		$res = array('code'=>$code, 'msg'=>$msg, 'data'=>$data, 'proxy'=> (isset($this->ci->curlProxy) && $this->ci->curlProxy===true)? 1:0);

		if($rt){
			return $res;
		} else {
			die(json_encode($res));
		}
	}

	public function outsucc(){
		die(json_encode(array("code"=>100)));
	}
	public function outerr($code=400, $msg='', $rt=false){
		if(is_bool($msg)){
			$rt = $msg;
			$msg = '';
		}
		$res = array('code' => $code , 'msg' => empty($msg)? $this->code2msg($code) : $msg);
		//保留原本舊的參數
		$res['error_info'] = $res['msg'];

		//err status rec
		try {
			if(isset($this->ci->cmod)){
				if(method_exists($this->ci->cmod,'status_rec') && isset($this->ci->currentid)){
					$this->ci->cmod->status_rec($this->ci->currentid, 3);
				}
			}
		} catch (Exception $e) {
			//do nothing
		}

		if($rt){
			return $res;
		} else {
			die(json_encode($res));
		}
	}
	
	/**
	 * 結果代碼轉換為文字
	 * @param  integer $code 結果代碼
	 * @return string
	 */
	public function code2msg($code=0){
		$res_str = array(
			'100' => 'SUCCESS',
			//For lotterydata api
			'200' => 'TOKEN遺失',
			'201' => '沒有資料存在',
			'202' => '無效的TOKEN',
			'203' => 'TOKEN逾時',
			'204' => '日期格式錯誤',
			'205' => '資料內容為空',
			'206' => '找不到對應的網址資料',
			'501' => 'TOKEN遺失',
			//For CURL
			'500' => '網站CURL連線失敗',
			'502' => 'CURL未取得有效資料',
			'503' => '資料庫寫入錯誤',
			'504' => 'API JSON格式錯誤',
			'505' => '尚未開發完成',

			//new 2016-08-25
			'600' => '來源開獎時間異常',
			'601' => '可疑的開獎內容',

			//Unknown
			'0'   => '未知的錯誤'
		);
		$msg = isset($res_str[$code])? $res_str[$code] : $res_str['0'];
		return $msg;		
	}

	/* 將資料輸出並中斷程式，DEBUG用 */
	public function pr($data, $json=false){
		echo '<pre>';
		if(is_array($data)){
			if($json){
				echo json_encode($data, JSON_PRETTY_PRINT);	
			} else {
				print_r($data);
			}
		}
		echo '</pre>';
		die();
	}

	//2016-08-25
	/** 透過 telegram API 發送通知 */
	public function telegram($content=''){
		if(!$content) return false;
		$content = "[彩票抓結果預防通知-測試站] \n\n".$content;
		try {
	        $ch = curl_init('http://api.3cstore.info/main/API/NotificationForTelegram/2/');
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_POST,1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, array('Content'=>$content));
	        $chres = curl_exec($ch);
	        curl_close($ch);
	    } catch (Exception $e) {
	        //do nothing
	    }
	}
}