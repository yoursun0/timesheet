<?php
	function actionLog($cohort, $type1, $type2, $type3, $action, $result = "Success", $remark = "", $reference = "", $form_no="", $scrn="", $strn=""){
		$user = $_SESSION['acl']['user_name'];
		$datetime = date("Y-m-d H:i:s", time());
		$sql_f = array('datetime','user','cohort','type1','type2','type3','action','result','remark','reference','form_no','scrn', 'strn');
		$sql_v = array();
		foreach($sql_f as $f) $sql_v[] = ${$f};
		$sql = "insert into action_log ".gen_insert_sql($sql_f, $sql_v);
		
		mysql_query($sql);
		
		if(mysql_affected_rows() == 1){
			return mysql_insert_id();	
		} else {
			return 0;
		}
	}
	
	function dbLog($sql){
		$result = mysql_affected_rows();
		$error = mysql_error();
		
		if($error != ""){
			echo "Warning: $sql <br>\n";
			echo "Error: $error<br>\n";
		} 
		
		$datetime = date("Y-m-d H:i:s", time());
		$sql = "insert into db_log (log_date, sql_txt, result, error) values ('$datetime', '"._addslashes($sql)."', '"._addslashes($result)."', '"._addslashes($error)."')";
		mysql_query($sql);
	}
?>