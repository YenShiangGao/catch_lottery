<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

@ini_set('display_errors', 1);
/*設置錯誤信息的類別*/
class callapi extends web_mem
{
	public function call_api($Action = null){
		// $urlAd = 'http://www.catch-lottery.yuni20170704.rdgs.team/lotteryapi';
		$urlAd = 'http://' . $_SERVER['HTTP_HOST'] . '/lotteryapi';
		$token = $this->libc->aes_en(date("YmdHis") . "||itwinstars","itwinstars");

		switch ($Action) {
			case 'specifyGame':
				// 總後端 → 抓獎系統( 重慶時時彩 )
			    // 依照 時間 抓出資料(20171011000000-20171011235959)
			    $url = $urlAd.'/specifyGame/'.$token.'/24/20171108000000/20171108235959';
				break;
			case 'openDate':
				// 總後端 → 抓獎系統(六合彩抓取開獎日期)
			    // 抓出只訂月份的 開獎時間
			    $url = $urlAd.'/openDate/'.$token.'/66/2018/3';
				break;
			case 'specifyPeriod':
				// 總後端 →抓獎系統(指定遊戲期數給全部抓到的開獎)
			    // 抓出指定期數的資料 所有網址
			    $url = $urlAd.'/specifyPeriod/'.$token.'/1/20171030067';
				break;
			case 'specifyPeriodError':
				// 總後端 → 抓獎系統(錯誤開獎號碼查詢)
	    		$url = $urlAd.'/specifyPeriodError/' . $token . '/201500101/20171030';
				break;
		}

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($ch);
	    curl_close($ch);

	    echo $url;
	    return $result;
	}

	public function testttt(){
        $sql = "Select id,lottery From LT_periods WHERE `game_id` = 127 AND `period_date` = '2019-04-27' and `lottery_status` = 1";
        $info = $this->mod->select($sql);
        foreach ($info as $k => $v) {
            $aaa = explode(',', $v['lottery']);
            $bbb = [];
            foreach ($aaa as $key => $value) {
                $bbb[] = substr('00' . $value, -2);
            }

            $this->mod->modi_by(
                'LT_periods',
                array('id' => $v['id'], 'game_id'=>127, 'period_date'=>'2019-04-27', 'lottery_status'=>'1'),
                array(
                    "lottery"        => implode(',', $bbb)
                )
            );
        }

    }

    public function api() {
    	if (!isset($_POST['telegram_str'])) {
    		$this->obj['code'] = 403;
    		$this->output();
    	}
    	$post = $_POST;
    	$this->obj['code'] = 100;
    	$this->obj['data'] = $post;
    	$this->output();
    }

    public function testTelegram(){
    	$data = $this->man_lib('Test_telegram')->index();
    	// $data = $this->man_lib('Test_telegram')->SendNotice();
    }

    public function gameurlList(){
    	 $sql  = "
            SELECT
				lt_game_catch.LT_history.period_str,
				lt_game_catch.LT_history.lottery,
				lt_game_catch.LT_url.url_name,
				lt_game_catch.LT_history.lottery_time,
				lt_game_catch.LT_periods.be_lottery_time
            FROM
                lt_game_catch.LT_history
            JOIN
            	lt_game_catch.LT_periods
            ON
            	lt_game_catch.LT_periods.game_id = lt_game_catch.LT_history.game_id AND
            	lt_game_catch.LT_periods.period_str = lt_game_catch.LT_history.period_str
            JOIN
            	lt_game_catch.LT_url
            ON
				lt_game_catch.LT_url.game_id = lt_game_catch.LT_history.game_id AND
				lt_game_catch.LT_url.id      = lt_game_catch.LT_history.url_id
            WHERE
            	lt_game_catch.LT_history.lottery_time between ? AND ? AND
            	lt_game_catch.LT_history.game_id = ?
            Order by
            	lt_game_catch.LT_history.period_str Desc";

        $info = $this->mod->select($sql, array('2018-01-01 00:00:00','2018-01-31 23:59:59',1));

       	$file="demo.xls";
       	$data = "<table>";
       	foreach ($info as $k => $v) {
       		$test="<tr>
       		<td>".$v["period_str"]."</td>
       		<td>".$v["lottery"]."</td>
       		<td>".$v["url_name"]."</td>
       		<td>".$v["lottery_time"]."</td>
       		<td>".$v["be_lottery_time"]."</td>
       		</tr>";
       		$data = $data.$test;
       	}
       	$data = $data."</table>";


		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$file");

		echo $data;
        // $this->pr($this->mod->last_qstr);
        // $this->pr($info);
    }
    public function ttttt(){
    	$sql  = "SELECT lottery FROM LT_periods WHERE game_id = 66 and lottery_status=1";
    	$info = $this->mod->select($sql);

    	$file="demo.xls";
       	$data = "<table>";
       	foreach ($info as $k => $v) {
       		$lottery = explode(',',$v["lottery"]);//開獎號碼
       		$test ="<tr>";
       		$data = $data.$test;
       		foreach ($lottery as $key => $value) {
       			$test ="<td>".$value."</td>";
	       		$data = $data.$test;
       		}
       		$test ="</tr>";
       		$data = $data.$test;

       	}
       	$data = $data."</table>";


		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$file");

		echo $data;


    }
}