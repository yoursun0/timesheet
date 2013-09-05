<?php
// $Id: edit_area_room.php,v 1.14.2.1 2005/03/29 13:26:22 jberanek Exp $

require_once "grab_globals.inc.php";
include "config.inc.php";
include "functions.inc";
include "$dbsys.inc";
include "mrbs_auth.inc";
include "html_inc.php";

#If we dont know the right date then make it up
if(!isset($day) or !isset($month) or !isset($year))
{
	$day   = date("d");
	$month = date("m");
	$year  = date("Y");
}

if(!getAuthorised(2))
{
	showAccessDenied($day, $month, $year, $area);
	exit();
}

// Done changing area or room information?
if (isset($change_done))
{
	if (!empty($room)) // Get the area the room is in
	{
		$area = sql_query1("SELECT area_id from $tbl_room where id=$room");
	}
	Header("Location: admin.php?day=$day&month=$month&year=$year&area=$area");
	exit();
}

print_header($day, $month, $year, isset($area) ? $area : "");

?>

<h2><?php echo get_vocab("editroomarea") ?></h2>

<table border=1>

<?php
if(!empty($room)) {
    include_once 'Mail/RFC822.php';
    
    $areas_list = array();
    $res = sql_query("SELECT id, area_name FROM $tbl_area ORDER BY area_name");
	while($row = mysql_fetch_array($res)){
		$areas_list[$row[0]] = $row[1];
	}
	sql_free($res);
    
    (!isset($room_admin_email)) ? $room_admin_email = '': '';
    $emails = explode(',', $room_admin_email);
    $valid_email = TRUE;
    $email_validator = new Mail_RFC822();
    foreach ($emails as $email)
    {
        // if no email address is entered, this is OK, even if isValidInetAddress
        // does not return TRUE
        if ( !$email_validator->isValidInetAddress($email, $strict = FALSE)
            && ('' != $room_admin_email) )
        {
            $valid_email = FALSE;
        }
    }
    //
	if ( isset($change_room) && (FALSE != $valid_email) )
	{
        if (empty($capacity)) $capacity = 0;
		$sql = "UPDATE $tbl_room SET area_id = $room_belong_area, room_name='" . slashes($room_name)
			. "', description='" . slashes($description)
			. "', capacity=$capacity, room_admin_email='"
            . slashes($room_admin_email) . "'";
		
        // change the sort order if area id change
        $ori_area = sql_query1("SELECT area_id from $tbl_room where id=$room");
        if($room_belong_area != $ori_area){
        	$sort_order = get_db_max_id($tbl_room, 'sort_order', " area_id=$room_belong_area")+1;
        	$sql .= ", sort_order=$sort_order";
        }
        
        $sql .= " WHERE id=$room";
		if (sql_command($sql) < 0)
			fatal_error(0, get_vocab("update_room_failed") . sql_error());
	}

	$res = sql_query("SELECT * FROM $tbl_room WHERE id=$room");
	if (! $res) fatal_error(0, get_vocab("error_room") . $room . get_vocab("not_found"));
	$row = sql_row_keyed($res, 0);
	sql_free($res);
?>
<h3 ALIGN=CENTER><?php echo get_vocab("editroom") ?></h3>
<form action="edit_area_room.php" method="post">
<input type=hidden name="room" value="<?php echo $row["id"]?>">
<CENTER>
<TABLE>
<TR><TD><?php echo get_vocab("areas") ?>:       </TD><TD>
  <select name="room_belong_area"> 
<?
	foreach ($areas_list as $t_area_id => $t_area_name){
		echo ("<OPTION value=\"$t_area_id\"");
		if($row["area_id"] == $t_area_id) echo " selected";
		echo (">$t_area_name</OPTION>\n");
	}
?>
  </select></TD></TR>
<TR><TD><?php echo get_vocab("name") ?>:       </TD><TD><input type=text name="room_name" value="<?php
echo htmlspecialchars($row["room_name"]); ?>"></TD></TR>
<TR><TD><?php echo get_vocab("description") ?></TD><TD><input type=text name=description value="<?php
echo htmlspecialchars($row["description"]); ?>"></TD></TR>
<TR><TD><?php echo get_vocab("capacity") ?>:   </TD><TD><input type=text name=capacity value="<?php
echo $row["capacity"]; ?>"></TD></TR>
<TR><TD><?php echo get_vocab("room_admin_email") ?></TD><TD><input type=text name=room_admin_email MAXLENGTH=75 value="<?php
echo htmlspecialchars($row["room_admin_email"]); ?>"></TD>
<?php if (FALSE == $valid_email) {
    echo ("<TD>&nbsp;</TD><TD><STRONG>" . get_vocab('invalid_email') . "<STRONG></TD>");
} ?></TR>
</TABLE>
<input type=submit name="change_room"
value="<?php echo get_vocab("change") ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit name="change_done" value="<?php echo get_vocab("backadmin") ?>">
</CENTER>
</form>
<?php } ?>

<?php
if(!empty($area))
{
    include_once 'Mail/RFC822.php';
    (!isset($area_admin_email)) ? $area_admin_email = '': '';
    $emails = explode(',', $area_admin_email);
    $valid_email = TRUE;
    $email_validator = new Mail_RFC822();
    foreach ($emails as $email)
    {
        // if no email address is entered, this is OK, even if isValidInetAddress
        // does not return TRUE
        if ( !$email_validator->isValidInetAddress($email, $strict = FALSE)
            && ('' != $area_admin_email) )
        {
            $valid_email = FALSE;
        }
    }
    //
    if ( isset($change_area) && (FALSE != $valid_email) )
	{
		$sql = "UPDATE $tbl_area SET area_name='" . slashes($area_name)
			. "', area_admin_email='" . slashes($area_admin_email)
            . "' WHERE id=$area";
		if (sql_command($sql) < 0)
			fatal_error(0, get_vocab("update_area_failed") . sql_error());
	}

	$res = sql_query("SELECT * FROM $tbl_area WHERE id=$area");
	if (! $res) fatal_error(0, get_vocab("error_area") . $area . get_vocab("not_found"));
	$row = sql_row_keyed($res, 0);
	sql_free($res);
?>
<h3 ALIGN=CENTER><?php echo get_vocab("editarea") ?></h3>
<form action="edit_area_room.php" method="post">
<input type=hidden name="area" value="<?php echo $row["id"]?>">
<CENTER>
<TABLE>
<TR><TD><?php echo get_vocab("name") ?>:       </TD><TD><input type=text name="area_name" value="<?php
echo htmlspecialchars($row["area_name"]); ?>"></TD></TR>
<TR><TD><?php echo get_vocab("area_admin_email") ?>:       </TD><TD><input type=text name="area_admin_email" MAXLENGTH=75 value="<?php
echo htmlspecialchars($row["area_admin_email"]); ?>"></TD>
<?php if (FALSE == $valid_email) {
    echo ("<TD>&nbsp;</TD><TD><STRONG>" . get_vocab('invalid_email') . "</STRONG></TD>");
} ?></TR>
</TABLE>
<input type=submit name="change_area"
value="<?php echo get_vocab("change") ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit name="change_done" value="<?php echo get_vocab("backadmin") ?>">
</CENTER>
</form>
<?php } ?>
</TABLE>
<?php include "trailer.inc" ?>