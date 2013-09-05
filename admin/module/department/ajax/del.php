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
	if (false === ($rs = $db->executeDelete("DELETE FROM `{$GLOBALS['db_table']['department']}` WHERE `id`=$id"))) {
		CS_Response::warning('record not found');
	} else {
		CS_Response::success();
	}
?>