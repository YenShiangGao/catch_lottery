<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*越南彩*/

require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class hn extends lib_base {
	public function __construct() {
		$this->ci = &get_instance();
		parent::__construct();
	}
	//處理網址
	public function getlottery_url($sql_data = null){
		$date1 = date("Ymd" , strtotime("now"));		//500彩票網用 (當天日期)
		$date2 = date("Ymd" , strtotime("-1 day"));		//500彩票網用 (前一天日期)
		$url_data = array();

		if($sql_data){
			array_push($url_data, $sql_data["url"]);
		}
		
		return $url_data;
	}
	/**
     * 越南彩開獎源資料整理
     * @param  String $pusharray 開獎源內容
     * @param  Array $queryData  開獎源資訊
     * @return Function          資料整理結果
     */
	public function getlotterycycle_url($pusharray = null, $queryData = null){
		// 出現403 Forbidden時，回傳 false
		if (!is_array($pusharray) && preg_match('/<center><h1>403 Forbidden<\/h1><\/center>/', $pusharray)) {
			return false;
		}
		switch ($queryData['url_name']) {
			case 'xskt':
				switch ($queryData['area']) {
					case 'NAM':
						return $this->xsktNAMLotteryResult($pusharray);
						break;
					case 'TRUNG':
						return $this->xsktTRUNGLotteryResult($pusharray);
						break;
					case 'BAC':
						return $this->xsktBACLotteryResult($pusharray);
						break;
				}
				break;
			case 'minhngoc':
				switch ($queryData['area']) {
					case 'NAM':
						return $this->minhngocNAMLotteryResult($pusharray);
						break;
					case 'TRUNG':
						return $this->minhngocTRUNGLotteryResult($pusharray);
						break;
					case 'BAC':
						return $this->minhngocBACLotteryResult($pusharray);
						break;
				}
				break;
			case 'kqxs':
				switch ($queryData['area']) {
					case 'NAM':
						return $this->kqxsNAMLotteryResult($pusharray);
						break;
					case 'TRUNG':
						return $this->kqxsTRUNGLotteryResult($pusharray);
						break;
					case 'BAC':
						return $this->kqxsBACLotteryResult($pusharray);
						break;
				}
				break;
			case 'xoso':
				switch ($queryData['area']) {
					case 'NAM':
						return $this->xosoNAMLotteryResult($pusharray);
						break;
					case 'TRUNG':
						return $this->xosoTRUNGLotteryResult($pusharray);
						break;
					case 'BAC':
						return $this->xosoBACLotteryResult($pusharray);
						break;
				}
				break;
			default:
				return false;
		}
	}

	/**
     * xskt整理南部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function xsktNAMLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'G.8' => '8',
			'G.7' => '7',
			'G.6' => '6',
			'G.5' => '5',
			'G.4' => '4',
			'G.3' => '3',
			'G.2' => '2',
			'G.1' => '1', 
			'ĐB'  => '0',
		);
		preg_match_all('/<table class="tbl-xsmn(.*)<\/table>/smUi', $webPage, $openTables);
		foreach ($openTables[0] as $table) {
			$openDate = '';
			$cityList = array();
			preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $table, $rows);
			foreach ($rows[0] as $rowKey => $value) {
				if($rowKey == 0){
					preg_match_all('/<th>(.*)<\/th>/smUi', $value, $itemList);
					foreach ($itemList[0] as $itemKey => $item) {
						if ($itemKey == 0) {
							preg_match('/<br>(.*)<\/th>/smUi', $item, $date);
							$dateAry = explode('/', $date[1]);
							$openDate = date("Y").'/'.$dateAry[1].'/'.$dateAry[0];
							$opencode[$openDate] = array();
						} else {
							preg_match('/<a ?.*>(.*)<\/a>/smUi', $item, $city);
							$cityName = trim($city[1]);
							array_push($cityList, $cityName);
							$opencode[$openDate][$cityName] = array();
						}
					}
				} else {
		    		preg_match_all('/<td ?.*>(.*)<\/td>/smUi', $value, $cells);
		    		foreach ($cityList as $cityKey => $city) {
			    		foreach ($cells[1] as $cellKey => $cell) {
			        		if($cellKey == 0){
			        			$opencode[$openDate][$city][$prizeLevels[$cell]]= array();
			        		}
		    			}
	    			}
		    		foreach ($cityList as $cityKey => $city) {
		    			$prizeItem = '';
		    			foreach ($cells[1] as $cellKey => $cell) {
			        		if ($cellKey == 0){
			        			$prizeItem = $cell;
			        			$opencode[$openDate][$city][$prizeLevels[$prizeItem]] = array();
			        		}
			        		if ($cellKey == ($cityKey + 1)) {
			        			preg_match_all('!\d+!', $cell, $codeGroup);
			        			foreach ($codeGroup[0] as $code) {
			        				array_push($opencode[$openDate][$city][$prizeLevels[$prizeItem]],trim($code));
			        			}
			        		}
		    			}
		    		}
				}
			}
		}
		return $opencode;
	}

	/**
     * xskt整理中部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function xsktTRUNGLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'G.8' => '8',
			'G.7' => '7',
			'G.6' => '6',
			'G.5' => '5',
			'G.4' => '4',
			'G.3' => '3',
			'G.2' => '2',
			'G.1' => '1', 
			'ĐB'  => '0',
		);
		preg_match_all('/<table class="tbl-xsmn(.*)<\/table>/smUi', $webPage, $openTables);
		foreach ($openTables[0] as $table) {
			$openDate = '';
			$cityList = array();
			preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $table, $rows);
			foreach ($rows[0] as $rowKey => $value) {
				if($rowKey == 0){
					preg_match_all('/<th>(.*)<\/th>/smUi', $value, $itemList);
					foreach ($itemList[0] as $itemKey => $item) {
						if ($itemKey == 0) {
							preg_match('/<br>(.*)<\/th>/smUi', $item, $date);
							$dateAry = explode('/', $date[1]);
							$openDate = date("Y").'/'.$dateAry[1].'/'.$dateAry[0];
							$opencode[$openDate] = array();
						} else {
							preg_match('/<a ?.*>(.*)<\/a>/smUi', $item, $city);
							$cityName = trim($city[1]);
							array_push($cityList, $cityName);
							$opencode[$openDate][$cityName] = array();
						}
					}
				} else {
		    		preg_match_all('/<td ?.*>(.*)<\/td>/smUi', $value, $cells);
		    		foreach ($cityList as $cityKey => $city) {
			    		foreach ($cells[1] as $cellKey => $cell) {
			        		if($cellKey == 0){
			        			$opencode[$openDate][$city][$prizeLevels[$cell]]= array();
			        		}
		    			}
	    			}
		    		foreach ($cityList as $cityKey => $city) {
		    			$prizeItem = '';
		    			foreach ($cells[1] as $cellKey => $cell) {
			        		if ($cellKey == 0){
			        			$prizeItem = $cell;
			        			$opencode[$openDate][$city][$prizeLevels[$prizeItem]] = array();
			        		}
			        		if ($cellKey == ($cityKey + 1)) {
			        			preg_match_all('!\d+!', $cell, $codeGroup);
			        			foreach ($codeGroup[0] as $code) {
			        				array_push($opencode[$openDate][$city][$prizeLevels[$prizeItem]],trim($code));
			        			}
			        		}
		    			}
		    		}
				}
			}
		}
		return $opencode;
	}

	/**
     * xskt整理北部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function xsktBACLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'G7' => '7',
			'G6' => '6',
			'G5' => '5',
			'G4' => '4',
			'G3' => '3',
			'G2' => '2',
			'G1' => '1', 
			'ĐB'  => '0',
		);
		preg_match_all('/<div class="box-ketqua">(.*)<\/div>/smUi', $webPage, $openTables);
		foreach ($openTables[0] as $tableKey => $table) {
			$nowDate = '';
			$nowCity = 'HA NOI';
			preg_match_all('/<h2 ?.*>(.*)<\/h2>/smUi', $table, $openTableItem);
			preg_match_all('/<table class="result"(.*)<\/table>/smUi', $table, $tableBody);
			foreach ($openTableItem[0] as $item) {
				preg_match('/[0-9]{1,2}\/[0-9]{1,2}/', $item, $date);
				if(count($date) == 1){
					$dateAry = explode('/', $date[0]);
					$openDate = date("Y").'/'.$dateAry[1].'/'.$dateAry[0];
					$nowDate = $openDate;
					$opencode[$openDate] = array();
					$opencode[$nowDate][$nowCity] = array();
					foreach ($prizeLevels as $prize) {
						$opencode[$nowDate][$nowCity][$prize] =array();
					}
				}
			}
			foreach ($tableBody[0] as $tableItem) {
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableItem, $rows);
				foreach ($rows[0] as $rowKey => $value) {
					if($rowKey > 0) {
						preg_match_all('/<td ?.*>(.*)<\/td>/smUi', $value, $cells);
						if(count($cells[1]) > 1 && array_key_exists($cells[1][0], $prizeLevels)){
							$opencode[$nowDate][$nowCity][$prizeLevels[$cells[1][0]]]= array();
							preg_match_all('!\d+!', $cells[1][1], $codeGroup);
							foreach ($codeGroup[0] as $code) {
								array_push($opencode[$nowDate][$nowCity][$prizeLevels[$cells[1][0]]], $code);
							}
						}
					}
				}
			}
		}
		return $opencode;
	}
	/**
     * minhngoc整理南部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function minhngocNAMLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'Giải tám'      => '8',
            'Giải bảy'      => '7',
            'Giải sáu'      => '6',
            'Giải năm'      => '5',
            'Giải tư'       => '4',
            'Giải ba'       => '3',
            'Giải nhì'      => '2',
            'Giải nhất'     => '1',
            'Giải Đặc Biệt' => '0'
		);
		preg_match_all('/<table[^>]+class="bkqmiennam"[^>]*>(.*)<table class="boxBottom">/smUi', $webPage, $openTables);
		foreach ($openTables[0] as $tableKey => $table) {
			$nowDate = '';
			preg_match_all('/<table[^>]+class="leftcl"[^>]*>(.*)<\/table>/smUi', $table, $openTableLeft);
			preg_match_all('/<table[^>]+class="rightcl"[^>]*>(.*)<\/table>/smUi', $table, $openTableRight);
			foreach ($openTableLeft[0] as $tableItem) {
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableItem, $rows);
				foreach ($rows[0] as $rowKey => $value) {
					if($rowKey == 1){
						preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value, $date);
						$dateAry = explode('/', $date[0]);
						$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
						$nowDate = $openDate;
						$opencode[$openDate] = array();
					}
				}
			}
			foreach ($openTableRight[0] as $tableItem) {
				$nowCity = '';
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableItem, $rows);
				foreach ($rows[0] as $rowKey => $value) {
					if($rowKey == 0){
						preg_match('/<a ?.*>(.*)<\/a>/smUi', $value, $city);
						$nowCity = trim($city[1]);
						$opencode[$nowDate][$nowCity] = array();
					}
				}
				foreach ($prizeLevels as $prizeItem => $level) {
					$opencode[$nowDate][$nowCity][$level] =array();
					$className = 'giai'.$level;
					if($level == 0){
						$className = 'giaidb';
					}
					preg_match('/<td class="'.$className.'">(.*)<\/td>/smUi', $tableItem, $rows);
					preg_match_all('!\d+!', $rows[1], $codeGroup);
					foreach ($codeGroup[0] as $code) {
						array_push($opencode[$nowDate][$nowCity][$level], trim($code));
					}
				}
			}
		}
		return $opencode;
	}

	/**
     * minhngoc整理中部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function minhngocTRUNGLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'Giải tám'      => '8',
            'Giải bảy'      => '7',
            'Giải sáu'      => '6',
            'Giải năm'      => '5',
            'Giải tư'       => '4',
            'Giải ba'       => '3',
            'Giải nhì'      => '2',
            'Giải nhất'     => '1',
            'Giải Đặc Biệt' => '0'
		);
		preg_match_all('/<table[^>]+class="bkqmiennam"[^>]*>(.*)<table class="boxBottom">/smUi', $webPage, $openTables);
		foreach ($openTables[0] as $tableKey => $table) {
			$nowDate = '';
			preg_match_all('/<table[^>]+class="leftcl"[^>]*>(.*)<\/table>/smUi', $table, $openTableLeft);
			preg_match_all('/<table[^>]+class="rightcl"[^>]*>(.*)<\/table>/smUi', $table, $openTableRight);

			foreach ($openTableLeft[0] as $tableItem) {
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableItem, $rows);
				foreach ($rows[0] as $rowKey => $value) {
					if($rowKey == 1){
						preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value, $date);
						$dateAry = explode('/', $date[0]);
						$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
						$nowDate = $openDate;
						$opencode[$openDate] = array();
					}
				}
			}
			foreach ($openTableRight[0] as $tableItem) {
				$nowCity = '';
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableItem, $rows);
				foreach ($rows[0] as $rowKey => $value) {
					if($rowKey == 0){
						preg_match('/<a ?.*>(.*)<\/a>/smUi', $value, $city);
						$nowCity = trim($city[1]);
						$opencode[$nowDate][$nowCity] = array();
					}
				}
				foreach ($prizeLevels as $prizeItem => $level) {
					$opencode[$nowDate][$nowCity][$level] =array();
					$className = 'giai'.$level;
					if($level == 0){
						$className = 'giaidb';
					}
					preg_match('/<td class="'.$className.'">(.*)<\/td>/smUi', $tableItem, $rows);
					preg_match_all('!\d+!', $rows[1], $codeGroup);
					foreach ($codeGroup[0] as $code) {
						array_push($opencode[$nowDate][$nowCity][$level], trim($code));
					}
				}
			}
		}
		return $opencode;
	}

	/**
     * minhngoc整理北部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function minhngocBACLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
            'Giải bảy'      => '7',
            'Giải sáu'      => '6',
            'Giải năm'      => '5',
            'Giải tư'       => '4',
            'Giải ba'       => '3',
            'Giải nhì'      => '2',
            'Giải nhất'     => '1',
            'Giải ĐB'       => '0',
		);
		preg_match_all('/<table[^>]+class="bkqtinhmienbac"[^>]*>(.*)<\/table>/smUi', $webPage, $openTables);
		foreach ($openTables[0] as $tableKey => $table) {
			$nowDate = '';
			$nowCity = 'HA NOI';
			preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $table, $rows);
			foreach ($rows[0] as $rowKey => $value) {
				if($rowKey == 0){
					preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value, $date);
					$dateAry = explode('/', $date[0]);
					$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
					$nowDate = $openDate;
					$opencode[$openDate] = array();
				}
			}
			foreach ($prizeLevels as $prizeItem => $level) {
				$opencode[$nowDate][$nowCity][$level] =array();
				$className = 'giai'.$level;
				if($level == 0){
					$className = 'giaidb';
				}
				preg_match('/<td class="'.$className.'">(.*)<\/td>/smUi', $table, $prizeResult);
				preg_match_all('!\d+!', $prizeResult[1], $codeGroup);
				foreach ($codeGroup[0] as $code) {
					array_push($opencode[$nowDate][$nowCity][$level], trim($code));
				}
			}
		}
		return $opencode;
	}

	/**
     * kqxs整理南部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function kqxsNAMLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'Giải tám'		=> '8',
            'Giải bảy'		=> '7',
            'Giải sáu'		=> '6',
            'Giải năm'		=> '5',
            'Giải tư'		=> '4',
            'Giải ba'		=> '3',
            'Giải nhì'		=> '2',
            'Giải nhất'		=> '1',
            'Giải đặc biệt' => '0'
		);
		$tableBody = $webPage[0]['body']['div']['div'][2]['div'][2]['div'][2]['div'];
		foreach ($tableBody as $tableKey => $lotteryTable) {
			if($tableKey > 0 && $lotteryTable['div'][1]['@attributes']['class'] == 'miennam bggradient1'){
				$cityList = array();
				$nowDate = '';
				$dateRow = $lotteryTable['div'][0]['h3']['table']['tr']['td'][0];
				preg_match('/[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}/', $dateRow, $date);
				if(!isset($date[0]) || trim($date[0]) === ''){
					continue;
				}
				$dateAry = explode('-', $date[0]);
				$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
				$nowDate = $openDate;
				$opencode[$openDate] = array();
				$tableContext = $lotteryTable['div'][1]['table']['tbody']['tr'];
				foreach ($tableContext as $rowKey => $row) {
					if($rowKey == 0){
						$tableItems = $row['th'];
						foreach ($tableItems as $itemKey => $item) {
							if($itemKey > 0){
								$cityName = trim($item);
								array_push($cityList, $cityName);
								$opencode[$nowDate][$cityName] = array();
								foreach ($prizeLevels as $prize) {
									$opencode[$nowDate][$cityName][$prize] =array();
								}
							}
						}
					} else{
						$openResults = $row['td'];
						$nowPrize = '';
						foreach ($openResults as $key => $value) {
							if($key == 0){
								$nowPrize = trim($value);
							} else{
								$cityIndex = $key - 1;
								if(!isset($value['p']) && $rowKey == (count($tableContext) - 1)){
									if(is_string($value)){
										$code = trim($value);
										if(is_numeric($code)){
											array_push($opencode[$nowDate][$cityList[$cityIndex]][$prizeLevels[$nowPrize]], $code);
										}
									}
								} else if(is_array($value['p'])){
									foreach ($value['p'] as $code) {
										if(is_string($code)){
											if(is_numeric(trim($code))){
												array_push($opencode[$nowDate][$cityList[$cityIndex]][$prizeLevels[$nowPrize]], trim($code));
											}
										}
									}
								} else {
									if(is_string($value['p'])){
										$code = trim($value['p']);
										if(is_numeric($code)){
											array_push($opencode[$nowDate][$cityList[$cityIndex]][$prizeLevels[$nowPrize]], $code);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $opencode;
	}

	/**
     * kqxs整理中部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function kqxsTRUNGLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'Giải tám'		=> '8',
            'Giải bảy'		=> '7',
            'Giải sáu'		=> '6',
            'Giải năm'		=> '5',
            'Giải tư'		=> '4',
            'Giải ba'		=> '3',
            'Giải nhì'		=> '2',
            'Giải nhất'		=> '1',
            'Giải đặc biệt' => '0'
		);
		$tableBody = $webPage[0]['body']['div']['div'][2]['div'][2]['div'][2]['div'];
		foreach ($tableBody as $tableKey => $lotteryTable) {
			if($tableKey > 0 && $lotteryTable['div'][1]['@attributes']['class'] == 'miennam bggradient1'){
				$cityList = array();
				$nowDate = '';
				$dateRow = $lotteryTable['div'][0]['h3']['table']['tr']['td'][0];
				preg_match('/[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}/', $dateRow, $date);
				if(!isset($date[0]) || trim($date[0]) === ''){
					continue;
				}
				$dateAry = explode('-', $date[0]);
				$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
				$nowDate = $openDate;
				$opencode[$openDate] = array();
				$tableContext = $lotteryTable['div'][1]['table']['tbody']['tr'];
				foreach ($tableContext as $rowKey => $row) {
					if($rowKey == 0){
						$tableItems = $row['th'];
						foreach ($tableItems as $itemKey => $item) {
							if($itemKey > 0){
								$cityName = trim($item);
								array_push($cityList, $cityName);
								$opencode[$nowDate][$cityName] = array();
								foreach ($prizeLevels as $prize) {
									$opencode[$nowDate][$cityName][$prize] =array();
								}
							}
						}
					} else{
						$openResults = $row['td'];
						$nowPrize = '';
						foreach ($openResults as $key => $value) {
							if($key == 0){
								$nowPrize = trim($value);
							} else{
								$cityIndex = $key - 1;
								if(!isset($value['p']) && $rowKey == (count($tableContext) - 1)){
									if(is_string($value)){
										$code = trim($value);
										if(is_numeric($code)){
											array_push($opencode[$nowDate][$cityList[$cityIndex]][$prizeLevels[$nowPrize]], $code);
										}
									}
								} else if(is_array($value['p'])){
									foreach ($value['p'] as $code) {
										if(is_string($code)){
											if(is_numeric(trim($code))){
												array_push($opencode[$nowDate][$cityList[$cityIndex]][$prizeLevels[$nowPrize]], trim($code));
											}
										}
									}
								} else {
									if(is_string($value['p'])){
										$code = trim($value['p']);
										if(is_numeric($code)){
											array_push($opencode[$nowDate][$cityList[$cityIndex]][$prizeLevels[$nowPrize]], $code);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $opencode;
	}

	/**
     * kqxs整理北部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function kqxsBACLotteryResult($webPage){
		$opencode = array();
		$prizeLevels = array(
			'Giải bảy'	=> '7',
            'Giải sáu'	=> '6',
            'Giải năm'	=> '5',
            'Giải tư'	=> '4',
            'Giải ba'	=> '3',
            'Giải nhì'	=> '2',
            'Giải nhất' => '1',
            'Đặc biệt'  => '0'
		);
		$tableBody = $webPage[0]['body']['div']['div'][2]['div'][2]['div'][2]['table']['tbody']['tr'];
		foreach ($tableBody as $tableBodyKey => $tableBodyRow) {
			$lotteryTable = $tableBodyRow['td']['div']['div'];
			$nowDate = '';
			$nowCity = 'HA NOI';
			foreach ($lotteryTable as $lotteryTableKey => $table) {
				if($table['@attributes']['class'] == 'tieude'){
					$dateRow = $table['h3']['table']['tr']['td'][0];
					preg_match('/[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}/', $dateRow, $date);
					if(!isset($date[0]) || trim($date[0]) === ''){
						continue;
					}
					$dateAry = explode('-', $date[0]);
					$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
					$nowDate = $openDate;
					$opencode[$openDate] = array();
					$opencode[$openDate][$nowCity] = array();
				}
			}
			if($nowDate === ''){
				continue;
			}
			foreach ($prizeLevels as $prize) {
				$opencode[$nowDate][$nowCity][$prize] =array();
			}
			
			foreach ($lotteryTable as $lotteryTableKey => $table) {
				if($table['@attributes']['class'] == 'floatL bangkq'){
					$openResults = $table['div'][0]['div']['table']['tbody']['tr'];
					foreach ($openResults as $key => $value) {
						if($key > 0){
							if (!empty($value['td'])) {
								$prizeItem = trim($value['td'][0]);
								$codeGroup = gettype($value['td'][1]) == 'array' ? $value['td'][1]:explode('-', trim($value['td'][1]));
								
								foreach ($codeGroup as $code) {
									if(is_string($code)){
										if(is_numeric(trim($code))){
											array_push($opencode[$nowDate][$nowCity][$prizeLevels[$prizeItem]], trim($code));
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $opencode;
	}

	/**
     * xoso整理南部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function xosoNAMLotteryResult($webPage){
		$opencode = array();
		$dateList = array();
		$prizeLevels = array(
            'Giải tám'      => '8',
            'Giải bảy'      => '7',
            'Giải sáu'      => '6',
            'Giải năm'      => '5',
            'Giải tư'       => '4',
            'Giải ba'       => '3',
            'Giải nhì'      => '2',
            'Giải nhất'     => '1',
            'Giải Đặc Biệt' => '0'
		);
		preg_match_all('/<table[^>]+class="table table-bordered text-center"[^>]*>(.*)<\/table>/smUi', $webPage, $openTables);
		preg_match_all('/<div[^>]+class="title-xsmb-item"[^>]*>(.*)<\/div>/smUi', $webPage, $openDates);

		foreach ($openDates[0] as $key => $value) {
			preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value, $date);
			if(!empty($date)){
				$dateAry = explode('/', $date[0]);
				$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
				array_push($dateList, $openDate);
				$opencode[$openDate] = array();
			}
		}
		if(count($dateList) != count($openTables[0])){
			return $opencode;
		}
		foreach ($openTables[0] as $tableKey => $table) {
			$cityList=array();
			preg_match_all('/<tbody>(.*)<\/tbody>/smUi', $table, $tableTbody);
			if(!empty($tableTbody[0])){
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableTbody[0][0], $rows);
				foreach ($rows[0] as $key => $value) {
					preg_match('/<td[^>]+class="giai-txt"[^>]*>(.*)<\/td>/smUi', $value, $rowItem);
					if(trim($rowItem[1]) == 'G'){
						preg_match_all('/<a ?.*>(.*)<\/a>/smUi', $value, $cityGroup);
						foreach ($cityGroup[1] as $city) {
							$nowCity = html_entity_decode(trim($city));
							$nowDateIndex = $tableKey;
							$opencode[$dateList[$nowDateIndex]][$nowCity] = array();
							array_push($cityList, $nowCity);
							foreach ($prizeLevels as $prizeItem => $level) {
								$opencode[$dateList[$nowDateIndex]][$nowCity][$level] =array();
							}
						}
					}
					if(in_array(trim($rowItem[1]), $prizeLevels) || trim($rowItem[1]) == 'ĐB'){
						preg_match_all('/<td[^>]+class="number col*[^>]*>(.*)<\/td>/smUi', $value, $codeGroupSpan);
						foreach ($codeGroupSpan[1] as $codePrizeKey => $codeSpanList) {
							preg_match_all('/<span ?.*>(.*)<\/span>/smUi', $codeSpanList, $codeGroup);
							$nowPrize = trim($rowItem[1]) == 'ĐB' ? '0' : trim($rowItem[1]);
							$nowDateIndex = $tableKey;
							foreach ($codeGroup[1] as $code) {
								if(is_numeric(trim($code))){
									array_push($opencode[$dateList[$nowDateIndex]][$cityList[$codePrizeKey]][$nowPrize], trim($code));
								}
							}
						}
					}
				}
			}
		}
		return $opencode;
	}

	/**
	 * xoso整理中部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function xosoTRUNGLotteryResult($webPage){
		$opencode = array();
		$dateList = array();
		$prizeLevels = array(
            'Giải tám'      => '8',
            'Giải bảy'      => '7',
            'Giải sáu'      => '6',
            'Giải năm'      => '5',
            'Giải tư'       => '4',
            'Giải ba'       => '3',
            'Giải nhì'      => '2',
            'Giải nhất'     => '1',
            'Giải Đặc Biệt' => '0'
		);
		preg_match_all('/<table[^>]+class="table table-bordered text-center"[^>]*>(.*)<\/table>/smUi', $webPage, $openTables);
		preg_match_all('/<div[^>]+class="title-xsmb-item"[^>]*>(.*)<\/div>/smUi', $webPage, $openDates);

		foreach ($openDates[0] as $key => $value) {
			preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value, $date);
			if(!empty($date)){
				$dateAry = explode('/', $date[0]);
				$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
				array_push($dateList, $openDate);
				$opencode[$openDate] = array();
			}
		}
		if(count($dateList) != count($openTables[0])){
			return $opencode;
		}
		foreach ($openTables[0] as $tableKey => $table) {
			$cityList=array();
			preg_match_all('/<tbody>(.*)<\/tbody>/smUi', $table, $tableTbody);
			if(!empty($tableTbody[0])){
				preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $tableTbody[0][0], $rows);
				foreach ($rows[0] as $key => $value) {
					preg_match('/<td[^>]+class="giai-txt"[^>]*>(.*)<\/td>/smUi', $value, $rowItem);
					if(trim($rowItem[1]) == 'G'){
						preg_match_all('/<a ?.*>(.*)<\/a>/smUi', $value, $cityGroup);
						foreach ($cityGroup[1] as $city) {
							$nowCity = html_entity_decode(trim($city));
							$nowDateIndex = $tableKey;
							$opencode[$dateList[$nowDateIndex]][$nowCity] = array();
							array_push($cityList, $nowCity);
							foreach ($prizeLevels as $prizeItem => $level) {
								$opencode[$dateList[$nowDateIndex]][$nowCity][$level] =array();
							}
						}
					}
					if(in_array(trim($rowItem[1]), $prizeLevels) || trim($rowItem[1]) == 'ĐB'){
						preg_match_all('/<td[^>]+class="number col*[^>]*>(.*)<\/td>/smUi', $value, $codeGroupSpan);
						foreach ($codeGroupSpan[1] as $codePrizeKey => $codeSpanList) {
							preg_match_all('/<span ?.*>(.*)<\/span>/smUi', $codeSpanList, $codeGroup);
							$nowPrize = trim($rowItem[1]) == 'ĐB' ? '0' : trim($rowItem[1]);
							$nowDateIndex = $tableKey;
							foreach ($codeGroup[1] as $code) {
								if(is_numeric(trim($code))){
									array_push($opencode[$dateList[$nowDateIndex]][$cityList[$codePrizeKey]][$nowPrize], trim($code));
								}
							}
						}
					}
				}
			}
		}
		return $opencode;
	}

	/**
	 * xoso整理北部彩資料
     * @param  String $webPage 開獎源內容
     * @return opencode        開獎號碼
     */
	public function xosoBACLotteryResult($webPage){
		$opencode = array();
		$dateList = array();
		$nowCity = 'HA NOI';
		$prizeLevels = array(
            'Giải bảy'      => '7',
            'Giải sáu'      => '6',
            'Giải năm'      => '5',
            'Giải tư'       => '4',
            'Giải ba'       => '3',
            'Giải nhì'      => '2',
            'Giải nhất'     => '1',
            'Giải ĐB'       => '0'
		);
		preg_match_all('/<table[^>]+class="table table-bordered text-center"[^>]*>(.*)<\/table>/smUi', $webPage, $openTables);
		preg_match_all('/<div[^>]+class="title-xsmb-item"[^>]*>(.*)<\/div>/smUi', $webPage, $openDates);

		foreach ($openDates[0] as $key => $value) {
			preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value, $date);
			if(!empty($date)){
				$dateAry = explode('/', $date[0]);
				$openDate = $dateAry[2].'/'.$dateAry[1].'/'.$dateAry[0];
				array_push($dateList, $openDate);
				$opencode[$openDate] = array();
				$opencode[$openDate][$nowCity] =array();
				foreach ($prizeLevels as $level) {
					$opencode[$openDate][$nowCity][$level] =array();
				}
			}
		}
		if(count($dateList) != count($openTables[0])){
			return $opencode;
		}
		foreach ($openTables[0] as $tableKey => $table) {
			preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $table, $rows);
			array_shift($rows[0]);
			foreach ($rows[0] as $key => $value) {
				preg_match_all('/<span ?.*>(.*)<\/span>/smUi', $value, $codeGroup);
				preg_match('/<td[^>]+class="giai-txt"[^>]*>(.*)<\/td>/smUi', $value, $prizeItem);
				$nowPrize = trim($prizeItem[1]) == 'ĐB' ? '0' : trim($prizeItem[1]);
				$nowDateIndex = $tableKey;
				foreach ($codeGroup[1] as $code) {
					if(is_numeric(trim($code))){
						array_push($opencode[$dateList[$nowDateIndex]][$nowCity][$nowPrize], trim($code));
					}
				}
			}
		}
		return $opencode;
	}
}