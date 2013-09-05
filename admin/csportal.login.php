<?php
include_once("csportal.lib.php");
if (isset($_GET['key'])) {
	list($csportal_portal_ssid,$csportal_portlet_id) = csportal_AnalysisSessionKey($_GET['key']);
	$csportal_portlet_ssid = csportal_InitializeSession(CONFIG::NAME ,$csportal_portlet_id);
	if (false === csportal_GetLoginStatus()) {
		return csportal_Login(CONFIG::PORTAL_URL ,$csportal_portal_ssid,$csportal_portlet_ssid, $csportal_portlet_id);
	}
} elseif (isset($_GET['pid'])) {
	$csportal_portlet_id = $_GET['pid'];
	$csportal_portlet_ssid = csportal_InitializeSession(CONFIG::NAME ,$csportal_portlet_id);
} else {
}
return csportal_GetParam();
?>