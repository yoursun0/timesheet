<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
	$GLOBALS['js'][] = '<MODULE>eventbox.js';
	$GLOBALS['js'][] = '<MODULE>date.js';
	$GLOBALS['js'][] = '<MODULE>jquery.datePicker.js';
	$GLOBALS['css'][] = '<MODULE>datePicker.css';
/*

	if(!isset($_SESSION['selected_date']['d']) || !isset($_SESSION['selected_date']['m']) || !isset($_SESSION['selected_date']['y'])) {
		$_SESSION['selected_date']['d'] = date("d");
		$_SESSION['selected_date']['m'] = date("m");
		$_SESSION['selected_date']['y'] = date("Y");
	}
	
	echo CS_Form::hidden('selected_day',$_SESSION['selected_date']['d']);
	echo CS_Form::hidden('selected_mon',$_SESSION['selected_date']['m']);
	echo CS_Form::hidden('selected_year',$_SESSION['selected_date']['y']);

*/
?>
<!--<div class="noprint">
	<a href="javascript:;" class="csportal_txtbutton" title="Create New Record" onclick="create();">Add New</a>
</div>
<div class="csportal_dotbreakline"></div>-->
<!--<div class="noprint">
	<table>
		<tr>
			<td><div id="weekCalendar"></div></td>
			<td><div id="dateCalendar"></div></td>
		</tr>
	</table>
	
	
</div>-->

<div style="width:99%;">
	<div id="div_view_container"></div>
</div>
