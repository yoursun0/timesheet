<?php
function fieldspecialchars($val){
	$special_char = array("&", "\"");
	$replace_char = array("&amp;", "&quot;");
	return str_replace($special_char, $replace_char, $val);
}

function mkwarn ($msg) {
	return("<span class=\"warnMsg\">${msg}</span>\n");
}

function mkerror ($msg) {
	//return("<font color=\"". $GLOBALS['ESPCONFIG']['error_color'] ."\" size=\"+1\">[ ${msg} ]</font>\n");
	return("<span class=\"errMsg\">${msg}</span>\n");
}

function mkselect ($_name, $options, $varr = "", $otherParam = "", $blankOption = true, $optionsBefore = array()) {
		if(!empty($other_param)) $other_param = " ".$other_param;
		
		$str  = "<select name=\"$_name\" id=\"$_name\"$otherParam>\n";
		
		// create additional before option first
		while(list($cid, $content) = each($optionsBefore)) {
			$checked = '';
			if (isset($varr) && strval($varr) === strval($cid))
				$checked = ' selected';
			$str .= "<option value=\"".fieldspecialchars($cid)."\"${checked}>".fieldspecialchars($content)."</option>\n";
		}
		
		// create blank option
		if($blankOption) $str .= "<option></option>\n";
		
		while(list($cid, $content) = each($options)) {
			$checked = '';
			if (isset($varr) && strval($varr) === strval($cid))
				$checked = ' selected';
			$str .= "<option value=\"".fieldspecialchars($cid)."\"${checked}>".fieldspecialchars($content)."</option>\n";
		}
		$str .= "</select>\n";
	return($str);
}

function mkSortOder($url, $currentOrder, $maxOrder){
	// use dropdown
	/*
	for($i = 1; $i <= $maxOrder; $i++) $sort_order_array[$i] = $i;
	echo(mkselect("", $sort_order_array, $currentOrder, "onChange=\"document.location='".$url."&fr_order=".$currentOrder."&to_order='+this.options[this.selectedIndex].value;\"", false));
	*/
	if($currentOrder > 1){
    	echo("<a href=\"$url&fr_order=$currentOrder&to_order=".($currentOrder-1)."\"><img src=\"arrow_up.gif\" width=\"15\" height=\"15\" border=\"0\" hspace=\"1\" vspace=\"2\" alt=\"Move Up\" align=\"absmiddle\"></a>");
    } else {
    	echo("<img src=\"arrow_up.gif\" width=\"15\" height=\"15\" border=\"0\" hspace=\"1\" vspace=\"2\" align=\"absmiddle\">");
    }
    if($currentOrder < $maxOrder){
    	echo("<a href=\"$url&fr_order=$currentOrder&to_order=".($currentOrder+1)."\"><img src=\"arrow_down.gif\" width=\"15\" height=\"15\" border=\"0\" hspace=\"1\" vspace=\"2\" alt=\"Move Down\" align=\"absmiddle\"></a>");
    } else {
    	echo("<img src=\"arrow_down.gif\" width=\"15\" height=\"15\" border=\"0\" hspace=\"1\" vspace=\"2\" align=\"absmiddle\">");
    }
}

function get_db_max_id($table_name, $id_field_name, $condition = ""){
	$sql = "select max($id_field_name) from $table_name";
	if(!empty($condition)){
		$sql .= " where $condition";
	}
	$max_id = sql_query1($sql);
	if(empty($max_id)) $max_id = 0;
	return $max_id;
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
				$sql = "select $id_field_name, $sort_field_name from $table_name $condition order by $sort_field_name LIMIT 0, $fr_order";
				$rs = mysql_query($sql);
				$i = 0;
				$curr_row = 0;
				while($row = mysql_fetch_row($rs)){
					$curr_row++;
					
					if($curr_row < $to_order) continue;
					if($i == 0) $new_order = $row[1];
					$new_id = $row[0];
					$sql2 = "update $table_name set $sort_field_name=".(intval($row[1])+1)." where $id_field_name='".$row[0]."'".$ori_condition;
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
					mysql_query("update $table_name set $sort_field_name=".(intval($row[1])-1)." where $id_field_name='".$row[0]."'".$ori_condition);
					$i++;
				}
			
			}
			mysql_query("update $table_name set $sort_field_name=".$new_order." where $id_field_name='".$new_id."'".$ori_condition);
		}
    }
?>