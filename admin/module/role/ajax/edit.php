<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	$system_fields		= array('id');
	
	//Get Submit Values & Validation
	CS_Form::getSubmit($system_fields);											//get system fields
	if (empty($id) || !is_numeric($id)) {										//check id
		CS_Response::warning('invalid id');
	}
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	if (false !== ($rs = $db->getRow("SELECT `id`,`name`,`description`,`status` FROM `{$GLOBALS['db_table']['role']}` WHERE `id`=$id"))) {
		
		$sql = "SELECT `func_id` FROM `{$GLOBALS['db_table']['role_func_rel']}` WHERE `role_id`=$id";
		$rs['func_id[]'] = $db->getArray($sql,1);
		
		CS_Response::success('',$rs);
	} else {
		CS_Response::warning('record not found');
	}
?>