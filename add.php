<?php

# $Id: add.php,v 1.5.2.1 2005/03/29 13:26:15 jberanek Exp $

require_once "grab_globals.inc.php";
include "config.inc.php";
include "functions.inc";
include "$dbsys.inc";
include "mrbs_auth.inc";
include "html_inc.php";

if(!getAuthorised(2))
{
	showAccessDenied($day, $month, $year, $area);
	exit();
}

# This file is for adding new areas/rooms

# we need to do different things depending on if its a room
# or an area

if ($type == "area")
{
	$area_name_q = slashes($name);
	$sort_order = get_db_max_id($tbl_area, 'sort_order', "")+1;
	$sql = "insert into $tbl_area (area_name, sort_order) values ('$area_name_q', $sort_order)";
	if (sql_command($sql) < 0) fatal_error(1, "<p>" . sql_error());
	$area = sql_insert_id("$tbl_area", "id");
}

if ($type == "room")
{
	$room_name_q = slashes($name);
	$description_q = slashes($description);
	$sort_order = get_db_max_id($tbl_room, 'sort_order', " area_id=$area")+1;
	if (empty($capacity)) $capacity = 0;
	$sql = "insert into $tbl_room (room_name, area_id, description, capacity, sort_order)
	        values ('$room_name_q',$area, '$description_q',$capacity, $sort_order)";
	if (sql_command($sql) < 0) fatal_error(1, "<p>" . sql_error());
}

header("Location: admin.php?area=$area");
