<?php

function error($message) {
	die($message);
}

function getParameters($fields, $default = '') {
	foreach ($fields as $f) {
		global $$f;
		if (isset($_GET[$f])) {
			 $$f = $_GET[$f];
		} else {
			 $$f = $default;
		}
	}
}

function getApprovedDate($room) {
	global $tbl_approve;
	if (ctype_digit($room)) {
		$sql = "SELECT `approved_timestamp` as `time`, `approved_by` as `user` FROM $tbl_approve WHERE `room_id` = $room";
		if ($rs = mysql_query($sql)) {
			$row = mysql_fetch_assoc($rs);
			mysql_free_result($rs);
			return $row;
		}
	}
	return array();
}

function getPermission() {
	global $tbl_room, $tbl_permission;
	if (isset($_SESSION['permission']) && isset($_SESSION['DefaultRoomID'])) {
		return ;
	}

	$sql = "SELECT `action`,`target_id`,`area_id` FROM $tbl_permission p, $tbl_room r 
			WHERE r.id=p.target_id AND p.room_id= {$_SESSION['DefaultRoomID']}";
	
	$room_pm = $area_pm = array();
	if ($result = sql_query($sql)) {
		while ($row = mysql_fetch_assoc($result)) {
			$area_pm[$row["area_id"]] = true;
			
			$room_pm[$row["target_id"]][$row["action"]] = true;
		}
	}
	
	// $area_pm[$_SESSION['DefaultAreaID']][] = true;
	$area_pm[$_SESSION['DefaultAreaID']] = true;
	$room_pm[$_SESSION['DefaultRoomID']]['view'] = true;
	$room_pm[$_SESSION['DefaultRoomID']]['edit'] = true;

	$_SESSION['permission']['area'] = $area_pm;
	$_SESSION['permission']['room'] = $room_pm;

	
	//var_dump($result);
}


function access($id, $action = false) {
	if (false === $action) {
		return isset($_SESSION['permission']['area'][intval($id)]);
	} else {
		return isset($_SESSION['permission']['room'][intval($id)][strtolower($action)]);
	}	
}
?>