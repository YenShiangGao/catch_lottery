<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

@ini_set('display_errors', 1);
/*設置錯誤信息的類別*/
class Gloco extends web_mem
{
    public $delItem;

    public function __construct()
    {
        parent::__construct();
        $this->delItem = $this->defaultms();
        $this->load->library('curl');
        $this->load->library('lib_func');
    }

    public function index(){
        $sql  = "
            SELECT
                id,
                game_id,        -- 遊戲名稱
                lottery,        -- 開獎號碼
                period_date,    -- 開獎日期
                period_str,     -- 開獎期數
                lottery_time,   -- 實際開獎時間
                be_lottery_time,-- 預計開獎時間
                lottery_status, -- 開獎狀態
                url_id          -- 開獎來源
            FROM
                LT_periods
            WHERE
                game_id = ? AND
                period_date = ? AND
                be_lottery_time <= ?
        ";
        $info = $this->mod->select($sql, array(1,date("Y-m-d"),date("Y-m-d H:i:s")));

        if (empty($info)) {
            $this->obj["code"] = 104;
            $this->obj['msg']  = $this->delItem[104];
        } else {
            foreach ($info as $key => $value) {
                $sql  = "SELECT cname,ename FROM LT_game WHERE id = ?";
                $game = array_shift($this->mod->select($sql, array($value["game_id"])));

                $sql = "SELECT url_name,id FROM LT_url WHERE id = ?";
                $url = array_shift($this->mod->select($sql, array($value["url_id"])));

                if (!empty($game))
                    $info[$key]["name"] = $game["cname"];
                else
                    $info[$key]["name"] = "null";

                if (!empty($game))
                    $info[$key]["ename"] = $game["ename"];
                else
                    $info[$key]["ename"] = "null";

                if (!empty($url))
                    $info[$key]["game_url"] = "[".$url["id"]."]".$url["url_name"];
                else
                    $info[$key]["game_url"] = "null";

                if ($info[$key]["lottery_status"] == "0")
                    $info[$key]["lottery_status"] = "<i class='fa fa-minus-circle' aria-hidden='true' style='color:red;font-size: xx-large;'></i>";
                else
                    $info[$key]["lottery_status"] = "<i class='fa fa-check-circle-o' aria-hidden='true' style='color:green;font-size: xx-large;'></i>";
            }
            
            $this->obj["code"]      = 100;
            $this->obj["data"]      = $info;
            $this->obj["gameGroup"] = $this->gameGroup();
        }

        $this->output();
    }
    //取出 已開獎 或 未開獎 狀態
    public function statusSel($from = null)
    {
        $status = $_POST["status"]; //0未開獎1已開獎
        $gameID = $_POST["gameID"];
        $date   = $_POST["date"];

        $sql   = "
            SELECT
                id,
                game_id,
                lottery_status,
                game_id,
                lottery,
                period_date,
                period_str,
                lottery_time,
                be_lottery_time,
                url_id
            FROM
                LT_periods
            WHERE
                1=1
        ";
        $where = array();

        switch ($gameID) {
            case 7:
            case 8:
            case 66:
            case 155:
                $sql .= " AND game_id = ?";
                $where[] = $gameID;

                if ($status == 'Y' || $status == 'N') {
                    $status = $status == 'Y' ? '1' : '0';
                    $sql .= " AND lottery_status = ?";
                    $where[] = $status;
                }
                
                if (!empty($date)) {
                    $sql .= " AND period_date = ?";
                    $where[] = $date;
                }
                break;
            default:
                if (empty($_POST["date"]))
                    $date = date("Y-m-d");

                $sql .= " AND period_date = ?";
                $where[] = $date;

                $sql .= " AND game_id = ?";
                $where[] = $gameID;
                if ($status == 'Y' || $status == 'N') {
                    $status = $status == 'Y' ? '1' : '0';
                    $sql .= " AND lottery_status = ?";
                    $where[] = $status;
                }
                break;
        }

        $info = $this->mod->select($sql, $where);
        
        if (empty($info)) {
            $this->obj["code"] = 104;
            $this->obj['msg']  = $this->delItem[104];
        } else {
            foreach ($info as $key => $value) {
                $sql  = "SELECT cname,ename FROM LT_game WHERE id = ?";
                $game = array_shift($this->mod->select($sql, array($value["game_id"])));

                $sql = "SELECT url_name,id FROM LT_url WHERE id = ?";
                $url = array_shift($this->mod->select($sql, array($value["url_id"])));

                if (!empty($game))
                    $info[$key]["name"] = $game["cname"];
                else
                    $info[$key]["name"] = "null";

                if (!empty($game))
                    $info[$key]["ename"] = $game["ename"];
                else
                    $info[$key]["ename"] = "null";

                if (!empty($url))
                    $info[$key]["game_url"] = "[".$url["id"]."]".$url["url_name"];
                else
                    $info[$key]["game_url"] = "null";
                
                if ($value['game_id'] == 155 || $value['game_id'] == 110) 
                    $size = "{width: 500, height: 820}";
                else 
                    $size = "{width: 350, height: 300}";
                
                if ($from == null) {
                    if ($value["lottery_status"] == "0")
                        $info[$key]["lottery_status"] = "<i class='fa fa-minus-circle' aria-hidden='true' style='color:red;font-size: xx-large;'></i>";
                    else
                        $info[$key]["lottery_status"] = "<i class='fa fa-check-circle-o' aria-hidden='true' style='color:green;font-size: xx-large;'></i>";

                } else {
                    if ($value["lottery_status"] == "1" || $value['be_lottery_time'] < date('Y-m-d H:i:s')) //已開獎 || 已過開獎時間
                        $info[$key]["lottery_status"] = '<button type="button" class="btns btns-sm btns-blue" onclick="ZgWindowFun.GoPage(\'main/pop/Highweightlist/editInfo/' . $value['id'] . '\', \'iframeSimple\', '.$size.' , \'修改\')"><i class="fa fa-pencil" aria-hidden="true"></i>修改</button>';
                    else
                        $info[$key]["lottery_status"] = "<i class='fa fa-minus-circle' aria-hidden='true' style='color:red;font-size: xx-large;'></i>";
                }
                
                if (count(explode("_",$value['period_str'])) > 1) {
                    $hnid = explode("_",$value['period_str'])[1];
                    $info[$key]["city"] = array_shift($this->mod->select('SELECT area FROM hn_city WHERE id = "'.$hnid.'"'))['area'];
                    if ($info[$key]["city"] == null) 
                        unset($info[$key]);
                } 
            }
            
            $this->obj["code"]      = 100;
            $this->obj["data"]      = $info;
            $this->obj["gameGroup"] = $this->gameGroup();
        }

        $this->output();
    }
    //取出遊戲資源
    public function gameUrlstatus()
    {
        $sql   = "
            SELECT
                game_id,    -- 遊戲ID
                id,         -- 資料表
                api_name,   -- api_name
                url_name,   -- url_name
                last_period,-- 最後更新期數
                last_status,-- 最後更新狀態
                last_time,  -- 最後更新日期
                last_proxy  -- 最後proxy
            FROM
                LT_url
            WHERE
                enable = ?
        ";
        $where = array(0);

        if (isset($_POST["gameID"]) && !empty($_POST["gameID"])) {
            $sql .= " AND game_id = ?";
            $where[] = $_POST["gameID"];
        }

        $LTUrl = $this->mod->select($sql, $where);

        $gameUrl = array();
        $host    = 'http://' . $_SERVER['HTTP_HOST'] . '/';

        if (empty($LTUrl)) {
            $this->obj["code"] = 104;
            $this->obj['msg']  = $this->delItem[104];
        } else {
            foreach ($LTUrl as $key => $value) {
                $sql  = "SELECT cname FROM LT_game WHERE id = ?";
                $game = array_shift($this->mod->select($sql, array($value["game_id"])));

                if (!empty($game))
                    $data["name"] = $game["cname"];
                else
                    $data["name"] = '';

                switch ($value["last_status"]) {
                    case '0':
                        $value["last_status"] = "<i class='fa fa-minus-circle' aria-hidden='true' style='color:red;font-size: xx-large;'></i>";
                        break;
                    case '1':
                        $value["last_status"] = "<i class='fa fa-check-circle-o' aria-hidden='true' style='color:green;font-size: xx-large;'></i>";
                        break;
                }
                $data["id"] = $value["id"];
                $data["last_status"] = $value["last_status"];
                $data["last_period"] = $value["last_period"];
                $data["last_time"]   = $value["last_time"];
                $data["proxy"]       = $value["last_proxy"];
                $data["api_name"]    = $value["api_name"];
                $data["url_name"]    = $value["url_name"];
                $data["btn"]         = "<a href='" . $host . "gloco/urlResources/game_id/" . $value["game_id"] . "' target='_blank' class='btns btns-blue'>資源</a>";

                array_push($gameUrl, $data);
            }

            $this->obj["code"] = 100;
            $this->obj["data"] = $gameUrl;
        }

        $this->output();
    }
    //資源
    public function urlResources($field = 'all', $val = ''){
        $host   = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $sql    = "SELECT cname,ename,id FROM LT_game WHERE id = ?";
        $LTGame = array_shift($this->mod->select($sql, array($val)));

        $sql   = "SELECT api_name,url_name,url,id FROM LT_url WHERE game_id = ? AND enable = ?";
        $LTUrl = $this->mod->select($sql, array($val,0));

        $style = "background-color: #73c7eb;color: #333;line-height: 35px;padding: 0 15px;font-size: 0.875rem;border: none;";
        $style2 = "background-color: #ffeb3b;color: #333;line-height: 35px;padding: 0 15px;font-size: 0.875rem;border: none;";
        $styleTar = "background-color: #ef5050;color: #333;line-height: 35px;padding: 0 15px;font-size: 0.875rem;border: none;";
        $areaAry = ['NAM', 'TRUNG', 'BAC'];

        if (!empty($LTGame)) {
            $cname = $LTGame["cname"];
            $ename = $LTGame["ename"];

            echo '<tr><td colspan=\'4\'><nav><center><a href="#game' . $LTGame["id"] . '" style=" display:inline-block; width:200px; margin:5px; padding:3px; background:#369; color:#fff; text-decoration:none">[' . $LTGame["id"] . ']' . $cname . '</a></center></nav></td></tr>';
            echo '<tr><td colspan=\'4\'><center><h1>game_id:' . $LTGame["id"] . '</h1></center></td></tr>';
        }
        $html = '<table style="border:1px #cccccc solid; margin-left:auto; margin-right:auto;" cellpadding="10" border="2"><tbody>';
        if (!empty($LTUrl)) {
            foreach($LTUrl as $k => $v) {
                //處理網址
                $sql_data["api_name"] = $v["api_name"];
                $sql_data["url"]      = $v["url"];

                if ($ename === "3da01")
                    $ename = "A3da01";

                if ($ename === "3d")
                    $ename = "A3d";

                $url_data = $this->url_lib($ename)->getlottery_url($sql_data);

                if ($k % 2 === 0 ) {
                    $html .= '<tr>';
                }
                $html .= '<td>
                            <table style="width: 735px;">
                                <tr>
                                    <td align="center" colspan=\'4\' style="font-size: 20px;">[' . $v["id"] . '] <b>' . $cname . " " . $v["url_name"] . '</b> - ' . $v["api_name"] . '</td>
                                </tr>
                                <tr>';
                                if ($val == 110) {
                                    foreach($areaAry as $area) {
                                        $apiurl = $host . "lotteryapi/hnUnofficialCatchApi/" . $v["id"] . "/" . $v["api_name"] . '/' . $area;
                                        $html .='
                                            <td align="center">
                                            <button onclick="window.open(\''.$apiurl.'/false/interface\')" style="'.$style.'">'.$area.'-CURL接口</button><br>
                                            <button onclick="window.open(\''.$apiurl.'\')" style="'.$style2.' margin:5px">手動寫入</button>
                                            </td>
                                            ';
                                    }
                                } else {
                                    $apiurl = $host . "lotteryapi/catchApi/" . $ename . "/" . $v["id"] . "/" . $v["api_name"];
                                    
                                    $html .='
                                        <td><button onclick="window.open(\''.$apiurl.'/false/interface\')" style="'.$style.'">CURL接口</button></td>
                                        <td><button onclick="window.open(\''.$apiurl.'/false/interface/1\')" style="'.$style.'">CURL接口(PROXY)</button></td>
                                        <td><button onclick="window.open(\''.$apiurl.'\')" style="'.$style2.'">手動寫入</button></td>
                                        ';
                                }
                                foreach ($url_data as $k2 => $v2) {
                                    $html .= '<td><button onclick="window.open(\''.$v2.'\')" style="'.$styleTar.'">CURL目標網址</button></td>';
                                }
                $html .= '</tr><tr><td colspan=\'4\'><iframe width="600" height="250" frameborder="0" scrolling="auto" src="'.$apiurl.'/false/interface"></iframe></td></tr>';
                $html .= '</table></td>';
                if ($k % 2 === 1 ) {
                    $html .= '</tr>';
                }
            }
        }
        $html .= '</tbody></table>';
        
        echo $html;
    }
    private function tableCreate() {

    }
    //抓出所有遊戲
    public function gameGroup()
    {
        $sql     = "SELECT id,cname FROM LT_game WHERE enable = ?";
        $LT_game = $this->mod->select($sql, array(0));

        $gameGroup = array();
        foreach ($LT_game as $key => $value) {
            $gameGroup[$LT_game[$key]["id"]]["id"]   = $LT_game[$key]["id"];
            $gameGroup[$LT_game[$key]["id"]]["name"] = $LT_game[$key]["cname"];
        }

        return $gameGroup;
    }
}