<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

@ini_set('display_errors', 1);
/*設置錯誤信息的類別*/
class lotteryapi extends web_mem{
    public $delItem;
    public $proxy = false;
    public function __construct() {
        parent::__construct();
        $this->delItem = $this->defaultms();
        $this->load->library('curl');
        $this->load->library('lib_func');
        $this->pubfun = $this->man_lib("lib_pubfun", "libs/ext");
    }
    public function fun($cls, $fun, $data = null, $extra_data = null) {
		$cls = $this->man_lib($cls);
		return $cls->$fun($data, $extra_data);
	}
    //抓獎 單一game name
    public function catchApi($ename = null, $url_id = null, $api_name = null, $proxy = false, $from = null){
        $sql   = "SELECT game_id,url,url_name,code_order,post FROM LT_url WHERE id = ? AND api_name = ? AND enable = ?";
        $LTUrl = array_shift($this->mod->select($sql, array($url_id, $api_name, 0)));
        
        if (!empty($LTUrl)) {
            $sql_data               = array(); //存放從資料庫抓出的資料格式
            $sql_data["id"]         = $url_id;
            $sql_data["api_name"]   = $api_name;
            $sql_data["game_id"]    = $LTUrl["game_id"];
            $sql_data["url"]        = $LTUrl["url"];
            $sql_data["url_name"]   = $LTUrl["url_name"];
            $sql_data["code_order"] = $LTUrl["code_order"];

            $ename = $ename === "3da01" ? "A3da01" : $ename;
            $ename = $ename === "3d" ? "A3d" : $ename;
            $post = isset($LTUrl["post"]) ? $LTUrl["post"] : '';
            
            if ($from == 'node') {
                $pusharray = json_decode(file_get_contents('php://input', 'r'), true);
                $pusharray = array($pusharray);
            } else {
                $url_data = $this->url_lib($ename)->getlottery_url($sql_data);
                $header = $post !== '' && !empty(json_decode($post, true)) ? array('HTTPHEADER' => array('Content-Type: application/json')) : array();
                $pusharray  = $this->curl_array_url($url_data, $post, $api_name, $proxy, $header);
            }

            $data = array(
                "ename"    => $ename,
                "url_id"   => $url_id,
                "api_name" => $api_name,
                "proxy"    => json_encode($this->pxy->current)
            );
            
            foreach ($pusharray as $key => $val) {
                if ($val === false) {
                    $this->obj["code"] = 404;
                    $this->obj["msg"]  = $api_name . "  CURL FAIL. RESPONSE EMPTY.";
                    $this->obj["data"] = $data;
                } else {
                    //資料正規化
                    $data_array = $this->url_lib($ename)->getlotterycycle_url($val, $api_name);
                    //false 代表出現403 Forbidden
                    if ($data_array !== false) {
                        //整理資料
                        $resultData = $this->url_lib($ename)->getloggery($sql_data, $data_array);
                       
                        if ($from == 'interface') {
                            $result = true;
                        } else {
                            //新增 資料庫
                            $libS = $ename == 'sg4d' ? "lib_sg4d" : "lt_global";
                            $result = $this->man_lib($libS)->getloggery_insert($sql_data, $resultData);
                            //更新LT_url表
                            $this->man_lib("lt_global")->LTurlUpdate($sql_data);
                        }
                        
                        if ($result) {
                            $this->obj["code"] = 100;
                            $this->obj["msg"]  = $resultData;
                        } else {
                            $this->obj["code"] = 500;
                            $this->obj["msg"]  = $api_name . "　DB error";
                        }
                    } else {
                        $this->obj["code"] = 404;
                        $this->obj["msg"]  = $api_name . "　網頁 403 Forbidden";
                        $this->obj["data"] = $data;
                    }
                }
            }
        } else {
            $this->obj["code"] = 500;
            $this->obj["msg"]  = $api_name . "　網址資料表找不到資料";
        }
        $this->output();
    }
    /**
     * 越南彩抓獎
     * @param  String $url_id   網址ID
     * @param  String $url_name 網址名稱
     * @param  String $area     開獎地區
     * @param  Boolean $proxy   代理啟用
     */
    public function hnUnofficialCatchApi($url_id = null, $url_name = null, $area = null, $proxy = false, $from = null){
        $ename = 'hn';
        $sql   = "
            SELECT
                url_id,     -- 網址ID
                area,       -- 地區
                url_name,   -- 網址名稱
                url        -- 網址
            FROM
                hn_unofficial_url
            WHERE
                url_id = ? AND
                url_name = ? AND
                area = ?
        ";
        $LTUrl = array_shift($this->mod->select($sql, array(
            $url_id,
            $url_name,
            $area
        )));
        if (!empty($LTUrl)) {
            $sql_data               = array(); //存放從資料庫抓出的資料格式
            $sql_data["id"]         = $LTUrl['url_id'];
            $sql_data["area"]       = $LTUrl['area'];
            $sql_data["url_name"]   = $LTUrl["url_name"];
            $sql_data["url"]        = $LTUrl["url"];
            $url_data = array($sql_data["url"]);//處理網址
            $post = '';
            $header = $post !== '' && !empty(json_decode($post, true)) ? array('HTTPHEADER' => array('Content-Type: application/json')) : array();

            $pusharray  = $this->curl_array_url($url_data, $post, $url_name, $proxy, $header);//網頁抓取資料
            $data = array(
                "ename"       => $ename,
                "url_name"    => $url_name,
                "url_id"      => $url_id,
                "area"        => $area,
                "proxy"       => json_encode($this->pxy->current)
            );
            foreach ($pusharray as $key => $val) {
                if ($val === false) {
                    $this->obj["code"] = 404;
                    $this->obj["msg"]  = $url_name . "  CURL FAIL. RESPONSE EMPTY.";
                    $this->obj["data"] = $data;
                } else {
                    $data_array = $this->url_lib($ename)->getlotterycycle_url($val, $data);
                    if ($data_array !== false) {
                        if ($from == 'interface') {
                            $this->obj["code"] = 100;
                            $this->obj["data"] = $data_array;
                        } else {
                            $this->fun('lib_hn', 'getloggery_insert', $url_id, $data_array);
                        }
                    } else {
                        $this->obj["code"] = 404;
                        $this->obj["msg"]  = $url_name . "網頁 403 Forbidden";
                        $this->obj["data"] = $data;
                    }
                }
            }
        } else {
            $this->obj["code"] = 500;
            $this->obj["msg"]  = $url_name . "網址資料表找不到資料";
        }
        $this->output();
    }
    //總後端 → 抓獎系統( game )
    public function specifyGame($token = null, $gameid = null, $periodS = null, $periodE = null){
        $this->tokenValidate($token);

        if ($periodS === null)
            $periodS = date("Y-m-d H:i:s");
        else
            $periodS = date('Y-m-d H:i:s', strtotime($periodS));

        if ($periodE === null)
            $periodE = date("Y-m-d H:i:s");
        else
            $periodE = date('Y-m-d H:i:s', strtotime($periodE));

        $sql  = "
            SELECT
                period_date,    -- 開獎日期
                lottery,        -- 開獎號碼
                period_str,        -- 期數編碼
                be_lottery_time,-- 預計開獎時間
                lottery_time    -- 實際開獎時間
            FROM
                LT_periods
            WHERE
                be_lottery_time BETWEEN ? AND ? AND
                game_id = ? AND
                lottery_status = ?
            ORDER BY period_str DESC

        ";
        $data = $this->mod->select($sql, array(
            $periodS,
            $periodE,
            $gameid,
            1
        ));

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $sqlData[$key]["date"]    = $data[$key]["period_date"];
                $sqlData[$key]["game_id"] = $gameid;
                $sqlData[$key]["code"]    = $data[$key]["lottery"]; //開獎號碼
                $sqlData[$key]["period"]  = $data[$key]["period_str"]; //期數編碼
                $sqlData[$key]["time"]    = $data[$key]["be_lottery_time"]; //開獎時間
                $sqlData[$key]["rectime"] = $data[$key]["lottery_time"];
            }

            $this->obj["code"]         = 100;
            $this->obj["GameTypeInfo"] = $sqlData;
            $this->output();
        } else {
            $this->obj["code"] = 405; //period在periods資料表找不到
            $this->obj['msg']  = $this->delItem[405];
            $this->output();
        }
    }
    //總後端 → 抓獎系統(指定遊戲期數給全部抓到的開獎 所有網址)
    public function specifyPeriod($token = null, $gameid = null, $period = null){
        $this->tokenValidate($token);
        $sql = "
            SELECT
                url_id,            -- 網址ID
                lottery,        -- 開獎號碼
                lottery_time,    -- 開獎時間
                game_id,        -- 遊戲ID
                lottery_time,    -- 實際開獎時間
                period_str        -- 期數編碼
            FROM
                LT_history
            WHERE
                game_id = ? AND
                period_str = ?
            ORDER BY lottery_time ASC
        ";
        $LTPeriods = $this->mod->select($sql, array(
            $gameid,
            $period
        ));

        $lottery_info = array();
        $GameTypeInfo = array();

        foreach ($LTPeriods as $key => $value) {
            $sql    = "
                SELECT
                    url_name,    -- 網址名稱
                    url            -- 網址
                FROM
                    LT_url
                WHERE
                    id = ?
            ";
            $LT_url = array_shift($this->mod->select($sql, array(
                $value["url_id"]
            )));

            if (!empty($LT_url)) {
                $data = array(
                    "code" => $value["lottery"],
                    "source" => $LT_url["url_name"],
                    "url" => $LT_url["url"],
                    "rectime" => $value["lottery_time"]
                );
                array_push($lottery_info, $data);
            }

            $GameTypeInfo = array(
                "game_id"      => $value["game_id"],
                "period_date"  => date('Y-m-d', strtotime($value["lottery_time"])),
                "periods"      => $value["period_str"],
                "time"         => $value["lottery_time"],
                "lottery_info" => $lottery_info
            );
        }

        $this->obj["code"]         = 100;
        $this->obj["GameTypeInfo"] = $GameTypeInfo;
        $this->output();
    }
    //總後端 → 抓獎系統(錯誤開獎號碼查詢)
    public function specifyPeriodError($token = null, $periodS = null, $periodE = null){
        $this->tokenValidate($token);

        $data['begin_date'] = date("Y-m-d H:i:s",strtotime($periodS));
        $data['end_date']   = date("Y-m-d H:i:s",strtotime($periodE));

        if ((!strtotime($data['begin_date'])) && (!strtotime($data['begin_date'])))
             $this->obj['msg']  = $this->delItem[204];

        if(empty($data['end_date']))
            $data['end_date'] =Date('Y-m-d h:i:s', strtotime("-0 days"));

        $sql  = "
            SELECT
                *
            FROM
                LT_period_error
            WHERE
                setuptime BETWEEN ? AND ?
            ";
        $data = $this->mod->select($sql, array(
            $data['begin_date'],
            $data['end_date']
        ));

        foreach ($data as $k => $v) {
            $sql2  = "SELECT period_str FROM LT_periods WHERE id = ?";
            $data2 = array_shift($this->mod->select($sql2, array($v["lottery_id"])));
            $data[$k]["period_str"] = $data2["period_str"];
        }

        if (!empty($data)) {
            $this->obj["code"]         = 100;
            $this->obj["GameTypeInfo"] = $data;
        } else {
            $this->obj["code"] = 201; //period在periods資料表找不到
            $this->obj['msg']  = $this->delItem[201];
        }
            $this->output();
    }
    //總後端 → 抓獎系統(六合彩&萬字票抓取開獎日期)
    public function openDate($token = null, $gameid = null, $year = null, $month = null){
        $this->tokenValidate($token);
        if ($year === null)
            $year = date("Y");

        $sql   = "
            SELECT
                lottery_year,    -- 年
                lottery_month,    -- 月
                lottery_day        -- 日
            FROM
                LT_openset
            WHERE
                1=1
        ";
        $where = array();

        if ($month === null) {
            $month = date("m");
            $sql .= " AND game_id = ? AND lottery_year = ?";
            $where[] = $gameid;
            $where[] = $year;
        } else {
            $sql .= " AND game_id = ? AND lottery_year = ? AND lottery_month = ?";
            $where[] = $gameid;
            $where[] = $year;
            $where[] = $month;
        }

        $LTVac = $this->mod->select($sql, $where);

        if (!empty($LTVac)) {
            foreach ($LTVac as $key => $value) {
                $sqlData[$key]["Lottery_year"]  = $LTVac[$key]["lottery_year"];
                $sqlData[$key]["Lottery_month"] = $LTVac[$key]["lottery_month"];
                $sqlData[$key]["Lottery_day"]   = $LTVac[$key]["lottery_day"];
            }

            $this->obj["code"]        = 100;
            $this->obj["Lottery_day"] = $sqlData;
            $this->output();
        } else {
            $this->obj["code"] = 201; //period在periods資料表找不到
            $this->obj['msg']  = $this->delItem[201];
            $this->output();
        }
    }
    //總後端 → 抓取遊戲假期
    public function vacList($token = null, $year = null, $gameid = null) {
        $this->tokenValidate($token);
        $sql   = "SELECT id,game_id,vacStart,vacEnd FROM LT_vac WHERE 1 = 1";
        $where = array();

        if ($year != null) {
            $sql .= " AND `vacStart` > ? AND `vacEnd` <= ?";
            $where[] = $year . '-01-01 00:00:00';
            $where[] = $year . '-12-31 23:59:59';
        }
        if ($gameid != null) {
            $sql .= " AND game_id = ?";
            $where[] = $gameid;
        }

        $info = $this->mod->select($sql, $where);
        
        $this->obj["code"] = 100;
        $this->obj['list']  = $info;
        $this->output();
    }
    //抓出所有遊戲 以及 遊戲的網址
    public function gameGroup(){
        $sql  = "
            SELECT
                cname,    -- 遊戲名稱
                ename,    -- 遊戲名稱
                id,        -- 遊戲ID
                param_2    -- 參數 開獎格式
            FROM
                LT_game
            WHERE
                enable = ?
        ";
        $info = $this->mod->select($sql, array(0));

        $sql_data = array(); //存放從資料庫抓出的資料格式

        foreach ($info as $key => $value) {
            $sql   = "
                SELECT
                    id,            -- 遊戲ID
                    url,        -- 網址
                    api_name,    -- 網址名稱
                    proxy_enable -- Proxy 啟用狀態
                FROM
                    LT_url
                WHERE
                    game_id = ? AND
                    enable = ?
            ";
            $LTUrl = $this->mod->select($sql, array($value["id"],0));

            $url = array();

            foreach ($LTUrl as $key2 => $value2) {
                $LTUrlData             = array();
                $LTUrlData["id"]       = $value2["id"];
                $LTUrlData["url"]      = $value2["url"];
                $LTUrlData["api_name"] = $value2["api_name"];
                $LTUrlData["proxy_enable"] = $value2["proxy_enable"];
                array_push($url, $LTUrlData);
            }

            $obj = array(
                "cname" => $value["cname"],
                "ename" => $value["ename"],
                "gameId" => $value["id"],
                "param_2" => json_decode($value["param_2"], true),
                "url" => $url
            );
            array_push($sql_data, $obj);
        }

        $this->obj['code'] = 100;
        $this->obj['list'] = $sql_data;
        $this->output();
    }
    //檢查 開獎時間 延誤
    public function openNumCheck(){
        $sql = "
            SELECT
                id,
                cname,
                noticeTime -- 開獎時間range
            FROM
                LT_game
            WHERE
                enable = ?
        ";
        $data = $this->mod->select($sql, array(0));
        $Notice_count = 0;
        $today = date('Y-m-d') . ' 00:00:00';

        foreach ($data as $k => $v) {
            $sql = "
                SELECT
                    period_str,
                    be_lottery_time, -- 預計開獎時間
                    lottery_time     -- 實際開獎時間
                FROM
                    LT_periods
                WHERE
                    game_id = ? AND
                    lottery_status = ? AND
                    be_lottery_time >= ? 
            ";
            $dataPeriod = $this->mod->select($sql, array($v["id"], 0, $today));
            
            foreach ($dataPeriod as $k1 => $v1) {
                $noticeTime    = $v["noticeTime"];      //開獎range
                $rangeTime     = date('Y-m-d H:i:s', strtotime("-".$noticeTime." seconds"));        //區間時間
                $beLotteryTime = $v1["be_lottery_time"];    //預計開獎時間

                //抓取要傳送到哪個群組
                $sql = "SELECT user_id FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? AND web = ? ";
                $noticeID = $this->mod->select($sql, array(1, 'N', $this->gdata['country_code']));
                
                if($rangeTime > $beLotteryTime) {
                    $Notice_count++;
                    /**存到通知表**/
                    foreach ($noticeID as $ID) {
                        $msg = $v["cname"]. "　第".$v1["period_str"]."期，\n預計開獎時間為".$beLotteryTime;
                        $notice = array(
                            'noticeCode'=> 'delayOpen', //延誤開獎
                            'game_id'   => $v['id'],
                            'period_str'=> $v1['period_str'],
                            'user_id'   => $ID['user_id'],
                            'msg'       => $msg
                        );
                        $this->pubfun->noticeMsg($notice);
                    }
                    /**************/
                }
            }
        }

        if (!empty($data)) {
            $this->obj["code"]         = 100;
            $this->obj["Notice_count"] = $Notice_count;
        } else {
            $this->obj["code"] = 500;
        }
            $this->output();
    }
    //檢查期數表是否已經產生
    public function checkPeriods(){
        $nowtime = date("Y-m-d H:i:s");
        $tomorrow = date('Y-m-d',strtotime($nowtime . "+1 days"));//要產生的日期 -->產生日+1
        $week = date("w", strtotime($tomorrow));
        $sql = "SELECT id,cname FROM LT_game WHERE enable = ? AND cycle = ? ";
        $data = $this->mod->select($sql, array(0, 'days'));
        
        $Notice_count = 0;

        foreach ($data as $k => $v) {
            //假期表，檢查當日 日期 是不是有在假期區間
            $sql = "
                SELECT
                    id
                FROM
                    LT_vac
                WHERE
                    game_id = ? AND
                    enable = ? AND
                    ? BETWEEN vacStart AND vacEnd
                ";
            $LTVac = $this->mod->select($sql, array($v["id"],0,$tomorrow));

            //若資料表不等於空，則代表產生日期在假期區間內，因此不往下執行，直接往下一個ID執行
            if(!empty($LTVac))continue;

            //期數表
            if ($v['id'] == 110) {
                $sql = "SELECT count(id) as num FROM `hn_city` WHERE w".$week." = 'Y'";
                $period = array_shift($this->mod->select($sql));
            } else {
                $sql = "SELECT count(id) as num FROM tb_game_periods WHERE game_id = ?";
                $period = array_shift($this->mod->select($sql, array($v["id"])));
            }

            $sql = "SELECT count(id) as num FROM LT_periods WHERE game_id = ? AND period_date = ?";
            $info = array_shift($this->mod->select($sql, array($v["id"],$tomorrow)));
            $msg = $v["cname"]. "　" .$tomorrow;
            if($info['num'] == 0){
                $noticeCode = 'periodTabNotCre'; //期數表尚未產生
            } else if ($period['num'] != $info['num']) {
                $noticeCode = 'periodError'; //期數表不期全
                $msg = $msg . "\n預計產生期數為".$period['num']."筆\n實際產生期數為".$info['num']."筆";
                $this->autocreatdata($v["id"], $tomorrow, false);
            } else {
                continue;
            }
            /**存到通知表**/
            //抓取要傳送到哪個群組
            $sql = "SELECT user_id FROM tb_telegram_user WHERE group_id = ? AND `enable` = ? AND web = ? ";
            $noticeID = $this->mod->select($sql, array(1, 'N', $this->gdata['country_code']));
            foreach ($noticeID as $ID) {
                $notice = array(
                    'noticeCode'=> $noticeCode,
                    "game_id"   => $v["id"],
                    "period_str"=> $tomorrow,
                    'user_id'   => $ID['user_id'],
                    'msg'       => $msg
                );
                if($this->pubfun->noticeMsg($notice) == 0){
                    $Notice_count++;
                }
            }
            /**************/
        }
        if (!empty($data)) {
            $this->obj["code"]         = 100;
            $this->obj["Notice_count"] = $Notice_count;
        } else {
            $this->obj["code"] = 500;
        }
            $this->output();
    }
    //跑telegram通知
    public function sendNotice(){
        $data = $this->man_lib('lib_telegram')->SendNotice();

        if (!empty($data)) {
            $this->obj["code"] = 100;
            $this->obj["Notice_count"] = $data;
        } else {
            $this->obj["code"] = 101;
            $this->obj["Notice_count"] = 0;
        }
            $this->output();
    }
    //抓取網頁資料
    /**情況整理
     *情況ㄧ:正常情況下的網址 else{cqssc}
     *情況二:網址是迴圈 跑xml        抓資料
     *情況三:網址是迴圈 跑正常情況 抓資料
     */
    public function curl_array_url($url_data = array(), $postdata = array(), $api_name = null, $proxy = false, $option = array()){
        $retries = 3; //連線次數
        // $this->pxy->set(false);    // 開啟proxy 設false 是關閉 沒打預設是關閉
        if ($proxy)
            $this->pxy->set(-1);

        $default = array('CONNECTTIMEOUT' => 10, 'TIMEOUT' => 30);
        $option = array_merge($default, $option);

        // 網址超過一個
        if (count($url_data) > 1) {
            $data = array();
            foreach ($url_data as $key => $value) {
                $store = false;
                sleep(5);

                while (($store === false) && (--$retries > 0)) {
                    $store = $this->util->curl($value, array(), array(
                        'CONNECTTIMEOUT' => 10,
                        'TIMEOUT ' => 30
                    ), $api_name);
                }

                // 確認回來的東西是不是XML
                $checkXML = $this->_isValidXML($store);
                if ($checkXML) {
                    $pusharray    = array();
                    $xml          = simplexml_load_string($store, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $result_array = ($xml) ? json_decode(json_encode($xml), TRUE) : array();

                    if (!empty($result_array)) {
                        array_push($pusharray, $result_array);
                    }
                    array_push($data,$pusharray);
                    continue;
                }
                array_push($data,$store);
            }
            return $data;
        } else {
            $store = false;
            //連線失敗再度連線，@retries 是連線次數
            while (($store === false) && (--$retries > 0)) {
                $store = $this->util->curl($url_data[0], $postdata, $option, $api_name);
            }
            // 確認回來的東西是不是XML
            $checkXML = $this->_isValidXML($store);
            if ($checkXML) {
                $pusharray    = array();
                $xml          = simplexml_load_string($store, 'SimpleXMLElement', LIBXML_NOCDATA);
                $result_array = ($xml) ? json_decode(json_encode($xml), TRUE) : array();

                if (!empty($result_array)) {
                    array_push($pusharray, $result_array);
                }

                if (empty($pusharray)) {
                    $pusharray = false;
                }

                return array($pusharray);
            }
            return array($store);
        }
    }
    //自動產生期數表
    public function autocreatdata($gameId = null, $data = null, $return = true){
        $nowtime = date("Y-m-d H:i:s");
        $tomorrow = $data == null ? date('Y-m-d',strtotime($nowtime . "+1 days")) : date('Y-m-d',strtotime($data));
        $condition = $gameId == null ? $condition = '' : 'AND id = ' . $gameId;
        
        //各遊戲的規則
        $sql = "
            SELECT
                id,
                cycle,
                param,
                param_1,
                period_format,
                period_num,
                lottery_num,
                min_number,
                max_number
            FROM
                LT_game
            where 1
                AND enable = ?
                AND cycle = ?
                " . $condition ."
        ";
        $LT_game = $this->mod->select($sql, array(0, 'days'));

        /**抓取指定日期休市的遊戲 */
        $sql = "SELECT game_id,vacStart,vacEnd FROM LT_vac WHERE enable = ? AND ? BETWEEN vacStart AND vacEnd ";
        $LTVac = $this->mod->select($sql, array(0, $tomorrow));
        $LTVac = $this->mod->conv_to_key($LTVac, 'game_id');

        foreach ($LT_game as $key => $value) {
            $id            = $value["id"];
            $cycle         = $value["cycle"];
            $param         = $value["param"];
            $param_1       = $value["param_1"];
            $period_format = $value["period_format"];
            $period_num    = $value["period_num"];
            $lottery_num   = $value["lottery_num"];
            $min_number    = $value["min_number"];
            $max_number    = $value["max_number"];

            /**判斷是否存在於休市表裡，若存在以下均不執行，直接跳下一彩種 */
            if(isset($LTVac[$id])) continue;

            /** 抓取遊戲相對應 預設期數表*/
            $sql = "SELECT Periods,PeriodsTime FROM tb_game_periods WHERE game_id = ? ";
            $tb_game_periods = $this->mod->select($sql, array($id));
            
            /**判斷期數是不是已經存在periods表裡
            /*情況1：Ymd依照年+月+日+編號產生期數，因此比對期數 與 遊戲ID 已經存在則不需在新增。
            /*情況2：Y  依照年 　　　 +編號產生期數，因此檢查日期 與 遊戲ID 已經存在則不需在新增。
            /*情況3：編號遞增產生期數，與情況2雷同，比對方式相同。
            **/
            $itemInfo = array();
            $insertItem = array();
            switch ($period_format) {
                case 'Ymd':
                case 'ymd':
                    $firstPeriod = $this->mod->conv_to_key($tb_game_periods, 'Periods');
                    if (!isset($firstPeriod[1]['PeriodsTime'])) continue;
                    $firstTime = $tomorrow . " " . $firstPeriod[1]['PeriodsTime'];
                    
                    foreach ($tb_game_periods as $key2 => $value2) {
                        $period_str = date($period_format,strtotime($tomorrow)) . str_pad($value2["Periods"], $period_num, '0', STR_PAD_LEFT);
                        $be_lottery_time = $tomorrow . " " . $value2["PeriodsTime"];
                        if (strtotime($be_lottery_time) < strtotime($firstTime)) {
                            $be_lottery_time = date("Y-m-d H:i:s", strtotime($tomorrow . " " . $value2["PeriodsTime"] . "+1 day"));
                        }
                        $itemInfo[$tomorrow][$period_str] = $be_lottery_time;
                    }
                    break;
                case 'Ymd_id':
                    $week = date('w', strtotime($tomorrow));
                    $sql = "SELECT id,PeriodsTime FROM `hn_city` WHERE w".$week . " = 'Y'";
                    $info = $this->mod->select($sql);
                    foreach ($info as $k => $v) {
                        $period_str = date('Ymd',strtotime($tomorrow)) . '_' . $v['id'];
                        $be_lottery_time = $tomorrow . " " . $v["PeriodsTime"];

                        $itemInfo[$tomorrow][$period_str] = $be_lottery_time;
                    }
                    break;
                default:
                    if($period_format == "Y"){ //年+流水號--福彩3D、體彩P3
                        switch (date('m-d',strtotime($tomorrow))) {
                            case '01-01':
                                $period_str = date($period_format) . str_pad(0, $period_num, '0', STR_PAD_LEFT);;
                                break;
                            default:
                                $period_str = (int) $param;
                                break;
                            }
                    }else{ //流水號--北京賽車，北京時時彩，幸運28
                        $period_str = (int) $param;
                    }
                    $diff = (strtotime($tomorrow) - strtotime($param_1)) / (60*60*24);//要補的天數
                    for ($i = 1 ; $i <= $diff ; $i++ ) {
                        $tomorrow = date('Y-m-d', strtotime($param_1 . '+'.$i.' day'));//要產生的日期
                        
                        /**假期表，檢查「產生日期」 是否在假期區間*/
                        $sql = "SELECT id FROM LT_vac WHERE game_id = ? AND enable = ? AND ? BETWEEN vacStart AND vacEnd ";
                        $info = $this->mod->select($sql, array($id, 0, $tomorrow));

                        /**資料表狀態
                         * 空：指定日期不存在區間，繼續往下執行。
                         * 非空：指定日期在假期區間，不須產生期數表，直接跳過，前往下一個for。
                         */
                        if(!empty($info))continue;
                        foreach ($tb_game_periods as $k => $v) {
                            if (!empty($param) && $period_format == "Y"){
                                $period_str =  substr ($period_str, -3);
                                $period_str += 1;
                                $period_str = date($period_format,strtotime($tomorrow)) . str_pad($period_str, $period_num, '0', STR_PAD_LEFT);
                            }else if (!empty($param) && $period_format == ""){
                                $period_str += 1;
                            }else if (!empty($period_format)){
                                $period_str = date($period_format) . str_pad($v["Periods"], $period_num, '0', STR_PAD_LEFT);
                            }else{
                                $period_str = $param + $k;
                            }
            
                            $be_lottery_time = $tomorrow . " " . $v["PeriodsTime"];
                            $itemInfo[$tomorrow][$period_str] = $be_lottery_time;
                        }
                    }
                    break;
            }
            
            $this->autocreatdata_insert(array(
                'game_id' =>$id,
                'period_num' =>$period_num, 
                'itemInfo' =>$itemInfo
            ));
        }
        if ($return) {
            $this->obj["code"] = 100;
            $this->output();
        }
    }
    /** 
     * 流水號 期數 產生
     * type => period  
     * type => date    需要補中間沒有產生的期數
    */
 
    //指定日期產生期數表
    public function specifyDay(){
        //各遊戲的規則
        $sql = "SELECT param,id,period_num,period_format,param_2 FROM LT_game WHERE enable = ? AND cycle = ? ";
        $LTGame  = $this->mod->select($sql, array(0, "weeks"));
        
        foreach ($LTGame as $key => $value) {
            $openTime = 'Y-m-d ' . $value['param_2'];
            $nowYear = date('Y');
            $nowMonth = date('m');
            $nextYear = date('Y', strtotime(date('Y-m')." +1 month"));
            $nextMonth = date('m', strtotime(date('Y-m')." +1 month"));
            $sql = "SELECT lottery_year,lottery_month,lottery_day,id FROM LT_openset WHERE ((`lottery_year` = ? AND `lottery_month` = ?) OR(`lottery_year` = ? AND `lottery_month` = ?)) AND `enable` = ? AND game_id = ? ";
            $LTOpenset = $this->mod->select($sql, array($nextYear, $nextMonth, $nowYear, $nowMonth, 0, $value["id"]));

            switch ($value["period_format"]) {
                case 'Y':
                    switch (date('m-d')) {
                        case '01-01':
                            $period_str = date($value["period_format"]) . str_pad(0, $value["period_num"], '0', STR_PAD_LEFT);
                            break;
                        default:
                            $nowPerodsYear = substr ((int) $value["param"], 0,4);
                            if(date($nowPerodsYear) != $nextYear){
                                $periodStrNum = strlen($nextYear) + $value["period_num"];
                                $period_str = str_pad($nextYear, $periodStrNum, '0', STR_PAD_RIGHT);
                            } else {
                                $period_str = substr ((int) $value["param"], -3);
                                $period_str = date($value["period_format"]) . str_pad($period_str, $value["period_num"], '0', STR_PAD_LEFT);
                            }
                            break;
                    }
                    break;
                default:
                    $period_str = $value["param"];
                    break;
            }
            
            foreach ($LTOpenset as $key2 => $value2) {
                $lottery_day = explode(",", $value2["lottery_day"]);
                
                foreach ($lottery_day as $key3 => $value3) {
                    $period_str +=1;
                    $time            = $value2["lottery_year"] . "-" . $value2["lottery_month"] . "-" . $value3;
                    
                    $period_date = date('Y-m-d', strtotime($time));
                    $be_lottery_time = date($openTime, strtotime($time));
                    $itemInfo[$period_date][$period_str] = $be_lottery_time;
                }
                $this->autocreatdata_insert(array(
                    'game_id' =>$value['id'],
                    'period_num' =>$value["period_num"],
                    'itemInfo' =>$itemInfo
                ));
                //第一個參數是table, 第二個參數是where條件, 第三個參數是set
                $this->mod->modi_by('LT_openset', array('id' => $value2["id"]), array('enable' => 1));
            }
        }

        $this->obj["code"] = 100;
        $this->output();
    }
    /**
     * **檢查期數表是不是已存在
     * 不存在：直接新增
     * 存在：需一筆一筆檢查缺了哪幾筆，在做新增
     * **新增期數表
     * 將要新增的資料先轉成文字模式。
     * 這樣可以一次新增，減少新增時間
     */
    private function autocreatdata_insert($data = array()) {
        if (empty($data)) return;
        $game_id = $data['game_id'];
        $period_num = $data['period_num'];
        $itemInfo = $data['itemInfo'];

        foreach($itemInfo as $k => $v) {
            $param_1 = $k;
            $sql  = "SELECT period_str FROM LT_periods WHERE game_id = ? AND period_date = ?";
            $exist = $this->mod->select($sql, array($game_id, $k));
            $toKey = $this->mod->conv_to_key($exist, 'period_str');
            foreach($v as $per => $beTime) {
                $period_str = $per;
                if (!isset($toKey[$per]))
                    $sqlItem[$per] = "('{$game_id}', '{$param_1}', '{$period_num}', '{$per}', '{$beTime}')";
            }
        }
        
        if (empty($sqlItem)) return;
        $sql = 'INSERT INTO LT_periods (`game_id`, `period_date`, `period_num`, `period_str`, `be_lottery_time`) VALUES ';
        $sql .= implode(",",$sqlItem) . ';';
        $add = $this->mod->insert($sql);
        
        if (isset($add['lid'])) {
            $this->mod->modi_by('LT_game', array(
                'id' => $game_id
            ), array(
                'param'   => $period_str,
                'param_1' => $param_1
            ));
        }
    }
    //check token
    private function tokenValidate($token = null){
        // 如果未設置token
        if ($token === null)
            $this->output();

        $token = $this->libc->aes_de($token, 'itwinstars');

        $now   = time();
        $time  = strtotime(explode('||', $token)[0]);
        // 如果傳入token不為時間
        if (!((int) $time > 0))
            $this->output();

        if (($now - $time) > 60) {
            $this->obj["code"]       = 203;
            $this->obj["msg"]        = "Token expired.";
            $this->obj["error_info"] = "Token expired.";
            $this->output();
        }
    }
    //check if xml is valid document
    private function _isValidXML($xml){
        $doc = @simplexml_load_string($xml);
        if ($doc) {
            return true;
        } else {
            return false;
        }
    }
}