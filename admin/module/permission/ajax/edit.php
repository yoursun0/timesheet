<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
################################################################################
# Prepare Data
################################################################################
	//Settings
	$system_fields		= array('id');
	
	//Get Submit Values & Validation
	CS_Form::getSubmit($system_fields);											//get system fields
	if (empty($id) || !ctype_digit($id)) {										//check id
		CS_Response::warning('invalid id');
	}
################################################################################
# Main Program
################################################################################
	if (false !== ($rs = $db->getRow("SELECT `id`,`room_name` as `name` FROM `{$GLOBALS['db_table']['room']}` WHERE `id`=$id"))) {
		$prefix = 'edit_form_';
		
		$rp['id'] 				= $rs['id'];
		$rp[$prefix.'name'] 	= $rs['name'];	
		
		//get permission
		$rp['view_ids[]'] 	= getPermissionByAction($db, $id, 'view');
		$rp['edit_ids[]'] 	= getPermissionByAction($db, $id, 'edit');
		$rp['approve_ids[]']= getPermissionByAction($db, $id, 'approve');
		$rp['report_ids[]'] = getPermissionByAction($db, $id, 'report');
		
		CS_Response::success('',$rp);
	} else {
		CS_Response::warning('record not found');
	}
################################################################################
# Functions
################################################################################
/**
 * Enter description here...
 *
 * @param CS_Database $db
 * @param int $id
 * @param string $action
 */
function getPermissionByAction($db, $id, $action) {
	static $permission = false;
	if (false === $permission) {
		$sql = "SELECT `action`,`target_id` FROM `{$GLOBALS['db_table']['permission']}` WHERE `room_id`=$id";
		foreach ($db->query($sql) as $row) {
			if (isset($permission[$row['action']])) {
				$permission[$row['action']][] = $row['target_id'];
			} else {
				$permission[$row['action']] = array($row['target_id']);				
			}
		}
	}
	
	
	return isset($permission[$action]) ? $permission[$action] : array();
}
?>