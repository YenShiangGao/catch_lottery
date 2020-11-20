<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class lib_pubfun extends lib_base{
	function __construct(){
        parent::__construct();
        $this->ci =& get_instance();

        require_once WEBROOT_CUSTOM.'web_share/models/ext/noticeCode.inc';
        $this->code = noticeCode::init();
    }
    /**
     * 將訊息存入通知表
     * @param  Array $notice 通知資訊
     * @return boolean
     */
    public function noticeMsg($notice){
    	$noticeParams = array('noticeCode', 'game_id', 'period_str', 'user_id', 'msg');
    	foreach ($noticeParams as $param) {
			if (!isset($notice[$param]) || trim($notice[$param]) === '') {
				$this->obj['warn'] = 'Notice Message Parameter Lost';
				return 1;
			}
        }
        
        $type  = $this->code[$notice['noticeCode']][1];
        $type_id  = $this->code[$notice['noticeCode']][0];
        $sql = "SELECT id FROM tb_telegram_notice WHERE game_id = ? AND period_str = ? AND type_id = ? LIMIT 0,1 ";
        $data = $this->mod->select($sql, array($notice['game_id'], $notice['period_str'], $type_id));
        if(empty($data)){
            $add = $this->mod->add_by(
                'tb_telegram_notice',
                array(
                    "game_id"     => $notice['game_id'],
                    "period_str"  => $notice['period_str'],
                    "type"        => $type,
                    "type_id"     => $type_id,
                    "user_id"     => $notice['user_id'],
                    "content"     => $type. "\n" .$notice['msg'],
                    "notice"      => 'N'
                )
            );
            if (isset($add['lid'])) {
                return 0;
            } else {
            	return 1;
            }
        }
        return 0;
    }
    /**
     * 紀錄錯誤期數資訊
     * @param  Array $error 期數錯誤資訊
     * @return boolean
     */
    public function errorPeriodRecord($error){
    	$errorParams = array('game_id', 'lottery_id', 'lottery');
    	foreach ($errorParams as $param) {
			if (!array_key_exists($param, $error)) {
				$this->obj['warn'] = 'Error Period Record Parameter Lost';
				return 1;
			}
    	}
        $sql = "SELECT id FROM LT_period_error WHERE lottery_id = ?";
        $LTErr = $this->mod->select($sql, array($error['lottery_id']));
        if(empty($LTErr)){
            $add = $this->mod->add_by(
                'LT_period_error',
                array(
                    "game_id"    => $error['game_id'],
                    "lottery_id" => $error['lottery_id'],
                    "lottery"    => $error['lottery']
                )
            );
            if (isset($add['lid'])) {
                return 0;
            } else {
            	return 1;
            }
        }
        return 0;
    }
    /**
     * 開獎信賴組數檢查
     * @param  Array $urlConfidence 信賴檢查參數
     * @return boolean
     */
    public function urlConfidenceCheck($urlConfidence){
    	$urlConfidenceParams = array('game_id', 'period_str', 'lottery', 'lottery_time', 'code_order', 'periodid', 'urlCheck', 'url_id');
    	foreach ($urlConfidenceParams as $param) {
			if (!array_key_exists($param, $urlConfidence)) {
				$this->obj['warn'] = 'URL Confidence Parameter Lost';
				return 1;
			}
    	}
    	/**********************************************************
            檢查號碼是否已存在大於或等於設定值$urlConfidence['urlCheck']以及是否存在高權重0的開獎結果
            是：寫入開獎列表
            否：不做動作，繼續等待其他api 開獎回來
        ***********************************************************/
    	$sql = "SELECT lottery, code_order, url_id FROM LT_history WHERE game_id = ? AND period_str = ?";
        $info = $this->mod->select($sql, array($urlConfidence['game_id'], $urlConfidence['period_str']), true);
        $hightOrderUrlId = $urlConfidence['url_id'];
        $hightOrderCount = 0;
        $hightOrder = $urlConfidence['code_order'] == 0 ? true : false;
        if($urlConfidence['game_id'] == 155){
        	foreach ($info as $k => $v) {
	            if($this->checkOpenCodeResult($urlConfidence['lottery'], $v["lottery"])) {
	                $hightOrderCount++;
	                if($v["code_order"] == 0){
	                	$hightOrderUrlId = $v['url_id'];
	            		$hightOrder = true;
	            	}
	            }
	        }
	        $urlConfidence['lottery'] = json_encode($urlConfidence['lottery']);
        } else {
	        foreach ($info as $k => $v) {
	            if($urlConfidence['lottery'] == $v["lottery"]) {
	                $hightOrderCount++;
	                if($v["code_order"] == 0){
	                	$hightOrderUrlId = $v['url_id'];
	            		$hightOrder = true;
	            	}
	            }
	        }
    	}
        /****************************************
            比對結果
            1.大於或是等於所設定組數
            2.組數要有其中一組為高權重
            3.資料庫狀態為未開獎
        *****************************************/
        if((int)$hightOrderCount >= (int)$urlConfidence['urlCheck'] && $hightOrder) {
            $modify = $this->mod->modi_by(
                'LT_periods',
                array('id' => $urlConfidence['periodid'], 'period_str'=>$urlConfidence['period_str'], 'checks'=>0),
                array(
                    "lottery"        => $urlConfidence['lottery'],
                    "lottery_time"   => $urlConfidence['lottery_time'],
                    "lottery_status" => 1,
                    "checks"         => 1,
                    "url_id"         => $hightOrderUrlId
                )
            );
        }
        return 0;
    }
    public function checkOpenCodeResult($opencode, $historyLottery){
        $opencode = json_decode(json_encode($opencode), true);
        $compareLottery = json_decode($historyLottery, true);
        // $compareLottery[4][3] = 4903;
        // echo '<pre>' . print_r($compareLottery, true) . '</pre>';
        for($i = 0; $i<count($opencode); $i++) {
            $newArray = array_diff($opencode[$i], $compareLottery[$i]);
            if (!empty($newArray)) {
                $reD[$i] = $newArray;
                return $reD;
            }
        }
        return 1;
    }
}