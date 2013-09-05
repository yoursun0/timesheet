<?php
include_once('config.php');
include_once('lib.php');

if (!isset($_SESSION)) {
	session_start();
}

//check login session
if (!isset($_SESSION[CONFIG::LOGIN_FLAG]) || empty($_SESSION[CONFIG::LOGIN_FLAG])) {
	die('permission denied');
}

//get user information and acl
$current_user = new CS_User($_SESSION['user']);

define('__VALID_REQUEST__',true);

$module = &$_GET['m'];
$func 	= &$_GET['fn'];

if (isset($func,$module)) {
	if (empty($module)) {
		$path = "ajax/$func.php";
	} else {
		$path = "module/$module/ajax/$func.php";
	}
} else {
	die("unknown request");
}

if (file_exists($path)) {
	$db = new CS_Database($GLOBALS['db_config']);
	include($path);
} else {
	die("unknown request");
}
?>