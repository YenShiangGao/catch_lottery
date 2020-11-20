<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

@ini_set('display_errors', 1);
/*設置錯誤信息的類別*/
class main extends web_mem{
    public $IsLogin = 'false';
    public $UserInfo = false;
    public $delItem;

    public function __construct(){
        // $this->pr($_COOKIE);
        parent::__construct(); //將繼承的web_mem init
        // $this->lib_sess->destory();        //clear cookie
        $log_ag_code   = $this->lib_sess->GetCookie("log_ag_code");
        $this->delItem = $this->defaultms();

        if ($log_ag_code) {
            $this->IsLogin = 'true';
            $LoginInfo     = json_decode($this->lib_codes->aes_de($log_ag_code), true);

			$this->gdata["LoginID"]          = $LoginInfo["LoginID"]; //資料庫編碼
			$this->gdata["gda_lvl_name"]     = $LoginInfo["lvl_name"]; //階級名稱
			$this->gdata["agent_user_id"]    = $LoginInfo["agent_user_id"]; //使用者帳號
			$this->gdata["gda_cname"]        = $LoginInfo["cname"]; //使用者名稱
			$this->gdata["gda_lvl_id"]       = $LoginInfo["lvl_id"];
			$this->UserInfo["agent_user_id"] = $LoginInfo["agent_user_id"];
			$this->UserInfo["LoginID"]       = $LoginInfo["LoginID"];
        }
        $this->gdata['IsLogin'] = $this->IsLogin;
    }
    public function index(){
        $this->toview("index");
    }
    /**導向的頁面*/
    public function toview($Action, $Ajax = null, $data = null){
        $ShowHtml = "";
        switch ($Action) {
            case 'index':
				$this->gdata["inc_access"] = $this->WebInclude("inc/inc_access.html", $this->gdata, true);
				$this->gdata["footer"]     = $this->WebInclude("footer.html", $this->gdata, true);
				$this->toview("login");
				$ShowHtml                  = $this->WebInclude("index.html", $this->gdata, true);
                break;
            case 'login':
				$this->gdata["body"] = $this->WebInclude("login.html", $this->gdata, true);
                break;
            case 'logout':
				$this->gdata["inc_access"] = $this->WebInclude("inc/inc_access.html", $this->gdata, true);
				$this->gdata["body"]       = $this->WebInclude("login.html", $this->gdata, true);
				$this->gdata["footer"]     = $this->WebInclude("footer.html", $this->gdata, true);
				$page                      = "index.html";
				$ShowHtml                  = $this->WebInclude($page, $this->gdata, true);
            	break;
            case 'game_url_status':
            case 'openresult':
            case 'game_list':
            case 'game_url':
            case 'game_period':
            case 'game_history':
            case 'game_periods':
            case 'game_period_error':
            case 'openNumber':
            case 'openCheck':
            case 'game_hn_list':
                $page    = $Action . ".html";
				$this->gdata["gameGroup"] = $this->gameList('gameGroup');
				$ShowHtml                 = $this->WebInclude($page, $this->gdata, true);
                break;
            case 'game_openset':
                $page = $Action . ".html";
                $this->gdata["gameGroup"] = $this->gameList('gameGroup');
                $this->gdata['yearSel'] = $this->gameOpenset('yearExist')['year'];
                $this->gdata['nameSel'] = $this->gameOpenset('yearExist')['cname'];
				$ShowHtml = $this->WebInclude($page, $this->gdata, true);
                break;
            default:
                $page     = $Action . ".html";
                $ShowHtml = $this->WebInclude($page, $this->gdata, true);
                break;
        }

        switch ($Ajax) {
            case 'urlcode':
                $this->obj["code"] = 100;
                $this->obj["html"] = urlencode($ShowHtml);
                $this->output();
                break;
            default:
                echo $ShowHtml;
        }
    }
    /**彈跳視窗*/
    public function pop($Action = null, $type = null, $ID = null){
        $this->gdata["inc_pop_access"] = $this->WebInclude("inc/inc_pop_access.html", $this->gdata, true);
        switch ($Action) {
            case 'userList':
                $this->gdata["lvlGroup"] = $this->userLvl('lvlGroup');
                $this->gdata["data"]     = $this->userList('useredit', $ID);
                $this->gdata["type"]     = $type;
                $this->gdata["body"]     = $this->WebInclude("pop/pop_userList.html", $this->gdata, true);
                $ShowHtml                = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'userLvl':
            	switch ($type) {
            		case 'lvlFun':
            			$this->gdata["body"]     = $this->WebInclude("pop/pop_lvlFun.html", $this->gdata, true);
            			$this->gdata["data"]     = json_encode($this->userLvl('lvlFun', $ID));
            			break;
            		default:
		                $this->gdata["body"]     = $this->WebInclude("pop/pop_userLvl.html", $this->gdata, true);
		                $this->gdata["data"]     = $this->userLvl('userlvledit', $ID);
            			break;
            	}

            	$this->gdata["type"]     = $type;
            	$this->gdata["id"]		 = $ID;
                $ShowHtml = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'gameUrl':
                $this->gdata["gameGroup"] = $this->gamelist('gameGroup');
                $this->gdata["data"]      = $this->gameurl('gameurledit', $ID);
                $this->gdata["type"]      = $type;
                $this->gdata["body"]      = $this->WebInclude("pop/pop_gameUrlList.html", $this->gdata, true);
                $ShowHtml                 = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'gameList':
                $this->gdata["data"] = $this->gamelist('gamelistedit', $ID);
                $this->gdata["type"] = $type;
                $this->gdata["body"] = $this->WebInclude("pop/pop_gameList.html", $this->gdata, true);
                $ShowHtml            = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'Proxy':
                $this->gdata["data"] = $this->proxy('proxyedit', $ID);
                $this->gdata["type"] = $type;
                $this->gdata["body"] = $this->WebInclude("pop/pop_ProxyList.html", $this->gdata, true);
                $ShowHtml            = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'gameVac':
             	$this->gdata["gameGroup"] = $this->gamelist('gameGroup');
                $this->gdata["data"]      = $this->gameVac('gameVacList', $ID);
                $this->gdata["type"]      = $type;
                $this->gdata["body"]      = $this->WebInclude("pop/pop_gamevac.html", $this->gdata, true);
                $ShowHtml                 = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'gameOpenset':
                $this->gdata["gameGroup"] = $this->gamelist('gameGroup');
                $this->gdata["data"]      = $this->gameOpenset('gameOpensetList', $ID);
                $this->gdata["type"]      = $type;
                $this->gdata["body"]      = $this->WebInclude("pop/pop_gameOpenset.html", $this->gdata, true);
                $ShowHtml                 = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'funcList':
                $this->gdata["funGroup"] = $this->funclist('funGroup');
                $this->gdata["data"]     = $this->funclist('funclistedit', $ID);
                $this->gdata["type"]     = $type;
                $this->gdata["body"]     = $this->WebInclude("pop/pop_funcList.html", $this->gdata, true);
                $ShowHtml                = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'funcLvl':
                $this->gdata["lvlGroup"] = $this->userlvl('lvlGroup');
                $this->gdata["funGroup"] = json_encode($this->funclist('funGroup'));
                $this->gdata["data"]     = $this->funcLvl('funLvledit', $ID);
                $this->gdata["type"]     = $type;
                $this->gdata["body"]     = $this->WebInclude("pop/pop_funcLvl.html", $this->gdata, true);
                $ShowHtml                = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'hnUrl':
                $this->gdata["gameGroup"] = $this->gamelist('gameGroup');
                $this->gdata["data"]      = $this->gameurl('gameurledit', $ID);
                $this->gdata["type"]      = $type;
                $this->gdata["body"]      = $this->WebInclude("pop/pop_gameUrlList.html", $this->gdata, true);
                $ShowHtml                 = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
            case 'Highweightlist':
                $this->gdata['data'] = $this->Highweightlist('editInfo', $ID);
                $this->gdata["body"] = $this->WebInclude("pop/pop_highweightlist.html", $this->gdata, true);
                $ShowHtml            = $this->WebInclude("pop/index.html", $this->gdata, true);
                break;
        }
        echo $ShowHtml;
    }
    /**使用者登入*/
    public function tologin(){
        //需要接收哪些input data
        $IssetAry = array(
            "ipt_acc",
            "ipt_pwd",
            "lan"
        );
        //登入資訊
        $this->mod->add_by('tb_acc_login', array(
            "acc" => $_POST["ipt_acc"],
            "ip"   => $_SERVER['REMOTE_ADDR'],
            "agent"  => $_SERVER['HTTP_USER_AGENT']
        ));

        //檢查接收資料是否皆有輸入
        foreach ($IssetAry as $k => $v) {
            if (!isset($_POST[$v])) {
                $this->obj["code"]    = 109;
                $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                $this->output();
            } else {
                $$v = $_POST[$v];
            }
        }

        $acc = $_POST["ipt_acc"];
        $pwd = $_POST["ipt_pwd"];

        $sql  = "
            SELECT
                status,	-- 帳號狀態 0正常 1停止使用
                id,		-- 資料庫編碼
                cname,	-- 使用者名稱
                lvl_id	-- 階級 0超級管理員 1高級管理員 2管理員 3一般使用者
            FROM
                LT_user
            WHERE acc = ? AND pwd = ?
        ";
        $info = array_shift($this->mod->select($sql, array($acc, $this->lib_codes->aes_en($pwd))));

        if (empty($info)) { //找不到資料
            $this->obj["code"] = 101;
            $this->obj['msg']  = $this->delItem[101];
            $this->output();
        } else {
            /**權限列表 抓取使用者權限相對應階級名稱*/
            $sql = "
                SELECT
                    lvl_name,	-- 權限名稱
                    status      -- 權限狀態 0正常 1停止使用
                FROM
                    LT_user_level
                WHERE id = ?
            ";
            $lvl = array_shift($this->mod->select($sql, array($info["lvl_id"])));

            if (!empty($lvl)) {
                $lvl_name   = $lvl["lvl_name"]; //權限名稱
                $lvl_status = $lvl["status"]; //權限狀態 0正常 1停止使用
            }else{
            	$this->obj["code"] = 103;
                $this->obj['msg']  = $this->delItem[103];
                $this->output();
            }

            /************************************/
            $user_status = $info["status"];	//帳號狀態 0正常 1停止使用
            $id          = $info["id"];		//資料庫編碼
            $cname       = $info["cname"];	//使用者名稱
            $lvl_id      = $info["lvl_id"]; //階級 0超級管理員 1高級管理員 2管理員 3一般使用者

            if ($user_status == 0) {
                //帳號狀態 0正常 1停止使用
                if ($lvl_status == 0) {
                    //權限狀態 0正常 1停止使用
                    $NowTime   = strtotime(date("YmdHis"));
                    $cokie     = array(
						"LoginID"       => $id, 	//資料庫編碼
						"agent_user_id" => $acc, 	//登入者帳號
						"cname"         => $cname, 	//登入者帳號
						"lvl_id"        => $lvl_id, //階級 0超級管理員 1高級管理員 2管理員 3一般使用者
						"lvl_name"      => $lvl_name//登入者權限名稱
                    );
                    $LoginInfo = $this->lib_codes->aes_en(json_encode($cokie));
                    $this->lib_sess->SetCookie("log_ag_code", $LoginInfo);

                    $this->gdata["gda_cname"]      = $cname;	//使用者帳號
                    $this->gdata["gda_lvl_name"]   = $lvl_name; //使用者名稱

                    $this->gdata["cname"]      = $cname;	//使用者帳號
                    $this->gdata["LoginID"]    = $id;		//使用者帳號
                    $this->gdata["lvl_name"]   = $lvl_name; //使用者名稱
                    $this->gdata["gda_lvl_id"] = $lvl_id; 	//使用者階級

                    $this->toview('home', 'urlcode');
                } else {
                    $this->obj["code"] = 102;
                    $this->obj['msg']  = $this->delItem[102];
                    $this->output();
                }
            } else {
                $this->obj["code"] = 103;
                $this->obj['msg']  = $this->delItem[103];
                $this->output();
            }
        }
    }
    /**使用者登出*/
    public function tologout(){
    	$this->IsLogin = 'false';
    	$this->gdata['IsLogin'] = $this->IsLogin;
        $this->lib_sess->destory(); //clear cookie
        $this->toview('logout', 'urlcode');
    }
    /**使用者 SQL*/
    public function userList($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                    SELECT
                        acc,	-- 帳號
                        cname,	-- 名稱
                        status,	-- 狀態
                        remark,	-- 備註
                        lvl_id 	-- 階級ID
                    FROM
                        LT_user
                    where
                ";
                $where = array();

                if (isset($_POST["status"]) && !empty($_POST["status"])) {
					$sql .= " status = ?";
		            $where[] = $_POST["status"];
		        }else{
		        	$sql .= " status != ?";
		            $where[] = 2;
		        }

		        if (isset($_POST["cname"]) && !empty($_POST["cname"])) {
					$sql .= " AND cname = ?";
		            $where[] = $_POST["cname"];
		        }

		        if (isset($_POST["lvl"]) && !empty($_POST["lvl"])) {
					$sql .= " AND lvl_id = ?";
		            $where[] = $_POST["lvl"];
		        }

		        $info = $this->mod->select($sql,$where);

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        /**權限列表 抓取使用者權限相對應階級名稱*/
                        $sql = "SELECT lvl_name FROM LT_user_level WHERE id = ? ";
                        $lvl = array_shift($this->mod->select($sql, array($value["lvl_id"])));

                        if (empty($lvl))
                            $info[$key]["lvl_name"] = "null";
                        else
                            $info[$key]["lvl_name"] = $lvl["lvl_name"]; //權限名稱
                        /************************************/

                        $info[$key]["status"] = $this->basePublic('statusbtn', 'userList',array("id"=>$value["status"]));

		                $info[$key]["act"]    = $this->basePublic('toolbtn', 'userList',array("id" =>$value["acc"],"menuID" =>$_POST["menuID"]))["editBtn"];

                    }

                    $this->obj["code"] = 100;
                    $this->obj["data"] = $info;
                    $this->obj["addBtn"] = $this->basePublic('toolbtn', 'userList',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }

                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "lvl_id",
                    "status",
                    "cname",
                    "acc",
                    "pwd"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $lvl_id = $_POST["lvl_id"];
                $status = $_POST["status"];
                $cname  = $_POST["cname"];
                $acc    = $_POST["acc"];
                $pwd    = $_POST["pwd"];

                /*******驗證資料是否存在於DB 帳號重複********/
                $sql  = "SELECT id FROM LT_user WHERE acc = ?";
                $info = $this->mod->select($sql, array(
                    $acc
                ));
                /******************************************/

                if (!empty($info)) { //不等於空 代表帳號已存在
                    $this->obj["code"] = 108;
                    $this->obj['msg']  = $this->delItem[108];
                } else {
                    $this->mod->add_by('LT_user', array(
						"lvl_id"  => $lvl_id,
						"acc"     => $acc,
						"pwd"     => $this->lib_codes->aes_en($pwd),
						"cname"   => $cname,
						"status"  => $status,
						"nowtime" => $nowtime
                    ));
                    /*********紀錄存到資料庫********/
                    $editdata = array(
                        "vals" => $this->mod->last_qstr,
                        "tb_id" => 0000,
                        "type" => 0
                    );
                    $this->basePublic('editrecord', 'LT_user', $editdata);
                    /*****************************/

                    $this->obj["code"] = 100;
                    $this->obj['msg']  = $this->delItem[100];
                }
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "lvl_id",
                    "status",
                    "cname",
                    "acc"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

				$lvl_id = $_POST["lvl_id"];
				$status = $_POST["status"];
				$cname  = $_POST["cname"];
				$acc    = $_POST["acc"];
				$remark = $_POST["remark"];

                /*********修改資料********/
               	$this->mod->modi_by('LT_user', array(
                    'acc' => $acc
                ), array(
					"lvl_id"  => $lvl_id,
					"status"  => $status,
					"cname"   => $cname,
					"remark"  => $remark,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $acc,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_user', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_user', array(
                    'acc' => $id
                ), array(
					"status"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_user', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'useredit':
                $sql  = "SELECT id,cname,acc,pwd,remark,lvl_id,status FROM LT_user WHERE acc = ? ";
                $info = $this->mod->select($sql, array(
                    $ID
                ));

                if (empty($info)) {
                    $info[0] = array(
						"acc"    => '',
						"cname"  => '',
						"pwd"    => '',
						"remark" => '',
						"lvl_id" => '',
						"status" => ''
                    );
                }
                return $info;
                break;
            case 'pwedit':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "acc",
                    "LoginID",
                    "pwd",
                    "pwdNew",
                    "pwdNeww"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $acc    = $this->gdata["agent_user_id"];
                $id     = $_POST["LoginID"];
                $pwd    = $_POST["pwd"];
                $pwdNew = $_POST["pwdNew"];

                //驗證資料是否存在於DB 以及檢查密碼是否輸入正確
                $sql  = "SELECT id FROM LT_user WHERE id = ? AND acc = ? AND pwd =?";
                $info = $this->mod->select($sql, array(
                    $id,
                    $acc,
                    $this->lib_codes->aes_en($pwd)
                ));
                /******************************************/
                if (empty($info)) {
                    $this->obj["code"] = 105;
                    $this->obj['msg']  = $this->delItem[105];
                } else {
                    /*********修改資料********/
                    $this->mod->modi_by('LT_user', array(
						'id'  => $id,
						'acc' => $acc
                    ), array(
						"pwd"     => $this->lib_codes->aes_en($pwdNew),
						"nowtime" => $nowtime
                    ));

                    /*********紀錄存到資料庫********/
                    $editdata = array(
						"vals"  => $this->mod->last_qstr,
						"tb_id" => $id,
						"type"  => 1
                    );
                    $this->basePublic('editrecord', 'LT_user', $editdata);
                    /*****************************/

                    $this->obj["code"] = 100;
                    $this->obj['msg']  = $this->delItem[100];
                }
                $this->output();
                break;
        }
    }
    /**使用者階級lvl*/
    public function userLvl($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'lvlGroup':
                /**使用者權限 群組*/
                $sql  = "SELECT id,lvl_name FROM LT_user_level WHERE status = ?";
                $info = $this->mod->select($sql, array(0));

                $lvlnam = array();
                foreach ($info as $key => $value) {
                    $data         = array();
                    $data["id"]   = $value["id"];
                    $data["name"] = $value["lvl_name"];
                    array_push($lvlnam, $data);
                }
                return $lvlnam;
                break;
            case 'list':
                $sql  = "
                    SELECT
                        id,			-- ID
                        lvl_name,	-- 名稱
                        status,		-- 狀態
                        up_id       -- 階級
                    FROM
                        LT_user_level
                    WHERE
                ";
                $where = array();

                if (isset($_POST["status"]) && !empty($_POST["status"])) {
					$sql .= " status = ?";
		            $where[] = $_POST["status"];
		        }else{
		        	$sql .= " status != ?";
		            $where[] = 2;
		        }

		        if (isset($_POST["lvl"]) && !empty($_POST["lvl"])) {
					$sql .= " AND up_id = ?";
		            $where[] = $_POST["lvl"];
		        }else{
		        	$sql .= " AND up_id = ?";
		            $where[] = 0;
		        }

		        $info = $this->mod->select($sql,$where);

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                	$sql1 = "SELECT id FROM LT_user WHERE lvl_id = ?";

                	/*階級第零階*/
                    foreach ($info as $k => $v) {
                    	$data = $this->mod->select($sql1, array($v["id"]));
						$info[$k]["lvlcount"] = count($data);
						$info[$k]["status"]   = $this->basePublic('statusbtn', 'userLvl',array("id"=>$v["status"]));

						$info[$k]["act"] = $this->basePublic('toolbtn', 'userLvl',array("id"=>$v['id'],"menuID" =>$_POST["menuID"]))["editBtn"].$this->basePublic('toolbtn', 'userLvl',array("id"=>$v['id'],"menuID" =>$_POST["menuID"]))["lvlBtn"];

                    	/*階級第一階*/
                    	$info1 = $this->mod->select($sql, array(2,$v["id"]));

                    	$info[$k]["level"] = $info1;
                    	foreach ($info1 as $k1 => $v1) {
                    		$data = $this->mod->select($sql1, array($v1["id"]));
                    		$info[$k]["level"][$k1]["lvlcount"] = count($data);
							$info[$k]["level"][$k1]["status"]   = $this->basePublic('statusbtn', 'userLvl',array("id"=>$v["status"]));
							$info[$k]["level"][$k1]["act"]      = $this->basePublic('toolbtn', 'userLvl',array("id"=>$v1['id'],"menuID" =>$_POST["menuID"]))["editBtn"];

                    		/*階級第二階*/
	                    	$info2 = $this->mod->select($sql, array(2,$v1["id"]));

	                    	$info[$k]["level"][$k1]["level"] = $info2;
	                    	foreach ($info2 as $k2 => $v2) {
	                    		$data = $this->mod->select($sql1, array($v2["id"]));
	                    		$info[$k]["level"][$k1]["level"][$k2]["lvlcount"] = count($data);
								$info[$k]["level"][$k1]["level"][$k2]["status"]   = $this->basePublic('statusbtn', 'userLvl',array("id"=>$v2["status"]));
								$info[$k]["level"][$k1]["level"][$k2]["act"]      = $this->basePublic('toolbtn', 'userLvl',array("id"=>$v2['id'],"menuID" =>$_POST["menuID"]))["editBtn"];
	                    	}
                    	}
                    }

					$this->obj["code"]     = 100;
					$this->obj["data"]     = $info;
					$this->obj["lvlGroup"] = $this->userlvl('lvlGroup');
					$this->obj["addBtn"]   = $this->basePublic('toolbtn', 'userLvl',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "lvl_name",
                    "status"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

				$id       = $_POST["id"];
				$lvl_name = $_POST["lvl_name"];
				$status   = $_POST["status"];
                /*********修改資料********/
                $this->mod->modi_by('LT_user_level', array(
                    'id' => $id
                ), array(
					"lvl_name" => $lvl_name,
					"status"   => $status,
					"nowtime"  => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_user_level', $editdata);

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array("lvl_name");

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }

                $lvl_name = $_POST["lvl_name"];
                $up_id 	  = $_POST["id"];

                //驗證資料是否存在於DB ID重複
                $sql  = "SELECT id FROM LT_user_level WHERE lvl_name = ?";
                $info = $this->mod->select($sql, array(
                    $lvl_name
                ));
                /******************************************/

                if (!empty($info)) { //級別ID 已存在
                    $this->obj["code"] = 108;
                    $this->obj['msg']  = $this->delItem[108];
                } else {
                    $this->mod->add_by('LT_user_level', array(
						"up_id"    => $up_id,
						"lvl_name" => $lvl_name,
						"status"   => 0,
						"nowtime"  => $nowtime
                    ));
                    /*********紀錄存到資料庫********/
                    $editdata = array(
                        "vals" => $this->mod->last_qstr,
                        "tb_id" => 0000,
                        "type" => 0
                    );
                    $this->basePublic('editrecord', 'LT_user_level', $editdata);
                    /*****************************/

                    $this->obj["code"] = 100;
                    $this->obj['msg']  = $this->delItem[100];
                }
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /**修改狀態*/
                $this->mod->modi_by('LT_user_level', array(
                    'id' => $id
                ), array(
					'status'  => 2,
					'nowtime' => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_user_level', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'userlvledit':
                $sql  = "SELECT id,lvl_name,status FROM LT_user_level WHERE id = ? ";
                $info = $this->mod->select($sql, array(
                    $ID
                ));

                if (empty($info)) {
                    $info[0] = array(
						"lvl_name" => '',
						"status"   => ''
                    );
                }
                return $info;
                break;
        	case 'lvlFun':
        		$sql  = "
		            SELECT
						lt_game_catch.LT_permissions.up_id,
						lt_game_catch.LT_permissions.name,
						lt_game_catch.LT_permission_lvl.id,
						lt_game_catch.LT_permission_lvl.addcol,
						lt_game_catch.LT_permission_lvl.editcol,
						lt_game_catch.LT_permission_lvl.delcol,
						lt_game_catch.LT_permission_lvl.look
		            FROM
		                lt_game_catch.LT_permissions
		            JOIN
		            	lt_game_catch.LT_permission_lvl
		            ON
		            	lt_game_catch.LT_permissions.id = lt_game_catch.LT_permission_lvl.perm_id
		            WHERE
		            	lt_game_catch.LT_permissions.enable = ? AND
		            	lt_game_catch.LT_permission_lvl.lvl_id = ?
		        ";
				$info  = $this->mod->select($sql, array(0,$ID));
				return $info;
        }
    }
    /**遊戲*/
    public function gameList($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'gameGroup':
                /**************遊戲列表**************/
                $sql     = "SELECT id,cname FROM LT_game WHERE enable = ?";
                $LT_game = $this->mod->select($sql, array(
                    0
                ));

                $gameGroup = array();
                foreach ($LT_game as $key => $value) {
                    $gameGroup[$LT_game[$key]["id"]]["id"]   = $LT_game[$key]["id"];
                    $gameGroup[$LT_game[$key]["id"]]["name"] = $LT_game[$key]["cname"];
                }
                /************************************/
                return $gameGroup;
                break;
            case 'list':
                $sql = "SELECT * FROM LT_game WHERE";
               	$where = array();

                if (isset($_POST["enable"]) && (!empty($_POST["enable"]) || $_POST["enable"] === '0')) {
					$sql .= " enable = ?";
		            $where[] = $_POST["enable"];
		        }else{
		        	$sql .= " enable != ?";
		            $where[] = 2;
		        }

		        if (isset($_POST["gameID"]) && !empty($_POST["gameID"])) {
					$sql .= " AND id = ?";
		            $where[] = $_POST["gameID"];
		        }

		        $info = $this->mod->select($sql,$where);

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
						$info[$key]["repeat"] = $this->basePublic('statusbtn', 'gameList',array("id"=>$value["repeat"]));
						$info[$key]["notice"] = $this->basePublic('statusbtn', 'gameList',array("id"=>$value["notice"]));
						$info[$key]["enable"] = $this->basePublic('statusbtn', 'gameList',array("id"=>$value["enable"]));
						$info[$key]["act"]    = $this->basePublic('toolbtn', 'gameList',array("id" =>$value["id"],"menuID" =>$_POST["menuID"]))["editBtn"];
                    }

					$this->obj["code"]   = 100;
					$this->obj["data"]   = $info;
					$this->obj["addBtn"] = $this->basePublic('toolbtn', 'gameList',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }
                   
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "cname",
                    "ename",
                    "repeat",
                    "enable",
                    "notice",
                    "cycle",
                    "param",
                    "param_1",
                    "period_format",
                    "period_num",
                    "lottery_num",
                    "urlCheck",
                    "min_number",
                    "max_number"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $cname         = $_POST["cname"];
                $ename         = $_POST["ename"];
                $enable        = $_POST["enable"];
                $repeat        = $_POST["repeat"];
                $notice        = $_POST["notice"];
                $cycle         = $_POST["cycle"];
                $param         = $_POST["param"];
                $param_1       = $_POST["param_1"];
                $period_format = $_POST["period_format"];
                $period_num    = $_POST["period_num"];
                $lottery_num   = $_POST["lottery_num"];
                $urlCheck    = $_POST["urlCheck"];
                $min_number    = $_POST["min_number"];
                $max_number    = $_POST["max_number"];

                $this->mod->add_by('LT_game', array(
                    "cname" => $cname,
                    "ename" => $ename,
                    "enable" => $enable,
                    "repeat" => $repeat,
                    "notice" => $notice,
                    "cycle" => $cycle,
                    "param" => $param,
                    "param_1" => $param_1,
                    "period_format" => $period_format,
                    "period_num" => $period_num,
                    "lottery_num" => $lottery_num,
                    "urlCheck" => $urlCheck,
                    "min_number" => $min_number,
                    "max_number" => $max_number,
                    "nowtime" => $nowtime
                ));
                /*********紀錄存到資料庫********/
                $editdata = array(
                    "vals" => $this->mod->last_qstr,
                    "tb_id" => 0000,
                    "type" => 0
                );
                $this->basePublic('editrecord', 'LT_game', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "cname",
                    "ename",
                    "notice",
                    "enable",
                    "repeat",
                    "cycle",
                    "param",
                    "param_1",
                    "period_format",
                    "period_num",
                    "lottery_num",
                    "urlCheck",
                    "min_number",
                    "noticeTime",
                    "max_number"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id            = $_POST["id"];
                $cname         = $_POST["cname"];
                $ename         = $_POST["ename"];
                $enable        = $_POST["enable"];
                $repeat        = $_POST["repeat"];
                $notice        = $_POST["notice"];
                $cycle         = $_POST["cycle"];
                $param         = $_POST["param"];
                $param_1       = $_POST["param_1"];
                $period_format = $_POST["period_format"];
                $period_num    = $_POST["period_num"];
                $lottery_num   = $_POST["lottery_num"];
                $urlCheck       = $_POST["urlCheck"];
                $noticeTime     = $_POST["noticeTime"];
                $min_number    = $_POST["min_number"];
                $max_number    = $_POST["max_number"];

                /*********修改資料********/
                $this->mod->modi_by('LT_game', array(
                    'id' => $id
                ), array(
					"cname"         => $cname,
					"ename"         => $ename,
                    "enable"        => $enable,
                    "repeat"        => $repeat,
					"notice"        => $notice,
					"cycle"         => $cycle,
					"param"         => $param,
					"param_1"       => $param_1,
					"period_format" => $period_format,
					"period_num"    => $period_num,
					"lottery_num"   => $lottery_num,
                    "urlCheck"      => $urlCheck,
                    "noticeTime"    => $noticeTime,
					"min_number"    => $min_number,
					"max_number"    => $max_number,
					"nowtime"       => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_game', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /**修改狀態*/
                $this->mod->modi_by('LT_game', array(
                    'id' => $id
                ), array(
					'enable'  => 2,
					'nowtime' => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_game', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'gamelistedit':
                $sql  = "
                SELECT
                    id,cname,ename,
                    cycle,param,param_1,
                    period_format,period_num,
                    lottery_num,min_number,max_number,urlCheck,enable,`repeat`,notice,noticeTime
                FROM LT_game WHERE id = ? ";
                $info = $this->mod->select($sql, array($ID));
                if (empty($info)) {
                    $info[0] = array(
						"cname"         => '',
						"ename"         => '',
						"cycle"         => '',
						"param"         => '',
						"param_1"       => '',
						"period_format" => '',
						"period_num"    => '',
						"lottery_num"   => '',
						"min_number"    => '',
                        "max_number"    => '',
						"urlCheck"      => '',
                        "repeat"        => '',
                        "noticeTime"    => '',
                        "notice"        => '',
						"enable"        => ''
                    );
                }
                return $info;
                break;
        }
    }
    /**期數*/
    public function gamePeriod($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                    SELECT
                        game_id,    -- 編號
                        id,            -- 遊戲名稱
                        cycle,        -- 遊戲週期
                        Periods,    -- 期數編碼
                        PeriodsTime    -- 期數應開獎時間
                    FROM
                        tb_game_periods
                    WHERE
                ";

                if (isset($_POST["gameID"]) && !empty($_POST["gameID"])) {
					$sql .= " game_id = ?";
		            $where[] = $_POST["gameID"];
		        }else{
		        	$sql .= " game_id = ?";
		            $where[] = 1;
		        }

		        $info = $this->mod->select($sql,$where);


                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $info[$key]["game_id"] = $this->basePublic('catchCname', 'gameVac', array("id"=>$value["game_id"])); //遊戲中文名稱
                    }

                    $this->obj["code"] = 100;
                    $this->obj["data"] = $info;
                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "game_id",
                    "cycle",
                    "period_num",
                    "period_time"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $game_id     = $_POST["game_id"];
                $cycle       = $_POST["cycle"];
                $period_num  = $_POST["period_num"];
                $period_time = $_POST["period_time"];

                $this->mod->add_by('LT_period', array(
					"game_id"     => $game_id,
					"cycle"       => $cycle,
					"period_num"  => $period_num,
					"period_time" => $period_time,
					"nowtime"     => $nowtime
                ));
                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_period', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "game_id",
                    "cycle",
                    "period_num",
                    "period_time"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id          = $_POST["id"];
                $game_id     = $_POST["game_id"];
                $cycle       = $_POST["cycle"];
                $period_num  = $_POST["period_num"];
                $period_time = $_POST["period_time"];

                /**修改資料*/
                $this->mod->modi_by('LT_period', array(
                    'id' => $id
                ), array(
					"game_id"     => $game_id,
					"cycle"       => $cycle,
					"period_num"  => $period_num,
					"period_time" => $period_time,
					"nowtime"     => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_period', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
                // case 'gamePeriodList':
                //     $sql = "SELECT id,cname,ename,cycle,param,param_1,period_format,period_num,lottery_num,min_number,max_number FROM LT_game WHERE id = ? ";
                //     $info = $this->mod->select($sql, array($ID));

                //     if(empty($info)){
                //         $info[0]=array(
                //             "cname"         =>'',
                //             "ename"         =>'',
                //             "cycle"         =>'',
                //             "param"         =>'',
                //             "param_1"       =>'',
                //             "period_format" =>'',
                //             "period_num"    =>'',
                //             "lottery_num"   =>'',
                //             "min_number"    =>'',
                //             "max_number"    =>''
                //             );
                //     }
                //     return $info;
                //     break;
        }
    }
    /**抓獎紀錄(全)*/
    public function gameHistory($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                    SELECT
                        id,             -- 編號
                        game_id,         -- 遊戲名稱
                        lottery_id,     -- 期數
                        period_str,        -- 期數編碼
                        lottery,         -- 開獎號碼
                        lottery_time,     -- 開獎時間
                        url_id             -- 來源Url
                    FROM
                        LT_history
                    WHERE
                ";

                if (isset($_POST["gameID"]) && !empty($_POST["gameID"])) {
					$sql .= " game_id = ?";
		            $where[] = $_POST["gameID"];
		        }else{
		        	$sql .= " game_id = ?";
		            $where[] = 1;
		        }

                if (isset($_POST["period"]) && !empty($_POST["period"])) {
					$sql .= " AND period_str = ?";
		            $where[] = trim($_POST["period"]);
		        }

                if (isset($_POST["date"]) && !empty($_POST["date"])) {
					$sql .= " AND lottery_time between ? AND ?";
		            $where[] = $_POST["date"]." 00:00:00";
		            $where[] = $_POST["date"]." 23:59:59";
		        }else{
		        	$sql .= " AND lottery_time between ? AND ?";
		            $where[] = date('Y-m-d 00:00:00');
		            $where[] = date('Y-m-d 23:59:59');
		        }

		        $info = $this->mod->select($sql,$where);

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $k => $v) {
                    	//遊戲中文名稱
                        $info[$k]["name"] = $this->basePublic('catchCname', 'gameHistory',array("id"=>$v["game_id"]));
                        //遊戲英文名稱
                        $info[$k]["ename"] = $this->basePublic('catchEname', 'gameHistory',array("id"=>$v["game_id"]));

                        $sql = "SELECT url_name FROM LT_url WHERE id = ?";
                        $data = array_shift($this->mod->select($sql, array($v["url_id"])));
                        $info[$k]["url_id"] = $data["url_name"];
                    }

                    $this->obj["code"]      = 100;
                    $this->obj["data"]      = $info;
                    $this->obj["gameGroup"] = $this->gamelist('gameGroup');
                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "game_id",
                    "cycle",
                    "period_num",
                    "period_time"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $game_id     = $_POST["game_id"];
                $cycle       = $_POST["cycle"];
                $period_num  = $_POST["period_num"];
                $period_time = $_POST["period_time"];

               	$this->mod->add_by('LT_period', array(
					"game_id"     => $game_id,
					"cycle"       => $cycle,
					"period_num"  => $period_num,
					"period_time" => $period_time,
					"nowtime"     => $nowtime
                ));
                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_period', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "game_id",
                    "cycle",
                    "period_num",
                    "period_time"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id          = $_POST["id"];
                $game_id     = $_POST["game_id"];
                $cycle       = $_POST["cycle"];
                $period_num  = $_POST["period_num"];
                $period_time = $_POST["period_time"];

                /*********修改資料********/
                $this->mod->modi_by('LT_period', array(
                    'id' => $id
                ), array(
					"game_id"     => $game_id,
					"cycle"       => $cycle,
					"period_num"  => $period_num,
					"period_time" => $period_time,
					"nowtime"     => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_period', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'error':
                $sql  = "SELECT lottery_id,game_id,nowtime FROM LT_period_error";

                $where = [];
                if (isset($_POST["gameID"]) && !empty($_POST["gameID"])) {
					$sql .= " WHERE game_id = ?";
		            $where[] = $_POST["gameID"];
		        }

                $info = $this->mod->select($sql,$where);
                
                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $k => $v) {
                    	$sql  = "SELECT lottery,period_str FROM LT_periods WHERE id = ?";
						$data = array_shift($this->mod->select($sql,array($v["lottery_id"])));

						$sql  = "
                            SELECT a.id, a.lottery, a.lottery_time, a.url_id, a.period_str, b.url_name
                            FROM LT_history as a
                            LEFT JOIN LT_url as b ON a.url_id = b.id
                            WHERE a.period_str = ? AND a.game_id = ?
                            LIMIT 0,15
                        ";
                        $data2 = $this->mod->select($sql,array($data["period_str"],$v["game_id"]));

                        //遊戲中文名稱
                        $info[$k]["cname"]      = $this->basePublic('catchCname', 'gamePeriod', array("id"=>$v["game_id"]));
						$info[$k]["ename"]      = $this->basePublic('catchEname', 'gamePeriod', array("id"=>$v["game_id"]));
						$info[$k]["history"]    = $data2;
						$info[$k]["period_str"] = $data["period_str"];
						$info[$k]["lottery"]    = $data["lottery"];
                    }
                    $this->obj["code"]      = 100;
                    $this->obj["data"]      = $info;
                }

                $this->output();
                break;
        }
    }
    /**抓獎紀錄(高權重)*/
    public function Highweightlist($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                SELECT
                	id,
                    game_id,                    -- 遊戲名稱
                    lottery,                    -- 開獎號碼
                    period_date,                -- 開獎日期
                    period_str,                    -- 開獎期數
                    lottery_time,                -- 實際開獎時間
                    be_lottery_time,            -- 預計開獎時間
                    lottery_status                -- 開獎狀態
                FROM
                    LT_periods
                WHERE
                    game_id = ? AND
                    period_date = ? AND
                    be_lottery_time <= ?
                ";
                $info = $this->mod->select($sql, array(
                    1,
                    date("Y-m-d"),
                    date("Y-m-d H:i:s")
                ));

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $info[$key]["name"]        = $this->basePublic('catchCname', 'Highweightlist', array("id"=>$value["game_id"])); //遊戲中文名稱
                        $info[$key]["lottery_status"]    = $this->basePublic('toolbtn', 'Highweightlist', array("id" =>$value["id"], "menuID" => $info[$key]["id"]))["editBtn"];
                    }

                    $this->obj["code"] = 100;
                    $this->obj["data"] = $info;
                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "game_id",
                    "cycle",
                    "period_num",
                    "period_time"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $game_id     = $_POST["game_id"];
                $cycle       = $_POST["cycle"];
                $period_num  = $_POST["period_num"];
                $period_time = $_POST["period_time"];

                $this->mod->add_by('LT_period', array(
					"game_id"     => $game_id,
					"cycle"       => $cycle,
					"period_num"  => $period_num,
					"period_time" => $period_time,
					"nowtime"     => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_period', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array("id","game_id","lottery","period_str","period_date");
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

				$id          = $_POST["id"];
				$game_id     = $_POST["game_id"];
				$lottery     = $_POST["lottery"];
				$period_str  = $_POST["period_str"];
				$period_date = $_POST["period_date"];

                /*********修改資料********/
                $this->mod->modi_by('LT_periods', array(
					'id'          => $id,
					'game_id'     => $game_id,
					'period_str'  => $period_str,
					'period_date' => $period_date,
                ), array(
					'lottery'        => trim($lottery),
					'lottery_time'   => $nowtime,
					'lottery_status' => 1,
					'nowtime'        => $nowtime,
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_period', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'editInfo':
                $sql  = "SELECT id,game_id,lottery,period_date,period_str FROM LT_periods WHERE id = ?";
                $info = $this->mod->select($sql, array($ID));
                $info[0]["name"]        = $this->basePublic('catchCname', 'Highweightlist', array("id"=>$info[0]["game_id"])); //遊戲中文名稱
                if (count(explode("_",$info[0]['period_str'])) > 1) {
                    $hnid = explode("_",$info[0]['period_str'])[1];
                    $info[0]["city"] = array_shift($this->mod->select('SELECT area FROM hn_city WHERE id = "'.$hnid.'"'))['area'];
                }

                return $info;
                break;
            case 'edit':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "period_date",
                    "period_str",
                    "tableId",
                    "lottery"
                );
                /************************************/
                
                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                if (json_decode($lottery, true) != '') {
                    $lottery = json_decode($lottery, true);
                    krsort($lottery);
                    $lottery = json_encode($lottery, true);
                }
                
                /*********修改資料********/
               
                $this->mod->modi_by('LT_periods', array(
                    "id"            => $tableId,
                    "period_date"   => $period_date,
					"period_str"    => $period_str,
                ), array(
                    "lottery"       => $lottery
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $tableId,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_periods', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                print_r($_POST);
                break;
        }
    }
    /**URL*/
    public function gameUrl($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                    SELECT
                        id,            -- 編號
                        game_id,    -- 遊戲
                        api_name,    -- api
                        url_name,    -- 名稱
                        url,        -- 網址
                        enable,        -- 狀態
                        code_order,    -- 權重
                        last_period,-- 最後更新期數
                        last_status,-- 最後更新狀態
                        last_time,    -- 最後更新日期
                        last_cost,    -- 資料收集時間
                        last_proxy,    -- 最後更新之proxy
                        nowtime,        -- 最後更新時間
                        proxy_enable    -- Proxy 啟用狀態
                    FROM
                        LT_url
                    WHERE
                ";
                $where = array();

                if (isset($_POST["enable"]) && (!empty($_POST["enable"]) || $_POST["enable"] === '0')) {
					$sql .= " enable = ?";
		            $where[] = $_POST["enable"];
		        }else{
		        	$sql .= " enable != ?";
		            $where[] = 2;
		        }

		        if (isset($_POST["gameID"]) && !empty($_POST["gameID"])) {
					$sql .= " AND game_id = ?";
		            $where[] = $_POST["gameID"];
		        }

		        $info = $this->mod->select($sql, $where);

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $info[$key]["game_id"] = $this->basePublic('catchCname', 'gameVac',array("id"=>$value["game_id"])); //遊戲中文名稱
                        $info[$key]["enable"]  = $this->basePublic('statusbtn', 'gameUrl',array("id"=>$value["enable"]));
                        $info[$key]["proxy_enable"]  = $this->basePublic('statusbtn', 'gameUrl',array("id"=>$value["proxy_enable"]));
                        $info[$key]["act"]     = $this->basePublic('toolbtn', 'gameUrl',array("id" =>$value['id'],"menuID" =>$_POST["menuID"]))["editBtn"];
                    }

                    $this->obj["code"] = 100;
                    $this->obj["data"] = $info;
                    $this->obj["addBtn"] = $this->basePublic('toolbtn', 'gameUrl',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "game_id",
                    "enable",
                    "code_order",
                    "url_name",
                    "api_name",
                    "url",
                    "last_period",
                    "last_status",
                    "last_time",
                    "last_cost",
                    "last_proxy",
                    "proxy_enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $game_id     = $_POST["game_id"];
                $enable      = $_POST["enable"];
                $code_order  = $_POST["code_order"];
                $url_name    = $_POST["url_name"];
                $api_name    = $_POST["api_name"];
                $url         = $_POST["url"];
                $last_period = $_POST["last_period"];
                $last_status = $_POST["last_status"];
                $last_time   = $_POST["last_time"];
                $last_cost   = $_POST["last_cost"];
                $last_proxy  = $_POST["last_proxy"];
                $proxy_enable = $_POST["proxy_enable"];

                $this->mod->add_by('LT_url', array(
					"game_id"     => $game_id,
					"enable"      => $enable,
					"code_order"  => $code_order,
					"url_name"    => $url_name,
					"api_name"    => $api_name,
					"url"         => $url,
					"last_period" => $last_period,
					"last_status" => $last_status,
					"last_time"   => $last_time,
					"last_cost"   => $last_cost,
					"last_proxy"  => $last_proxy,
					"nowtime"     => $nowtime,
                    "proxy_enable" => $proxy_enable
                ));
                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_url', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "game_id",
                    "enable",
                    "code_order",
                    "url_name",
                    "api_name",
                    "url",
                    "last_period",
                    "last_status",
                    "last_time",
                    "last_cost",
                    "last_proxy",
                    "proxy_enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                // foreach($IssetAry as $k => $v){
                //     if(!isset($_POST[$v])){
                //         $this->obj["code"] = 109;
                //         $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                //         $this->output();
                //     }else{
                //         $$v = $_POST[$v];
                //     }
                // }
                /************************************/

                $id          = $_POST["id"];
                $game_id     = $_POST["game_id"];
                $enable      = $_POST["enable"];
                $code_order  = $_POST["code_order"];
                $url_name    = $_POST["url_name"];
                $api_name    = $_POST["api_name"];
                $url         = $_POST["url"];
                $last_period = $_POST["last_period"];
                $last_status = $_POST["last_status"];
                $last_time   = $_POST["last_time"];
                $last_cost   = $_POST["last_cost"];
                $last_proxy  = $_POST["last_proxy"];
                $proxy_enable = $_POST["proxy_enable"];

                /*********修改資料********/
                $this->mod->modi_by('LT_url', array(
                    'id' => $id
                ), array(
					"game_id"     => $game_id,
					"enable"      => $enable,
					"code_order"  => $code_order,
					"url_name"    => $url_name,
					"api_name"    => $api_name,
					"url"         => $url,
					"last_period" => $last_period,
					"last_status" => $last_status,
					"last_time"   => $last_time,
					"last_cost"   => $last_cost,
					"last_proxy"  => $last_proxy,
					"nowtime"     => $nowtime,
                    "proxy_enable" => $proxy_enable
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_url', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_url', array(
                    'id' => $id
                ), array(
					"enable"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_url', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'gameurledit':
                $sql  = "SELECT
                            id,
                            url_name,
                            api_name,
                            url,
                            last_period,
                            last_status,
                            last_time,
                            last_cost,
                            last_proxy,
                            enable,
                            game_id,
							code_order,
                            proxy_enable
                        FROM LT_url WHERE id = ? ";
                $info = $this->mod->select($sql, array($ID));

                if (empty($info)) {
                    $info[0] = array(
						"url_name"    => '',
						"api_name"    => '',
						"api"         => '',
						"url"         => '',
						"last_period" => '',
						"last_status" => '',
						"last_time"   => '',
						"last_cost"   => '',
						"last_proxy"  => '',
						"game_id"	  => '',
						"enable"      => '',
						"code_order"  => '',
                        "proxy_enable" => ''
                    );
                }
                return $info;
                break;
        }
    }
    /**PROXY*/
    public function Proxy($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                    SELECT
                        id,         -- 編號
                        enable,     -- 狀態
                        IP,         -- IP
                        port,         -- PORT
                        proxy_acc,     -- 帳號
                        proxy_pwd,     -- 密碼
                        nowtime     -- 最後更新時間
                    FROM
                        LT_proxy
                    WHERE
                ";
                $where = array();

                if (isset($_POST["enable"]) && (!empty($_POST["enable"]) || $_POST["enable"] === '0')) {
					$sql .= " enable = ?";
		            $where[] = $_POST["enable"];
		        }else{
		        	$sql .= " enable != ?";
		            $where[] = 2;
		        }

		        $info = $this->mod->select($sql, $where);

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $info[$key]["enable"] = $this->basePublic('statusbtn', 'Proxy',array("id"=>$value["enable"]));
                        $info[$key]["act"]    = $this->basePublic('toolbtn', 'Proxy',array("id" =>$value['id'],"menuID" =>$_POST["menuID"]))["editBtn"];
                    }

                    $this->obj["code"] = 100;
                    $this->obj["data"] = $info;
                    $this->obj["addBtn"] = $this->basePublic('toolbtn', 'Proxy',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }

                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "IP",
                    "port",
                    "proxy_acc",
                    "proxy_pwd",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $IP        = $_POST["IP"];
                $port      = $_POST["port"];
                $proxy_acc = $_POST["proxy_acc"];
                $proxy_pwd = $_POST["proxy_pwd"];
                $enable    = $_POST["enable"];

                $this->mod->add_by('LT_proxy', array(
					"enable"    => $enable,
					"IP"        => $IP,
					"port"      => $port,
					"proxy_acc" => $proxy_acc,
					"proxy_pwd" => $proxy_pwd,
					"nowtime"   => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals" => $this->mod->last_qstr,
                    "tb_id" => 0000,
                    "type" => 0
                );
                $this->basePublic('editrecord', 'LT_proxy', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "enable",
                    "IP",
                    "port",
                    "proxy_acc",
                    "proxy_pwd"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id        = $_POST["id"];
                $enable    = $_POST["enable"];
                $IP        = $_POST["IP"];
                $port      = $_POST["port"];
                $proxy_acc = $_POST["proxy_acc"];
                $proxy_pwd = $_POST["proxy_pwd"];

                /*********修改資料********/
                $this->mod->modi_by('LT_proxy', array(
                    'id' => $id
                ), array(
					"enable"    => $enable,
					"IP"        => $IP,
					"port"      => $port,
					"proxy_acc" => $proxy_acc,
					"proxy_pwd" => $proxy_pwd,
					"nowtime"   => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_proxy', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_proxy', array(
                    'id' => $id
                ), array(
					"enable"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_proxy', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'proxyedit':
                $sql  = "SELECT id,IP,port,proxy_acc,proxy_pwd,enable FROM LT_proxy WHERE id = ? ";
                $info = $this->mod->select($sql, array(
                    $ID
                ));

                if (empty($info)) {
                    $info[0] = array(
						"IP"        => '',
						"port"      => '',
						"proxy_acc" => '',
						"proxy_pwd" => '',
						"enable"    => ''
                    );
                }
                return $info;
                break;
        }
    }
    /**gameOpenset*/
    public function gameOpenset($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql  = "
                    SELECT
                        id,             -- 編號
                        game_id,         -- 遊戲名稱
                        lottery_year,     -- 年
                        lottery_month,     -- 月
                        lottery_day,    -- 日
                        enable,         -- 狀態
                        nowtime            -- 最後更新時間
                    FROM
                        LT_openset
                    WHERE
                ";
                $where = array();

                if (isset($_POST["enable"]) && (!empty($_POST["enable"]) || $_POST["enable"] === '0')) {
					$sql .= " enable = ?";
		            $where[] = $_POST["enable"];
		        }else{
		        	$sql .= " enable != ?";
		            $where[] = 2;
		        }

                if (isset($_POST["year"]) && (!empty($_POST["year"]))) {
                    $sql .= " AND lottery_year = ?";
		            $where[] = $_POST["year"];
                }

                if (isset($_POST["gameID"]) && (!empty($_POST["gameID"]))) {
                    $sql .= " AND game_id = ?";
		            $where[] = $_POST["gameID"];
                }
                
		        $info = $this->mod->select($sql, $where);
                // print_r($this->mod->last_qstr);
                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $info[$key]["game_id"] = $this->basePublic('catchCname', 'gameOpenset',array("id"=>$value["game_id"])); //遊戲中文名稱
                        $info[$key]["enable"]  = $this->basePublic('statusbtn', 'gameOpenset',array("id"=>$value["enable"]));
                        $info[$key]["act"]     = $this->basePublic('toolbtn', 'gameOpenset',array("id" =>$value['id'],"menuID" =>$_POST["menuID"]))["editBtn"];
                    }

                    $this->obj["code"] = 100;
                    $this->obj["data"] = $info;
                    $this->obj["addBtn"] = $this->basePublic('toolbtn', 'gameOpenset',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }
               
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "game_id",
                    "lottery_year",
                    "lottery_month",
                    "lottery_day",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $game_id       = $_POST["game_id"];
                $lottery_year  = $_POST["lottery_year"];
                $lottery_month = $_POST["lottery_month"];
                $lottery_day   = $_POST["lottery_day"];
                $enable        = $_POST["enable"];

                $this->mod->add_by('LT_openset', array(
					"game_id"       => $game_id,
					"lottery_year"  => $lottery_year,
					"lottery_month" => $lottery_month,
					"lottery_day"   => $lottery_day,
					"enable"        => $enable,
					"nowtime"       => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_openset', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "game_id",
                    "lottery_year",
                    "lottery_month",
                    "lottery_day",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id            = $_POST["id"];
                $game_id       = $_POST["game_id"];
                $lottery_year  = $_POST["lottery_year"];
                $lottery_month = $_POST["lottery_month"];
                $lottery_day   = $_POST["lottery_day"];
                $enable        = $_POST["enable"];

                /*********修改資料********/
                $this->mod->modi_by('LT_openset', array(
                    'id' => $id
                ), array(
					"game_id"       => $game_id,
					"lottery_year"  => $lottery_year,
					"lottery_month" => $lottery_month,
					"lottery_day"   => $lottery_day,
					"enable"        => $enable,
					"nowtime"       => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_openset', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array("id");
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_openset', array(
                    'id' => $id
                ), array(
					"enable"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_openset', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'gameOpensetList':
                $sql  = "SELECT id,lottery_year,lottery_month,lottery_day,enable,game_id FROM LT_openset WHERE id = ? ";
                $info = $this->mod->select($sql, array(
                    $ID
                ));

                if (empty($info)) {
                    $info[0] = array(
						"lottery_year"  =>'',
						"lottery_month" =>'',
						"lottery_day"   =>'',
						"enable"        =>'',
						"game_id"       =>''
                    );
                }
                return $info;
                break;
            case 'yearExist':
                $year = $this->mod->select('SELECT lottery_year FROM `LT_openset` group by lottery_year ORDER BY lottery_year DESC');
                $cname = $this->mod->select('SELECT a.game_id,b.cname FROM `LT_openset` as a LEFT JOIN LT_game as b ON a.game_id = b.id group by a.game_id');
                $data = array(
                    'year' => $year,
                    'cname' => $cname
                );
                return $data;
                break;
        }
    }
    /**vac*/
    public function gameVac($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
                $sql = "SELECT * FROM LT_vac WHERE";
               	$where = array();

                if (isset($_POST["enable"]) && (!empty($_POST["enable"]) || $_POST["enable"] === '0')) {
					$sql .= " enable = ?";
		            $where[] = $_POST["enable"];
		        }else{
		        	$sql .= " enable != ?";
		            $where[] = 2;
		        }

		        if (isset($_POST["year"]) && !empty($_POST["year"])) {
					$sql .= " AND `vacStart` > ? AND `vacEnd` <= ?";
                    $where[] = $_POST["year"] . '-01-01 00:00:00';
                    $where[] = $_POST["year"] . '-12-31 23:59:59';
                }
                
                $info = $this->mod->select($sql,$where);
                
                $yearAry = [];
                foreach($info as $k => $v) {
                    $date = explode(' ', $v['vacStart']);
                    $year = explode(('-'), $date[0]);
                    if (!in_array($year[0], $yearAry))
                        array_push($yearAry, $year[0]);
                    
                }
                
                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $info[$key]["game_id"] = $this->basePublic('catchCname', 'gameVac', array("id"=>$value["game_id"])); //遊戲中文名稱
                        $info[$key]["enable"]  = $this->basePublic('statusbtn', 'gameVac', array("id"=>$value["enable"]));
                        $info[$key]["act"]     = $this->basePublic('toolbtn', 'gameVac',array("id" =>$value['id'],"menuID" =>$_POST["menuID"]))["editBtn"];
                    }

					$this->obj["code"]      = 100;
					$this->obj["data"]      = $info;
					$this->obj["yearAry"]   = $yearAry;
					$this->obj["addBtn"]    = $this->basePublic('toolbtn', 'gameVac',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "game_id",
                    "vacStart",
                    "vacEnd",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

				$game_id  = $_POST["game_id"];
				$vacStart = date('Y-m-d 00:00:00', strtotime($_POST["vacStart"]));
				$vacEnd   = date('Y-m-d 23:59:59', strtotime($_POST["vacEnd"]));
				$enable   = $_POST["enable"];

                $this->mod->add_by('LT_vac', array(
					"game_id"  => $game_id,
					"vacStart" => $vacStart,
					"vacEnd"   => $vacEnd,
					"enable"   => $enable,
					"nowtime"  => $nowtime
				));
                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_vac', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "game_id",
                    "vacStart",
                    "vacEnd",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

				$id       = $_POST["id"];
				$game_id  = $_POST["game_id"];
				$vacStart = date('Y-m-d 00:00:00', strtotime($_POST["vacStart"]));
				$vacEnd   = date('Y-m-d 23:59:59', strtotime($_POST["vacEnd"]));
				$enable   = $_POST["enable"];

                /*********修改資料********/
                $this->mod->modi_by('LT_vac', array(
                    'id' => $id
                ), array(
					"game_id"  => $game_id,
					"vacStart" => $vacStart,
					"vacEnd"   => $vacEnd,
					"enable"   => $enable,
					"nowtime"  => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_vac', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_vac', array(
                    'id' => $id
                ), array(
					"enable"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_vac', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'gameVacList':
                $sql  = "SELECT id,game_id,vacStart,vacEnd,enable FROM LT_vac WHERE id = ? ";
                $info = $this->mod->select($sql, array($ID));

                if (empty($info)) {
                    $info[0] = array(
						"vacStart" => '',
						"vacEnd"   => '',
						"game_id"  => ''
                    );
                }
                return $info;
                break;
        }
    }
    /**功能列表*/
    public function funclist($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'funGroup':
                /**功能 群組*/
                $sql  = "SELECT id,name,up_id FROM LT_permissions WHERE enable = ? ";
                $info = $this->mod->select($sql, array(0));

                $funGroup = array();
                foreach ($info as $key => $value) {
					$data          = array();
					$data["id"]    = $value["id"];
					$data["name"]  = $value["name"];
					$data["up_id"] = $value["up_id"];
                    array_push($funGroup, $data);
                }

                return $funGroup;
                break;
            case 'homefun':
            	$lvl_id = $_POST["lvl_id"];
            	$sql  = "
		            SELECT
						lt_game_catch.LT_permissions.id,
						lt_game_catch.LT_permissions.name,
						lt_game_catch.LT_permissions.icon,
						lt_game_catch.LT_permissions.up_id,
						lt_game_catch.LT_permissions.data_rel,
						lt_game_catch.LT_permissions.link_type,
						lt_game_catch.LT_permissions.classify
		            FROM
		                lt_game_catch.LT_permissions
		        ";

				if($lvl_id==5){
					$sql = $sql."WHERE lt_game_catch.LT_permissions.enable = ?";
					$where = array(0);
				}else{
					$sql = $sql."JOIN lt_game_catch.LT_permission_lvl ";
					$sql = $sql."ON lt_game_catch.LT_permissions.id = lt_game_catch.LT_permission_lvl.perm_id ";
					$sql = $sql."WHERE lt_game_catch.LT_permissions.enable = ? AND
		            			 lt_game_catch.LT_permission_lvl.lvl_id = ? ";
					$where = array(0,$lvl_id);
				}
				$info  = $this->mod->select($sql, $where);

                $data = array();
                foreach ($info as $k => $v) {
                    if($v["classify"]==="C"){
						$data[$v["id"]]["name"]      = $v["name"];
						$data[$v["id"]]["icon"]      = $v["icon"];
						$data[$v["id"]]['data_rel']  = $v["data_rel"];
						$data[$v["id"]]["link_type"] = $v["link_type"];
                    }
                }

                foreach ($data as $k => $v) {
                	unset($sub);
                	foreach ($info as $k1 => $v1) {
                		if($k==$v1["up_id"]){
                			$sub[$v1["id"]]["name"]      = $v1["name"];
	                        $sub[$v1["id"]]["icon"]      = $v1["icon"];
	                        $sub[$v1["id"]]['up_id']     = $v1["up_id"];
	                        $sub[$v1["id"]]['data_rel']  = $v1["data_rel"];
	                        $sub[$v1["id"]]["link_type"] = $v1["link_type"];

                			$data[$k]["sub"] = $sub;
                		}
                	}
                }

                $this->obj["code"] = 100;
                $this->obj["data"] = $data;
                $this->output();
                break;
            case 'list':
                $sql  = "SELECT * FROM LT_permissions WHERE enable != ? AND up_id = ? ";
                $info = $this->mod->select($sql, array(
                    2,
                    0
                ));

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        $sql = "SELECT * FROM LT_permissions WHERE enable != ? AND up_id = ?";
                        $per = $this->mod->select($sql, array(
                            2,
                            $value["id"]
                        ));

                        foreach ($per as $k => $v) {
                            $info[$key]["childPerm"][$k] = $v;

                            $sql  = "SELECT * FROM LT_permissions WHERE enable != ? AND up_id = ? ";
                            $perv = $this->mod->select($sql, array(
                                2,
                                $v["id"]
                            ));

                            foreach ($perv as $k1 => $v1) {
                                $info[$key]["childPerm"][$k][$k1] = $v1;
                            }
                        }

                    }

                    $this->obj["code"]     = 100;
                    $this->obj["data"]     = $info;
                    $this->obj["funGroup"] = $this->funclist('funGroup');

                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "up_id",
                    "name",
                    "classify",
                    "link_type",
                    "addcol",
                    "editcol",
                    "delcol",
                    "look",
                    "data_rel",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $up_id     = $_POST["up_id"];
                $name      = $_POST["name"];
                $classify  = $_POST["classify"];
                $link_type = $_POST["link_type"];
                $addcol    = $_POST["addcol"];
                $editcol   = $_POST["editcol"];
                $delcol    = $_POST["delcol"];
                $look      = $_POST["look"];
                $data_rel  = $_POST["data_rel"];
                $enable    = $_POST["enable"];

                $this->mod->add_by('LT_permissions', array(
					"up_id"     => $up_id,
					"name"      => $name,
					"classify"  => $classify,
					"link_type" => $link_type,
					"addcol"    => $addcol,
					"editcol"   => $editcol,
					"delcol"    => $delcol,
					"look"      => $look,
					"data_rel"  => $data_rel,
					"enable"    => $enable,
					"nowtime"   => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_permissions', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "up_id",
                    "name",
                    "classify",
                    "link_type",
                    "addcol",
                    "editcol",
                    "delcol",
                    "look",
                    "data_rel",
                    "enable"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id        = $_POST["id"];
                $up_id     = $_POST["up_id"];
                $name      = $_POST["name"];
                $classify  = $_POST["classify"];
                $link_type = $_POST["link_type"];
                $addcol    = $_POST["addcol"];
                $editcol   = $_POST["editcol"];
                $delcol    = $_POST["delcol"];
                $look      = $_POST["look"];
                $data_rel  = $_POST["data_rel"];
                $enable    = $_POST["enable"];

                /*********修改資料********/
                $this->mod->modi_by('LT_permissions', array(
                    'id' => $id
                ), array(
					"up_id"     => $up_id,
					"name"      => $name,
					"classify"  => $classify,
					"link_type" => $link_type,
					"addcol"    => $addcol,
					"editcol"   => $editcol,
					"delcol"    => $delcol,
					"look"      => $look,
					"data_rel"  => $data_rel,
					"enable"    => $enable,
					"nowtime"   => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_permissions', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_permissions', array(
                    'id' => $id
                ), array(
					"enable"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_permissions', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'funclistedit':
                $sql  = "
                    SELECT
                       *
                    FROM
                        LT_permissions
                    WHERE
                        id = ? AND
                        enable = ?
                ";
                $info = $this->mod->select($sql, array($ID,0));

                if (empty($info)) {
                    $info[0] = array(
                        "name" => '',
                        "data_rel" => ''
                    );
                }
                return $info;
                break;
        }
    }
    /**功能階級*/
    public function funcLvl($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'list':
            	$sql  = "
                    SELECT
                        id,         -- 編號
                        lvl_id,     -- 階級ID
                        perm_id,     -- 階級ID
                        addcol,     -- 新增
                        editcol,     -- 刪除
                        delcol,     -- 修改
                        look,         -- 觀看
                        enable,     -- 狀態
                        nowtime     -- 最後更新時間
                    FROM
                        LT_permission_lvl
                    WHERE
                        enable != ?
                ";
                $info = $this->mod->select($sql, array(2));

                if (empty($info)) {
                    $this->obj["code"] = 104;
                    $this->obj['msg']  = $this->delItem[104];
                } else {
                    foreach ($info as $key => $value) {
                        /**權限列表 抓取使用者權限相對應階級名稱*/
                        $sql        = "SELECT lvl_name FROM LT_user_level WHERE id = ? ";
                        $user_level = array_shift($this->mod->select($sql, array(
                            $value["lvl_id"]
                        )));
                        if (empty($user_level)) //權限表無相對應資料
                            $info[$key]["lvl_name"] = "null";
                        else
                            $info[$key]["lvl_name"] = $user_level["lvl_name"]; //權限名稱

                        /**抓取功能權限*/
                        $sql        = "SELECT name FROM LT_permissions WHERE id = ? ";
                        $permission = array_shift($this->mod->select($sql, array(
                            $value["perm_id"]
                        )));
                        if (empty($permission)) //權限表無相對應資料
                            $info[$key]["per_name"] = "null";
                        else
                            $info[$key]["per_name"] = $permission["name"]; //權限名稱

                        $info[$key]["addcol"]  = $this->basePublic('zeroChange', 'funcLvl', array("id"=>$value["addcol"]));
                        $info[$key]["editcol"] = $this->basePublic('zeroChange', 'funcLvl', array("id"=>$value["editcol"]));
                        $info[$key]["delcol"]  = $this->basePublic('zeroChange', 'funcLvl', array("id"=>$value["delcol"]));
                        $info[$key]["look"]    = $this->basePublic('zeroChange', 'funcLvl', array("id"=>$value["look"]));

                        $info[$key]["enable"] = $this->basePublic('statusbtn', 'funcLvl', array("id"=>$value["enable"]));
                        $info[$key]["act"]    = $this->basePublic('toolbtn', 'funcLvl',array("id" =>$value['id'],"menuID" =>$_POST["menuID"]))["editBtn"];
                    }

                    $this->obj["code"]     = 100;
                    $this->obj["data"]     = $info;
                    $this->obj["lvlGroup"] = $this->userlvl('lvlGroup');
                    $this->obj["funGroup"] = $this->funclist('funGroup');
                    $this->obj["addBtn"] = $this->basePublic('toolbtn', 'funcLvl',array("menuID"=>$_POST["menuID"]))["addBtn"];
                }
                $this->output();
                break;
            case 'add':
                /*******需要接收哪些input data********/
                $IssetAry = array("Cupid","Iupid","lvl_id","enable","addcol","editcol","delcol","look");
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

				$id      = $_POST["id"];
				$Iupid   = $_POST["Iupid"]!=null ? $_POST["Iupid"] : $_POST["Cupid"];
				$lvl_id  = $_POST["lvl_id"];
				$enable  = $_POST["enable"];
				$addcol  = $_POST["addcol"];
				$editcol = $_POST["editcol"];
				$delcol  = $_POST["delcol"];
				$look    = $_POST["look"];

                $this->mod->add_by('LT_permission_lvl', array(
					"lvl_id"  => $lvl_id,
					"perm_id" => $Iupid,
					"addcol"  => $addcol,
					"editcol" => $editcol,
					"delcol"  => $delcol,
					"look"    => $look,
					"enable"  => $enable,
					"nowtime" => $nowtime
                ));
                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => 0000,
					"type"  => 0
                );
                $this->basePublic('editrecord', 'LT_permission_lvl', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'save':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id",
                    "Iupid",
                    "lvl_id",
                    "enable",
                    "addcol",
                    "editcol",
                    "delcol",
                    "look"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id      = $_POST["id"];
                $perm_id = $_POST["Iupid"];
                $lvl_id  = $_POST["lvl_id"];
                $enable  = $_POST["enable"];
                $addcol  = $_POST["addcol"];
                $editcol = $_POST["editcol"];
                $delcol  = $_POST["delcol"];
                $look    = $_POST["look"];

                /*********修改資料********/
                $this->mod->modi_by('LT_permission_lvl', array(
                    'id' => $id
                ), array(
					"perm_id" => $perm_id,
					"lvl_id"  => $lvl_id,
					"enable"  => $enable,
					"addcol"  => $addcol,
					"editcol" => $editcol,
					"delcol"  => $delcol,
					"look"    => $look,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_permission_lvl', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'del':
                /*******需要接收哪些input data********/
                $IssetAry = array(
                    "id"
                );
                /************************************/

                /*******檢查接收資料是否皆有輸入********/
                foreach ($IssetAry as $k => $v) {
                    if (!isset($_POST[$v])) {
                        $this->obj["code"]    = 109;
                        $this->obj["msg_str"] = $this->GetCodeMsg($this->obj["code"]);
                        $this->output();
                    } else {
                        $$v = $_POST[$v];
                    }
                }
                /************************************/

                $id = $_POST["id"];

                /*********修改資料********/
                $this->mod->modi_by('LT_permission_lvl', array(
                    'id' => $id
                ), array(
					"enable"  => 2,
					"nowtime" => $nowtime
                ));

                /*********紀錄存到資料庫********/
                $editdata = array(
					"vals"  => $this->mod->last_qstr,
					"tb_id" => $id,
					"type"  => 1
                );
                $this->basePublic('editrecord', 'LT_permission_lvl', $editdata);
                /*****************************/

                $this->obj["code"] = 100;
                $this->obj['msg']  = $this->delItem[100];
                $this->output();
                break;
            case 'funLvledit':
                $sql  = "SELECT id,perm_id as Iupid,lvl_id,enable,addcol,editcol,delcol,look FROM LT_permission_lvl WHERE id = ? ";
                $info = $this->mod->select($sql, array($ID));

                if (empty($info)) {
                    $info[0] = array(
                        "Cupid"=>'',
                        "Iupid"=>'',
						"lvl_id"=>'',
						"enable"=>'',
						"addcol"=>'',
						"editcol"=>'',
						"delcol"=>'',
						"look"=>''
                    );
                }
                return $info;
                break;
        }
    }
     /**檢查開獎號碼*/
    public function openNumber($Action = null, $ID = null){
        $nowtime = date("Y-m-d H:i:s");
        switch ($Action) {
            case 'search':
            	foreach ($_POST["data"] as $k => $v) {

            		//先抓出此遊戲與期數 的 號碼 做排序
	            	$sql  = "SELECT lottery FROM LT_periods";
	                $where = array();

			        if (isset($v["gameID"]) && !empty($v["gameID"])) {
						$sql .= " WHERE game_id = ?";
			            $where[] = $v["gameID"];
			        }else{
			        	$sql .= " WHERE game_id = ?";
			            $where[] = 1;
			        }

			        if (isset($v["period"]) && !empty($v["period"])) {
						$sql .= " AND period_str = ?";
			            $where[] = $v["period"];
			        }else{
			        	$sql .= " AND period_str = ?";
			            $where[] = "";
			        }

			        $value = array_shift($this->mod->select($sql,$where));

			        if(!empty($value)){
			        	$sortLotery = explode(",",$value["lottery"]);
						sort($sortLotery);
						$lotteryCode = implode(",",$sortLotery);

						if (isset($v["number"]) && !empty($v["number"])) {
							$sortLotery = explode(",",$v["number"]);
							sort($sortLotery);
							$postCode = implode(",",$sortLotery);

							if($lotteryCode===$postCode){
			        			// $this->obj["data"][$k]["msg"] = $this->delItem[100];
							}else{
			        			$this->obj["data"][$k]["msg"] = "[".$v["gameID"]."]".$v["period"]."　號碼不ㄧ";
							}
			        	}else{
			        		$this->obj["data"][$k]["msg"] = "[".$v["gameID"]."]".$v["period"].$this->delItem[104];
			        	}
			        }else{
			        	$this->obj["data"][$k]["msg"] = "[".$v["gameID"]."]".$v["period"].$this->delItem[104];
			        }
            	}
            	$this->obj["code"] = 100;
                $this->output();
                break;
        }
    }
    /**共用*/
    public function basePublic($Action = null, $table = null, $data = null){
		$nowtime = date("Y-m-d H:i:s");
		$id      = isset($data["id"]) ? $data['id']: '';
		$menuID  = isset($data["menuID"]) ? $data['menuID']: '';

        switch ($Action) {
            case 'editrecord': //紀錄修改紀錄
                $this->mod->add_by('LT_edit_record', array(
					"tb"        => $table,
					"tb_id"     => $data["tb_id"],
					"type"      => $data["type"],
					"vals"      => $data["vals"],
					"acc_tb"    => $this->UserInfo["agent_user_id"],
					"acc_tb_id" => $this->UserInfo["LoginID"],
					"nowtime"   => $nowtime
                ));
                break;
            case 'toolbtn': //操作按鈕
                $lvlBtn = '';
	            switch ($table) {
	            	case 'gameList':
                        $size = "{width: 600, height: 500}";
                        break;
	            	case 'gameUrl':
	            		$size = "{width: 600, height: 650}";
	            		break;
	            	case 'funcLvl':
	            		$size = "{width: 600, height: 500}";
	            		break;
	            	case 'userLvl' :
	            		$size = "{width: 600, height: 450}";
	            		$lvlBtn  = '<button type="button" class="btns btns-sm btns-green" onclick="ZgWindowFun.GoPage(\'main/pop/' . $table . '/lvlFun/' . $id . '\', \'iframeSimple\', '.$size.', \'功能檢視\')"><i class="fa fa-level-down" aria-hidden="true"></i>功能檢視</button>';
                    case 'Highweightlist':
                        if ($id == 155 || $id == 110) 
                            $size = "{width: 350, height: 600}";
                        else 
                            $size = "{width: 350, height: 300}";
                        break;
                    default:
	            		$size = "{width: 600, height: 450}";
	            		break;
	            }
	            $sql  = "
                    SELECT addcol,editcol,delcol
                    FROM
                        LT_permission_lvl
                    WHERE
                        enable != ? AND
                        lvl_id = ? AND
                        perm_id = ?
                ";
                $info = array_shift($this->mod->select($sql, array(2,$this->gdata["gda_lvl_id"],$menuID)));

            	//新增
                switch ($info["addcol"]){
                	case 0:
                		$addBtn  = '<button type="button" class="btns btns-blue btns-square '.$table.'_Button_add"><i class="icon-circle-plus"></i><span>新增</span></button>';
                	break;
                	case 1:
                		$addBtn  = '';
                	break;
                }
                //刪除
                switch ($info["delcol"]){
                	case 0:
	                	$delBtn = '<button type="button" class="btns btns-sm btns-red" onclick="popup.confirm(\'Del\', \'確定要刪除' . $id . '\', function() {tableAct.del(\'' . $table . '\', \'' . $id . '\')});"><i class="fa fa-times" aria-hidden="true"></i>刪除</button>';
                	break;
                	case 1:
                		$delBtn   = '';
                	break;
                }
                //修改
                switch ($info["editcol"]){
                	case 0:
	                	$editBtn  = '<button type="button" class="btns btns-sm btns-blue" onclick="ZgWindowFun.GoPage(\'main/pop/' . $table . '/edit/' . $id . '\', \'iframeSimple\', '.$size.', \'修改\')"><i class="fa fa-pencil" aria-hidden="true"></i>修改</button>';
					break;
                	case 1:
                		$editBtn  = '';
                	break;
                }
               	$feedback = array(
						"addBtn"  =>$addBtn,
						"editBtn" =>$editBtn,
						"delBtn"  =>$delBtn,
                        "lvlBtn"  =>$lvlBtn
               		);
                return $feedback;
                break;
            case 'statusbtn': //產生 狀態樣式
                if ($id == '0' || $id == 'Y')
                	$feedback = "<div style='color:#89d6aa;'><i class='glyphicon glyphicon-off'></i>啟用</div>";
                else
                	$feedback = "<div style='color:#f17272;'><i class='fa fa-ban'></i>停用</div>";
                return $feedback;
                break;
            case 'catchCname': //取出遊戲 中文名稱
                $sql  = "SELECT cname FROM LT_game WHERE id = ? ";
                $game = array_shift($this->mod->select($sql, array(
                    $id
                )));

                if (!empty($game))
                    $feedback = $game["cname"];
                else
                    $feedback = "null";

                return $feedback;
                break;
            case 'catchEname': //取出遊戲 英文名稱
                $sql  = "SELECT ename FROM LT_game WHERE id = ? ";
                $game = array_shift($this->mod->select($sql, array(
                    $id
                )));

                if (!empty($game))
                    $feedback = $game["ename"];
                else
                    $feedback = "null";

                return $feedback;
                break;
            case 'zeroChange':
                if ($id == "0")
                    $feedback = "<i class='fa fa-check' aria-hidden='true' style='color:#36ed85;'></i>";
                else
                    $feedback = "<i class='fa fa-times' aria-hidden='true' style='color:red;'></i>";

                return $feedback;
                break;
            case 'openState':
                if ($id == "0")
                    $feedback = "<i class='fa fa-minus-circle' aria-hidden='true' style='color:red;font-size: xx-large;'></i>";
                else
                    $feedback = "<i class='fa fa-check-circle-o' aria-hidden='true' style='color:green;font-size: xx-large;'></i>";
                return $feedback;
                break;
        }
    }
}