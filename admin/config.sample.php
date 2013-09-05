<?php
require('../global_config.php');
class CONFIG extends GLOBAL_CONFIG {
	/* required fields */
	const DEBUG 		= false;
	const NAME			= "online_timesheet";
	const BUILD_NO		= "";
	const VERSION		= "";
	const TITLE			= "Online Timesheet(HK)";
	const LOGIN_TITLE	= "Online Timesheet(HK)";
	
	const SSKEY_PARAM	= "param";
	
	const LOGGED_IN		= "LOGGED IN";
}
$GLOBALS['db_config']= array(
	"type"		=> "mysql",
	"host"		=> "localhost",
	"user"		=> "portlet",
	"password"	=> "wSQYZ7M5tTRdxnQK",
	"schema"	=> "timmy_timesheet",
	"charset"	=> "utf8",
	"prefix"	=> "cs_"
);
$GLOBALS['db_table'] = array(
	'func' 			=> $GLOBALS['db_config']['prefix'].'func',
	'user' 			=> $GLOBALS['db_config']['prefix'].'user',
	'user_role_rel' => $GLOBALS['db_config']['prefix'].'user_role_rel',
	'user_func_rel' => $GLOBALS['db_config']['prefix'].'user_func_rel',
	'role' 			=> $GLOBALS['db_config']['prefix'].'role',
	'role_func_rel' => $GLOBALS['db_config']['prefix'].'role_func_rel',
	'' => ''
);

$CSPORTAL_THEME = "default";

session_name(CONFIG::NAME);
?>