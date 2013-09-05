<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
//------------------------------------------------------------------------------
// Prepare Data
//------------------------------------------------------------------------------
	//Settings
	
	$selected_department 	= CS_Form::getPost('g',0);
	$selected_user 			= CS_Form::getPost('u',0);
	$selected_date			= CS_Form::getPost('d',0);
	$selected_view			= CS_Form::getPost('v','week');
	
//------------------------------------------------------------------------------
// Main Program
//------------------------------------------------------------------------------
	include('func.inc.php');

	$view = 'week';
	$permissions = getUserPermissions($db,$current_user->getId());
	if (empty($permissions)) {
		die('access denied');
	}

	$departments = getActiveDepartments($db);

	$selected_date = processSelectedDate($selected_date);
	
	$selected_department = processSelectedDepartment($selected_department,$departments,$permissions,'view');
 	
	$department_members = getDepartmentMembers($db,$selected_department);
	
	$selected_user = processSelectedUser($department_members,$selected_user);

	$_SESSION['selected_date'] = $selected_date;
	$_SESSION['selected_department'] = $selected_department;
	$_SESSION['selected_user'] = $selected_user;
	$_SESSION['selected_view'] = $selected_view;

	$start_time = $selected_date;
	$end_time	= $start_time + 7 * 86400 - 1;
	$events = getEvents($db,$start_time,$end_time,$selected_user);
	$rowspan = array_fill(0,7,0);

	//CS_Debug::printArray($events);
//------------------------------------------------------------------------------
// Print Output
//------------------------------------------------------------------------------
echo CS_Form::hidden('selected_date',$selected_date);
echo CS_Form::hidden('selected_department',$selected_department);
echo CS_Form::hidden('selected_user',$selected_user);
echo CS_Form::hidden('selected_view',$selected_view);
?>
<table width="100%">
	<tr>
		<td width="20%">Department</td>
		<td width="20%">Staff</td>
		<td width="60%">Date</td>
	</tr>
	<tr>
		<td valign="top"><?php printDepartments($selected_date,$departments,$selected_department,$view); ?></td>
		<td valign="top"><?php printUsers($selected_date,$department_members,$selected_user,$selected_department,$view); ?></td>
		<td valign="top"><div id="weekCalendar"></div></td>
	</tr>
</table>
<div class="csportal_dotbreakline"></div>
<table border="1" width="100%" style="border-style:dotted;">
	<thead>
		<tr>
			<th width="7%">Option</th>
			<?php
			for ($i = 0; $i <= 6; $i++) {
				$day_ts = $start_time + $i * 86400;
				echo "<th width='13%'>";
				echo CS_Form::txtButton("approved(this,$selected_user,$day_ts)",'Approve');
				echo "</th>";
			}
			?>
		</tr>
		<tr>
			<th width="7%">Time</th>
			<?php
			for ($i = 0; $i <= 6; $i++) {
				echo "<th width='13%'>".date('D, j M',$start_time + $i * 86400)."</th>";
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
		$show_this_row = false;
		for ($i = 0; $i < 86400; $i+=900) {
			$table_row_conten = '';
			
			$table_row_conten .= "<tr>";
			$table_row_conten .= "<th>".date('H:i',($start_time + $i))."</th>";
			for ($j = 0; $j <= 6; $j++) {
				$key = $selected_user.'_'.($start_time + ($j * 86400) + $i);
				if (isset($events[$key])) {
					$event = $events[$key];
					$rowspan[$j] = $event['duration'] / 900;
					$table_row_conten .= "<td class='eventbox_type_{$event['type']}' rowspan='{$rowspan[$j]}' valign='top'>";					
					$table_row_conten .= "<b><a href='javascript:;' onclick='view()'>".$event['name']."</a></b><br/>";
					$table_row_conten .= date('H:i',$event['start_time']).'~'.date('h:i',$event['end_time']);
					$table_row_conten .= "</td>";
					$show_this_row = true;
				} elseif ($rowspan[$j] > 1) {
					$rowspan[$j]--;
				} else {
					$table_row_conten .= "<td> </td>";		
				}
			}
			$table_row_conten .= "</tr>";
			if (true === $show_this_row) {
				echo $table_row_conten;
			}
		}
	?>
	</tbody>
</table>
<script type="text/javascript">
	var selected_date = new Date($('#selected_date').val() * 1000);
    $('#weekCalendar').datePicker({
        inline: true,
        selectWeek: true,
        startDate: '01/12/2008'
    }).dpSetSelected(selected_date.format('d/m/Y'))
    .trigger('change').bind('dateSelected', onWeekChanged);
</script>