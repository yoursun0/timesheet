<?
require_once "grab_globals.inc.php";
include "config.inc.php";
include "functions.inc";
include "$dbsys.inc";

session_start();

$job_no = "";
$s_date = "2009-01-01";
$e_date = "2009-03-31";
if(isset($_POST['job_no'])) $job_no = $_POST['job_no'];
if(isset($_POST['s_date'])) $s_date = $_POST['s_date'];
if(isset($_POST['e_date'])) $e_date = $_POST['e_date'];

if(isset($_POST['passwd'])){
	if($_POST['passwd'] == include "password.php") $_SESSION['logined'] = true;
}

?>
<html>
<head>        
	<meta content="text/html; charset=utf-8" http-equiv="content-type">

</head>
<body>
<?
if(!isset($_SESSION['logined'])){

echo '<form name="form1" method="post">Password: <input type="password" id="passwd" name="passwd" value=""></form>';

} else {
?>
<form name="form1" method="post">
<table>
<tr><td>Job Name: <td><td><input type="text" id="job_no" name="job_no" value="<?=$job_no?>"></td><tr>
<tr><td>Start Date: <td><td><input type="text" id="s_date" name="s_date" value="<?=$s_date?>"></td><tr>
<tr><td>End Date: <td><td><input type="text" id="e_date" name="e_date" value="<?=$e_date?>"></td><tr>
</table>
<input type="submit" value="Submit">
</form>
<?
if($s_date != "" && $e_date != ""){

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
	
	/*
	echo "<pre>";
	print_r($users);
	echo "</pre>";
	*/
	$s_date_part = split("-", $s_date);
	$start_date = mktime(0, 0, 0, ($s_date_part[1]), ($s_date_part[2]), ($s_date_part[0]));
	$e_date_part = split("-", $e_date);
	$end_date = mktime(0, 0, 0, ($e_date_part[1]), ($e_date_part[2] + 1), ($e_date_part[0]));
	
	// echo "From " .date("Y-m-d", $start_date). "($start_date) to ".date("Y-m-d", $end_date)." ($end_date)<hr size=1>\n";
 
	
	
	
	$sql = "select start_time, end_time, room_id, name as job_id from ".$db_tbl_prefix ."entry where start_time >= $start_date and end_time < $end_date".( $job_no == "" ? "" : " and name like '$job_no'")." order by start_time";
	
	$rs = mysql_query($sql);

	while($r = mysql_fetch_array($rs)){
		$job_id = $r['job_id'];
		$user_id = $r['room_id'];
		$dept_id = $users[$user_id]['dept_id'];
		$start_time = $r['start_time'];
		$end_time = $r['end_time'];
		$hr = ($end_time - $start_time) / 3600.0;
		
		$total_hour += $hr;
		
		if(!array_key_exists($job_id, $jobs)) $jobs[$job_id] = 0.0;
		$jobs[$job_id] += $hr;
		
		// echo "user_id = $user_id , dept_id = $dept_id <br>";
		if(!array_key_exists($job_id, $depts[$dept_id]['job'])) $depts[$dept_id]['job'][$job_id] = 0.0;
		$depts[$dept_id]['job'][$job_id] += $hr;
		$depts[$dept_id]['hr'] += $hr;
		@$depts_total_hr[$dept_id] += $hr;
		
		if(!array_key_exists($job_id, $users[$user_id]['job'])) $users[$user_id]['job'][$job_id] = 0.0;
		$users[$user_id]['job'][$job_id] += $hr;
		$users[$user_id]['hr'] += $hr;
	}

	echo "<hr size=1><table border=1><tr align='center'><td>Job</td><td>Total Hour</td>";
	foreach($depts as $dept_id => $dept_detail){
		$detail_link = "<a href='detail.php?job_no=".urlencode($job_no)."&dept_id=$dept_id&s_date=$s_date&e_date=$e_date'  target='_blank'>{$dept_detail['name']}</a>";
		echo "<td>".$detail_link."</td>";
	}
	echo "</tr>";
	
	ksort($jobs);
	
	foreach($jobs as $job_id=>$job_details){
		$detail_link = "<a href='detail.php?job_no=".urlencode($job_id)."&s_date=$s_date&e_date=$e_date' target='_blank'>$job_id</a>";
		echo "<tr align='center'><td>".$detail_link."</td><td>" . $job_details."</td>";
		foreach($depts as $dept_id => $dept_detail){
			echo "<td>";
			if(array_key_exists($job_id, $dept_detail['job'])){
				echo $dept_detail['job'][$job_id];
			} else {
				echo "-";
			}
			echo "</td>";
		}	
		echo "</tr>";
	}
	echo "<tr align='center'><td>All</td><td>".array_sum($depts_total_hr)."</td>";
	foreach($depts as $dept_id => $dept_detail){
			echo "<td>";
		if (isset($depts_total_hr[$dept_id])) {
			echo $depts_total_hr[$dept_id];
		} else {
				echo "-";
		}
			echo "</td>";
	}
	echo "</tr>";
	echo "</table>";
	
	echo "<hr size=1>";
	
	echo "<table border=1>";
	
	echo "</table>";
}
}
?>
</body>
</html>