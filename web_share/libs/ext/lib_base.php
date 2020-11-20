<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class lib_base {
	public $ci;
	public $gdata;
	public $obj;
	public $mod;
	/**proxy*/
	public $util;
	public $logs;
	public $pxy;
	/********/

	public function lib_base() {
		$this->ci = &get_instance();
		/**proxy*/
		$this->util =& $this->ci->util;
		$this->logs =& $this->ci->logs;
		$this->pxy  =& $this->ci->pxy;
		/********/

		$this->ci->load->library("lib_codes");
		$this->ci->load->library('lib_func');
		$this->gdata = &$this->ci->gdata;
		$this->obj   = &$this->ci->obj;
		$this->mod   = $this->ci->get_mod("mod_msys", "", true);
	}
	public function output($data = "json") {
		return $this->ci->output($data);
	}
	public function chk_rep($tb, $key, $val) {
		$chk = $this->ci->mod->get_by($tb, array($key => $val));
		if (count($chk) == 0) {
			return true;
		} else {
			return false;
		}
	}
	public function key_obj($data, $key) {
		$raw = $this->ci->mod->conv_to_key($data, $key);
		return json_encode($raw);
	}
	public function get_view($file, $rt = false) {
		$cls = get_called_class();
		var_dump('in');
		exit();
		if ($rt) {
			return $this->ci->parser->parse("ctl/" . $cls . "/" . $this->ci->gdata["akey"] . "/" . $file . ".html", $this->ci->gdata, true);
		} else {
			$this->ci->parser->parse("ctl/" . $cls . "/" . $this->ci->gdata["akey"] . "/" . $file . ".html", $this->ci->gdata);
		}
	}
	/*流水號*/
	public function SerialNumber($Table = null, $Field = null, $Condition = "", $Serial, $Scount = 5) {
		if ($Table === null || $Field === null) {
			return false;
		}

		$Sql       = "select $Field from `$Table` where $Field like '%$Serial%' $Condition order by $Field desc limit 0,1";
		$r         = $this->ci->mod->select($Sql);
		$total_num = count($r);
		if ($total_num == 0) {
			return $Serial . sprintf("%0" . $Scount . "s", "1");
		} else {
			$a1       = $r[0][$Field];
			$s_number = strlen($Serial);
			$s_len    = strlen($a1) - $s_number;
			$s_num    = (int) substr($a1, ($s_number + 1), $s_len);
			$s_num    = $s_num + 1;
			return $Serial . sprintf("%0" . $Scount . "s", $s_num);
		}
	}
	public function transfer($str = null){
		$ary = array();
		$newStr = '';
		for ($i=0; $i < strlen($str) ; $i++) {
			$ary[] = $str[$i];
		}

		$newStr = implode(',', $ary);
		return $newStr;
	}
	//補0
	public function zeroFill($number = null){
		if (!is_array($number) && !is_string($number) && !is_numeric($number)) {
			return false;
		}

		if (is_array($number)) {
			$result = array();
			foreach ($number as $value) {
				$result[] = str_pad($value, 2, '0', STR_PAD_LEFT);
			}

			return $result;
		}

		if (is_string($number) || is_numeric($number)) {
			$result = str_pad($number, 2, '0', STR_PAD_LEFT);

			return $result;
		}
	}
	//拿掉0
	public function zeroDel($number = null){
		$code = explode(',', $number);
		foreach ($code as $key => $val) {
			$code[$key] = (int) $val;
		}
		return implode(",", $code);
	}
	//北京快樂8 幸運28 北京時時彩 開獎結果算法
	public function getGameSolCode($lotteryStr='',$gameId){
		$ary = explode(',',$lotteryStr);
		if(count($ary) == 21){unset($ary[20]);}
			sort($ary);
		if(count($ary) !=20) $this->lib_func->outerr(524,"Invalid lottery length");
			$i = 0;
		switch ($gameId) {
			case 24:
				$res =  array(0,0,0);
				foreach($ary as $k=>$v){
					if($k!=0 && $k % 6 == 0){
						$res[$i] = substr($res[$i],-1);
						$i++;
						if($i>2) break;
					}
					$res[$i] += (int)$v;
				}
				break;
			case 26:
				sort($ary);
				$res =  array(0,0,0,0,0);
				foreach($ary as $k=>$v){
					$res[$i] += (int)$v;
					if($k!=0 && ($k+1) % 4 == 0 ){
						$res[$i] = substr($res[$i],-1);
						$i++;
						if($i>5) break;
					}
				}
				break;
			case 27:
			case 28:
			case 29:
				$res =  array(0,0,0,0,0);
				foreach($ary as $k=>$v){
					$res[$i] += (int)$v;
					if($k!=0 && ($k+1) % 4 == 0 ){
						$res[$i] = substr($res[$i],-1);
						$i++;
						if($i>5) break;
					}
				}
				break;
			default:
				$res = $ary;
				break;
		}
		return implode(',',$res);
	}
	//print data
	public function pr($obj, $isfag = false) {
		if ($isfag) {
			echo '<pre>'.htmlspecialchars(print_r($obj, true)).'</pre>';
		} else {
			echo '<pre>'.print_r($obj, true).'</pre>';
		}
	}
}