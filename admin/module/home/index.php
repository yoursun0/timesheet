<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
	$GLOBALS['js'][] = '<MODULE>eventbox.js';
	$GLOBALS['js'][] = '<MODULE>date.js';
	$GLOBALS['js'][] = '<MODULE>jquery.datePicker.js';
	$GLOBALS['css'][] = '<MODULE>datePicker.css';
/*	if(!isset($_SESSION['selected_date']['d']) || !isset($_SESSION['selected_date']['m']) || !isset($_SESSION['selected_date']['y'])) {
		$_SESSION['selected_date']['d'] = date("d");
		$_SESSION['selected_date']['m'] = date("m");
		$_SESSION['selected_date']['y'] = date("Y");
	}*/

	if (!isset($_SESSION['selected_date'])) {
		$now_timestamp = time();
		$_SESSION['selected_date'] = $now_timestamp - (date('N') - 1) * 86400 - ($now_timestamp % 86400);
	}
	$selected_date = $_SESSION['selected_date'];
	
	
	echo CS_Form::hidden('selected_date',$selected_date);
	
	//echo CS_Form::hidden('selected_day',$_SESSION['selected_date']['d']);
	//echo CS_Form::hidden('selected_mon',$_SESSION['selected_date']['m']);
	//echo CS_Form::hidden('selected_year',$_SESSION['selected_date']['y']);

	$columnCount = 7;
	$columnWith = round(100.0 / 7, 3);
?>


<table>
	<tr valign="top">
		<td valign="top" height="160" width="140"><div id="weekCalendar" style="height: 80px; width:"></div></td>

	</tr>
</table>
<div id="timesheet_container"><table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td class="timesheet_columnTop">
			<span class="year" style="color:#fff;position:absolute;">[year]</span>
			<div style="width:auto !important;width:100%">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr valign="top">
			<?				
				for($i = 0; $i < $columnCount; $i++){
					$tempDate = mktime(0, 0, 0, $_SESSION['selected_date']['m'], $_SESSION['selected_date']['d'] + $i, $_SESSION['selected_date']['y']);
					echo "<td width='".$columnWith."%'>".date("D d/m", $tempDate)."</td>\n";
				}
			?>
					</tr>
				</tbody>
			</table>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="gridcontainer" style="overflow-x: hidden; overflow-y: scroll; height: 400px;" unselectable="on">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" style=" height: 1440px;">
					<tbody>
						<tr valign="top">
							<td style="width: 40px;" class="timesheet_hourbg">
							  <div>12am</div>
							  <div>1am</div>
							  <div>2am</div>
							  <div>3am</div>
							  <div>4am</div>
							  <div>5am</div>
							  <div>6am</div>
							  <div>7am</div>
							  <div>8am</div>
							  <div>9am</div>
							  <div>10am</div>
							  <div>11am</div>
							  <div>12pm</div>
							  <div>1pm</div>
							  <div>2pm</div>
							  <div>3pm</div>
							  <div>4pm</div>
							  <div>5pm</div>
							  <div>6pm</div>
							  <div>7pm</div>
							  <div>8pm</div>
							  <div>9pm</div>
							  <div>10pm</div>
							  <div>11pm</div>
							</td>
							<td style="width: auto;">
								<table width="100%" cellspacing="0" cellpadding="0" border="0" class="timesheet_timebg" style=" height: 1440px;">
									<tbody>
										<tr valign="top">
								<?php for($i = 0; $i < $columnCount; $i++): ?>
										<td width="<?=$columnWith?>%" class="timesheet_columnBody">
										<div class="timesheet_daywrapper" id='column_<?=strtotime("2009-01-0$i 00:00:00")?>' unselectable="on">
											<span class="timesheet_columnParam">{json format}</span>
										</div></td>
								<?php endfor; ?>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</td>
	</tr>
</table></div>
<div id="hiddenArea"  style="display:none;"></div>
	<div id="timesheet_detailDialog" style="display:none;">
		<form id="timesheet_detailDialog_form" name="timesheet_detailDialog_form" class="csportal_form" onsubmit="return false;">
			<div style="width:210px; padding-right:10px;">
				<span id="formTime"></span>
				<input type="hidden" name="start_time" value="" />
				<input type="hidden" name="end_time" value="" />
				<p><label for="job_no">Job No.: <span>＊</span></label><br />
					<input type="text" name="job_no" id="job_no" class="required" maxlength="5" size="5" style="text-align:center;" />
				</p>
				<p><label for="job_no">Description: </label><br />
					<textarea name="job_description" id="job_description" class="" rows="5" style="width:100%" ></textarea>
				</p>
			</div>
			
			<input type="button" value="Save" onclick="save()" />
			<input type="button" value="Delete" onclick="del()" />
			<br clear="all"/>
		</form>
	</div>
