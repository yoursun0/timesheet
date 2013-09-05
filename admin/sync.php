<?php
include_once("config.php");
include_once("lib.php");
//connect db
$db = new CS_Database($GLOBALS['db_config']);

$fs = array('id','start_time','end_time','entry_type','repeat_id','room_id','timestamp','create_by','name','type','description');


$sql = "SELECT `room_id`,`id` FROM `mrbs_users`";
$INDEX_ROOM_USER = $db->getArray($sql,2);

//handle cs_entry
$sql = "SELECT * FROM `cs_entry`";
foreach ($db->getArray($sql) as $row) {
	if (isset($INDEX_ROOM_USER[$row['room_id']])) {	
		$data = array(
			'user_id'=>$INDEX_ROOM_USER[$row['room_id']],
			'duration'=>$row['end_time'] - $row['start_time']
		);
		$db->autoExecuteUpdate('cs_entry',$data,"`id`={$row['id']}");
	} else {
		echo "[error] user not found id={$row['id']}, create by={$row['create_by']}<br/>\n";
		continue;
	}
}

//handle cs_repeat
$sql = "SELECT * FROM `cs_repeat`";
foreach ($db->getArray($sql) as $row) {
	if (isset($INDEX_ROOM_USER[$row['room_id']])) {	
		$data = array(
			'user_id'=>$INDEX_ROOM_USER[$row['room_id']]
		);
		$db->autoExecuteUpdate('cs_repeat',$data,"`id`={$row['id']}");
	} else {
		echo "[error] user not found id={$row['id']}, create by={$row['create_by']}<br/>\n";
		continue;
	}
}
?>