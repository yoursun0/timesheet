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
	if (false !== ($rs = $db->getRow("SELECT `id`,`name`,`email` FROM `{$GLOBALS['db_table']['user']}` WHERE `id`=$id"))) {
		
		$sql = "SELECT `role_id` FROM `{$GLOBALS['db_table']['user_role_rel']}` WHERE `user_id`=$id";
		$rs['role_id[]'] = $db->getArray($sql,1);

		CS_Response::success('',$rs);
	} else {
		CS_Response::warning('record not found');
	}
?>