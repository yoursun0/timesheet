<?php
class CS_Debug {
	private $timer;

	public static function getVarName(&$var,$scope=false,$prefix='unique',$suffix='value') {
		$vals = $scope ? $scope : $GLOBALS;
		$old = $var;
		$var = $new = $prefix.rand().$suffix;
		$vname = false;
		foreach($vals as $key => $val) {
			if($val === $new) $vname = $key;
		}
		$var = $old;
		return $vname;
	}	
	public static function printVar(&$array,$print=true) {
		$s = isset($array) ? "var $".self::getVarName($array)." = ".var_export($array,true).";" : "unknown variable";
		if ($print) {
			echo "<pre class='debug'>\n".$s."</pre>\n";
		} else {
			return "<pre class='debug'>\n".$s."</pre>\n";
		}
	}
	public static function printArray(&$array) {
		if (!isset($array)) {
			echo "unknown variable";
		}
		if (is_array($array)) {
			echo "<pre class='debug'>\n";
			print_r($array);
			echo "</pre>\n";
		} else {
			echo "var $".self::getVarName($array)." = ".var_export($array,true).";";
		}
	}
	public static function printWarn($msg) {
		echo "<span class='csportal_warnmsg'>".htmlspecialchars($msg)."</span>";		
	}
	public static function printError($msg) {
		echo "<span class='csportal_errormsg'>".htmlspecialchars($msg)."</span>";
	}

	public static function timerStart($id="main"){
		self::$timer[$id]['interval'] = null;
		self::$timer[$id]['stop'] 	= null;
		self::$timer[$id]['start']	= microtime();
	}
	public static function timerStop($id="main") {
		self::$timer[$id]['stop'] 	= microtime();		
	}
	public static function timerInterval($id="main") {
		if (empty(self::$timer[$id]['end'])) {self::timerStop();}
		$s 	= array_sum(explode(" ",self::$timer[$id]['start']));
		$e 	= array_sum(explode(" ",self::$timer[$id]['end']));
		return self::$timer[$id]['interval'] = $e - $s;
	}
	public static function timerPrint($id="main"){
		if (empty(self::$timer[$id]['interval'])) {self::timerInterval();}
		$s 	= array_sum(explode(" ",self::$timer[$id]['start']));
		$t	= self::$timer[$id]['interval'];
		echo "$id start time = ".date("H:i:s",$s).", running time = ".round($t*1000,16)." ms.<br />\n"; 		
	}	
}

class CS_Response{
	public static function error($msg = '',$params = array()) {
		self::returnJSON('error',$msg,$params);
	}
	public static function warning($msg = '',$params = array()) {
		self::returnJSON('warn',$msg,$params);	
	}
	public static function success($msg = '',$params = array()) {
		self::returnJSON('ok',$msg,$params);		
	}
	public static function returnJSON($type,$msg,$params) {
		$r = array();
		$r['type'] = $type;
		if (!empty($msg)) {
			if (is_array($msg)) {				
				$r['msg'] = var_export($msg,true);
			} else {
				$r['msg'] = $msg;				
			}
		}
		if (!empty($params)) {			
			$r['params'] = $params;
		}
		exit(json_encode($r));
	}
}

class CS_Basic {
	public static function makeArray($s){
		$a = array();
		$AAs = preg_split("/[,]/", $s);
		foreach($AAs as $AA){
			$AA = trim($AA);
			if(strpos($AA, "-") === false){
				$a[] = $AA;
			} else {
				$BBs = preg_split("/[-]/", $AA);
				if(count($BBs) != 2){
					echo "mkArray error: Invalid range found in string (".$AA.")" ;
					return array();
				} else {
					$BBs[0] = trim($BBs[0]);
					$BBs[1] = trim($BBs[1]);
					if(ereg('^[0-9]*$', $BBs[0]) && ereg('^[0-9]*$', $BBs[1])){
						if($BBs[0] < $BBs[1]){
							for($i = $BBs[0]; $i <= $BBs[1]; $i++){
								$a[] = $i;
							}
						} else {
							echo "mkArray error: Invalid range found in string (".$AA.")" ;
							return array();
						}
					} else if(ereg('^[A-Za-z]$', $BBs[0]) && ereg('^[A-Za-z]$', $BBs[1])){
						if(ereg('^[A-Z]$', $BBs[0])){
							$BBs[0] = strtoupper($BBs[0]);
							$BBs[1] = strtoupper($BBs[1]);
						} else {
							$BBs[0] = strtolower($BBs[0]);
							$BBs[1] = strtolower($BBs[1]);
						}
	
						if($BBs[0] < $BBs[1]){
							for($i = $BBs[0]; $i <= $BBs[1]; $i++){
								$a[] = $i;
							}
						} else {
							echo "mkArray error: Invalid range found in string (".$AA.")" ;
							return array();
						}
					} else {
						echo "mkArray error: Invalid range found in string (".$AA.")" ;
						return array();
					}
				}
			}
		}
		return $a;
	}
	public static function exportHeader($filename,$init_charset = true){
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=$filename ");
		header("Content-Transfer-Encoding: binary ");
		if ($init_charset) {
			echo '<meta content="text/html; charset='.Config::Charset.'" http-equiv="content-type">';
		}
	}
	public static function exportFile($file,$filename){
		$b = get_browser(null, true);
		if ($b['browser'] === "IE") {
	 		$filename = urlencode($filename);		
		}
		if(file_exists($file)) {
	        header('Content-Description: File Transfer');
	        header('Content-Type: application/octet-stream');
	        header('Content-Disposition: attachment; filename="'.$filename.'"');
	        header('Content-Transfer-Encoding: binary');
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	        header('Pragma: public');
	        header('Content-Length: ' . filesize($file));
	        flush();
	        readfile($file);
	        exit;
	    }
	}
	public static function getRandomKeys($length){
		$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
		$key = "";
		for($i=0;$i<$length;$i++)$key.=$pattern{rand(0,35)};
		return $key;
	}
	public static function getPercentage($val,$base=100,$digi=2) {
		return number_format(($base>0)?$val/$base*100:0,$digi);
	}	
	public static function count(&$var,$value) {
		//todo
	}
	public static function sum(&$var,$value) {
		$c=isset($value)?$value:0;
		return $var=isset($var)?$var+$c:$c;
	}
	public static function display(&$value,$replace_null="-") {
		return isset($value)?$value:$replace_null;
	}
	public static function time2num($time = "00:00") {
		list($m,$s) = explode(':',$time);		
		return round(doubleval($m) + (doubleval($s) / 60),2);
	}
	public static function num2time($num = "0.0") {
		return sprintf("%d:%02d",$num,round(($num-intval($num))*60,2));
	}
}

class TableSorterRequest {
	function __construct(){
		
	}
	function processPagination(&$p,$count,$default_rows = 30){
		parse_str($p,$p);
		if (empty($p)) {
			$limit = "LIMIT $default_rows";
		}
		if (empty($p['rows'])) {$p['rows'] = $default_rows;}
		$p['page'] = isset($p['page']) ? trim($p['page']) : 1;
		$p['page'] = is_numeric($p['page']) ? intval($p['page']) : 1;
		$p['count'] = $count;
		$p['max'] = intval(($count - 1) / $p['rows'] + 1);
		if($p['max'] <= 0) $p['max'] = 1;
		if($p['page'] <= 0) $p['page'] = 1;
		if($p['page'] > $p['max']) $p['page'] = $p['max'];
		$start = ($p['page'] - 1) * $p['rows'];
		return (empty($limit) ? "LIMIT $start,".$p['rows'] : $limit);
	}
	function processSorting($sorter,$fields_name){
		if (empty($sorter)) {return "";}
		$arr = array();
		foreach ($sorter as $f){
			list($idx,$order) = explode(",",$f);
			if (isset($fields_name[$idx]) && !empty($fields_name[$idx])) {
				if ($order == "0") {$arr[] = "`".$fields_name[$idx]."` ASC";} 
				elseif ($order == "1") {$arr[] = "`".$fields_name[$idx]."` DESC";}			
			}
		}
		return ( empty($arr) ? "" : "ORDER BY ".join(",",$arr)."");
	}
}
?>