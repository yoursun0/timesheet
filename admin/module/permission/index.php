<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
	$userList = $db->getArray("SELECT `id`,`room_name` as `name` FROM {$GLOBALS['db_table']['room']} ORDER BY `room_name`");
?>
<!--<div class="noprint">
	<a href="javascript:;" class="csportal_txtbutton" title="Create New Record" onclick="create();">Add New</a>
</div>
<div class="csportal_dotbreakline"></div>-->
<div class="noprint">
	<form id="filter_form" name="filter_form" class="csportal_form" onsubmit="return false;">
	<div style="float:left;">
		<label>Name : <input type="text" name="filter_name" id="filter_name" maxlength="20" /></label>
		<a href="javascript:;" class="csportal_txtbutton" title="search by name" onclick="filter();">Search</a>
	</div>
	<br style="clear:both" />
	</form>
</div>
<div class="csportal_dotbreakline"></div>
<div style="width:99%;">
	<div style="height:390px;">
	<table class="tablesorter" id="main_list" width="100%">
	<thead><tr>
		<th width="10%" class="{sorter: false}">Option</th>
		<th width="90%">Name</th>
	</tr></thead>
	<tbody>	
	</tbody>
	</table>
	</div>
	<div>
		<div id="main_pager"></div>
	</div>
</div>

<div id="edit_dialog" style="display:none;">
	<form id="edit_form" name="edit_form" class="csportal_form" onsubmit="return false;">	
		<div style="float:left;">
			<p>
			<label>`<span id="edit_form_name"></span>` Permission :</label>
			<div style="width:450px; overflow-y:hidden;border:2px solid #ccc;">
				<table style="font-size:12px;text-align:center;">
				<tr>
					<th width="150"> </th>
					<th width="50" bgcolor="#B3FFAF">All</th>
					<th width="50" bgcolor="#eeeeee">View</th>
					<th width="50">Edit</th>
					<th width="50" bgcolor="#eeeeee">Approve</th>
					<th width="50">Report</th>
				</tr>
				<tr>
					<td align="right"><b>All Users</b>&nbsp;&nbsp;</td>
					<td bgcolor="#B3FFAF"> - </td>
					<td bgcolor="#eeeeee"><input type="checkbox" onclick="$('input[name=view_ids[]]').attr('checked', $(this).attr('checked'));" /></td>
					<td ><input type="checkbox" onclick="$('input[name=edit_ids[]]').attr('checked', $(this).attr('checked'));" /></td>
					<td bgcolor="#eeeeee"><input type="checkbox" onclick="$('input[name=approve_ids[]]').attr('checked', $(this).attr('checked'));" /></td>
					<td><input type="checkbox" onclick="$('input[name=report_ids[]]').attr('checked', $(this).attr('checked'));" /></td>
				</tr>
				</table>
			</div>
			<div style="width:450px; height:300px;overflow-y:auto;border:2px solid #ccc;border-top:0;">
				<table style="font-size:12px;text-align:center;">
				<colgroup>
					<col width="150"/>
					<col width="50"/>
					<col width="50"/>
					<col width="50"/>
					<col width="50"/>
					<col width="50"/>
				</colgroup>
			<?php foreach ($userList as $row) : ?>
				<tr>
					<td align="right"><?=$row['name']?>&nbsp;&nbsp; </td>					
					<td bgcolor="#B3FFAF"><input type="checkbox" onclick="$(this).parent().siblings().find('input:checkbox').attr('checked', $(this).attr('checked'));" /></td>
					<td bgcolor="#eeeeee"><input type="checkbox" name="view_ids[]" value="<?=$row['id']?>" /></td>
					<td><input type="checkbox" name="edit_ids[]" value="<?=$row['id']?>" /></td>
					<td bgcolor="#eeeeee"><input type="checkbox" name="approve_ids[]" value="<?=$row['id']?>" /></td>
					<td><input type="checkbox" name="report_ids[]" value="<?=$row['id']?>" /></td>
				</tr>
			<?php endforeach; ?>
				</table>
			</div>
		</div>
		
		<br style="clear:both" />
		<div style="display:block;">		
			<input type="hidden" name="action" id="action" value="CREATE" />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="button" onclick="save()" value="Save" />
		</div>
	</form>
</div>