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
	if (false === ($rs = $db->getRow("SELECT `id`,`date`,`name` FROM `{$GLOBALS['db_table']['holidays']}` WHERE `id`=$id"))) {
		CS_Response::warning('record not found');
	} else {
		CS_Response::success('',$rs);
	}
?>