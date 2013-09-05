<?php defined('__VALID_REQUEST__') or die('Restricted Access'); ?>
<?php
	CS_Form::getSubmit(array("sorter","pager"));
	CS_Form::getSubmit(array("filter_name"));
	$name= $filter_name;
	$ENUM_STATUS = array(0=>"Blocked",1=>"Active");	
	$table_columns = array('','date','name');
	
	$count = $db->getOne("SELECT COUNT(*) FROM `{$GLOBALS['db_table']['holidays']}` WHERE `name` LIKE '%".$db->quote($name,false)."%'");
	
	$ts = new TableSorterRequest();
	$limit = $ts->processPagination($pager,$count,30);
	$order = $ts->processSorting($sorter,$table_columns);
	
	$sql = "SELECT * FROM `{$GLOBALS['db_table']['holidays']}` WHERE `name` LIKE '%".$db->quote($name,false)."%' $order $limit";
	$rs = $db->getArray($sql);

	/* make result */
	$json['pager'] 	= $pager;
	$json['html'] 	= "";
	foreach ($rs as $row) {
		$json['html'] .= '
		<tr><td nowrap="nowrap">
				<input type="checkbox" value="'.$row['id'].'" />
				<a href="javascript:;" onclick="edit('.$row['id'].');" class="csportal_txtbutton">Edit</a>
				<a href="javascript:;" onclick="del('.$row['id'].');" class="csportal_txtbutton">Delete</a>
			</td>
			<td>'.$row['date'].'</td>
			<td>'.$row['name'].'</td>
		</tr>';
	}
	echo json_encode($json);
?>