<?php
   function internal_login($login_id, $passwd) {
	// see if session is expired, or has been logged out
	$auth = false; // default to unauthorized
	
	if (empty($login_id) || empty($passwd)) {
		$GLOBALS['errmsg'] = mkerror("Login name and password cannot be blank");
		return false;
	}
	
	$login_id = strtoupper($login_id);
	$acl = array(
		'user_id' => '',
		'login_id'  => $login_id,
		'user_name' => '',
		'row_status' => '',
		'roles' => array(),
		'funcs' => array()
	);

	
	// use database to store the password
	$passwd = md5($passwd);
	$sql = "SELECT * from user WHERE login_id = '"._addslashes($login_id)."' AND passwd = '$passwd'";
	$accres = mysql_query( $sql );
	if(!$accres) {
		header( 'HTTP/1.0 503 '. _('Service Unavailable'));
		echo('<html><head><title>'. _('Service Unavailable') .
			'</title></head><body><h1>HTTP 503 '.
			_('Service Unavailable') .'</h1>'.
			mkerror(_('Unable to load ACL.') .' ('. mysql_error() .')') .
			'</body></html>');
		return(false);
	}
	
	if (mysql_num_rows( $accres ) > 0) {
		$auth = true;
	} else {
		$GLOBALS['errmsg'] = mkerror('Login Fail');
		return(false);
	}
	
	
	/*
	// use ldap to store the password
	include ("adLDAP.php");
	$adldap = new adLDAP();
	//authenticate the user
	if ($adldap -> authenticate("cshk\\".$login_id, $passwd)){
		$auth = true;
       	$sql = "SELECT * from user WHERE login_id = '"._addslashes($login_id)."' AND row_status='A'";
		$accres = mysql_query( $sql );
	} else {
		$GLOBALS['errmsg'] = mkerror('Login Fail');
		return(false);
	}
	*/
	
	while( $arr = mysql_fetch_array($accres, MYSQL_ASSOC) ) {
		$acl['user_id'] = $arr['user_id'];
		$acl['login_id'] = $arr['login_id'];
		$acl['user_name'] = $arr['user_name'];
		$acl['row_status'] = $arr['row_status'];
	}
	mysql_free_result($accres);

	if ( ! $auth ) {
		session_unset();
		$GLOBALS['errmsg'] = mkerror("No right to access");
		return(false);
	}
		
	// if one were to want login accounting (logs) this
	// would be the ideal place to do so...
	
	$acl['roles'] = sql_to_array("select role_id from user_role_rel where user_id = '".$acl['user_id']."'", 1);
	$acl['funcs'] = sql_to_array("select f.func_id from user_role_rel u, role_func_rel f where u.role_id = f.role_id and u.user_id='".$acl['user_id']."'", 1);

	$GLOBALS['HTTP_SESSION_VARS']['acl'] =& $acl;
	// $Breadcurmbs = array();
	// $GLOBALS['HTTP_SESSION_VARS']['Breadcurmbs'] =& $Breadcurmbs;
	
	return(true);
}

function checkAccess($func_id, $outputErrorMsg = false, $matchAny = true){
	$funcs = $_SESSION['acl']['funcs'];
	if(!in_range($func_id, $funcs, $matchAny)){
		if($outputErrorMsg)	auth_no_access("to access this page");
		return false;
	}
	return true;
}
/*
function access_check($role, $outputErrorMsg){
	if($GLOBALS['isSuperUser']) return true;
	
	if($role == 'admin'){
		if($GLOBALS['isAdmin']){
			return true;
		} else {
			//$GLOBALS['errmsg'] = "<p>&nbsp;</p>".mkerror("e-Survey Administrator only");
			if($outputErrorMsg)	auth_no_access("to access this page");
			return false;
		} 
	}
	
	return false;
}
*/
function auth_no_access($description) {
	echo("<p>&nbsp;</p>".mkerror(_('This account does not have permission') .' '. $description .'.'));
	echo("\n<p>&nbsp;</p>\n");
	// echo("<a href=\"". $GLOBALS['CONFIG']['ME'] ."?where=manage\">" . _('Go back to Management Interface') . "</a>\n");
	return false;
}

?>
