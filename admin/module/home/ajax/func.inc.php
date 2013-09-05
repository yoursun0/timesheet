<?php

function formatDatabaseEvent($row) {
	$tmp['id'] = $row['id'];
	$tmp['name'] = $row['name'];
	$tmp['date'] = strtotime(date('Y-m-d 00:00:00',$row['start_time'])) ;
	$tmp['start'] = ($row['start_time'] - $tmp['date']) / 60;
	$tmp['end'] = ($row['end_time'] - $tmp['date']) / 60;
	$tmp['description'] = $row['description'];
	$tmp['type'] = $row['type'];
	return $tmp;
}


?>