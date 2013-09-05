<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	$required_fields 	= array('name','start_time','end_time');
	$other_fields 		= array('description');
	
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
	
	$data['name'] = trim($data['name']);
	$data['name'] = strtoupper($data['name']);
	if (strpos($data['name'],'J') === 0) {
		$data['type'] = 'A';
	} elseif (is_numeric($data['name'])) {
		$data['type'] = 'B';
	} else {
		$data['type'] = 'Z';		
	}
	
	$data['duration'] = $data['end_time'] - $data['start_time'];
	$data['description'] = htmlspecialchars_decode($data['description']);
	
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	include('func.inc.php');
	
	if ('CREATE' == $action) {													//create new record
		$data['create_by'] = $current_user->getName();
		$data['user_id'] = $current_user->getId();
				
		if (false !== ($insert_id = $db->autoExecuteInsert($GLOBALS['db_table']['entry'],$data))) {
			
			$sql = "SELECT * FROM `{$GLOBALS['db_table']['entry']}` WHERE `id`=$insert_id";
			if (false !== ($row = $db->getRow($sql))) {
				$rp = formatDatabaseEvent($row);
				CS_Response::success('',$rp);
			} else {
				CS_Response::error('create failure');
			}
		} else {
			CS_Response::warning($db->getErrorMsg());
		}
	} elseif ('EDIT' == $action) {												//update record
		if (false === $db->autoExecuteUpdate($GLOBALS['db_table']['entry'],$data,"`id` = '$id'")) {
			CS_Response::error('update failure');
		}		
		
		$sql = "SELECT * FROM `{$GLOBALS['db_table']['entry']}` WHERE `id`=$id";
		if (false !== ($row = $db->getRow($sql))) {
			$rp = formatDatabaseEvent($row);
			CS_Response::success('',$rp);
		} else {
			CS_Response::error('update failure');
		}

	} else {																	//unknown action
		CS_Response::error('unknown action');
	}
?>