<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	
	//Get Submit Values & Validation
	$self_ids 			= CS_Form::getPost('self_ids',array());					//get self departments
	$view_ids 			= CS_Form::getPost('view_ids',array());					//get view permission ids
	$edit_ids 			= CS_Form::getPost('edit_ids',array());					//get edit permission ids
	$report_ids 		= CS_Form::getPost('report_ids',array());				//get report permission ids

	CS_Form::getSubmit($system_fields);											//get system fields

	if ($action == 'EDIT' && empty($id)) {										//check id
		CS_Response::error('no id');
	}
	$data['user_id'] = $id;
	$data['view'] 	= join(',',$view_ids);
	$data['edit'] 	= join(',',$edit_ids);
	$data['report'] = join(',',$report_ids);
	$data['status'] = 1;
	
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	if ('CREATE' == $action) {													//create new record
		//no function
	} elseif ('EDIT' == $action) {												//update record

		if (false === $db->autoExecuteReplace($GLOBALS['db_table']['permission'],$data)) {
			CS_Response::warning('update fail');
		}
		
		updateDepartment($db,$id,$self_ids);
		
		CS_Response::success();
	} else {																	//unknown action
		CS_Response::error('unknown action');
	}
//------------------------------------------------------------------------------
// Common Function
//------------------------------------------------------------------------------
function updateDepartment($db,$key_id,$ids) {
	$sql = "DELETE FROM `{$GLOBALS['db_table']['department_user']}` WHERE `user_id`=$key_id";
	$db->executeDelete($sql);
	
	foreach ($ids as $id) {
		$db->autoExecuteInsert($GLOBALS['db_table']['department_user'],array(
			'user_id'=>$key_id,
			'department_id'=>$id
		));
	}
}
?>