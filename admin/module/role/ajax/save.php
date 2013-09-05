<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	$required_fields 	= array('name','status');
	$other_fields 		= array('description');
	
	//Get Submit Values & Validation
	$func_ids 			= CS_Form::getPost('func_id',array());					//get function id
	
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
		if (false !== ($insert_id = $db->autoExecuteInsert($GLOBALS['db_table']['role'],$data))) {
			
			updateFunc($db,$insert_id,$func_ids);
			
			CS_Response::success('',array('id'=>$insert_id));
		} else {
			CS_Response::warning($db->getErrorMsg());
		}
	} elseif ('EDIT' == $action) {												//update record
		
		if (empty($id) || !is_numeric($id)) {									//check id
			CS_Response::warning('invalid id');
		}
		
		if (false === $db->autoExecuteUpdate($GLOBALS['db_table']['role'],$data,"`id` = '$id'")) {
			CS_Response::warning('update fail');
		}
		
		updateFunc($db,$id,$func_ids);

		CS_Response::success();
	} else {																	//unknown action
		CS_Response::error('unknown action');
	}
//------------------------------------------------------------------------------
// Common Function
//------------------------------------------------------------------------------
function updateFunc($db,$key_id,$ids) {
	$sql = "DELETE FROM `{$GLOBALS['db_table']['role_func_rel']}` WHERE `role_id`=$key_id";
	$db->executeDelete($sql);
	
	foreach ($ids as $id) {
		$db->autoExecuteInsert($GLOBALS['db_table']['role_func_rel'],array(
			'role_id'=>$key_id,
			'func_id'=>$id
		));
	}
}
?>