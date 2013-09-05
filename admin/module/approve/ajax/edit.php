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
	if (false !== ($rs = $db->getRow("SELECT `id`,`name`,`status` FROM `{$GLOBALS['db_table']['user']}` WHERE `id`=$id"))) {
		$prefix = 'edit_form_';
		
		$rp['id'] 				= $rs['id'];
		$rp[$prefix.'name'] 	= $rs['name'];
		
		//get department
		$sql = "SELECT `department_id` FROM `{$GLOBALS['db_table']['department_user']}` WHERE `user_id`=$id";
		$rp['self_ids[]'] = $db->getArray($sql,1);		
		
		//get permission list
		$sql = "SELECT * FROM `{$GLOBALS['db_table']['permission']}` WHERE `user_id`=$id LIMIT 1";
		$permission = $db->getRow($sql);
		if (empty($permission)) {
			$rp['view_ids[]'] = $rp['edit_ids[]'] = $rp['report_ids[]'] = array();
		} else {
			$rp['view_ids[]'] 	= CS_Basic::makeArray($permission['view']);
			$rp['edit_ids[]'] 	= CS_Basic::makeArray($permission['edit']);
			$rp['report_ids[]'] = CS_Basic::makeArray($permission['report']);
		}
		
		CS_Response::success('',$rp);
	} else {
		CS_Response::warning('record not found');
	}
?>