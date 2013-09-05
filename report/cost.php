<?php
require_once "../grab_globals.inc.php";
include "../config.inc.php";
include "../functions.inc";
include "../$dbsys.inc";

session_start();

/*$action = isset($_GET['action']) ? $_GET['action'] : "";
$selected_user_id = isset($_GET['id']) ? $_GET['id'] : 0;
$start_time = isset($_POST['start']) ? $_POST['start'] : "";
$end_time = isset($_POST['end']) ? $_POST['end'] : "";
$cost = isset($_POST['cost']) ? $_POST['cost'] : 0.0;*/

if(isset($_POST['passwd'])){
	if($_POST['passwd'] == include "password.php") $_SESSION['logined'] = true;
}

?>
<html>
<head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
	<title>Time Cost List</title>
</head>
<body>
	<h1>Time Cost List</h1>
<?
if(!isset($_SESSION['logined'])){

echo '<form name="form1" method="post">Password: <input type="password" id="passwd" name="passwd" value=""></form>';

} else {
	
	$sql = "SELECT * FROM ".$db_tbl_prefix."cost ORDER BY `start_time`";
	if (false !== ($rs = mysql_query($sql))) {
		while($row = mysql_fetch_array($rs)){		
			$cost_list[$row['room_id']][] = $row;
		}
	} else{
		$cost_list = array();
	}
	
	

	$total_hour = 0.0;
	$jobs = array();
	$depts = array();
	$sql = "select id, area_name from ".$db_tbl_prefix."area order by sort_order";
	$rs = mysql_query($sql);
	while($r = mysql_fetch_array($rs)){
		$depts[$r['id']] = array("name"=>$r['area_name'], "hr"=>0.0, "job"=>array());
	}
	
	
	
	$users = array();
	$sql = "select r.id as id, r.room_name as u_name, a.id as dept_id from ".$db_tbl_prefix."room r left join ".$db_tbl_prefix."area a on (r.area_id = a.id) order by r.room_name";
	
	// echo "$sql";
	$rs = mysql_query($sql);
	
	
	while($r = mysql_fetch_array($rs)){
		$users[$r['id']] = array("name"=>$r['u_name'], "dept_id"=>$r['dept_id'], "hr"=>0.0, "job"=>array());
		
		echo "<p>";
		
		echo "<table width='750'>";
		echo "<tr>";
		echo "<td valign='top' colspan='4'><b><a name='bm{$r['id']}'>{$r['u_name']}</a></b>@{$depts[$r['dept_id']]['name']}";
		if (empty($cost_list[$r['id']])) {
			echo " - <font color='red'><b>No Record !!</b></font>";
		}
		
		echo "</td>";
	
		echo "</tr>";
		
		if (!empty($cost_list[$r['id']])) {
			foreach ($cost_list[$r['id']] as $cost_record) {
				$start_time = date('Y-m-d',$cost_record['start_time']);
				if ($cost_record['end_time'] ==2147483647) {					
					$end_time = "Now";
				} else {
					$end_time = date('Y-m-d',$cost_record['end_time']);					
				}
				
				echo "<tr>";
				echo "<td width='200' valign='top'>$start_time ~ $end_time</td>";
				echo "<td width='60' valign='top'>$".$cost_record['cost']."</td>";
				echo "<td valign='top'>".htmlspecialchars($cost_record['remarks'])."</td>";
				echo "<td width='300' valign='top'>
				[ <a href='cost_edit.php?action=edit&id={$cost_record['id']}'>Edit</a> ] 
				[ <a href='cost_edit.php?action=del&id={$cost_record['id']}'>Delete</a> ] ";
				
				if ($cost_record['end_time'] ==2147483647) {
					echo "[ <a href='cost_edit.php?action=update&id={$cost_record['id']}'>Change Cost</a> ]";
				}
				echo "</td>";
				echo "</tr>";
			}
		}
			echo "<tr><td colspan='4'><a href='cost_edit.php?action=add&id={$r['id']}'>Add Record.</a></td></tr>";
		
		echo "</table>";
		echo "</p>";
	}

}
?>

</body>
</html>