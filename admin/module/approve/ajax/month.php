<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	
	$selected_department 	= CS_Form::getPost('g',0);
	$selected_user 			= CS_Form::getPost('u',0);
	$selected_date			= CS_Form::getPost('d',0);
	$selected_view			= CS_Form::getPost('v','month');
	
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	include('func.inc.php');

	$view = 'month';
	$permissions = getUserPermissions($db,$current_user->getId());
	if (empty($permissions)) {
		die('access denied');
	}

	$departments = getActiveDepartments($db);

	$selected_date = processSelectedDate($selected_date);
	
	$selected_department = processSelectedDepartment($selected_department,$departments,$permissions,'view');
 	
	$department_members = getDepartmentMembers($db,$selected_department);
	
	$selected_user = processSelectedUser($department_members,$selected_user);

	$_SESSION['selected_date'] = $selected_date;
	$_SESSION['selected_department'] = $selected_department;
	$_SESSION['selected_user'] = $selected_user;
	$_SESSION['selected_view'] = $selected_view;

	$start_time = strtotime(date('Y-m-01',$selected_date));
	$end_time	= $start_time + date('t',$selected_date) * 86400 - 1;
	$events = getEvents($db,$start_time,$end_time,$selected_user);
	//$rowspan = array_fill(0,7,0);

	//CS_Debug::printArray($events);
//------------------------------------------------------------------------------
// Print Output
//------------------------------------------------------------------------------

//CS_Debug::printArray($events);