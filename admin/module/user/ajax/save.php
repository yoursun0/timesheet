<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	$required_fields 	= array('name','email');
	$other_fields 		= array();
	
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
	if ('CREATE' == $action) {													//create new record
		$data['password'] = empty($data['password']) ? md5($data['login']) : md5($data['password']);

		if (false !== ($insert_id = $db->autoExecuteInsert($GLOBALS['db_table']['user'],$data))) {
			
			updateRole($db,$insert_id,$role_ids);
			
			CS_Response::success('',array('id'=>$insert_id));
		} else {
			CS_Response::warning($db->getErrorMsg());
		}
	} elseif ('EDIT' == $action) {												//update record
		
/*		if (empty($data['password'])) {
			unset($data['password']);
		} else {
			$data['password'] = md5($data['password']);			
		}*/

		if (false === $db->autoExecuteUpdate($GLOBALS['db_table']['user'],$data,"`id` = '$id'")) {
			CS_Response::warning('update fail');
		}
		
		updateRole($db,$id,$role_ids);
		
		CS_Response::success();
	} else {																	//unknown action
		CS_Response::error('unknown action');
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