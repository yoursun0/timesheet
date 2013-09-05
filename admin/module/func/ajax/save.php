<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('action','id');
	$required_fields 	= array('key','name','status');
	$other_fields 		= array('description');
	
	//Get Submit Values & Validation
	CS_Form::getSubmit($system_fields);											//get system fields
	if (false === ($data = CS_Form::getSubmit($required_fields,true))) {		//get required fields
		CS_Response::error('required fields cannot be blank');
	}
	$data = array_merge($data,CS_Form::getSubmit($other_fields,false));			//get other fields
	if ($action == 'EDIT' && empty($id)) {										//check id
		CS_Response::error('no id');
	}
	$data['key'] = strtoupper($data['key']);
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	if ('CREATE' == $action) {													//create new record
		if (false !== ($insert_id = $db->autoExecuteInsert($GLOBALS['db_table']['func'],$data))) {
			CS_Response::success('',array('id'=>$insert_id));
		} else {
			CS_Response::warning($db->getErrorMsg());
		}
	} elseif ('EDIT' == $action) {												//update record
		if (false === $db->autoExecuteUpdate($GLOBALS['db_table']['func'],$data,"`id` = '$id'")) {
			CS_Response::warning('update fail');
		}
		CS_Response::success();
	} else {																	//unknown action
		CS_Response::error('unknown action');
	}
?>