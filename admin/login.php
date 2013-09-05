<?php
require('config.php');
require('lib.php');

//CS_Form::getSubmit(array("login","pw","code"));

$message = "&nbsp;";

if (isset($_POST['action']) && $_POST['action']=='login') {
	$dbo = new CS_Database($GLOBALS['db_config']);
	$user = new CS_Auth($dbo);
	if (false === ($rs  = $user->login($_POST['login'],$_POST['pw']))) {
		$message = $user->errorInfo();
	} else {
		header('location:./');
		exit();
	}
}

$title		=CONFIG::TITLE ;
$charset	=CONFIG::PAGE_CHARSET ;
$login_title=CONFIG::LOGIN_TITLE ;
$login=		isset($_POST['login']) ? $_POST['login'] : '';

?>
<html>
    <head>
        <meta content="text/html; charset=<?=$charset?>" http-equiv="content-type">
		<link type="text/css" rel="stylesheet" href="include/page/login/login.css" />
        <title><?=$title?></title>
    </head>
    <body>
        <table style="height:100%;width:100%" cellpadding="0" cellspacing="0" summary="">
            <tr>
                <td valign="top">
                    <img  src="include/page/login/logo.gif" align="top" alt="Logo" style="padding:3px">
                </td>
            </tr>
            <tr style="height:14px;background-image:url(include/page/login/top.gif)">
                <td>
                </td>
            </tr>
            <tr style="height:30px;background-color:#F5F5F5;">
                <td valign="top"><?=$message?></td>
            </tr>
            <tr style="height:100%;background-color:#F5F5F5;">
                <td align="center" valign="middle">
                    <table style="width:80%" summary="">
                        <tr>
                            <td style="border-right:1px solid #ccc">
                                <p style="font-size:30px">
                                    <?=$login_title?>
                                </p>
                            </td>
                            <td style="width:250px">
                                <form name="login_form" id="login_form" method="post" action="">
                                    <label for="login">
                                    	Name
                                    </label>
                                    <input type="text" id="login" name="login" value="<?=$login?>" maxlength="20" />
                                    <label for="pw">
                                        Password
                                    </label>
                                    <input type="password" id="pw" name="pw" maxlength="20" />
                                    <input type="hidden" name="action" value="login" />
                                    <div style="width:100%;text-align:center">
                                        <input id="submit_button" type="submit" value="Login"/>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="height:14px;background-image:url(include/page/login/bottom.gif)">
                <td>
                </td>
            </tr>
            <tr style="height:24px;">
                <td>
                </td>
            </tr>
        </table>
        <script type="text/javascript" src="include/control/core/jquery-1.2.6.js"></script>
        <script type="text/javascript">
            $(function(){
                if ($("#login").val() == "") setTimeout('$("#login").focus();', 0);
                else setTimeout('$("#pw").focus();', 0);
            });
        </script>
    </body>
</html>