<?php
include_once("../../config.php");
include_once('../../lib.php');

class uac {
	function login($login,$pw){
		global $DB_CONFIG;
		$db = new CS_Database($DB_CONFIG);

		$row = $db->getRow("SELECT * FROM `recruiter` WHERE `id`='$login' AND `pw`='".md5($pw)."'");

		if (!empty($row)) {
			$db->execute("UPDATE `recruiter` SET `last_login` = now() WHERE `id` = ".$row['id']);
			return $row;
		}
		return false;
	}
}

$server = new SoapServer(null,array('uri'=>""));
$server->setClass('uac');
$server->handle();
?>