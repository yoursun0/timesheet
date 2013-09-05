<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	$required_fields 	= array();
	$other_fields 		= array('date');
	
	//Get Submit Values & Validation
	$role_ids 			= CS_Form::getPost('role_id',array());					//get role id
	
	CS_Form::getSubmit($system_fields);											//get system fields
	if (false === ($data = CS_Form::getSubmit($required_fields,true))) {		//get required fields
		CS_Response::error('required fields cannot be blank');
	}
	$data = array_merge($data,CS_Form::getSubmit($other_fields,false));			//get other fields
	if ($action == 'EDIT' && empty($id)) {										//check id
		CS_Response::error('no id');
	}

//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	include('func.inc.php');
	switch ($action) {
		case 'DATE_CHANGED':
			$_SESSION['selected_date'] = $date;
			//$_SESSION['selected_date']['d'] = date("d",$date);
			//$_SESSION['selected_date']['m'] = date("m",$date);
			//$_SESSION['selected_date']['y'] = date("Y",$date);
			
			$start_time = $date;
			$end_time 	= $date + (86400*7) - 1;
			$sql = "SELECT * FROM `{$GLOBALS['db_table']['entry']}` WHERE `user_id`=".$current_user->getId().
				" AND (`start_time` BETWEEN $start_time AND $end_time)";
			if (false !== ($rs = $db->getArray($sql))) {
				$rp = array();
				foreach ($rs as $row) {					
					$rp[] = formatDatabaseEvent($row);
				}			
				
				CS_Response::success('',$rp);
			} else {
				CS_Response::error('sql error');
			}
			
			
			
			break;
	}
//------------------------------------------------------------------------------
// Common Function
//------------------------------------------------------------------------------
function updateRole($db,$key_id,$ids) {
	$sql = "DELETE FROM `{$GLOBALS['db_table']['user_role_rel']}` WHERE `user_id`=$key_id";
	$db->executeDelete($sql);
	
	foreach ($ids as $id) {
		$db->autoExecuteInsert($GLOBALS['db_table']['user_role_rel'],array(
			'user_id'=>$key_id,
			'role_id'=>$id
		));
	}
}
?>