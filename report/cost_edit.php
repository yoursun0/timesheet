<?
require_once "../grab_globals.inc.php";
include "../config.inc.php";
include "../functions.inc";
include "../$dbsys.inc";

session_start();
ob_start();

$action = isset($_GET['action']) ? $_GET['action'] : "";
$selected_id = isset($_GET['id']) ? $_GET['id'] : 0;
$start_time = isset($_POST['start']) ? $_POST['start'] : "";
$end_time = isset($_POST['end']) ? $_POST['end'] : "";
$cost = isset($_POST['cost']) ? $_POST['cost'] : 0.0;
$remark = isset($_POST['remark']) ? $_POST['remark'] : "";

if(isset($_POST['passwd'])){
	if($_POST['passwd'] == include "password.php") $_SESSION['logined'] = true;
}

?>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">

	<title>Edit Time Cost</title>
</head>
<body>
<?
if(!isset($_SESSION['logined'])){

echo '<form name="form1" method="post">Password: <input type="password" id="passwd" name="passwd" value=""></form>';

} elseif (!empty($action)) {
	
	if (isset($_POST['submit'])) {
		switch ($action) {
			case 'add':
				if (empty($start_time) || empty($end_time) || empty($cost)) {
					printErrorMessage("The fields cannot be empty.");
				} elseif (parseParams($start_time,$end_time,$cost,$remark)) {
					
					$sql = "INSERT INTO ".$db_tbl_prefix."cost VALUES('',$selected_id,$start_time,$end_time,'$cost','$remark')";
					
					$rs = mysql_query($sql);
					if (mysql_affected_rows() == 1) {
						back($selected_id);
					} else {
						printErrorMessage(mysql_error());
						
						$start_time = "";
						$end_time = "";
						$cost = "";
					}
				}
				
				break;
			case 'edit':
				
				if (empty($start_time) || empty($end_time) || empty($cost)) {
					printErrorMessage("The fields cannot be empty.");
				} elseif (parseParams($start_time,$end_time,$cost,$remark)) {
					
					$sql = "SELECT * FROM ".$db_tbl_prefix."cost WHERE `id`=$selected_id LIMIT 1";
					if (false !== ($rs = mysql_query($sql))) {
						$row = mysql_fetch_assoc($rs);
						
						$user_id = $row['room_id'];
						echo $sql = "UPDATE ".$db_tbl_prefix."cost SET `start_time`=$start_time,`end_time`=$end_time, `cost`='$cost',`remarks`='$remark' WHERE `id`=$selected_id LIMIT 1";
						
						$rs = mysql_query($sql);
						if (mysql_affected_rows() == 1) {
							back($user_id);
						} else {
							$error_message = mysql_error();
							if (empty($error_message)) {
								back($user_id);
							} else {
								printErrorMessage($error_message);							
							}
							
						}
					} else {
						printErrorMessage("error : unknown records");
					}
				}
				$start_time = $_POST['start'];
				$end_time = $_POST['end'];
				$cost =  $_POST['cost'];
				break;
			case 'update':

				if (empty($start_time) || empty($end_time) || empty($cost)) {
					printErrorMessage("The fields cannot be empty.");
				} elseif (parseParams($start_time,$end_time,$cost,$remark)) {
					
					$sql = "SELECT * FROM ".$db_tbl_prefix."cost WHERE `id`=$selected_id LIMIT 1";
					if (false !== ($rs = mysql_query($sql))) {
						$row = mysql_fetch_assoc($rs);
						
						$user_id = $row['room_id'];
						
						
						$sql = "INSERT INTO ".$db_tbl_prefix."cost VALUES('',$user_id,$start_time,$end_time,'$cost','$remark')";
						
						$rs = mysql_query($sql);
						if (mysql_affected_rows() == 1) {
							$sql = "UPDATE ".$db_tbl_prefix."cost SET `end_time`=".($start_time-1)." WHERE `id`=$selected_id LIMIT 1";
							
							$rs = mysql_query($sql);
							if (mysql_affected_rows() == 1) {
								back($user_id);
							} else {
								$error_message = mysql_error();
								if (empty($error_message)) {
									back($user_id);
								} else {
									printErrorMessage($error_message);							
								}
								
							}
						} else {
							printErrorMessage(mysql_error());
							
							$start_time = "";
							$end_time = "";
							$cost = "";
						}
						
						

					} else {
						printErrorMessage("error : unknown records");
					}
				}
				$start_time = $_POST['start'];
				$end_time = $_POST['end'];
				$cost =  $_POST['cost'];
				break;
				
			case 'del':
				
				$sql = "SELECT * FROM ".$db_tbl_prefix."cost WHERE `id`=$selected_id LIMIT 1";
				if (false !== ($rs = mysql_query($sql))) {
					$row = mysql_fetch_assoc($rs);
					
					$user_id = $row['room_id'];
					
					if (is_numeric($selected_id)) {
						$sql = "DELETE FROM ".$db_tbl_prefix."cost WHERE `id` = $selected_id LIMIT 1";
						
						$rs = mysql_query($sql);
						if (mysql_affected_rows() == 1) {
							back($user_id);
						} else {
							printErrorMessage(mysql_error());
						}
					}
				} else {
						printErrorMessage("error : unknown records");
				}
				
				
				break;
			default:
				break;
		}		
	}
	
	$users = array();
	$sql = "select r.id as id, r.room_name as u_name, a.id as dept_id from ".$db_tbl_prefix."room r left join ".$db_tbl_prefix."area a on (r.area_id = a.id) order by r.room_name";
	
	// echo "$sql";
	$rs = mysql_query($sql);
	
	
	while($r = mysql_fetch_array($rs)){
		$users[$r['id']] = array("name"=>$r['u_name'], "dept_id"=>$r['dept_id'], "hr"=>0.0, "job"=>array());
	}
	
	
	echo '<form name="form1" method="post">';
	switch ($action) {
		case 'add':
			
			if (!isset($_POST['submit'])) {
				$start_time = "";
				$end_time = "-";
				$cost = "";
			}
			$user_id = $selected_id;
			
			printUserInformation($users[$user_id]);
			printEditForm($start_time,$end_time,$cost,$remark);
			
			
			break;
			
		case 'edit':
			$sql = "SELECT * FROM ".$db_tbl_prefix."cost WHERE `id`=$id LIMIT 1";
			if (false !== ($rs = mysql_query($sql))) {
				$row = mysql_fetch_assoc($rs);
				
				if (!isset($_POST['submit'])) {
					$user_id = $row['room_id'];
					$start_time = date('Y-m-d',$row['start_time']);
					if ($row['end_time'] == 2147483647) {
						$end_time = "-";
					} else {
						$end_time = date('Y-m-d',$row['end_time']);					
					}
					$cost = $row['cost'];
					$remark = $row['remarks'];
				}
				
				printUserInformation($users[$user_id]);
				printEditForm($start_time,$end_time,$cost,$remark);
			} else {
				printErrorMessage("error : unknown records");
			}
			
			break;
			
		case 'update':
			
			$sql = "SELECT * FROM ".$db_tbl_prefix."cost WHERE `id`=$id LIMIT 1";
			if (false !== ($rs = mysql_query($sql))) {
				$row = mysql_fetch_assoc($rs);
						
				if (!isset($_POST['submit'])) {					
					$user_id = $row['room_id'];
					$start_time = "";
					$end_time = "-";
					$cost = "";
				}
				
				printUserInformation($users[$user_id]);
				printEditForm($start_time,$end_time,$cost,$remark);
			} else {
				printErrorMessage("error : unknown records");
			}
			
			break;
			
		case 'del':
			
			$sql = "SELECT * FROM ".$db_tbl_prefix."cost WHERE `id`=$id LIMIT 1";
			if (false !== ($rs = mysql_query($sql)) && mysql_num_rows($rs) > 0) {
				$row = mysql_fetch_assoc($rs);
				
				$start_time = date('Y-m-d',$row['start_time']);
				if ($row['end_time'] ==2147483647) {					
					$end_time = "Now";
				} else {
					$end_time = date('Y-m-d',$row['end_time']);					
				}
				
				$user_id = $row['room_id'];
				
				printUserInformation($users[$user_id]);
								
				echo "<h2><font color='red'>$start_time ~ $end_time : $ {$row['cost']}</font></h2>";
				echo "<h2><font color='red'>Delete ? Cannot undo !</font></h2>";
				
				echo "<input type='hidden' name='confirm'>";
			} else {
				printErrorMessage("error : unknown records");
			}
			
			break;
	
		default:
			break;
	}
	
	echo "<hr />";
	echo "<input type='submit' name='submit' value=' O K ' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<a href='cost.php#bm$user_id'>[ Cancel & Back ]</a>";
	
	
	echo "</form>";

	$total_hour = 0.0;
	$jobs = array();
	$depts = array();
	$sql = "select id, area_name from ".$db_tbl_prefix."area order by sort_order";
	$rs = mysql_query($sql);
	while($r = mysql_fetch_array($rs)){
		$depts[$r['id']] = array("name"=>$r['area_name'], "hr"=>0.0, "job"=>array());
	}
	
	
	
	$users = array();
	$sql = "select r.id as id, r.room_name as u_name, a.id as dept_id from ".$db_tbl_prefix."room r left join ".$db_tbl_prefix."area a on (r.area_id = a.id) order by r.sort_order";
	
	// echo "$sql";
	$rs = mysql_query($sql);
	while($r = mysql_fetch_array($rs)){
		$users[$r['id']] = array("name"=>$r['u_name'], "dept_id"=>$r['dept_id'], "hr"=>0.0, "job"=>array());
	}
	
}
?>

</body>
</html>

<?php


function back($uid='') {
	ob_clean();
	if (empty($uid)) {
		header("location: cost.php");
	} else{
		header("location: cost.php#bm$uid");
	}
	exit();
}
function parseParams(&$start_time,&$end_time,&$cost,&$remark) {
	$stime = trim($start_time);
	$etime = trim($end_time);
	$cost = trim($cost);
	
	$error = false;
	
	$stime = strtotime($stime." 00:00:00");
	if (!($stime > 0)) {
		printErrorMessage("Please check the start time");
		$error = true;
	}
	if ($etime == '-') {
		$etime = 2147483647;
	} else {
		$etime = strtotime($etime." 23:59:59");
	}
	
	if (!($etime > 0)) {
		printErrorMessage("Please check the end time");
		$error = true;
	}
	if (!is_numeric($cost)) {
		printErrorMessage("Please check the cost");	
		$error = true;						
	}
	
	if ($stime > $etime) {
		printErrorMessage("Error : Start more than End");
		$error = true;
	}
	
	if ($error) {
		return false;
	} else {
		$remark = mysql_real_escape_string($remark);
		$cost = mysql_real_escape_string($cost);
		$start_time = $stime;
		$end_time = $etime;
		return true;
	}
}
function printErrorMessage($msg) {
	echo "<h4><font color='red'>$msg</font></h4>";
}
function printEditForm($start_time,$end_time,$cost,$remark='') {
	echo "<table>
		<tr>
			<th valign='top' width='80' align='right'>Range : </th>
			<td valign='top'><input type='text' name='start' value='$start_time' /> ~ <input type='text' name='end' value='$end_time' /></td>
			<td>4 digi year, 2 digi month, 2 digi day <br/>
				 <b>e.g.</b><br/>
				Fix range : <b>2009-01-01 ~ 2010-06-30</b><br/>
				Begin to now : <b>2009-01-01 ~ -</b><br/><br/>
			</td>
		</tr>
		<tr>
			<th valign='top' align='right'>Cost : </th>
			<td valign='top'><input type='text' name='cost' value='$cost' /></td>
			<td valign='top'>e.g. <b>888</b></td>
		</tr>
		<tr>
			<th valign='top' align='right'>Remarks : </th>
			<td valign='top'><textarea type='text' name='remark' rows='6' cols='40' />".htmlspecialchars($remark)."</textarea></td>
			<td valign='top'></td>
		</tr>
	</table>";
}
function printUserInformation($info) {
	echo "<h1>{$info['name']}</h1>";
}
?>