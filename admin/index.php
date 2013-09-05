<?php
//set_time_limit(3);
include_once("config.php");

//verify login status
/*if (false === ($portlet = include_once("csportal.login.php"))) {
	if (!isset($_SESSION)) {
		session_start();
	}
	if (!isset($_SESSION[CONFIG::LOGIN_FLAG]) || $_SESSION[CONFIG::LOGIN_FLAG] != LOGIN_MODE::LOCAL) {
		header('location: login.php');
		exit();
	}
}*/
include_once('lib.php');



//create database connection
$db = new CS_Database($GLOBALS['db_config']);

if (isset($_SESSION['UserName'])) {
	if (!isset($_SESSION[CONFIG::LOGIN_FLAG])) {
		$user = new CS_Auth($db);
		$user->timesheetLogin();
		
	}
	//header('location: login.php');
} else {
	exit();
}
/*echo '<pre>OUT
';
var_dump($_SESSION);
echo '</pre>';*/

define('__VALID_REQUEST__',true);

//get user information and acl
$current_user = new CS_User($_SESSION['user']);


//get selected page from url
$selected_page = CS_Form::getGet("tab","permission");

//settings
$PATH_THEME = "theme/default";
$PATH_MODULE_STYLE 	= "module/$selected_page/style/";
$MENU = array(
	'holidays'=>'Holidays',
	'department'=>'Department',
	'permission'=>'Permission',
	'func'=>'Functions',
	'role'=>'Roles',
	'user'=>'Users',
	'help'=>'Helps'
);

//output
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title><?=CONFIG::TITLE?></title>
	<link type="text/css" rel="stylesheet" href="theme/default/common.css" />
	<link type="text/css" rel="stylesheet" href="theme/default/jquery-ui-1.7.1.custom.css" />	
	<link type="text/css" rel="stylesheet" href="theme/default/jquery.checktree.css" />
	<link type="text/css" rel="stylesheet" href="theme/default/jquery.boxy.css" />
	<link type="text/css" rel="stylesheet" href="<?=$PATH_MODULE_STYLE?>style.css" />
</head>
<body>
<div id="divLoading" class="csportal_loading_32" style="display:none;position:absolute;left:0;top:0;background-color:#fff;border:1px solid red;z-index:9999;">Loading ...</div>
<div id="csportal_main_menu">
	<ul class="csportal_basictab">
<?php
foreach ($MENU as $tab_name=>$title) {
	if ($current_user->isAllowedKey('PAGE::'.trim($tab_name))) {
		if ($_SESSION[CONFIG::LOGIN_FLAG] == LOGIN_MODE::LOCAL ) {
			echo "<li".($tab_name==$selected_page ? " class='selected'" : "")."><a title='$title' href='?tab=$tab_name'>$title</a></li>";
		} else {
			echo "<li".($tab_name==$selected_page ? " class='selected'" : "")."><a title='$title' href='?pid=".$portlet['id']."&tab=$tab_name'>$title</a></li>";
		}
	}
}
/*if ($_SESSION[CONFIG::LOGIN_FLAG] == LOGIN_MODE::LOCAL) {
	echo "<li><a title='Logout' href='logout.php' style='right:10px;position:absolute;top:10px;' >Logout</a></li>";
}*/
?>
	</ul>
</div>
<div id="csportal_body">
<?php
$main_page_path = "module/$selected_page/index.php";
if (file_exists($main_page_path)) {
	include($main_page_path);
} else {
	echo "function not found.";
}
?>
</div>
	<script type="text/javascript" src="js/core/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/core/jquery-ui-1.7.1.custom.min.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.metadata.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.validate.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.updateWithJSON.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.boxy.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="js/core/common.js"></script>
	
<?php
	if (!empty($GLOBALS['js'])) {
		foreach ($GLOBALS['js'] as $link) {
			$link = str_replace(array('<MOD>','<MODULE>'),"module/$selected_page/",$link);
			echo '<script type="text/javascript" src="'.$link.'"></script>';
		}
	}
	if (!empty($GLOBALS['css'])) {
		foreach ($GLOBALS['css'] as $link) {
			$link = str_replace(array('<MOD>','<MODULE>'),"module/$selected_page/style/",$link);
			echo '<link rel="stylesheet" href="'.$link.'" type="text/css">';
//			echo '<style type="text/css">';
//			echo $link;
//			readfile($link);
//			echo '</style>';
		}
	}
?>

	<script type="text/javascript">
    $(function(){
        $("#divLoading").ajaxStart(function(){
            $(this).show();
        }).ajaxStop(function(){
            $(this).hide();
        });
    });
<?php
if (file_exists("module/$selected_page/index.js")) {
	//echo '<script type="text/javascript">';
	readfile("module/$selected_page/index.js");
	//echo '</script>';
}
?>
	</script>
</body>
</html>