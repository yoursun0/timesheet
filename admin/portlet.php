<?php
set_time_limit(3);
include_once("config.php");
if (false === ($portlet = include_once("csportal.login.php"))) {
	die("please login first");
}
include_once('lib.php');

define('__VALID_REQUEST__',true);

$selected_page = CS_Form::getGet("tab","home");
$db = new CS_Database($GLOBALS['db_config']);


$PATH_THEME = "theme/default";
$PATH_MODULE_STYLE 	= "module/$selected_page/style/";

$MENU = array(
	"home"=>"Home",
	"screening"=>"Screening",
	"progress_update"=>"Progress Update",
	"job"=>"Jobs",																//manage jobs
	"recruiter"=>"Recruiters",													//magage recruiters
	"user"=>"Users",															//manage internal users
	"help"=>"Helps"
);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title><?=CONFIG::TITLE?></title>
	<link type="text/css" rel="stylesheet" href="theme/default/common.css" />
	<link type="text/css" rel="stylesheet" href="theme/default/jquery.checktree.css" />
	<link type="text/css" rel="stylesheet" href="theme/default/jquery.boxy.css" />
	<link type="text/css" rel="stylesheet" href="<?=$PATH_MODULE_STYLE?>/style.css" />
</head>
<body>
<div id="divLoading" class="csportal_loading_32" style="display:none;position:absolute;left:0;top:0;background-color:#fff;border:1px solid red;z-index:9999;">Loading ...</div>
<div id="csportal_main_menu">
<?php
echo '<ul class="csportal_basictab">';
foreach ($MENU as $tab_name=>$title) {
	echo "<li".($tab_name==$selected_page ? " class='selected'" : "")."><a title='$title' href='?pid=".$portlet['id']."&tab=$tab_name'>$title</a></li>";
}
echo '</ul>';
?>
</div>
<div id="csportal_body">
<?php
if (file_exists("module/$selected_page/index.php")) {
	include("module/$selected_page/index.php");
} else {
	echo "function not found.";
}
?>
</div>
	<script type="text/javascript" src="js/core/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/core/jquery-ui-1.7.custom.min.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.metadata.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.validate.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.updateWithJSON.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.boxy.js"></script>
	<script type="text/javascript" src="js/plugin/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="js/core/common.js"></script>
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