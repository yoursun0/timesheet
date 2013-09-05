<?php

# $Id: admin.php,v 1.16.2.1 2005/03/29 13:26:15 jberanek Exp $

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

if (empty($area))
{
    $area = get_default_area();
}

if(!getAuthorised(2))
{
	showAccessDenied($day, $month, $year, $area);
	exit();
}

if(isset($action)){
	// sort area or room
	if($action == 'reorder_area' && !empty($fr_order) && !empty($to_order)){
		sort_order($tbl_area, 'id', 'sort_order', $fr_order, $to_order, "");
	} else if($action == 'reorder_room' && !empty($area) && !empty($fr_order) && !empty($to_order)){
		sort_order($tbl_room, 'id', 'sort_order', $fr_order, $to_order, " area_id=$area");
	}
}

print_header($day, $month, $year, isset($area) ? $area : "");

// If area is set but area name is not known, get the name.
if (isset($area))
{
	if (empty($area_name))
	{
		$res = sql_query("select area_name from $tbl_area where id=$area");
    	if (! $res) fatal_error(0, sql_error());
		if (sql_count($res) == 1)
		{
			$row = sql_row($res, 0);
			$area_name = $row[0];
		}
		sql_free($res);
	} else {
		$area_name = unslashes($area_name);
	}
}
?>

<h2><?php echo get_vocab("administration") ?></h2>

<table border=1>
<tr>
<th><center><b><?php echo get_vocab("areas") ?></b></center></th>
<th><center><b><?php echo get_vocab("rooms") ?> <?php if(isset($area_name)) { echo get_vocab("in") . " " .
  htmlspecialchars($area_name); }?></b></center></th>
</tr>

<tr>
<td>
<?php 
# This cell has the areas
$res = sql_query("select id, area_name from $tbl_area order by sort_order");
if (! $res) fatal_error(0, sql_error());
$url = "admin.php?action=reorder_area&area=$area";
$num_areas = mysql_num_rows($res);
if (sql_count($res) == 0) {
	echo get_vocab("noareas");
} else {
	//echo "<ul>";
	for ($i = 0; ($row = sql_row($res, $i)); $i++) {
		$area_name_q = urlencode($row[1]);
		//echo "<li>";
		echo mkSortOder($url, ($i+1), $num_areas);
		echo " <a href=\"admin.php?area=$row[0]&area_name=$area_name_q\">"
			. htmlspecialchars($row[1]) . "</a> (<a href=\"edit_area_room.php?area=$row[0]\">" . get_vocab("edit") . "</a>) (<a href=\"del.php?type=area&area=$row[0]\">" .  get_vocab("delete") . "</a>)<br>\n";
	}
	//echo "</ul>";
}
?>
</td>
<td>
<?php
# This one has the rooms
if(isset($area)) {
	$res = sql_query("select id, room_name, description, capacity from $tbl_room where area_id=$area order by sort_order");
	if (! $res) fatal_error(0, sql_error());
	if (sql_count($res) == 0) {
		echo get_vocab("norooms");
	} else {
		$url = "admin.php?action=reorder_room&area=$area";
		$num_rooms = mysql_num_rows($res);
		for ($i = 0; ($row = sql_row($res, $i)); $i++) {
			echo mkSortOder($url, ($i+1), $num_rooms)." ".htmlspecialchars($row[1]) . "(" . htmlspecialchars($row[2])
			. ", $row[3]) (<a href=\"edit_area_room.php?room=$row[0]\">" . get_vocab("edit") . "</a>) (<a href=\"del.php?type=room&room=$row[0]\">" . get_vocab("delete") . "</a>)<br>\n";
		}
	}
} else {
	echo get_vocab("noarea");
}

?>

</tr>
<tr>
<td>
<h3 ALIGN=CENTER><?php echo get_vocab("addarea") ?></h3>
<form action=add.php method=post>
<input type=hidden name=type value=area>

<TABLE>
<TR><TD><?php echo get_vocab("name") ?>:       </TD><TD><input type=text name=name></TD></TR>
</TABLE>
<input type=submit value="<?php echo get_vocab("addarea") ?>">
</form>
</td>

<td>
<?php if (0 != $area) { ?>
<h3 ALIGN=CENTER><?php echo get_vocab("addroom") ?></h3>
<form action=add.php method=post>
<input type=hidden name=type value=room>
<input type=hidden name=area value=<?php echo $area; ?>>

<TABLE>
<TR><TD><?php echo get_vocab("name") ?>:       </TD><TD><input type=text name=name></TD></TR>
<TR><TD><?php echo get_vocab("description") ?></TD><TD><input type=text name=description></TD></TR>
<TR><TD><?php echo get_vocab("capacity") ?>:   </TD><TD><input type=text name=capacity></TD></TR>
</TABLE>
<input type=submit value="<?php echo get_vocab("addroom") ?>">
</form>
<?php } else { echo "&nbsp;"; }?>
</td>
</tr>
</table>

<br>

<?php echo get_vocab("browserlang") . " " . $HTTP_ACCEPT_LANGUAGE . " " . get_vocab("postbrowserlang") ; ?>

<?php include "trailer.inc" ?>

<iframe id="backend_admin" width="100%" src="admin/" style="border:1px solid #ccc;"></iframe>
<script type="text/javascript">
	$(function(){
		$(window).resize(windowResize);
		windowResize();
	});
	function windowResize() {
		$('#backend_admin').height($(window).height() - 20);
	}
</script>