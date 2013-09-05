<?php
//error_reporting(0);

function sum(&$var,$value) {
	if (isset($var)) {
		$var += $value;
	} else {
		$var = $value;
	}
}
function getDisplay(&$val,$empty='-',$format=true) {
	if (isset($val)) {
		return $format ? number_format($val,2) : $val;			
	} else {
		return $empty;
	}
}
function display(&$value,$empty='-') {
	echo getDisplay($value);
}

?>