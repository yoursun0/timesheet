<?php

/*
function _($t){
	return mText($t);
}
*/
// Returen text for multi-language
function mText($org_txt){
	global $mText;
	if(isset($mText[$org_txt])){
		return $mText[$org_txt];
	} else {
		return $org_txt;
	}
}

?>