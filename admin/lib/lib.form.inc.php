<?php

class CS_Form {
	public static function getPost($field,$default=''){
		return isset($_POST[$field])?$_POST[$field]:$default;
	}
	public static function getGet($field,$default=''){
		return isset($_GET[$field])?$_GET[$field]:$default;
	}
	public static function getSubmit($fields,$required=false,$default=''){
		$rs = array();
		$err = false;
		foreach((is_array($fields)?$fields:array($fields)) as $f){
			global $$f;
			if (!isset($$f)) {
				$rs[$f] = $$f = $default;
			}
			if (isset($_POST[$f])) {
				$rs[$f] = $$f = is_array($_POST[$f]) ? $_POST[$f] : trim($_POST[$f]);
			} elseif (isset($_GET[$f])) {
				$rs[$f] = $$f = is_array($_GET[$f]) ? $_GET[$f] : trim($_GET[$f]);
			} elseif (true === $required) {
				return false;
			}
			
			if (true === $required && '' == $rs[$f]) {
				return false;
			}
		}
		return $rs;
	}
	
	
	/* UI - Standard Component */
	public function label($text,$for="",$other_param="") {
		return "<label for='$for' $other_param>".htmlspecialchars($text)."</label>";
	}
	public function hidden($name,$value="",$other_param=""){
		return "<input type='hidden' id='$name' name='$name' value='".htmlspecialchars($value)."' $other_param />";
	}
	public function text($name,$value="",$size=20,$max=0,$other_param="") {
		$other_param .= empty($max) ? "" : " maxlength='$max'";
		return "<input type='text' id='$name' name='$name' value='".htmlspecialchars($value)."' size='$size' $other_param />";
	}
	public function textarea($name,$value="",$rows=4,$cols=50,$wrap="",$other_param="") {
		return "<textarea id='$name' name='$name' rows='$rows' cols='$cols' wrap='$wrap' $other_param>".htmlspecialchars($value)."</textarea>";
	}
	public function checkbox($name,$value,$checked_value=array(),$other_param="") {
		if (!is_array($checked_value)) {$checked_value=array($checked_value);}
		return "<input type='checkbox' id='$name' name='$name' value='".htmlspecialchars($value)."' ".
			(in_array($value,$checked_value)?"checked":"")." $other_param/>";	
	}
	public function radio($name,$value,$checked_value=array(),$other_param="") {
		if (!is_array($checked_value)) {$checked_value=array($checked_value);}
		return "<input type='radio' id='$name' name='$name' value='".htmlspecialchars($value)."' ".
			(in_array($value,$checked_value)?"checked":"")." $other_param/>";	
	}
	public function txtButton($func_call,$caption,$other_param="") {
		return "<a href='javascript:;' onclick='$func_call' $other_param >".htmlspecialchars($caption)."</a>";
	}
	public function button($func_call,$caption="Button",$other_param="") {
		return "<input type='button' value='".htmlspecialchars($caption)."' onclick='$func_call' $other_param/>";
	}
	public function password($name,$other_param="") {
		$name = CS_Form::getName($name);
		return "<input type='password' id='$name' name='$name' $other_param/>";
	}
	public function select($name,$options,$selected_value=array(),$optionsBefore=array(),$blankOption=true,$other_param="") {
		if (!is_array($selected_value)) {$selected_value=array($selected_value);}
		$s = "<select id='$name' name='$name' $other_param>\n";
		if($blankOption) $s .= "<option></option>\n";
		while(list($id, $content) = each($optionsBefore)) {			
			$checked = isset($varr) && in_array($id,$selected_value) ? " selected" : "";			
			$s .= "<option value='".htmlspecialchars($id)."' $checked>".htmlspecialchars($content)."</option>\n";
		}
		while(list($id, $content) = each($options)) {
			$checked = isset($varr) && in_array($id,$selected_value) ? " selected" : "";	
			$s .= "<option value='".htmlspecialchars($id)."' $checked>".htmlspecialchars($content)."</option>\n";
		}
		return "$s</select>\n";
	}

	public function getName($v) {
		return $v;
	}
}

/*class CSPortal_FormValidation {
	const END = ";";

	public static function onBlur($script)	{
		return " onblur=\"".$script."\" ";
	}
	public static function required($title = '') {
		return ' class="required" title="'.$title.'" ';
	}
	public static function contains($nTxt,$control = 'this',$txt = '') {
		//javascript : function ValidateContains(txtControl, nTxt)
		return self::OnBlur("ValidateContains($control,'$nTxt')");
	}
	public static function alphaNumeric($control = 'this',$txt = '') {
		//javascript : function ValidateAlphaNumeric(txtControl,fieldCaption)
		return self::OnBlur("ValidateAlphaNumeric($control,'$txt')");
	}
	public static function length($maxLength, $minLength = 0, $control = 'this',$txt = '') {
		//javascript : function ValidateLength(txtControl, maxLength, minLength, fieldCaption)
		return self::OnBlur("ValidateLength($control,$maxLength,$minLength,'$txt')");
	}
	public static function date($other="",$control="this",$txt = '') {
		//javascript : function ValidateDate(txtControl, fieldCaption)
		return empty($other) ? self::OnBlur("ValidateDate($control,'$txt')") : "ValidateDate($control,'$txt');$other";	
	}
	public static function email($other="",$control="this") {
		//javascript : function ValidateEmail(txtControl)
		return empty($other) ? self::OnBlur("ValidateEmail($control)") : "ValidateEmail($control);$other";
	}	
	public static function int($other="",$control="this",$txt = '') {
		//javascript : function ValidateInteger(txtControl, fieldCaption)
		return empty($other) ? self::OnBlur("ValidateInteger($control,'$txt')") : "ValidateInteger($control,'$txt');$other";	
	}
}
*/
?>