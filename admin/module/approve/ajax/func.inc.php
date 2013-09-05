<?php
	function processSelectedDate($selected_date) {
		if (empty($selected_date)) {
			if (isset($_SESSION['selected_date']) && !empty($_SESSION['selected_date'])) {
				return $_SESSION['selected_date'];
			} else {
				$now_timestamp = time();
				return $now_timestamp - (date('N') - 1) * 86400 - ($now_timestamp % 86400);
			}
		}
		return $selected_date;
	}
	function processSelectedDepartment($selected_department,$departments,$permissions,$action='view') {
		if (empty($selected_department)) {
			if (isset($_SESSION['selected_department']) && !empty($_SESSION['selected_department'])) {
				return $_SESSION['selected_department'];
			} else {
				$permit = explode(',',$permissions[$action]);
				foreach (array_keys($departments) as $id) {
					if (in_array($id,$permit)) {
						return $id;
					}
				}
				return 0;
			}
		}
		return $selected_department;
	}
	function processSelectedUser($department_members,$selected_user) {
		if (empty($department_members)) {
			return 0;
		}
		if (empty($selected_user)) {
			if (isset($_SESSION['selected_user']) && !empty($_SESSION['selected_user'])) {
				return $_SESSION['selected_user'];
			} else {
				return current(array_keys($department_members));
			}
		}
		return $selected_user;
	}

	function getActiveDepartments($dbo) {
		$sql = "SELECT `id`,`name` FROM `{$GLOBALS['db_table']['department']}` WHERE `status`=1 ORDER BY `name`";
		return $dbo->getArray($sql,2);
	}
	function getDepartmentMembers($db,$department) {
		if (empty($department) || !is_numeric($department)) {
			return array();
		}
		
		$sql = "SELECT `u`.`id`,`u`.`name` FROM `{$GLOBALS['db_table']['department_user']}` `du`,`{$GLOBALS['db_table']['user']}` `u` 
		WHERE `u`.`status`='A' AND `du`.`department_id` =$department AND `du`.`user_id`=`u`.`id`";

		return $db->getArray($sql,2);
	}
	function getUserPermissions($db,$user_id) {
		return $db->getRow("SELECT * FROM `{$GLOBALS['db_table']['permission']}` WHERE `user_id`=$user_id");
	}
	function getEvents($db,$start,$end,$user_id) {
		$sql = "SELECT CONCAT(`user_id`,'_',`start_time`) as `key`,`e`.* FROM `{$GLOBALS['db_table']['entry']}` `e`
			WHERE (`start_time` BETWEEN $start AND $end)";
		$sql;
		return $db->getArray($sql,100);
	}
	
	
	function printDepartments($selected_date,$departments,$selected_department,$view) {
		foreach ($departments as $id=>$name) {
			if ($id == $selected_department) {
				echo "<b>";
			}
			echo "<a href='javascript:;' onclick=\"view($selected_date,$id,0,'$view')\">$name</a>";
			if ($id == $selected_department) {
				echo "</b>";
			}
			echo "<br/>\n";
		}
	}
	function printUsers($selected_date,$users,$selected_user,$department,$view) {
		foreach ($users as $id=>$name) {
			if ($id == $selected_user) {
				echo "<b>";
			}
			echo "<a href='javascript:;' onclick=\"view($selected_date,$department,$id,'$view');\">$name</a>";
			if ($id == $selected_user) {
				echo "</b>";
			}
			echo "<br/>\n";
		}
	}
?>