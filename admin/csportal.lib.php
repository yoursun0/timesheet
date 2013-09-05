<?php
function csportal_AnalysisSessionKey($key){
	if (32 >= strlen($key)) {
		die("csportal_AnalysisSessionKey::please login portal frist");
	} else {
		return array(substr($key,0,32), substr($key,32));
	}
}
function csportal_InitializeSession($portlet_name,$portlet_id,$prefix="PHPSID_CSPORTAL_"){
	session_name($prefix.$portlet_name."_".$portlet_id);
	session_start();
	return session_id();
}
function csportal_GetParam(){
	return (isset($_SESSION[CONFIG::SSKEY_PARAM])?$_SESSION[CONFIG::SSKEY_PARAM]:false);
}
function csportal_GetLoginStatus(){
	return (isset($_SESSION[CONFIG::LOGIN_FLAG])?$_SESSION[CONFIG::LOGIN_FLAG]:false);
}
function csportal_Login($targer_url,$portal_ssid,$portlet_ssid,$portlet_id){
	$client = new SoapClient(null,array('location'=>CONFIG::PORTAL_SSO_URL ,'uri'=>""));
	if (false === ($rs = $client->login($portal_ssid,$portlet_ssid,$portlet_id))) {
		return false;
	} else {
		$_SESSION[CONFIG::LOGIN_FLAG] = LOGIN_MODE::SSO;
		return $_SESSION[CONFIG::SSKEY_PARAM] = $rs;
	}
}
function csportal_Logout($id){
	if (32 === strlen($id)) {
		session_id($id);
		session_start();
		session_destroy();
		return (!isset($_SESSION));
	} 
	return false;
}
?>