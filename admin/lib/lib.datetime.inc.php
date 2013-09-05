<?php
class CSPortal_DateTime {
	public function display(&$date,$replace = "-") {
		if (!isset($date) || empty($date) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00")return $replace;
		if ($date == "1900-01-01" || $date == "1900-01-01 00:00:00")return "NA";
		if ($date == "1900-01-02" || $date == "1900-01-02 00:00:00")return "DK";
		return $date;
	}
}
class CSPortal_Date extends CSPortal_DateTime {
	public $default_format = "Y-m-d";
	public function add($date, $adj = 1, $unit = "day") {
		return date($this->default_format, strtotime("$adj $unit",strtotime($date)));
	}
	public function diff($begin,$end) {
		return unixtojd(strtotime($end)) - unixtojd(strtotime($begin));
	}
	public function format($date = "now",$format = "Y-m-d"){
		return date($format,strtotime($date));		
	}
	public function today() {
		return date("Y-m-d");
	}
	public function year_mon($date = "now") {
		return date("Y-m",strtotime($date));
	}
	public function year($date = "now") {
		return date("Y",strtotime($date));		
	}
	public function day($date = "now", $fillzero = true) {
		return date(($fillzero?'d':'j'),strtotime($date));		
	}
}
?>