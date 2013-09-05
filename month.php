<?php
# $Id: month.php,v 1.31.2.4 2007/02/23 22:06:45 jberanek Exp $

# mrbs/month.php - Month-at-a-time view

require_once "grab_globals.inc.php";
include "config.inc.php";
include "functions.inc";
include "$dbsys.inc";
include "mrbs_auth.inc";
include "mincals.inc";

# 3-value compare: Returns result of compare as "< " "= " or "> ".
function cmp3($a, $b)
{
    if ($a < $b) return "< ";
    if ($a == $b) return "= ";
    return "> ";
}

# Default parameters:
if (empty($debug_flag)) $debug_flag = 0;
if (empty($month) || empty($year) || !checkdate(intval($month), 1, intval($year)))
{
    $month = date("m");
    $year  = date("Y");
}
$day = 1;

if(!getAuthorised(1))
{
	showAccessDenied($day, $month, $year, $area);
	exit;
}


# print the page header
print_header($day, $month, $year, $area);

if (empty($area)) $area = get_default_area();
// if (empty($room))   $room = get_default_room($area);
if (empty($room))   $room = "";
	
# Note $room will be 0 if there are no rooms; this is checked for below.

# Month view start time. This ignores morningstarts/eveningends because it
# doesn't make sense to not show all entries for the day, and it messes
# things up when entries cross midnight.
$month_start = mktime(0, 0, 0, $month, 1, $year);

# What column the month starts in: 0 means $weekstarts weekday.
$weekday_start = (date("w", $month_start) - $weekstarts + 7) % 7;

$days_in_month = date("t", $month_start);

$month_end = mktime(23, 59, 59, $month, $days_in_month, $year);

if( $enable_periods ) {
	$resolution = 60;
	$morningstarts = 12;
	$eveningends = 12;
	$eveningends_minutes = count($periods)-1;
}


# Define the start and end of each day of the month in a way which is not
# affected by daylight saving...
for ($j = 1; $j<=$days_in_month; $j++) {
	# are we entering or leaving daylight saving
	# dst_change:
	# -1 => no change
	#  0 => entering DST
	#  1 => leaving DST
	$dst_change[$j] = is_dst($month,$j,$year);
        if(empty( $enable_periods )){
		$midnight[$j]=mktime(0,0,0,$month,$j,$year, is_dst($month,$j,$year, 0));
		$midnight_tonight[$j]=mktime(23,59,59,$month,$j,$year, is_dst($month,$j,$year, 23));
	}
        else {
		$midnight[$j]=mktime(12,0,0,$month,$j,$year, is_dst($month,$j,$year, 0));
		$midnight_tonight[$j]=mktime(12,count($periods),59,$month,$j,$year, is_dst($month,$j,$year, 23));
        }
}

if ( $pview != 1 ) {
    # Table with areas, rooms, minicals.
    echo "<table width=\"100%\"><tr>";
    $this_area_name = "";
    $this_room_name = "";

    # Show all areas
    echo "<td width=\"30%\"><u>".get_vocab("areas")."</u><br>";
}

  # show either a select box or the normal html list
  if ($area_list_format == "select") {
    echo make_area_select_html('month.php', $area, $year, $month, $day); # from functions.inc
    $this_area_name = sql_query1("select area_name from $tbl_area where id=$area");
    $this_room_name = empty($room) ? "" : sql_query1("select room_name from $tbl_room where id=$room");
  } else {
    $sql = "select id, area_name from $tbl_area order by sort_order";
    $res = sql_query($sql);
    if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
    {
        if ( $pview != 1 ) echo "<a href=\"month.php?year=$year&month=$month&area=$row[0]\">";
        if ($row[0] == $area)
        {
            $this_area_name = htmlspecialchars($row[1]);
            if ( $pview != 1 )
                echo "<font color=\"red\">$this_area_name</font></a><br>\n";
        }
        else if ( $pview !=1 ) echo htmlspecialchars($row[1]) . "</a><br>\n";
    }
  } # end select if

if ( $pview != 1 ) {
    echo "</td>\n";
    
    # Show all rooms in the current area:
    echo "<td width=\"30%\"><u>".get_vocab("rooms")."</u><br>";
}


$room_list = array();
  # should we show a drop-down for the room list, or not?
  if ($area_list_format == "select") {
    echo make_room_select_html('month.php', $area, $room, $year, $month, $day); # from functions.inc
  } else {
    $sql = "select id, room_name from $tbl_room where area_id=$area order by sort_order";
    $res = sql_query($sql);
    if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
    {
    	$room_list[$row[0]] = $row[1];
        if($pview != 1) echo "<a href=\"month.php?year=$year&month=$month&area=$area&room=$row[0]\">";
        if ($row[0] == $room)
        {
            $this_room_name = htmlspecialchars($row[1]);
            if ( $pview != 1 )
                echo "<font color=\"red\">$this_room_name</font></a><br>\n";
        }
        else if ( $pview != 1 ) echo htmlspecialchars($row[1]) . "</a><br>\n";
    }
  } # end select if

if ( $pview != 1 ) {
    echo "</td>\n";
    
    #Draw the three month calendars
    minicals($year, $month, $day, $area, $room, 'month');
    echo "</tr></table>\n";
}

# Don't continue if this area has no rooms:
/*
if ($room <= 0)
{
    echo "<h1>".get_vocab("no_rooms_for_area")."</h1>";
    include "trailer.inc";
    exit;
}
*/

# Show Month, Year, Area, Room header:
// if ($pview != 1 ) echo "<div align=\"right\">Last Update By: ".getLastUpdateBy()."</div>\n";
echo "<h2 align=center>" . utf8_strftime("%B %Y", $month_start)
  . " - $this_area_name - $this_room_name</h2>\n";

# Show Go to month before and after links
#y? are year and month of the previous month.
#t? are year and month of the next month.

$i= mktime(12,0,0,$month-1,1,$year);
$yy = date("Y",$i);
$ym = date("n",$i);

$i= mktime(12,0,0,$month+1,1,$year);
$ty = date("Y",$i);
$tm = date("n",$i);
if ( $pview != 1 ) {
    echo "<table width=\"100%\"><tr><td>
      <a href=\"month.php?year=$yy&month=$ym&area=$area&room=$room\">
      &lt;&lt; ".get_vocab("monthbefore")."</a></td>
      <td align=center><a href=\"day.php?area=$area\">".get_vocab("gototoday")."</a>
	  &nbsp;&nbsp;
	  <a href=\"week.php?area=$area&room=$room\">".get_vocab("gotothisweek")."</a>
      &nbsp;&nbsp;
      <a href=\"month.php?area=$area&room=$room\">".get_vocab("gotothismonth")."</a></td>
      <td align=right><a href=\"month.php?year=$ty&month=$tm&area=$area&room=$room\">
      ".get_vocab("monthafter")."&gt;&gt;</a></td></tr></table>";
}

if ($debug_flag)
    echo "<p>DEBUG: month=$month year=$year start=$weekday_start range=$month_start:$month_end\n";

# Used below: localized "all day" text but with non-breaking spaces:
$all_day = get_vocab("all_day"); //ereg_replace(" ", "&nbsp;", get_vocab("all_day"));

#Get all meetings for this month in the room that we care about
# row[0] = Start time
# row[1] = End time
# row[2] = Entry ID
# This data will be retrieved day-by-day fo the whole month
for ($day_num = 1; $day_num<=$days_in_month; $day_num++) {
	$sql = "SELECT e.start_time, e.end_time, e.id, e.name, e.room_id, e.type FROM $tbl_entry e, $tbl_room r"
	.(empty($room) ? ", $tbl_area a" : "") 
	. " WHERE e.room_id = r.id"
	." AND e.start_time <= $midnight_tonight[$day_num] AND e.end_time > $midnight[$day_num]"
	.(empty($room) ? " AND r.area_id = a.id AND a.id=$area" : " AND e.room_id=$room")
	." ORDER by r.sort_order ASC, e.start_time ASC";
	# Build an array of information about each day in the month.
	# The information is stored as:
	#  d[monthday]["id"][] = ID of each entry, for linking.
	#  d[monthday]["data"][] = "start-stop" times or "name" of each entry.

	$res = sql_query($sql);
	$last_room_id = 0;
	if (! $res) echo sql_error();
	else for ($i = 0; ($row = sql_row($res, $i)); $i++)
	{
	    if ($debug_flag)
        	echo "<br>DEBUG: result $i, id $row[2], starts $row[0], ends $row[1]\n";

            if ($debug_flag) echo "<br>DEBUG: Entry $row[2] day $day_num\n";
            
            if(empty($room)){
            	// if $room is empty (show all room), get the room name
				if($row[4] != $last_room_id){
					$last_room_id = $row[4];
					$d[$day_num]["id"][] = 0;
					$d[$day_num]["data"][] = $room_list[$last_room_id];
					$d[$day_num]["shortdescrip"][] = "";
					$d[$day_num]["type"][] = "";
				}
            }
            
            $d[$day_num]["id"][] = $row[2];
            $d[$day_num]["shortdescrip"][] = $row[3];
            $d[$day_num]["type"][] = $row[5];
            
            # Describe the start and end time, accounting for "all day"
            # and for entries starting before/ending after today.
            # There are 9 cases, for start time < = or > midnight this morning,
            # and end time < = or > midnight tonight.
            # Use ~ (not -) to separate the start and stop times, because MSIE
            # will incorrectly line break after a -.
			
            if(empty( $enable_periods ) ){
              switch (cmp3($row[0], $midnight[$day_num]) . cmp3($row[1], $midnight_tonight[$day_num] + 1))
              {
        	case "> < ":         # Starts after midnight, ends before midnight
        	case "= < ":         # Starts at midnight, ends before midnight
                    $d[$day_num]["data"][] = utf8_strftime(hour_min_format(), $row[0]) . "~" . utf8_strftime(hour_min_format(), $row[1]);
                    break;
        	case "> = ":         # Starts after midnight, ends at midnight
                    $d[$day_num]["data"][] = utf8_strftime(hour_min_format(), $row[0]) . "~24:00";
                    break;
        	case "> > ":         # Starts after midnight, continues tomorrow
                    $d[$day_num]["data"][] = utf8_strftime(hour_min_format(), $row[0]) . "~==>";
                    break;
        	case "= = ":         # Starts at midnight, ends at midnight
                    $d[$day_num]["data"][] = $all_day;
                    break;
        	case "= > ":         # Starts at midnight, continues tomorrow
                    $d[$day_num]["data"][] = $all_day . "==>";
                    break;
        	case "< < ":         # Starts before today, ends before midnight
                    $d[$day_num]["data"][] = "<==~" . utf8_strftime(hour_min_format(), $row[1]);
                    break;
        	case "< = ":         # Starts before today, ends at midnight
                    $d[$day_num]["data"][] = "<==" . $all_day;
                    break;
        	case "< > ":         # Starts before today, continues tomorrow
                    $d[$day_num]["data"][] = "<==" . $all_day . "==>";
                    break;
              }
	    }
            else
            {
              $start_str = ereg_replace(" ", "&nbsp;", period_time_string($row[0]));
              $end_str   = ereg_replace(" ", "&nbsp;", period_time_string($row[1], -1));
              switch (cmp3($row[0], $midnight[$day_num]) . cmp3($row[1], $midnight_tonight[$day_num] + 1))
              {
        	case "> < ":         # Starts after midnight, ends before midnight
        	case "= < ":         # Starts at midnight, ends before midnight
                    $d[$day_num]["data"][] = $start_str . "~" . $end_str;
                    break;
        	case "> = ":         # Starts after midnight, ends at midnight
                    $d[$day_num]["data"][] = $start_str . "~24:00";
                    break;
        	case "> > ":         # Starts after midnight, continues tomorrow
                    $d[$day_num]["data"][] = $start_str . "~====>";
                    break;
        	case "= = ":         # Starts at midnight, ends at midnight
                    $d[$day_num]["data"][] = $all_day;
                    break;
        	case "= > ":         # Starts at midnight, continues tomorrow
                    $d[$day_num]["data"][] = $all_day . "====>";
                    break;
        	case "< < ":         # Starts before today, ends before midnight
                    $d[$day_num]["data"][] = "<====~" . $end_str;
                    break;
        	case "< = ":         # Starts before today, ends at midnight
                    $d[$day_num]["data"][] = "<====" . $all_day;
                    break;
        	case "< > ":         # Starts before today, continues tomorrow
                    $d[$day_num]["data"][] = "<====" . $all_day . "====>";
                    break;
              }
            }

	}
}
if ($debug_flag)
{
    echo "<p>DEBUG: Array of month day data:<p><pre>\n";
    for ($i = 1; $i <= $days_in_month; $i++)
    {
        if (isset($d[$i]["id"]))
        {
            $n = count($d[$i]["id"]);
            echo "Day $i has $n entries:\n";
            for ($j = 0; $j < $n; $j++)
                echo "  ID: " . $d[$i]["id"][$j] .
                    " Data: " . $d[$i]["data"][$j] . "\n";
        }
    }
    echo "</pre>\n";
}

// Include the active cell content management routines. 
// Must be included before the beginnning of the main table.
if ($javascript_cursor) // If authorized in config.inc.php, include the javascript cursor management.
    {
    echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\" src=\"xbLib.js\"></SCRIPT>\n";
    echo "<SCRIPT language=\"JavaScript\">InitActiveCell("
       . ($show_plus_link ? "true" : "false") . ", "
       . "false, "
       . "false, "
       . "\"$highlight_method\", "
       . "\"" . get_vocab("click_to_reserve") . "\""
       . ");</SCRIPT>\n";
    }

echo "<table border=\"1\" cellspacing=\"0\" width=\"100%\">\n<tr>";
# Weekday name header row:
for ($weekcol = 0; $weekcol < 7; $weekcol++)
{
    echo "<th width=\"14%\">" . day_name(($weekcol + $weekstarts)%7) . "</th>";
}
echo "</tr><tr>\n";

# Skip days in week before start of month:
for ($weekcol = 0; $weekcol < $weekday_start; $weekcol++)
{
    echo "<td bgcolor=\"#cccccc\" height=120>&nbsp;</td>\n";
}

# Draw the days of the month:
for ($cday = 1; $cday <= $days_in_month; $cday++)
{
    if ($weekcol == 0) echo "</tr><tr>\n";
    echo "<td valign=top height=120 class=\"month\"><div class=\"monthday\"><a href=\"day.php?year=$year&month=$month&day=$cday&area=$area\">$cday</a>&nbsp;\n";
    echo "</div>";

    # Anything to display for this day?
    if (isset($d[$cday]["id"][0]))
    {
        echo "<table width=\"100%\" cellspacing=0 cellpadding=0 border=0>\n";
        $n = count($d[$cday]["id"]);
        # Show the start/stop times, 2 per line, linked to view_entry.
        # If there are 12 or fewer, show them, else show 11 and "...".
        for ($i = 0; $i < $n; $i++)
        {
        	// break if box is full
        	/* remove checking to show all evens
            if ( ($i == 11 && $n > 12 && $monthly_view_entries_details != "both") or
                 ($i == 6 && $n > 6 && $monthly_view_entries_details == "both") )
            {
                echo " ...\n";
                break;
            }
            */
        	if($d[$cday]["type"][$i] == "Z"){
        		echo "<tr><td class=\"".$d[$cday]["type"][$i]."\">\n";
        	} else {
        		echo "<tr><td>\n";
        	}
        	echo "<font size=-2>";
            if ( ($i > 0 && $i % 2 == 0) or
                ($monthly_view_entries_details == "both"  && $i > 0) )
            {
                // echo "<br>";
            }
            else
            {
                echo " ";
            }
            switch ($monthly_view_entries_details)
            {
                case "description":
                {
                	if($d[$cday]["id"][$i] == 0){
                		echo htmlspecialchars($d[$cday]["shortdescrip"][$i]);
                	} else {
                    	echo "<a href=\"view_entry.php?id=" . $d[$cday]["id"][$i]
                        . "&day=$cday&month=$month&year=$year\" title=\""
                        . htmlspecialchars($d[$cday]["data"][$i]) . "\">"
                        . htmlspecialchars($d[$cday]["shortdescrip"][$i])
                        . "</a>";
                	}
                    break;
                }
                case "slot":
                {
                	if($d[$cday]["id"][$i] == 0){
                		echo htmlspecialchars($d[$cday]["data"][$i]);
                	} else {
                    	echo "<a href=\"view_entry.php?id=" . $d[$cday]["id"][$i]
                        . "&day=$cday&month=$month&year=$year\" title=\""
                        . htmlspecialchars($d[$cday]["shortdescrip"][$i]) . "\">"
                        . htmlspecialchars($d[$cday]["data"][$i]) . "</a>";
                	}
                    break;
                }
                case "both":
                {
                	if($d[$cday]["id"][$i] == 0){
                		echo htmlspecialchars($d[$cday]["data"][$i]) . " "
                        . htmlspecialchars($d[$cday]["shortdescrip"][$i]);
                	} else {
                    	echo "<a href=\"view_entry.php?id=" . $d[$cday]["id"][$i]
                        . "&day=$cday&month=$month&year=$year\">"
                        . htmlspecialchars($d[$cday]["data"][$i]) . " "
                        . htmlspecialchars($d[$cday]["shortdescrip"][$i]) . "</a>";
                	}
                    break;
                }
                default:
                {
                    echo "error: unknown parameter";
                }
            }
            echo "</font></td><tr>\n";
        }
        echo "</table>\n";
    }

    //echo "<br>";
    if ( $pview != 1 ) {
        if ($javascript_cursor)
	    {
            echo "<SCRIPT language=\"JavaScript\">\n<!--\n";
            echo "BeginActiveCell();\n";
            echo "// -->\n</SCRIPT>";
            }
        if( $enable_periods ) {
            echo "<a href=\"edit_entry.php?room=$room&area=$area"
            . "&period=0&year=$year&month=$month"
            . "&day=$cday\"><img src=new.gif width=10 height=10 border=0></a>";
        } else {
            echo "<a href=\"edit_entry.php?room=$room&area=$area"
            . "&hour=$morningstarts&minute=0&year=$year&month=$month"
            . "&day=$cday\"><img src=new.gif width=10 height=10 border=0></a>";
        }
        if ($javascript_cursor)
            {
            echo "<SCRIPT language=\"JavaScript\">\n<!--\n";
            echo "EndActiveCell();\n";
            echo "// -->\n</SCRIPT>";
            }
    }
    else
        echo '&nbsp;';
    echo "</td>\n";
    if (++$weekcol == 7) $weekcol = 0;
}

# Skip from end of month to end of week:
if ($weekcol > 0) for (; $weekcol < 7; $weekcol++)
{
    echo "<td bgcolor=\"#cccccc\" height=100>&nbsp;</td>\n";
}
echo "</tr></table>\n";

include "trailer.inc";
?>
