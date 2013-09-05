<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<div class="noprint">
	<a href="javascript:;" class="csportal_txtbutton" title="Create New Record" onclick="create();">Add New</a>
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
		<th width="20%">Key</th>
		<th width="20%">Name</th>
		<th width="30%">Description</th>
		<th width="10%">Status</th>
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
				<label>Key <span>＊</span><br /><input type="text" name="key" id="key" class="required" maxlength="100" style="width:200px" /></label>
			</p>
			<p>
				<label>Name <span>＊</span><br /><input type="text" name="name" id="name" class="required" maxlength="100" style="width:200px" /></label>
			</p>
			<p>
				<label>Description <br /><input type="text" name="description" id="description" class="" maxlength="100" style="width:200px" /></label>
			</p>
			<p>
				<label>Status <span>＊</span></label><br />
				<label class="option"><input type="radio" name="status" id="status_active"  value="1" class="required" checked="checked" />Active</label>
				<label class="option"><input type="radio" name="status" id="status_blocked"  value="0" />Blocked</label>
				<label for="status" class="error" style="display: none;">Please select the status</label>
			</p>
			<br style="clear:both" />
		</div>
		<br style="clear:both" />
		<div style="display:block;">		
			<input type="hidden" name="action" id="action" value="CREATE" />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="button" onclick="save()" value="Save" />
		</div>
	</form>
</div>