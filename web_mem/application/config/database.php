<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('../web_conf/config_mem.inc');
$set=get_conf_mem();

$active_group = 'db_w';
$active_record = TRUE;

$db=get_db_conf_mem($set);