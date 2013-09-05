<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
	$active_role = $db->getArray("SELECT `id`,`name` FROM `{$GLOBALS['db_table']['role']}` WHERE `status`=1");
?>
<div class="noprint">
	<!--<a href="javascript:;" class="csportal_txtbutton" title="Create New Record" onclick="create();">Add New</a>-->
</div>
<div class="csportal_dotbreakline"></div>
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
	<table class="tablesorter" id="main_list" width="100%">
	<thead><tr>
		<th width="20%" class="{sorter: false}">Option</th>
		<th width="20%">Name</th>
		<th width="">Email</th>
		<th width="10%">Area ID</th>
		<th width="10%">Room ID</th>
	</tr></thead>
	<tbody>	
	</tbody>
	</table>
	<div>
		<div id="main_pager"></div>
	</div>
</div>

<div id="edit_dialog" style="display:none;">
	<form id="edit_form" name="edit_form" class="csportal_form" onsubmit="return false;">
		<div style="width:210px;float:left; padding-right:10px;">
			<p>
				<label>Name <span>＊</span><br /><input type="text" name="name" id="name" class="required" maxlength="20" style="width:200px" /></label>
			</p>
			<p>
				<label>Email <span>＊</span><br /><input type="text" name="email" id="email" class="required" maxlength="100" style="width:200px" /></label>
			</p>
			<p>
				<label>Password <br /><input type="text" name="password" id="password" class="" maxlength="20" style="width:200px" /></label>
			</p>
			<br style="clear:both" />
		</div>
		
		<div style="float:left;">
			<label>Role :</label>
			<p>
			<div style="width:230px; height:200px;overflow-y:auto;">
			<?php foreach ($active_role as $row) : ?>
				<input type="checkbox" name="role_id[]" id="role_<?=$row['id']?>" value="<?=$row['id']?>" />
				<label style="width:180px" for="role_<?=$row['id']?>"><?=$row['name']?></label>
				<br />
			<?php endforeach; ?>
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