<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	
	//Get Submit Values & Validation
	$view_ids 			= CS_Form::getPost('view_ids',array());					
	$edit_ids 			= CS_Form::getPost('edit_ids',array());					
	$approve_ids 		= CS_Form::getPost('approve_ids',array());				
	$report_ids 		= CS_Form::getPost('report_ids',array());				

	CS_Form::getSubmit($system_fields);											//get system fields

	if ($action == 'EDIT' && !ctype_digit($id)) {								//check id
		CS_Response::error('no id');
	}
	
	$data = array();
	parsePermission($data, $id, 'view', $view_ids);
	parsePermission($data, $id, 'edit', $edit_ids);
	parsePermission($data, $id, 'approve', $approve_ids, false);
	parsePermission($data, $id, 'report', $report_ids, false);
	
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	if ('CREATE' == $action) {													//create new record
		//no function
	} elseif ('EDIT' == $action) {												//update record
		
		updatePermission($db,$id,$data);
		
		CS_Response::success();
	} else {																	//unknown action
		CS_Response::error('unknown action');
	}
//------------------------------------------------------------------------------
// Common Function
//------------------------------------------------------------------------------
function updatePermission($db,$key_id,&$data) {
	$sql = "DELETE FROM `{$GLOBALS['db_table']['permission']}` WHERE `room_id`=$key_id";
	$db->executeDelete($sql);
	
	foreach ($data as $row) {
		$db->autoExecuteInsert($GLOBALS['db_table']['permission'],$row);
	}
}

function parsePermission(&$arr,$userId,$action,$ids, $autoPM = true) {
	if (true == $autoPM && !in_array($userId, $ids)) {
		array_push($ids, $userId);
	}
	foreach ($ids as $targetId) {
		$arr[] = array(
			'room_id'	=> $userId,
			'action'	=> $action,
			'target_id'	=> $targetId
		);
	}
}
?>