<?php
require_once 'config.php';

check_login();

$action = arg('action', 'G', 'Submit');
if ($action == 'Download Data') {
    include 'data.php';
    exit;
}

$s_date = arg('s_date', 'G', '2012-01-01');
$e_date = arg('e_date', 'G', '2012-03-31');
$job_no = urldecode(arg('job_no', 'G', ''));


include header_tpl();
?>
<table width="90%">
    <tr>
        <td width="50%">
            <form name="form1" method="get" style="text-align: left;">
                <table>
                    <tr><td>Job Name: </td><td><input type="text" id="job_no" name="job_no" value="<?= $job_no ?>"></td><tr>
                    <tr><td>Start Date: </td><td><input type="text" id="s_date" name="s_date" value="<?= $s_date ?>"></td><tr>
                    <tr><td>End Date: </td><td><input type="text" id="e_date" name="e_date" value="<?= $e_date ?>"></td><tr>
                    <tr><td> </td><td>
                        <input type="submit" name="action" value="Submit" />
                        <input type="submit" name="action" value="Download Data" />
                        <td><tr>
                </table>
            </form>
        </td>
        <td width="50%">
            <h1><a href='cost.php' target='_timecost'>Open Time Cost List</a></h1>
        </td>
    </tr>
</table>
<?
if ($s_date != "" && $e_date != "") {

    $total_hour = 0.0;
    $jobs = array();
    $depts = array();
    $sql = "select id, area_name from " . $db_tbl_prefix . "area order by sort_order";
    $rs = mysql_query($sql);
    while ($r = mysql_fetch_array($rs)) {
        if ($r['id'] == 4) { //exclude management
            continue;
        }
        $depts[$r['id']] = array("name" => $r['area_name'], "hr" => 0.0, "job" => array());
    }



    $users = array();
    $sql = "select r.id as id, r.room_name as u_name, a.id as dept_id from " . $db_tbl_prefix . "room r left join " . $db_tbl_prefix . "area a on (r.area_id = a.id) order by r.sort_order";

    // echo "$sql";
    $rs = mysql_query($sql);
    while ($r = mysql_fetch_array($rs)) {
        $users[$r['id']] = array("name" => $r['u_name'], "dept_id" => $r['dept_id'], "hr" => 0.0, "job" => array());
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
    //echo $sql = "SELECT start_time, end_time, room_id, name AS job_id FROM ".$db_tbl_prefix ."entry WHERE start_time >= $start_date AND end_time < $end_date".( $job_no == "" ? "" : " AND name like '$job_no'")." ORDER BY start_time";

    $missing_cost_user = array();

    $sql = "SELECT e.start_time, e.end_time, e.room_id, m.cost,e.name AS job_id 
FROM {$db_tbl_prefix}entry e LEFT OUTER JOIN {$db_tbl_prefix}cost m ON m.room_id=e.room_id	AND (e.start_time BETWEEN m.start_time AND m.end_time)
WHERE e.start_time >= $start_date AND e.end_time < $end_date
	" . ( $job_no == "" ? "" : " AND e.name like '$job_no'") . "
ORDER BY e.start_time";

    $rs = mysql_query($sql);
    while ($r = mysql_fetch_array($rs)) {
        $job_id = $r['job_id'];
        $user_id = $r['room_id'];
        $start_time = $r['start_time'];
        $end_time = $r['end_time'];
        $dept_id = $users[$user_id]['dept_id'];
        if (!isset($r['cost']) || empty($r['cost'])) {
            $cost_per_hour = 0;
            $missing_cost_user[$user_id] = $users[$user_id]['name'];
        } else {
            $cost_per_hour = $r['cost'];
        }

        $hour = ($end_time - $start_time) / 3600.0;
        $cost = $hour * $cost_per_hour;

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
    }
//print table by department
    echo "<hr size=1>";

    echo "<table border='1' cellspacing='0'>";
    echo "<thead>";
    echo "<tr align='center'><th rowspan='2'>Job</th><th colspan='2'>Total Hour</th>";
    foreach ($depts as $dept_id => $dept_detail) {
        echo "<th colspan='2'>";
        echo "<a href='detail.php?job_no=" . urlencode($job_no) . "&dept_id=$dept_id&s_date=$s_date&e_date=$e_date'  target='_blank'>{$dept_detail['name']}</a>";
        echo "</th>";
    }
    echo "</tr>";
    echo "<tr align='center'>";
    for ($i = 1; $i <= count($depts) + 1; $i++) {
        echo "<th width='80'>By hours</th>";
        echo "<th width='80'>By dollars</th>";
    }
    echo "</tr>";
    echo "</thead>";

    ksort($jobs_hour);

    echo "<tbody>";
    foreach ($jobs_hour as $job_id => $job_details) {
        $detail_link = "<a href='detail.php?job_no=" . urlencode($job_id) . "&s_date=$s_date&e_date=$e_date' target='_blank'>$job_id</a>";
        echo "<tr align='center'><th align='left'>" . $detail_link . "</th><td>{$jobs_hour[$job_id]}</td><td>{$jobs_cost[$job_id]}</td>";
        foreach ($depts as $dept_id => $dept_detail) {
            $detail_link = "<a href='detail.php?job_no=" . urlencode($job_id) . "&dept_id=$dept_id&s_date=$s_date&e_date=$e_date' target='_blank'>" . getDisplay($dept_detail['hour'][$job_id]) . "</a>";
            echo "<td>" . $detail_link . "</td>";
            $detail_link = "<a href='detail.php?job_no=" . urlencode($job_id) . "&dept_id=$dept_id&s_date=$s_date&e_date=$e_date' target='_blank'>" . getDisplay($dept_detail['cost'][$job_id]) . "</a>";
            echo "<td>" . $detail_link . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";

    //echo "<tfoot>";
    echo "<tr align='center'><th>All</th><td>$total_hour</td><td>$total_cost</td>";
    foreach ($depts as $dept_id => $dept_detail) {
        echo "<td>" . getDisplay($depts[$dept_id]['hour']['all']) . "</td>";
        echo "<td>" . getDisplay($depts[$dept_id]['cost']['all']) . "</td>";
    }
    echo "</tr>";
    //echo "</tfoot>";
    echo "</table>";

    echo "<hr size=1>";

    if (!empty($missing_cost_user)) {
        ?>
        <font color='red'>
        <h2>The following staffs missing hourly cost, please <a href='cost.php' target='_timecost'>click here update the time cost list.</a></h2>
        <?= implode('<br/>', $missing_cost_user); ?>
        </font>
        <script type="text/javascript">
            alert("Found some error, please scroll to page end.");
        </script>
        <?php
    }
}
?>
        
<?php include footer_tpl(); ?>