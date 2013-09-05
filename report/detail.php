<?php

require_once 'config.php';

check_login();


$selected_user_id = arg('user_id', 'G', NULL);
$selected_dept_id = arg('dept_id', 'G', NULL);
$s_date = arg('s_date', 'G', '2012-01-01');
$e_date = arg('e_date', 'G', '2012-03-31');
$job_no = urldecode(arg('job_no', 'G', ''));

include header_tpl();

if ($s_date != "" && $e_date != "") {

    $total_hour = 0.0;
    $jobs = array();
    $depts = array();
    $sql = "select id, area_name from " . $db_tbl_prefix . "area order by sort_order";
    $rs = mysql_query($sql);
    while ($r = mysql_fetch_array($rs)) {
        /* 		if ($r['id'] == 4) { //exclude management
          continue;
          } */
        $depts[$r['id']] = array("name" => $r['area_name'], "hr" => 0.0, "job" => array());
    }



    $users = array();
    $sql = "select r.id as id, r.room_name as u_name, a.id as dept_id from " . $db_tbl_prefix . "room r left join " . $db_tbl_prefix . "area a on (r.area_id = a.id) order by r.sort_order";

    $rs = mysql_query($sql);
    while ($r = mysql_fetch_array($rs)) {
        $users[$r['id']] = array("name" => $r['u_name'], "dept_id" => $r['dept_id'], "hr" => 0.0, "job" => array());
    }



    /* 	echo "<pre>";
      print_r($users);
      echo "</pre>"; */

    $s_date_part = split("-", $s_date);
    $start_date = mktime(0, 0, 0, ($s_date_part[1]), ($s_date_part[2]), ($s_date_part[0]));
    $e_date_part = split("-", $e_date);
    $end_date = mktime(0, 0, 0, ($e_date_part[1]), ($e_date_part[2] + 1), ($e_date_part[0]));

    // echo "From " .date("Y-m-d", $start_date). "($start_date) to ".date("Y-m-d", $end_date)." ($end_date)<hr size=1>\n";
    //$sql = "select start_time, end_time, room_id, name as job_id from ".$db_tbl_prefix ."entry where start_time >= $start_date and end_time < $end_date".( $job_no == "" ? "" : " and name = '$job_no'")." order by start_time";
    $sql = "SELECT e.start_time, e.end_time, e.room_id, m.cost,e.name AS job_id 
FROM {$db_tbl_prefix}entry e LEFT OUTER JOIN {$db_tbl_prefix}cost m ON m.room_id=e.room_id	AND (e.start_time BETWEEN m.start_time AND m.end_time)
WHERE e.start_time >= $start_date AND e.end_time < $end_date

	" . ( $job_no == "" ? "" : " AND e.name like '$job_no'") . "
ORDER BY e.room_id,e.start_time";


    $rs = mysql_query($sql);
    echo "<h3>";
    if (!empty($job_no)) {
        echo "Job : <font color='blue'>$job_no</font> ";
    }
    if (!empty($selected_dept_id)) {
        echo "Department : <font color='blue'>" . $depts[$selected_dept_id]['name'] . "</font> ";
    }
    if (!empty($selected_user_id)) {
        echo "Staff : <font color='blue'>" . $users[$selected_user_id]['name'] . "</font> ";
    }
    echo " (<font color='blue'>$s_date ~ $e_date</font>)";
    echo "</h3>";


    echo "<hr size='1' />";

    $detail_table = array();
    while ($r = mysql_fetch_array($rs)) {
        $job_id = $r['job_id'];
        $user_id = $r['room_id'];
        $start_time = $r['start_time'];
        $end_time = $r['end_time'];
        $dept_id = $users[$user_id]['dept_id'];
        if (!isset($r['cost']) || empty($r['cost'])) {
            $cost_per_hour = 0;
            $missing_cost_user[$user_id] = $users[$user_id];
        } else {
            $cost_per_hour = $r['cost'];
        }

        $hour = ($end_time - $start_time) / 3600.0;
        $cost = $hour * $cost_per_hour;
        $users[$user_id]['cost_per_hour'] = $cost_per_hour;
        $users[$user_id]['department'] = $depts[$dept_id]['name'];


        if (!empty($selected_dept_id) && $selected_dept_id != $dept_id) {
            continue;
        }
        if (!empty($selected_user_id) && $selected_user_id != $user_id) {
            continue;
        }
        if (!empty($job_no) && $job_no != $job_id) {
            continue;
        }

        sum($total_hour, $hour);
        sum($total_cost, $cost);

        sum($jobs_hour[$job_id], $hour);
        sum($jobs_cost[$job_id], $cost);

        sum($depts[$dept_id]['hour'][$job_id], $hour);
        sum($depts[$dept_id]['cost'][$job_id], $cost);

        sum($depts[$dept_id]['hour']['all'], $hour);
        sum($depts[$dept_id]['cost']['all'], $cost);

        sum($users[$user_id]['hour'][$job_id], $hour);
        sum($users[$user_id]['cost'][$job_id], $cost);

        sum($users[$user_id]['hour']['all'], $hour);
        sum($users[$user_id]['cost']['all'], $cost);


        //format and print detail row
        $row['job_no'] = $job_id;
        $row['start'] = date('Y-m-d H:i', $start_time);
        $row['end'] = date('Y-m-d H:i', $end_time);
        $row['name'] = $users[$user_id]['name'];
        $row['department'] = $depts[$dept_id]['name'];
        $row['cost_per_hour'] = $cost_per_hour;
        $row['hour'] = $hour;
        $row['cost'] = $cost;
        $row['link'] = "<a href='detail.php?job_no=$job_no&dept_id=$selected_dept_id&user_id=$user_id&s_date=$s_date&e_date=$e_date' target='_blank'>" . $row['name'] . "</a>";

        $detail_table[$row['name'] . $row['start']] = $row;
    }
    ksort($detail_table);
    ksort($jobs_hour);

    /* 	foreach ($depts as &$dept_detail) {
      if (empty($dept_detail['hour']['all'])) {
      continue;
      }
      } */

    //echo "<hr size='1' />";
//print table by department
    echo "<h2>Department Summary</h2>";
    echo "<table border='1' cellspacing='0'>";
    echo "<tr align='center'><th rowspan='2'>Job</th><th colspan='2'>Total Hour</th>";
    $column_count = 0;
    foreach ($depts as $dept_id => $dept_detail) {
        if (empty($dept_detail['hour']['all'])) {
            continue;
        }
        $column_count++;
        echo "<th colspan='2'>";
        echo "<a href='detail.php?job_no=" . urlencode($job_no) . "&dept_id=$dept_id&s_date=$s_date&e_date=$e_date'  target='_blank'>{$dept_detail['name']}</a>";
        echo "</th>";
    }
    echo "</tr>";
    echo "<tr align='center'>";
    for ($i = 1; $i <= $column_count + 1; $i++) {
        echo "<th width='80'>By hours</th>";
        echo "<th width='80'>By dollars</th>";
    }
    echo "</tr>";


    foreach ($jobs_hour as $job_id => $job_details) {
        $detail_link = "<a href='detail.php?job_no=" . urlencode($job_id) . "&s_date=$s_date&e_date=$e_date' target='_blank'>$job_id</a>";
        echo "<tr align='center'><th align='left'>" . $detail_link . "</th><td>{$jobs_hour[$job_id]}</td><td>{$jobs_cost[$job_id]}</td>";
        foreach ($depts as $dept_id => $dept_detail) {
            if (empty($dept_detail['hour']['all'])) {
                continue;
            }
            echo "<td>" . getDisplay($dept_detail['hour'][$job_id]) . "</td>";
            echo "<td>" . getDisplay($dept_detail['cost'][$job_id]) . "</td>";
        }
        echo "</tr>";
    }
    echo "<tr align='center'><th>All</th><td>$total_hour</td><td>$total_cost</td>";
    foreach ($depts as $dept_id => $dept_detail) {
        if (empty($dept_detail['hour']['all'])) {
            continue;
        }
        echo "<td>" . getDisplay($depts[$dept_id]['hour']['all']) . "</td>";
        echo "<td>" . getDisplay($depts[$dept_id]['cost']['all']) . "</td>";
    }
    echo "</tr>";
    echo "</table>";

    echo "<hr size=1>";

//print table by users
    echo "<h2>Staff Summary</h2>";
    echo "<table border='1' cellspacing='0' width='700'>";
    echo "<tr align='center'><th>Name</th>";
    echo "<th>Department</th>";
    echo "<th width='80'>cost/hr</th>";
    echo "<th width='80'>By hours</th>";
    echo "<th width='80'>By dollars</th>";
    echo "<th width='80'>% hours</th>";
    //echo "<th width='80'>% dollars</th>";
    echo "</tr>";
    foreach ($users as $user_id => $user_detail) {
        if (empty($user_detail['hour']['all'])) {
            continue;
        }
        $highlight = '';
        if (empty($user_detail['cost_per_hour'])) {
            $highlight = "style='background-color:#FF6F6F;font-weight:bold;'";
        }

        echo "<tr align='center' $highlight>";
        echo "<th>";
        echo "<a href='detail.php?job_no=" . urlencode($job_no) . "&user_id=$user_id&dept_id={$user_detail['dept_id']}&s_date=$s_date&e_date=$e_date'  target='_blank'>{$user_detail['name']}</a>";
        echo "<td>{$user_detail['department']}</td>";
        echo "<td>{$user_detail['cost_per_hour']}</td>";
        echo "<td>" . getDisplay($user_detail['hour']['all']) . "</td>";
        echo "<td>" . getDisplay($user_detail['cost']['all']) . "</td>";
        echo "<td>" . number_format((getDisplay($user_detail['hour']['all'], 0, false) / $total_hour * 100), 2) . "</td>";
        //echo "<td>".number_format((getDisplay($user_detail['cost']['all'],0,false)/$total_cost*100),2)."</td>";
        echo "</th>";
        echo "</tr>";
    }
    echo "<tr align='center'><th>All</th>";
    echo "<td>&nbsp;</td>";
    echo "<td>&nbsp;</td>";
    echo "<td><b>$total_hour</b></td>";
    echo "<td><b>$total_cost</b></td>";
    echo "<td>&nbsp;</td>";
    //echo "<td>&nbsp;</td>";
    echo "</tr>";
    echo "</table>";

    echo "<hr size=1>";

//print detail
    echo "<h2>Detail</h2>";
    echo "<table border='1' cellspacing='0' width='700'>";
    echo "<tr>
	<th>Name</th>
	<th>Department</th>
	<th>Job</th>
	<th>Start</th>
	<th>End</th>
	<th>Hours</th>
	<th>Cost Per Hours</th>
	<th>Cost</th>
	</tr>";
    foreach ($detail_table as $row) {
        echo "<tr>";
        echo "<th>{$row['link']}</th>";
        echo "<td>{$row['department']}</td>";
        echo "<td>{$row['job_no']}</td>";
        echo "<td>{$row['start']}</td>";
        echo "<td>{$row['end']}</td>";
        echo "<td>{$row['hour']}</td>";
        echo "<td>{$row['cost_per_hour']}</td>";
        echo "<td>" . number_format($row['cost'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

include footer_tpl();
?>