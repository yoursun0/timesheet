<?php
require_once "grab_globals.inc.php";
include "config.inc.php";
include "functions.inc";
include "$dbsys.inc";
include "mrbs_auth.inc";
include "mincals.inc";
include 'include/func.inc.php';

	if (true) {
		echo <<<HTML
		<h2>Notice: function disabled. (<a href="week.php?year=$year&month=$month&day=$day&area=$area&room=$room">Goto Back Page</a>)</h2>
HTML;
		exit();
	}


if ('week' == $_GET['type']) {
	$fs = array('year', 'month', 'day', 'area', 'room');
	getParameters($fs);
	foreach ($fs as $f) {
		if (!ctype_digit($$f)) {
			error('Error: Invalid Parameters.');
		}
	}
	
	$time 				= mktime(0, 0, 0, $month, $day, $year);
	$newApprovedTime	= $time + (8 - date('N', $time)) * 86400;	
	$approved_date		= date('Y-m-d', $newApprovedTime);
	$approved_timestamp	= $newApprovedTime;	
	$oldApprovedTime = sql_query1("select approved_timestamp from $tbl_approve where room_id='$room'");
	if (-1 == $oldApprovedTime) {
		$oldApprovedTime = 1246377600;	//2009-07-07 00:00:00
	}
	
	if ($newApprovedTime - $oldApprovedTime > (86400 * 7)) {
		echo <<<HTML
		<h2>Please approve the week before. (<a href="week.php?year=$year&month=$month&day=$day&area=$area&room=$room">Goto Back Page</a>)</h2>
HTML;
		exit();
	}
	
	$time = $oldApprovedTime;
	$dates = array();
	do {
		if (date('N', $time) > 5) {
			continue;
		}		
		$dates[date('Y-m-d', $time)] = date('N', $time);
	} while (($time += 86400) < $newApprovedTime);
	
	//exclude holidays
	$sql = "SELECT `date` FROM $tbl_holidays WHERE (`date` BETWEEN '" . date('Y-m-d', $oldApprovedTime) . "' AND '" . date('Y-m-d', $newApprovedTime) . "')";
	if ($rs = mysql_query($sql)) {
		while ($row = mysql_fetch_assoc($rs)) {
			if (isset($dates[$row['date']])) {
				unset($dates[$row['date']]);
			}
		}
		mysql_free_result($rs);
	}
	
	//check records
	$sql = "SELECT * FROM $tbl_entry WHERE room_id = $room AND (start_time BETWEEN $oldApprovedTime AND $newApprovedTime)";
	if ($rs = mysql_query($sql)) {
		while ($row = mysql_fetch_assoc($rs)) {
			
			$date = date('Y-m-d', $row['start_time']);
			if (isset($dates[$date])) {
				unset($dates[$date]);
			}
		}
		
		mysql_free_result($rs);
	}
	
	if (count($dates) > 0) {
		echo <<<HTML
		<h2>Error: some problem found in the following dates: (<a href="week.php?year=$year&month=$month&day=$day&area=$area&room=$room">Goto Back Page</a>)</h2>
		<hr>
HTML;
		
		echo implode('<br />', array_keys($dates));
		
	} else {
		$sql = <<<SQL
REPLACE INTO $tbl_approve(room_id, approved_date, approved_timestamp, approved_by) 
	VALUES($room, '$approved_date', $approved_timestamp, '{$_SESSION['DisplayName']}')
SQL;
		mysql_query($sql);
		
		//redirect to week view;
		header("location: week.php?year=$year&month=$month&day=$day&area=$area&room=$room");
	}
}
?>