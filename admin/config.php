<?php


class LOGIN_MODE {
	const LOCAL = 'LOCAL';
	const SSO = 'SSO';
}
class GLOBAL_CONFIG {
	const PORTAL_URL	= "http://pc043/portal/";
	const PORTAL_SSO_URL= "http://pc043/portal/webservice/sso/";
	
	const LOGIN_FLAG	= "login_status";
	
	const PAGE_CHARSET	= "utf-8";
	const SERVER_CHARSET= "big5";
}
class CONFIG extends GLOBAL_CONFIG{
	/* required fields */
	const DEBUG 		= false;
	const NAME			= "online_timesheet";
	const BUILD_NO		= "";
	const VERSION		= "";
	const TITLE			= "Online Timesheet(HK)";
	const LOGIN_TITLE	= "Online Timesheet(HK)";
	
	const SSKEY_PARAM	= "param";
	
	const LOGGED_IN		= "LOGGED IN";
	
	const LDAP_SERVER	= 'ldap://ibmx3200';
}
$GLOBALS['db_config']= array(
	"type"		=> "mysql",
	"host"		=> "localhost",
	"user"		=> "root",
	"password"	=> "password",
	"schema"	=> "timesheet",
	"charset"	=> "utf8",
	"prefix"	=> "mrbs_"
);
$GLOBALS['db_table'] = array(
	'func' 			=> $GLOBALS['db_config']['prefix'].'func',
	'user' 			=> $GLOBALS['db_config']['prefix'].'users',
	'user_role_rel' => $GLOBALS['db_config']['prefix'].'user_role_rel',
	'user_func_rel' => $GLOBALS['db_config']['prefix'].'user_func_rel',
	'role' 			=> $GLOBALS['db_config']['prefix'].'role',
	'role_func_rel' => $GLOBALS['db_config']['prefix'].'role_func_rel',
	'department' 	=> $GLOBALS['db_config']['prefix'].'department',
	'department_user'	=> $GLOBALS['db_config']['prefix'].'department_user',
	'permission' 	=> $GLOBALS['db_config']['prefix'].'permission',
	'room' 			=> $GLOBALS['db_config']['prefix'].'room',
	'entry' 		=> $GLOBALS['db_config']['prefix'].'entry',
	'holidays' 		=> $GLOBALS['db_config']['prefix'].'holidays',
	 
	'' => ''
);

$CSPORTAL_THEME = "default";

date_default_timezone_set('Asia/Hong_Kong');


require_once('../grab_globals.inc.php');
include "../config.inc.php";
include "../mrbs_auth.inc";
?>