<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('ext/lib_base'.EXT);
class lib_pemissions extends lib_base{
	function __construct(){
		parent::__construct();
		$this->ci =& get_instance();
	}
	public function Pemissions($Action,$data=null,$data2=null,$data3=null){
		switch($Action){
			case 'GetListTreeInfo':{
				switch($data2){
					case 'up':{
						$id = $data;
						$info = $this->Pemissions("GetInfoByID",$id);
						if($info){
							$info["name"] = $this->ci->LP->PullStrByLang($info["name"]);
							$up_info = $this->Pemissions("GetListTreeInfo",$info["up_id"],$data2);
							if($up_info){
								$all_info = $up_info;
								$all_info[] = $info;
							}else{
								$all_info[] = $info;
							}
							return $all_info;
						}else{
							return false;
						}
						break;
					}
					case 'down':{
						$_tb = "`ag_permissions`";
						$up_id = $data;
						$info = $this->mod->select("
							SELECT *
							FROM ".$_tb."
							where 1 
							And del_info = 0
							And up_id = '".$up_id."'
							order by sequence 
						");
						if($info){
							foreach($info as $k => $v){
								$info[$k]["name"] = $this->ci->LP->PullStrByLang($v["name"]);
								$children_info = $this->Pemissions("GetListTreeInfo",$v["id"],$data2);
								if($children_info){
									$info[$k]["children"] = $children_info;
								}
							}
							return $info;
						}else{
							return false;
						}
						break;
					}
					default:{
						return false;
					}
				}
				break;
			}
			case 'GetInfoByID':{
				$id = $data;
				$_tb = "`ag_permissions`";
				$info = $this->mod->select("
					SELECT *
					FROM ".$_tb."
					where id = ? 
					limit 0,1
				",$id);
				if($info){
					$info = $info[0];
					return $info;
				}else{
					return false;
				}
			}
			case 'GetInfo':{
				$_tb = "`ag_permissions`";
				$Limit = "";
				$condition = "";
				if($data!=null){
					$condition = " And up_id='".$data."'";
				}
				if(isset($_POST["Page"])){
					$pagenumber = isset($_POST["pagenumber"]) ? $_POST["pagenumber"] : 20;
					$NowPage = $_POST["Page"] - 1;
					$row = $this->mod->select("
						SELECT COUNT(id) as Num
						FROM ".$_tb."
						where 1
						".$condition."
					");
					$Count = $row[0]["Num"];
					$EndPage = ceil($Count/$pagenumber);
					$Limit = " limit ".($NowPage * $pagenumber).",".$pagenumber;
					$this->obj["Count"] = $Count;
					$this->obj["NowPage"] = $NowPage + 1;
					$this->obj["EndPage"] = $EndPage;
				}
				$info = $this->mod->select("
					SELECT *
					FROM ".$_tb."
					where 1 
					And del_info = 0
					".$condition."
					order by sequence 
					".$Limit."
				");
				if($info){
					return $info;
				}else{
					return false;
				}
				break;
			}
			case 'AddTo':{
				$_tb = "ag_permissions";
				$IssetAry = array(
					"up_id","classify","name","data_rel","link_type"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$sequence = $this->mod->GetSerial($_tb,"sequence","And up_id='".$up_id."'",$up_id."-",12);
				$add = $this->mod->add_by($_tb,array(
					"up_id"			=> $up_id,
					"classify"		=> $classify,
					"name"			=> $name,
					"add"			=> isset($_POST["add"]) ? $_POST["add"] : 0,
					"edit"			=> isset($_POST["edit"]) ? $_POST["edit"] : 0,
					"del"			=> isset($_POST["del"]) ? $_POST["del"] : 0,
					"look"			=> isset($_POST["look"]) ? $_POST["look"] : 0,
					"sequence"		=> $sequence,
					"link_type"		=> $link_type,
					"data_rel"		=> $data_rel,
					"SetupTime"		=> date("Y-m-d H:i:s"),
					"nowtime"		=> date("Y-m-d H:i:s")
				));
				return $add["lid"];
				break;
			}
			case 'EditTo':{
				$_tb = "ag_permissions";
				$IssetAry = array(
					"edit_id","up_id","classify","name","data_rel","link_type"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$this->mod->modi_by($_tb,array("id" => $edit_id),array(
					"up_id"			=> $up_id,
					"classify"		=> $classify,
					"name"			=> $name,
					"add"			=> isset($_POST["add"]) ? $_POST["add"] : 0,
					"edit"			=> isset($_POST["edit"]) ? $_POST["edit"] : 0,
					"del"			=> isset($_POST["del"]) ? $_POST["del"] : 0,
					"look"			=> isset($_POST["look"]) ? $_POST["look"] : 0,
					"link_type"		=> $link_type,
					"data_rel"		=> $data_rel,
					"nowtime"		=> date("Y-m-d H:i:s")
				));
				return $edit_id;
				break;
			}
			case 'DelTo':{
				$_tb = "ag_permissions";
				$IssetAry = array(
					"del_id"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$this->mod->modi_by($_tb,array("id" => $del_id),array(
						"del_info"		=> "1",
						"nowtime"		=> date("Y-m-d H:i:s")
				));
				return $del_id;
				break;
			}
			case 'UpTo':{
				$IssetAry = array(
					"id","up_id"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$_tb = 'ag_permissions';
				$Sql = "select sequence from `".$_tb."` where id='$id'";
				$r = $this->mod->select($Sql);
				$SequenceNumber = $r[0]['sequence'];
				$Sql = "select id,sequence 
					from ".$_tb." 
					where sequence<'$SequenceNumber' 
					And del_info=0 
					And up_id='".$up_id."' 
					order by sequence desc 
					limit 0,1";
				$r = $this->mod->select($Sql);
				$Number = count($r);
				if($Number!=0){
					$r = $r[0];
					$UpSequenceNumber = $r['sequence'];
					$UpInfID = $r['id'];
					$mod = $this->mod->modi_by_id($_tb,$id,array(
						"sequence"=> $UpSequenceNumber
					),"id",false);
					$mod = $this->mod->modi_by_id($_tb,$UpInfID,array(
						"sequence"=> $SequenceNumber
					),"id",false);
				}
				return true;
				break;
			}
			case 'DownTo':{
				$IssetAry = array(
					"id","up_id"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$_tb = 'ag_permissions';
				$Sql = "select sequence from `".$_tb."` where id='$id'";
				$r = $this->mod->select($Sql);
				$SequenceNumber = $r[0]['sequence'];
				$Sql = "select id,sequence 
					from ".$_tb." 
					where sequence>'$SequenceNumber'
					And del_info=0 
					And up_id='".$up_id."' 
					order by sequence 
					limit 0,1";
				$r = $this->mod->select($Sql);
				$Number = count($r);
				if($Number!=0){
					$r = $r[0];
					$UpSequenceNumber = $r['sequence'];
					$UpInfID = $r['id'];
					$mod = $this->mod->modi_by_id($_tb,$id,array(
						"sequence"=> $UpSequenceNumber
					),"id",false);
					$mod = $this->mod->modi_by_id($_tb,$UpInfID,array(
						"sequence"=> $SequenceNumber
					),"id",false);
				}
				return true;
				break;
			}
		}
	}
	public function Level($Action,$data=null,$data2=null,$data3=null){
		switch($Action){
			case 'GetInfoByID':{
				$id = $data;
				$_tb = "`ag_agent_user_level`";
				$condition = "";
				if( isset($_POST["agent_id"]) ){
					$condition .= " And agent_id='".$_POST["agent_id"]."'";
				}
				$info = $this->mod->select("
					SELECT *
					FROM ".$_tb."
					where id = ? 
					".$condition."
					limit 0,1
				",$id);
				if($info){
					return $info[0];
				}else{
					return false;
				}
			}
			case 'GetInfoByLevelID':{
				$id = $data;
				$_tb = "`ag_agent_user_level_pemissions`";
				$info = $this->mod->select("
					SELECT *
					FROM ".$_tb."
					where level_id = ? 
				",$id);
				if($info){
					return $info;
				}else{
					return false;
				}
			}
			case 'GetInfoByLevelIDForCheck':{
				$id = $data;
				$info = $this->mod->select("
					SELECT a.*,b.data_rel
					FROM `ag_agent_user_level_pemissions` as a join
					`ag_permissions` as b on a.pemissions_id = b.id
					where a.level_id = ? 
				",$id);
				if($info){
					return $info;
				}else{
					return false;
				}
			}
			case 'GetInfo':{
				$_tb = "`ag_agent_user_level`";
				$Limit = "";
				$condition = "";
				if( isset($_POST["agent_id"]) ){
					$condition .= " And agent_id='".$_POST["agent_id"]."'";
				}
				if($data!=null){
					$condition .= " And up_id='".$data."'";
				}
				if(isset($_POST["Page"])){
					$pagenumber = isset($_POST["pagenumber"]) ? $_POST["pagenumber"] : 20;
					$NowPage = $_POST["Page"] - 1;
					$row = $this->mod->select("
						SELECT COUNT(id) as Num
						FROM ".$_tb."
						where 1
						".$condition."
					");
					$Count = $row[0]["Num"];
					$EndPage = ceil($Count/$pagenumber);
					$Limit = " limit ".($NowPage * $pagenumber).",".$pagenumber;
					$this->obj["Count"] = $Count;
					$this->obj["NowPage"] = $NowPage + 1;
					$this->obj["EndPage"] = $EndPage;
				}
				$info = $this->mod->select("
					SELECT *
					FROM ".$_tb."
					where 1 
					And del = 0
					".$condition."
					order by sequence 
					".$Limit."
				");
				if($info){
					return $info;
				}else{
					return false;
				}
				break;
			}
			case 'AddTo':{
				$_tb = "ag_agent_user_level";
				$IssetAry = array(
					"agent_id","up_id","level_name"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$pemissions_id = isset($_POST["pemissions_id"]) ? $_POST["pemissions_id"] : array();
				$sequence = $this->mod->GetSerial($_tb,"sequence","And agent_id = '".$agent_id."' And up_id='".$up_id."'",$agent_id."-".$up_id."-",12);
				$add = $this->mod->add_by($_tb,array(
					"agent_id"		=> $agent_id,
					"up_id"			=> $up_id,
					"level_name"	=> $level_name,
					"sequence"		=> $sequence,
					"SetupTime"		=> date("Y-m-d H:i:s"),
					"nowtime"		=> date("Y-m-d H:i:s")
				));
				$level_id = $add["lid"];
				foreach($pemissions_id as $k => $v){
					$f_add = 0;$f_edit = 0;$f_del = 0;$f_look = 0;
					if(isset($_POST["pemissions_id_$v"])){
						if (in_array("add", $_POST["pemissions_id_$v"])) {
							$f_add = 1;
						}
						if (in_array("edit", $_POST["pemissions_id_$v"])) {
							$f_edit = 1;
						}
						if (in_array("del", $_POST["pemissions_id_$v"])) {
							$f_del = 1;
						}
						if (in_array("look", $_POST["pemissions_id_$v"])) {
							$f_look = 1;
						}
					}
					$add = $this->mod->add_by("ag_agent_user_level_pemissions",array(
						"level_id"		=> $level_id,
						"pemissions_id"	=> $v,
						"add"			=> $f_add,
						"edit"			=> $f_edit,
						"del"			=> $f_del,
						"look"			=> $f_look,
						"SetupTime"		=> date("Y-m-d H:i:s")
					));
				}
				return $level_id;
				break;
			}
			case 'EditTo':{
				$_tb = "ag_agent_user_level";
				$IssetAry = array(
					"edit_id","level_name"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$this->mod->modi_by($_tb,array("id" => $edit_id),array(
					"level_name"	=> $level_name,
					"nowtime"		=> date("Y-m-d H:i:s")
				));
				$this->mod->del_by("ag_agent_user_level_pemissions",array("level_id" => $edit_id));
				
				$pemissions_id = isset($_POST["pemissions_id"]) ? $_POST["pemissions_id"] : array();
				$level_id = $edit_id;
				foreach($pemissions_id as $k => $v){
					$f_add = 0;$f_edit = 0;$f_del = 0;$f_look = 0;
					if(isset($_POST["pemissions_id_$v"])){
						if (in_array("add", $_POST["pemissions_id_$v"])) {
							$f_add = 1;
						}
						if (in_array("edit", $_POST["pemissions_id_$v"])) {
							$f_edit = 1;
						}
						if (in_array("del", $_POST["pemissions_id_$v"])) {
							$f_del = 1;
						}
						if (in_array("look", $_POST["pemissions_id_$v"])) {
							$f_look = 1;
						}
					}
					$add = $this->mod->add_by("ag_agent_user_level_pemissions",array(
						"level_id"		=> $level_id,
						"pemissions_id"	=> $v,
						"add"			=> $f_add,
						"edit"			=> $f_edit,
						"del"			=> $f_del,
						"look"			=> $f_look,
						"SetupTime"		=> date("Y-m-d H:i:s")
					));
				}
				return $edit_id;
				break;
			}
			case 'DelTo':{
				$_tb = "ag_agent_user_level";
				$IssetAry = array(
					"del_id"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$this->mod->modi_by($_tb,array("id" => $del_id),array(
					"del"		=> "1",
					"nowtime"		=> date("Y-m-d H:i:s")
				));
				return $del_id;
				break;
			}
			case 'UpTo':{
				$IssetAry = array(
					"id","up_id"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$_tb = 'ag_agent_user_level';
				$Sql = "select sequence from `".$_tb."` where id='$id' And del=0";
				$r = $this->mod->select($Sql);
				$SequenceNumber = $r[0]['sequence'];
				$Sql = "select id,sequence 
					from ".$_tb." 
					where sequence<'$SequenceNumber' 
					And del=0 
					And up_id='".$up_id."' 
					order by sequence desc limit 0,1";
				$r = $this->mod->select($Sql);
				$Number = count($r);
				if($Number!=0){
					$r = $r[0];
					$UpSequenceNumber = $r['sequence'];
					$UpInfID = $r['id'];
					$mod = $this->mod->modi_by_id($_tb,$id,array(
						"sequence"=> $UpSequenceNumber
					),"id",false);
					$mod = $this->mod->modi_by_id($_tb,$UpInfID,array(
						"sequence"=> $SequenceNumber
					),"id",false);
				}
				return true;
				break;
			}
			case 'DownTo':{
				$IssetAry = array(
					"id","up_id"
				);
				foreach($IssetAry as $k => $v){
					if(!isset($_POST[$v])){
						$this->obj["code"]="109";
						$this->output();
					}else{
						$$v = $_POST[$v];
					}
				}
				$_tb = 'ag_agent_user_level';
				$Sql = "select sequence from `".$_tb."` where id='$id'";
				$r = $this->mod->select($Sql);
				$SequenceNumber = $r[0]['sequence'];
				$Sql = "select id,sequence 
						from ".$_tb." 
						where sequence>'$SequenceNumber' 
						And del=0 
						And up_id='".$up_id."' 
						order by sequence limit 0,1";
				$r = $this->mod->select($Sql);
				$Number = count($r);
				if($Number!=0){
					$r = $r[0];
					$UpSequenceNumber = $r['sequence'];
					$UpInfID = $r['id'];
					$mod = $this->mod->modi_by_id($_tb,$id,array(
						"sequence"=> $UpSequenceNumber
					),"id",false);
					$mod = $this->mod->modi_by_id($_tb,$UpInfID,array(
						"sequence"=> $SequenceNumber
					),"id",false);
				}
				return true;
				break;
			}
			case 'GetListTreeInfo':{
				switch($data2){
					case 'up':{
						$id = $data;
						$info = $this->Level("GetInfoByID",$id);
						if($info){
							$up_info = $this->Level("GetListTreeInfo",$info["up_id"],$data2);
							if($up_info){
								$all_info = $up_info;
								$all_info[] = $info;
							}else{
								$all_info[] = $info;
							}
							return $all_info;
						}else{
							return false;
						}
						break;
					}
					case 'down':{
						$condition = "";
						if( isset($_POST["agent_id"]) ){
							$condition .= " And agent_id='".$_POST["agent_id"]."'";
						}
						$_tb = "`ag_agent_user_level`";
						$up_id = $data;
						$info = $this->mod->select("
							SELECT *
							FROM ".$_tb."
							where 1 
							And del = 0
							And up_id = '".$up_id."'
							".$condition."
							order by sequence 
						");
						if($info){
							foreach($info as $k => $v){
								$children_info = $this->Level("GetListTreeInfo",$v["id"],$data2,true);
								if($children_info){
									$info[$k]["children"] = $children_info;
								}
							}
							return $info;
						}else{
							return false;
						}
						break;
					}
					default:{
						return false;
					}
				}
				break;
			}
			case 'GetLevelAry':{
				$ary = array();
				if($data){
					foreach($data as $v){
						$ary[] = $v["id"];
						if(isset($v["children"])){
							$temp = $this->Level("GetLevelAry",$v["children"]);
							foreach($temp as $v){
								$ary[] = $v;
							}
						}
					}
				}
				return $ary;
				break;
			}
			case 'GetMemberLevelTreeDown':{
				$mem_level_id = $data;
				$LevelTree = $this->Level("GetListTreeInfo",$mem_level_id,"down");
				if($LevelTree){
					$info = array();
					$info = $LevelTree;
					$LevelAry = $this->Level("GetLevelAry",$info);
					return $LevelAry;
				}else{
					return false;
				}
				break;
			}
			case 'GetLevelNumber':{
				$level_id = $data;
				$condition = "";
				if( isset($_POST["agent_id"]) ){
					$condition .= " And agent_id='".$_POST["agent_id"]."'";
				}
				$info = $this->mod->select("
					SELECT count(id) as num
					FROM `ag_agent_user`
					where level_id = ? 
					".$condition."
				",$level_id);
				if($info){
					return $info[0]["num"];
				}else{
					return 0;
				}
				break;
			}
			case 'GetListTreeInfoByID':{
				$LevelID = $data;
				$condition = "";
				if( isset($_POST["agent_id"]) ){
					$condition .= " And agent_id='".$_POST["agent_id"]."'";
				}
				$info = $this->mod->select("
					SELECT *
					FROM `ag_agent_user_level`
					where 1 
					And del = 0
					And id = '".$LevelID."'
					".$condition."
					limit 0,1
				");
				if($info){
					//$info = $info[0];
					switch($data2){
						case 'up':{
							
							break;
						}
						case 'down':{
							$down_info = $this->Level("GetListTreeInfo",$info[0]["id"],$data2);
							$info[0]["children"] = $down_info;
							return $info;
							break;
						}
						default:{
							return false;
						}
					}
				}else{
					return false;
				}
				break;
			}
		}
	}
}