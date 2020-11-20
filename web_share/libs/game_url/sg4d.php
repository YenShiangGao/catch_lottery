<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
/*萬字票*/

require_once WEBROOT_CUSTOM . 'web_share/libs/ext/lib_base' . EXT;
class sg4d extends lib_base {
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
			return false;
		}
		switch ($api_name) {
			case 'sg4d':
				$data_array = array();
				$prizeLevels = array('FIRST' => "0", 'SECOND' => "1",  'THIRD' => "2",  'STARTER' => "3", 'CONSOLATION' => "4" );
				preg_match_all("/<div class='tables-wrap'>(.*)<\/div>/smUi", $pusharray, $htmlTbodys);
				foreach ($htmlTbodys[1] as $key => $value) {
					$starterPrizes = array();
					$consolationPrizes = array();
					$opencode = new stdClass();
					preg_match("/<th class='drawNumber'>(.*)<\/th>/smUi", $value, $lotteryExpectRawData);
					preg_match('!\d+!', $lotteryExpectRawData[0], $expect);
					preg_match("/<td class='tdFirstPrize'>(.*)<\/td>/smUi", $value, $firstPrize);
					preg_match("/<td class='tdSecondPrize'>(.*)<\/td>/smUi", $value, $secondPrize);
					preg_match("/<td class='tdThirdPrize'>(.*)<\/td>/smUi", $value, $thirdPrize);
					preg_match("/<tbody class='tbodyStarterPrizes'>(.*)<\/tbody>/smUi", $value, $starterPrizesTbody);
					preg_match("/<tbody class='tbodyConsolationPrizes'>(.*)<\/tbody>/smUi", $value, $consolationPrizesTbody);
					preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $starterPrizesTbody[0], $starterPrizesTbodyRows);
					preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $consolationPrizesTbody[0], $consolationPrizesTbodyRows);

					foreach ($starterPrizesTbodyRows[0] as $rowValue) {
			    		preg_match_all('/<td>(.*)<\/td>/smUi', $rowValue, $starterPrizesTbodyCells);
			    		foreach ($starterPrizesTbodyCells[1] as $cell) {
			        		array_push($starterPrizes,trim($cell));
			    		}
					}
					foreach ($consolationPrizesTbodyRows[0] as $rowValue) {
			    		preg_match_all('/<td>(.*)<\/td>/smUi', $rowValue, $consolationPrizesTbodyCells);
			    		foreach ($consolationPrizesTbodyCells[1] as $cell) {
			        		array_push($consolationPrizes,trim($cell));
			    		}
					}
					$opencode->$prizeLevels['FIRST'] = array($firstPrize[1]);
					$opencode->$prizeLevels['SECOND'] = array($secondPrize[1]);
					$opencode->$prizeLevels['THIRD'] = array($thirdPrize[1]);
					$opencode->$prizeLevels['STARTER'] = $starterPrizes;
					$opencode->$prizeLevels['CONSOLATION'] = $consolationPrizes;
					array_push($data_array, array('expect' => $expect[0], 'opencode' => $opencode));
				}
				return $data_array;
				break;
			case 'sg4d_live':
				$data_array = array();
				$starterPrizes = array();
				$consolationPrizes = array();
				$prizeLevels = array('FIRST' => "0", 'SECOND' => "1",  'THIRD' => '2',  'STARTER' => '3', 'CONSOLATION' => '4' );
				$opencode = new stdClass();
				preg_match('/<div class="divTableCell fd-title-font a-right" id="sgdn">(\d*)<\/div>/smUi', $pusharray, $expect);
				preg_match_all('/<div class="divTableCell prize123-font prize123-pos a-left" id="sgn(.*)">(.*)<\/div>/smUi', $pusharray, $topThreePrizes);
				preg_match_all('/<div class="divTableCell number-font" id="sgs(.*)">(.*)<\/div>/smUi', $pusharray, $starterPrizesRawData);
				preg_match_all('/<div class="divTableCell number-font" id="sgc(.*)">(.*)<\/div>/smUi', $pusharray, $consolationPrizesRawData);

				foreach ($topThreePrizes[2] as $key => $prize) {
	    			preg_match_all('!\d+!', $prize, $code);
	    			$level = array_search($key,$prizeLevels);
	    			$opencode->$prizeLevels[$level] = array($code[0][2]);
				}
				foreach ($starterPrizesRawData[2] as $key => $prize) {
	    			preg_match_all('!\d+!', $prize, $code);
	    			array_push($starterPrizes,$code[0][2]);
				}
				foreach ($consolationPrizesRawData[2] as $key => $prize) {
	    			preg_match_all('!\d+!', $prize, $code);
	    			array_push($consolationPrizes,$code[0][2]);
				}
				$opencode->$prizeLevels['STARTER'] = $starterPrizes;
				$opencode->$prizeLevels["CONSOLATION"] = $consolationPrizes;
				array_push($data_array, array('expect' => $expect[1], 'opencode' => $opencode));
				return $data_array;
				break;
			case 'sg4d_hk':
				$data_array = array();
				$data = json_decode($pusharray, true);
				
				if ($data['code'] == 100) {
					$data_array = $data['msg'];
				}
				
				return $data_array;
				break;
		}
	}
	//將資料整理
	public function getloggery($sql_data = array(), $data_array = array()){
		$api_name = $sql_data["api_name"]; //url_name
		$url_data = array();
		$data_array_total = array();
		switch ($api_name) {
			case 'sg4d':
			case 'sg4d_live':
				foreach ($data_array as $key) {
					$url_data["expect"]   = $key['expect'];   //期數編碼
					$url_data["opencode"] = $key['opencode']; //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
					array_push($data_array_total,$url_data);
				}
				return $data_array_total;
				break;
			case 'sg4d_hk':
				foreach ($data_array as $key) {
					$url_data["expect"]   = $key['expect'];   //期數編碼
					$url_data["opencode"] = (object) $key['opencode']; //開獎號碼
					$url_data["opentime"] = date("Y-m-d H:i:s");//開獎時間
					array_push($data_array_total,$url_data);
				}
				return $data_array_total;
				break;
		}
		return true;
	}
}