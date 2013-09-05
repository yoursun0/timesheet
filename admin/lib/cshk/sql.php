<?php

/* {{{ proto string array_to_insql(array elements)
   Returns a string of a SQL fragment to klugde around
   mysql's lack of nested SELECTS. */
function array_to_insql($array) {
	if (count($array))
		return("IN (".ereg_replace("([^,]+)","'\\1'",join(",",$array)).")");
	return 'IS NULL';
}
/* }}} */



/* {{{ proto string survey_select_section_sql(int survey_id, int section)
   Returns a string of a SQL fragment to limit questions to
   specified section. */
function survey_select_section_sql($sid, $section, $table = '') {
	if(!empty($table))
		$table .= '.';
	$sql = "
SELECT position
FROM question
WHERE survey_id='${sid}' AND
   type_id='99' AND
   deleted='N'
ORDER BY position,id";
	$result = mysql_query($sql);
	$num_sections = mysql_num_rows($result) + 1;

	if($section > $num_sections)
		return('');	// invalid section

	$ret = array("${table}survey_id='${sid}'", "${table}deleted='N'");
	if($section>1 && $num_sections>1)
		array_push($ret, "${table}position>" . mysql_result($result,$section-2,0));
	if($section<$num_sections && $num_sections>1)
		array_push($ret, "${table}position<" . mysql_result($result,$section-1,0));

	mysql_free_result($result);
	return('WHERE ' . join(' AND ',$ret) . ' ');
}
/* }}} */

/* {{{ proto array esp_type_has_choices()
   Returns an associative array of bools indicating if each
   question type has answer choices. */
function esp_type_has_choices() {
	$has_choices = array();
	$sql = 'SELECT id, has_choices FROM question_type ORDER BY id';
	$result = mysql_query($sql);
	while(list($tid,$answ) = mysql_fetch_row($result)) {
		if($answ == 'Y')
			$has_choices[$tid]=1;
		else
			$has_choices[$tid]=0;
	}
	mysql_free_result($result);
	return($has_choices);
}
/* }}} */

/* {{{ proto array esp_type_response_table()
   Returns an associative array of bools indicating the
   table the responses are stored in. */
function esp_type_response_table() {
	$sql = 'SELECT id, response_table FROM question_type ORDER BY id';
	$result = mysql_query($sql);
	$response_table = array();
	while(list($tid,$answ) = mysql_fetch_row($result)) {
		$response_table[$tid]=$answ;
	}
	mysql_free_result($result);
	return($response_table);
}
/* }}} */
function sql_to_array_assoc($sql){
	$array = array();
	$rs = mysql_query($sql);
	if (!$rs) {
   		echo("<br>\nInvalid query: $sql <br>\n" . mysql_error());
	}
	while($row = mysql_fetch_assoc($rs)){
		$array[] = $row;
	}
	mysql_free_result($rs);
	return $array;
}

function sql_to_array($sql, $col_num = 2){
	$list_array = array();
	$rs = mysql_query($sql);
	if (!$rs) {
   		echo("<br>\nInvalid query: $sql <br>\n" . mysql_error());
	}
	if($col_num == 2){
		while(list($id, $val) = mysql_fetch_row($rs)){
			$list_array[$id] = $val;
		}
	} else if($col_num == 1){
		while(list($val) = mysql_fetch_row($rs)){
			$list_array[] = $val;
		}
	}
	
	mysql_free_result($rs);
	return $list_array;
}

function sql_to_assoc($sql){
	$array = null;
	$rs = mysql_query($sql);
	if (!$rs) {
   		echo("<br>\nInvalid query: $sql <br>\n" . mysql_error());
	}
	if(mysql_num_rows($rs) == 1){
		$row = mysql_fetch_assoc($rs);
		$array = $row;
	}
	mysql_free_result($rs);
	return $array;
}


function sql_exe($sql){
//	echo($sql."<br>\n");
	$str = '';
	$rs = mysql_query($sql);
	if (!$rs) {
   		echo("<br>\nInvalid query: $sql <br>\n" . mysql_error());
	}

	while($row = mysql_fetch_row($rs)){
		$str = $row[0];
	};
	mysql_free_result($rs);
	return $str;
}

function sqlWildcard($fieldName, $fieldValue, $operator = "AND"){
	$fieldValue = trim($fieldValue);
	$fieldValue = _addslashes($fieldValue);
	$fieldValue = str_replace("_", "\\_", $fieldValue);
	$fieldValue = str_replace("%", "\%", $fieldValue);
	$fieldValue = str_replace("*", "%", $fieldValue);
	if(!empty($fieldValue)){
		$fieldValue = " ".$operator." $fieldName LIKE '".$fieldValue."'";
	}
	return $fieldValue;
}

function survey_get_operators($sid, $type) {
	$operators = array();
	if(!empty($sid)){
		$sql = "SELECT oid FROM eoperator_right WHERE sid = '$sid' and type='$type'";
		$result = mysql_query($sql);
		while (list($oId) = mysql_fetch_row($result)) {
				array_push($operators, array($oId));
		}
		mysql_free_result($result);
	}
	return $operators;
}

function survey_get_org_user_group($sid) {
	$grp = array();
	if(!empty($sid)){
		$sql = "SELECT oid FROM org_right WHERE sid = '$sid'";
		$result = mysql_query($sql);
		while (list($oId) = mysql_fetch_row($result)) {
				array_push($grp, array($oId));
		}
		mysql_free_result($result);
	}
	return $grp;
}

function search_staff_dept_name($user_id, &$user_name, &$dept_name){
	$sql="select s.Fullname, d.dept_desc from v_survey_user s, dept_list d where s.Dept_ID = d.dept_id and s.User_id='$user_id'";
	
	$rs = mysql_query($sql);
	if(mysql_num_rows($rs) > 0){
		$row = mysql_fetch_array($rs);
		$user_name = $row[0];
		$dept_name = $row[1];
	} else {
		$user_name = "-";
		$dept_name = "-";
	}
}

function implode_with_keys($glue, $array) {
       $output = array();
       foreach( $array as $key => $item )
               array_push($output, $key);

       return implode($glue, $output);
} 

function get_rate($currency_id, $date = ''){
	$rate = sql_exe("SELECT rate FROM currency_rate WHERE curr_type_id ='$currency_id' and date >= '$date' ORDER BY date LIMIT 0, 1");

	//$sql = "SELECT rate FROM currency_rate WHERE curr_type_id ='$currency_id' and date <= '$date' ORDER BY date DESC LIMIT 0, 1";
	if(empty($rate)) $rate = sql_exe("SELECT rate FROM currency_rate WHERE curr_type_id ='$currency_id' and date <= '$date' ORDER BY date DESC LIMIT 0, 1");
		
	if(empty($rate)){
		$rate = 1;
	}
	return $rate;
}

function get_sql_var_by_field($sql_f){
	$sql_v = array();
	for($i = 0; $i < sizeof($sql_f); $i++){
		global ${$sql_f[$i]};
		if(isset(${$sql_f[$i]})){
			array_push($sql_v, ${$sql_f[$i]});
		} else {
			array_push($sql_v, "");
		}
	}
	return $sql_v;
}

function gen_insert_sql($sql_f, $sql_v){
	for($i = 0; $i < sizeof($sql_v); $i++){
		if($sql_v[$i] === null){
			$sql_v[$i] = 'NULL';
		} else {
			$sql_v[$i] = "'"._addslashes($sql_v[$i])."'";
		}
	}
	$sql = "(".join(",", $sql_f).") values ";
	$sql .= "(".join(",", $sql_v).")";
	
	return $sql;
}

function gen_update_sql($sql_f, $sql_v){
	for($i = 0; $i < sizeof($sql_v); $i++){
		if($sql_v[$i] === null){
			$sql_v[$i] = 'NULL';
		} else {
			$sql_v[$i] = "'"._addslashes($sql_v[$i])."'";
		}
	}
	$sql = "set ".$sql_f[0]."=".$sql_v[0];
	for($i = 1; $i < sizeof($sql_v); $i++){
		$sql .= ",".$sql_f[$i]."=".$sql_v[$i];
	}
	
	return $sql;
}
	
function check_related_table_exist($related_tables, $fk_value, &$msg, $table_desc){
	$exist = false;
	for($i = 0; $i < sizeof($related_tables); $i++){
		$related_table = $related_tables[$i];
		$table = $related_table[0];
		$field = $related_table[1];
		$desc = $related_table[2];
		$condition = '';
		if(count($related_table) >= 4) $condition = " and ".$related_table[3];
		$related_exist = (intval(sql_exe("select count(*) from $table where $field = '$fk_value'".$condition)) > 0);
		if($related_exist){
			if(!$exist){
				$msg .= "Deletion failure. The $table_desc is NOT empty.Please delete related records in the $table_desc:<br>\n";
			} else {
				$msg .= "<br>\n";
			}
			
			$msg .= "<span class='errMsgSm'>- ".$desc."</span>\n";
			$exist = true;

		}
	}
	return $exist;
}

function del_related_table($related_tables, $fk_value){
	for($i = 0; $i < sizeof($related_tables); $i++){
		$related_table = $related_tables[$i];
		$table = $related_table[0];
		$field = $related_table[1];
		$condition = '';
		if(count($related_table) >= 3) $condition = " and ".$related_table[2];
		$sql = "delete from $table where $field = '"._addslashes($fk_value)."'".$condition;
		mysql_query($sql);
	}
}

function del_table_by_id($table_name, $pk_field, $pk_value, &$msg, $table_desc = '', $related_tables = array(), $auto_del_tables = array(), $condition=''){
	$delete_ok = true;
	// check any related records
	if(check_related_table_exist($related_tables, $pk_value, $msg, $table_desc)){
		// error related table exist
		$delete_ok = false;
	} else {
		// no related table, can be deleted
		if($condition != '') $condition = " and ".$condition;
		mysql_query("delete from $table_name where $pk_field = '$pk_value'".$condition);
		if(mysql_affected_rows() == 1){
			$msg .= "Delete success";
			$delete_ok = true;
			del_related_table($auto_del_tables, $pk_value);
		}
	}
	return $delete_ok;
} 

function get_db_max_id($table_name, $id_field_name, $condition = ""){
	$sql = "select max($id_field_name) from $table_name";
	if(!empty($condition)){
		$sql .= " where $condition";
	}
	$max_id = sql_exe($sql);
	if(empty($max_id)) $max_id = 0;
	return $max_id;
}

function get_sort_order_array($size){
	$sort_order_select = array();
	for($i = 1; $i <= $size ; $i++){
		$sort_order_select[$i] = $i;
	}
	return $sort_order_select;
}

function sort_order($table_name, $id_field_name, $sort_field_name, $fr_order, $to_order, $condition = ''){
    if(!empty($table_name) 
    	&& !empty($id_field_name) 
    	&& !empty($sort_field_name) 
    	&& !empty($fr_order) 
    	&& !empty($to_order)){
//			$group_id = intval($group_id);
		$fr_order = intval($fr_order);
		$to_order = intval($to_order);
		if($fr_order == $to_order) return ; // return if "number of from" == "number of to"
		$ori_condition = '';
		if(!empty($condition)){
			$ori_condition = " and ($condition)";
			$condition = " where ($condition)";
		}
		
		$new_id = 0;
		$new_order = 0;
		if($fr_order > $to_order){
			//$sql = "select $id_field_name, $sort_field_name from $table_name $condition order by $sort_field_name limit ".($to_order -1).", ".($fr_order - $to_order + 1);
			$sql = "select $id_field_name, $sort_field_name from $table_name $condition order by $sort_field_name LIMIT 0, $fr_order";
			$rs = mysql_query($sql);
			$i = 0;
			$curr_row = 0;
			while($row = mysql_fetch_row($rs)){
				$curr_row++;
				
				if($curr_row < $to_order) continue;
				if($i == 0) $new_order = $row[1];
				$new_id = $row[0];
				$sql2 = "update $table_name set $sort_field_name=".(intval($row[1])+1)." where $id_field_name='"._addslashes($row[0])."'".$ori_condition;
				//echo("<br> $sql2");
				mysql_query($sql2);
				$i++;
			}
		} else {
			//$sql = "select $id_field_name, $sort_field_name from $table_name $condition order by $sort_field_name limit ".($fr_order -1).", ".($to_order - $fr_order + 1);
			$sql = "select $id_field_name, $sort_field_name from $table_name $condition order by $sort_field_name LIMIT 0, $to_order";
			$rs = mysql_query($sql);
			$curr_row = 0;
			$i = 0;
			while($row = mysql_fetch_row($rs)){
				$curr_row++;
				
				if($curr_row < $fr_order) continue;
				if($i == 0) $new_id = $row[0];
				$new_order = $row[1];
				//echo("update $table_name set $sort_field_name=".(intval($row[1])-1)." where $id_field_name=".$row[0]."<br>\n");
				mysql_query("update $table_name set $sort_field_name=".(intval($row[1])-1)." where $id_field_name='"._addslashes($row[0])."'".$ori_condition);
				$i++;
			}
		
		}
		mysql_query("update $table_name set $sort_field_name=".$new_order." where $id_field_name='"._addslashes($new_id)."'".$ori_condition);
	}
}
    
function saveContactChange($form_no){
	$slashed_form_no = _addslashes($form_no);
	$sql = "select count(*) from contact_info where form_no='$slashed_form_no'";
	$existing_count = intval(sql_exe($sql));
	if($existing_count > 0){
		$sql = "delete from contact_info where form_no='$slashed_form_no'";
		mysql_query($sql);
		dbLog($sql);
	}
	$sql_f = array("form_no");
	$sql_v = array($form_no);
	for($i = 1; $i <= 4; $i++){
		$sql_f[] = "contact_info_eng_name_".$i;
		$sql_f[] = "contact_info_chn_name_".$i;
		$sql_f[] = "contact_info_post_".$i;
		$sql_f[] = "contact_info_email_".$i;
		
		$sql_v[] = getPost("contact_info_eng_name_".$i);
		$sql_v[] = getPost("contact_info_chn_name_".$i);
		$sql_v[] = getPost("contact_info_post_".$i);
		$sql_v[] = getPost("contact_info_email_".$i);
	}
	$sql = "insert into contact_info ".gen_insert_sql($sql_f, $sql_v);
	mysql_query($sql);
	$mysql_affected_rows = mysql_affected_rows();
	dbLog($sql);
	if($mysql_affected_rows != 1) die("error: $sql");
}

function check2ndInputContactInfo($form_no){
	$error_found = "";
	$check_fields = array("contact_info_eng_name" => "English Name", "contact_info_chn_name"=>"Chinese Name", "contact_info_post"=>"Post", "contact_info_email"=>"Email");
	$contact_obj = sql_to_assoc("select * from contact_info where form_no='"._addslashes($form_no)."'");
	for($i = 1; $i <= 4; $i++){
		foreach($check_fields as $c_f=> $c_title){
			if($contact_obj[$c_f."_".$i] != getPost($c_f."_".$i)){
				$error_found = "Contact Persion ($i) $c_title not match (".$contact_obj[$c_f."_".$i].")";
				break;
			}
		}
		if($error_found != "") break;
	}
	return $error_found;
}
?>
