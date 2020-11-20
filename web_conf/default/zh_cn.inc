<?php
	require_once('../web_conf/languages/global.inc');
	function getdefaultcode(){
		// $Data = GetLanguageInf_gbl();

		$default[100] = "SUCCESS";
		$default[101] = "查無此帳號，或帳號密碼輸入錯誤。";
		$default[102] = "此帳號階級無登入權限";
		$default[103] = "此帳號無登入權限";
		$default[104] = "無資料";
		$default[105] = "舊密碼輸入錯誤";
		$default[106] = "失敗";
		$default[108] = "帳號已存在";
		
		$default[404] = "not found";
		$default[405] = "資料不存在";

		//For lotterydata api
		$default[200] = "TOKEN遺失";
		$default[201] = "沒有資料存在";
		$default[202] = "無效的TOKEN";
		$default[203] = "TOKEN逾時";
		$default[204] = "日期格式錯誤";
		$default[205] = "資料內容為空";
		$default[206] = "找不到對應的網址資料";
		$default[501] = "TOKEN遺失";

		//For CURL
		$default[500] = "網站CURL連線失敗";
		$default[502] = "CURL未取得有效資料";
		$default[503] = "資料庫寫入錯誤";
		$default[504] = "API JSON格式錯誤";
		$default[505] = "尚未開發完成";

		//new 2016-08-25
		$default[600] = "來源開獎時間異常";
		$default[601] = "可疑的開獎內容";

		//Unknown
		$default[0]   = "未知的錯誤";

		return $default;
	}
?>