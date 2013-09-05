<?php
if (isset($_GET['id'])) {
	include("csportal.lib.php");
	return csportal_Logout($_GET['id']);	
} else {
	return false;
}
?>