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
	return("<span class=\"errMsg\">${msg}</span>\n");
}

function mkradio ($_name, $value, $varr = "", $other_param = "") {
//    if ($varr == null)
//        $varr =& $GLOBALS['HTTP_POST_VARS'];
	$str = '<INPUT type="radio" name="'.$_name.'" id="'.$_name.'" value="' . fieldspecialchars($value) .'"'.$other_param;
	if (strval($varr) == strval($value) )
		$str .= ' checked';
	$str .= ' />';
	return($str);
}

function mkcheckbox ($_name, $value, $varr = "", $other_param ="") {
//    if ($varr == null)
//        $varr =& $GLOBALS['HTTP_POST_VARS'];
	$varr_array = array();
	if(is_array($varr)){
		foreach($varr as $v){
			if(is_array($v))
				array_push($varr_array, $v[0]);
			else
				array_push($varr_array, $v);
		}
	}

	$str = '<INPUT type="checkbox" name="'.$_name.'[]"  id="'.$_name.'[]" value="' . fieldspecialchars($value) .'"';
	if(is_array($varr)){
		if(in_array($value, $varr_array))
			$str .= ' checked'; 
	} else {
		if ($varr == $value)
			$str .= ' checked';
	}
	$str .= $other_param;
	$str .= ' />';
	return($str);
}

function mktext ($_name, $size = 20, $max = 0, $varr = "", $other_param = "") {
	if(stripos($other_param, "onblur=")  === false){
		$other_param .= " onblur=trimThis(this);";
	}
	
	if(!empty($other_param)) $other_param = " ".$other_param;
	$size = intval($size);
	$max  = intval($max);
	$str = "size=\"$size\"";
	if ($max)	$str .= " maxlength=\"$max\"";
	return('<INPUT type="text" '. $str .' name="'.$_name.'" id="'.$_name.'" value="'. fieldspecialchars($varr) .'"'.$other_param.' />');
}

function mkpass ($_name, $other_param = "") {
	if(!empty($other_param)) $other_param = " ".$other_param;
	return('<INPUT type="password" name="'. htmlspecialchars($_name) .'"  id="'.$_name.'"'.$other_param.' />');
}

function mkhidden ($_name, $varr = "", $otherParam = "") {
	if(!empty($other_param)) $other_param = " ".$other_param;
    return('<INPUT type="hidden" name="'.$_name.'" id="'.$_name.'" value="'. fieldspecialchars($varr) .'"'.$otherParam.' />');
}

function mktextarea ($_name, $rows, $cols, $wrap, $varr = "", $other_param = "") {
	if(stripos($other_param, "onblur=")  === false){
		$other_param .= " onblur=trimThis(this);";
	}
	
	if(!empty($other_param)) $other_param = " ".$other_param;
	
	$str = '<textarea name="' . $_name .'" id="'.$_name.'"';
	if($rows > 0)
		$str .= ' rows="' . $rows . '"';
	if($cols > 0)
		$str .= ' cols="' . $cols . '"';
	if($wrap != '')
		$str .= ' wrap="' . strtolower($wrap) . '"';
	$str .= $other_param.'>';

	$str .= $varr;
	$str .= '</textarea>';
	return($str);
}

function mkselect ($_name, $options, $varr = "", $otherParam = "", $blankOption = true, $optionsBefore = array()) {
		if(!empty($other_param)) $other_param = " ".$other_param;
		
		$str  = "<select name=\"$_name\" id=\"$_name\"$otherParam>\n";

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

function mkfile ($_name, $other_param = "") {
	if(!empty($other_param)) $other_param = " ".$other_param;
	return('<INPUT type="file" name="'.$_name.'" id="'.$_name.'" '.$other_param.'/>');
}

function mkdate($_name, $_value = '', $other_param = ""){
 	// <input type='text' size='10' readOnly> <img src=\"".$img_url."cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Click Here to Pick up the date\"> <img src=\"".$img_url."cal_clear.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Click Here to clear the date\">", $text);
 	$img_url = $GLOBALS['CONFIG']['image_url'];
 	if($_value == '0000-00-00') $_value = '';
	$html = mktext($_name, 10, 10, $_value, " readOnly onKeyPress=\"return (false)\""); 
	$html .= " <a href=\"javascript:popCal(MM_findObj('$_name'));\"><img src=\"".$img_url."cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".mText("Calendar pick up")."\"></a>";
	$html .= " <a href=\"javascript:clearCal(MM_findObj('$_name'));\"><img src=\"".$img_url."cal_clear.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".mText("Calendar clear")."\"></a>";
	return $html;
}

function mksubmit($_name, $_value = null, $other_param = "") {
	if(!empty($other_param)) $other_param = " ".$other_param;
    if ($_value == null)
        $_value = _('Submit');
    return '<INPUT type="submit" name="'.htmlspecialchars($_name).'" value="'.$_value.'"'.$other_param.' />';
}

function mkbutton($_name, $_value = null, $other_param = ""){
	if(!empty($other_param)) $other_param = " ".$other_param;
    if ($_value == null) $_value = 'Button';
    return '<INPUT type="button" name="'.htmlspecialchars($_name).'" value="'.$_value.'"'.$other_param.' />';
}

function mkrichtext($_name, $_value = "", $css = "", $width = 500, $height = 200, $other_param = ""){
	$pre_name = "temp_richtext_pre_$_name";
	$editor_name =  "temp_richtext_obj_$_name";
	
	if(empty($css)) $css = $GLOBALS['CONFIG']['style_sheet'];
	if($css == "none") $css_line = "";
	else $css_line = "$editor_name.css=\"$css\";\n";
	
	$html = mkhidden($_name, "")."\n"
	."<pre id=\"$pre_name\" name=\"$pre_name\" style=\"display:none\">".htmlentities($_value, ENT_NOQUOTES, 'UTF-8')."</pre>\n"
	."<script>\n"
	."var $editor_name = new InnovaEditor(\"$editor_name\");\n"
	.$css_line
	."$editor_name.width=$width;\n"
	."$editor_name.height=$height;\n"
	."$editor_name.cmdAssetManager=\"modalDialogShow('".$GLOBALS['CONFIG']['base_url']."Editor/assetmanager/assetmanager.php',640,465)\";\n"
	."$editor_name.RENDER(MM_findObj('$pre_name').innerHTML);\n"
//	."setTimeout(\"$editor_name.RENDER(MM_findObj('$pre_name').innerHTML)\", 1000);\n"
	."</script>";
	return $html;
}

function pretty_bytes ($bytes, $precision = 1){
   $suffix = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
   $index = floor (log ($bytes + 1, 1024)); // + 1 to prevent -INF
   return sprintf ("%0.{$precision}f %s", $bytes / pow (1024, $index), $suffix[$index]);
}

function getStatusName($status = 0){
	$stat = 'Editing';
	if($status & STATUS_DELETED) {
		$stat = 'Archived';
		continue;
	} elseif($status & STATUS_DONE) {
		$stat = 'Ended';
	} elseif($status & STATUS_ACTIVE) {
		$stat = 'Active';
	} elseif($status & STATUS_TEST) {
		$stat = 'Testing';
	}
	return $stat;
}

function genNatvigationBar($currentPage, $totalRowNum, $tableWidth = "100%", $align = "center", $rowPerPage = 0){

	$rowPerPage = $rowPerPage == 0 ? $GLOBALS['CONFIG']['row_per_page'] : $rowPerPage;
	if($totalRowNum <= $rowPerPage) return "";
	
	$currentPage = intval($currentPage);
	if(empty($currentPage)) $currentPage = 1;
	//$imgPath = $GLOBALS['CONFIG']['page_img_path'];
	$imgPath = "";
	$maxPageNum = 1;

	$maxPageNum = intval(($totalRowNum - 1) / $rowPerPage + 1);
	if($maxPageNum <= 0) $maxPageNum = 1;
	if($currentPage <= 0) $currentPage = 1;
	if($currentPage > $maxPageNum) $currentPage = $maxPageNum;

	$html = "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\" width=\"".$tableWidth."\"><tr><td align=\"".$align."\">\n";
	$html .= "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n";
	$html .= "<tr>\n";
	$html .= "<td>Page:</td>\n";

	if($currentPage <= 1){
		$html .= "<td><img src=\"" . $imgPath . "0.gif\" width=\"16\" height=\"16\"></td>";
	} else {
		$html .= "<td><img src=\"" . $imgPath . "Back.gif\" width=\"16\" height=\"16\" alt=\"Previous Page\" onclick=\""
		."gotoPage(" . ($currentPage - 1) . ")\" style=\"cursor: hand;\"></td>";
	}

	$html .= "<td><select name=\"pageControl\" onchange=\"gotoPage(this.options[this.selectedIndex].value)\">\n";
	for($i = 1 ; $i <= $maxPageNum ; $i++){
		$html .= "<option value=\"" . $i . "\"";
		if($i == $currentPage) $html .= " selected";
		$html .= ">" . $i . "</option>\n";
	}
	$html .= "</select></td>\n";

	if($currentPage >= $maxPageNum){
		$html .= "<td><img src=\"" . $imgPath . "0.gif\" width=\"16\" height=\"16\"></td>";
	} else {
		$html .= "<td><img src=\"" . $imgPath . "Forward.gif\" width=\"16\" height=\"16\" alt=\"Next Page\" onclick=\""
		. "gotoPage(" . ($currentPage + 1) . ")\" style=\"cursor: hand;\"></td>";
	}

	$html .= "<td>&nbsp; &nbsp; Total: $totalRowNum record(s)</td>";
	$html .= "</tr>\n";
	$html .= "</table>\n";
	$html .="</td></tr></table>\n";

	return $html;
}
/*
function addBreadcurmbs($bc_level, $title){
	global $bcs, $formAction;
	$max_level = count($bcs) - 1;
	
	for($i = $max_level; $i >= 0; $i--){
		if($i > $bc_level)	unset($bcs[$i]);
	}
	
	$_POST['formAction'] = $formAction;
	$bcs[$bc_level]['title'] = $title;
	$bcs[$bc_level]['POST'] = $_POST;
	$bcs[$bc_level]['GET'] = $_GET;
}
*/
/*
function genBreadcrumb(){
	global $image_url, $bcs;
	//$bcs = $_SESSION['Breadcurmbs'];
	$max_level = count($bcs) - 1;
	echo "<div align=\"left\">\n";
	for($i = 0 ; $i <= $max_level; $i++){
		$bc = $bcs[$i];
		
		echo "\n<img src=\"".$image_url."function_bullet.gif\" align=\"absmiddle\"> ";
		if($i < $max_level){
			echo "<a href='manage.php?bc_level=$i'>".$bc['title']."</a>";
		} else {
			echo "<b>".$bc['title']."</b>";
		}
	}
	echo "</div>\n";
}
*/
function cal_page_num(&$p, &$firstIndex, $rowPerPage, $total_row){
	if(empty($p)) $p = 1;
	$p = intval($p);
	$maxPageNum = intval(($total_row - 1) / $rowPerPage + 1);
	if($maxPageNum <= 0) $maxPageNum = 1;
	if($p <= 0) $p = 1;
	if($p > $maxPageNum) $p = $maxPageNum;
	$firstIndex = ($p - 1) * $rowPerPage;
}

function Timestamp2DateFormat($old_date, $pattern = "Y-m-d H:i:s"){ 
	return $old_date;
}

function getPostGetValue($fields){
	foreach($fields as $f){
		global $$f;
		if(!isset($$f)) $$f = '';
		if(isset($_POST[$f])){
			$$f = $_POST[$f];
		} else if(isset($_GET[$f])){
			$$f = $_GET[$f];
		}
	}
}	

function quotaPercent($count, $max){
	if($max == 0) return "";
	$percent = $count / $max * 100;
	$html = number_format($percent, 2)." %";
	$bar_width = number_format($percent,0);
	if($bar_width > 100) $bar_width = 100;
	$html .= "<table width=100 cellspacing=0 cellpadding=0 bgcolor=#eeeeee height=5><tr><td bgcolor=#ff0000 width=".$bar_width."></td><td width=".(100-$bar_width)."></td></tr></table>";
	return $html;
}

function randomKeys($length){
	$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
	for($i=0;$i<$length;$i++){
		$key .= $pattern{rand(0,35)};
	}
	return $key;
}

function getUniRandomKeys($length){
	$uni_key = str_replace(".", "", uniqid("", TRUE));
	if($length - 22 > 0){
		$uni_key .= randomKeys($length - 22);
		
	}
	return $uni_key;
}

function repeatString($string, $num_of_repeat){
	$s = "";
	for($i = 0; $i < $num_of_repeat; $i++) $s .= $string;
	return $s;
}

function convertGlossary($s_name, $ori_text){
	global $lang;
	if(empty($lang)) $lang = 'en';
	$text = $ori_text;
	while(true){
		$pos1 = strpos($text, "[[glossary(");
		if($pos1 === false) break; // exit if not [[glossary( found
		$pos2 = strpos($text, ")]]", $pos1);
		if($pos2 === false) break; // exit if no close tag found
		
		$glossary_name = substr($text, $pos1 + 11, $pos2 - $pos1 - 11);
		if(empty($glossary_name)){
			$text = str_replace("[[glossary(".$glossary_name.")]]", "[Argument Is Empty]", $text);
			continue; // exit if variable name or choice name is empty
		} 
		
		$glossary_obj = sql_to_assoc("select text, detail from glossary where s_name='"._addslashes($s_name)."' and name='"._addslashes(strtoupper($glossary_name))."'");
		if($lang != 'en'){
			$glossary_lang = sql_to_assoc("select text, detail from glossary_lang where s_name='"._addslashes($s_name)."' and name='"._addslashes($glossary_name)."' and lang_code='"._addslashes($lang)."'");
			if(!empty($glossary_lang['text'])) $glossary_obj['text'] = $glossary_lang['text'];
			if(!empty($glossary_lang['detail'])) $glossary_obj['detail'] = $glossary_lang['detail'];
		}

		if(count($glossary_obj) > 0){
			$html = "<span class=\"glossary\" onmouseover=\"return escape('"
			.str_replace(
				array("\r\n", "\r", "\n", "'", '"'), 
				array(" ", " ", " ", "\\'", '&quot;'), 
				$glossary_obj['detail']
			)
			."');\">"
			.$glossary_obj['text']
			// ."<img align='top' src='".$GLOBALS['CONFIG']['image_url']."slightbulb.gif' border='0'>"
			."</span>";
			$text = str_replace("[[glossary(".$glossary_name.")]]", $html, $text);
		} else {
			$text = str_replace("[[glossary(".$glossary_name.")]]", "<font color='#ff0000'>[Glossary Not Exists]</font>", $text);
		}
	}
	return $text;
}

function getRichTextSubmitJs($field_name, $form_name = "form"){
	$regExp = $GLOBALS['CONFIG']['editor_replace_regExp'];
	$replace_with = $GLOBALS['CONFIG']['base_url']."upload_assets/";
	
	$js = "";
	$f_array = array();
	if(is_array($field_name)) $f_array = $field_name;
	else $f_array[] = $field_name;
	
	foreach($f_array as $f_name){
		$js .= "if(typeof(temp_richtext_obj_$f_name) != 'undefined'){ \n"
		.$form_name.".".$f_name.".value = temp_richtext_obj_".$f_name.".getHTMLBody().replace(".$regExp.", \"".$replace_with."\");\n"
		."rtTempVal =  trimStr(".$form_name.".".$f_name.".value.toLowerCase());\n"
		."if(rtTempVal == \"<p>&nbsp;</p>\" || rtTempVal == \"<div>&nbsp;</div>\") ".$form_name.".".$f_name.".value = \"\";\n"
		."}\n";
	}
	return $js;
}

function mkSortOder($currentOrder, $maxOrder){
	if($maxOrder < 2){
		return "&nbsp;";
	} else {
		return mktext("", 4, 6, "", " onKeyDown=\"defaultReOrder($currentOrder, $maxOrder, this)\"");
	}
}

function getCurrentDatetime(){
	return date("Y-m-d H:i:s");
}

function getSearchContentInString($content, &$lastPos, $pfix, $sfix){
	$p1 = strpos($content, $pfix, $lastPos + 1);
	if($p1 === false){
		return "";
	} else {
		$lastPos = $p1;
		$p1 += strlen($pfix);
		$p2 = strpos($content, $sfix, $p1);
		if($p2 === false)	return "";
		return substr($content, $p1, $p2 - $p1);
	}
}

function printArray($array, $lineEnd = "\n"){
	foreach ($array as $line) echo $line.$lineEnd;
}

function array_rand_value(&$array, $size = 1){
	$ori_array = $array;
	$return_array = array();
	if(count($array) > 0){
    	$rand_keys = array_rand($array, $size);
    	
    	if(is_array($rand_keys)){
	    	foreach($rand_keys as $key){
	    		$return_array[] = $ori_array[$key];
	    	}
    	} else {
    		$return_array[] = $ori_array[$rand_keys];
    	}
	}
	return $return_array;
}

function turn_blue($haystack,$needle){
     $h=strtoupper($haystack);
     $n=strtoupper($needle);
     $pos=strpos($h,$n);
     if ($pos !== false)
         {
        $var=substr($haystack,0,$pos)."<b><font color='#FF6600'>".substr($haystack,$pos,strlen($needle))."</font></b>";
        $var.=substr($haystack,($pos+strlen($needle)));
        $haystack=$var;
        }
     return $haystack;
}

function mkArray($s){
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

function in_range($mixedValue, $rangeTxt, $matchAny = true){
	$vArray = (is_array($mixedValue)) ? $mixedValue : mkArray($mixedValue);
	$matchCount = 0;
	foreach($vArray as $v){
		if(is_array($rangeTxt)){
			if(in_array($v, $rangeTxt)){
				if($matchAny) return true;
				$matchCount++;
			}
		} else {
			$AAs = preg_split("/[,]/", $rangeTxt);
			foreach($AAs as $AA){
				$AA = trim($AA);
				if(strpos($AA, "-") === false){
					if($v == $AA){
						if($matchAny) return true;
						$matchCount++;
						break;
					}
				} else {
					$BBs = preg_split("/[-]/", $AA);
					if(count($BBs) != 2){
						echo "in_range error (1): Invalid range found in string (".$AA.")" ;
						return false;
					} else {
						$BBs[0] = trim($BBs[0]);
						$BBs[1] = trim($BBs[1]);
						if(ereg('^[0-9]*$', $BBs[0]) && ereg('^[0-9]*$', $BBs[1])){
							if($BBs[0] < $BBs[1]){
								if($v >= $BBs[0] && $v <= $BBs[1]){
									if($matchAny) return true;
									$matchCount++;
									break;
								}
							} else {
								echo "in_range error (2): Invalid range found in string (".$AA.")" ;
								return false;
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
								if($v >= $BBs[0] && $v <= $BBs[1]){
									if($matchAny) return true;
									$matchCount++;
									break;
								}
							} else {
								echo "in_range error (3): Invalid range found in string (".$AA.")" ;
								return false;
							}
						} else {
							echo "in_range error (4): Invalid range found in string (".$AA.")" ;
							return false;
						}
					}
				}
			}
		}
	}
	return (count($vArray) == $matchCount && $matchCount > 0);
}

function fl($text, $fix_length, $align='L', $show_error = true){
	$len = mb_strlen($text, "BIG5");
	if($len <= $fix_length){
		if($align=="L"){
			$t = $text.str_repeat(" ", $fix_length - $len);
		} else {
			$t = str_repeat(" ", $fix_length - $len).$text;
		}
	} else {
		$t = substr($text, 0, $fix_length);
		die("Convert text to fixed lenght error: text = (".$text.") length > max ($len > $fix_length)");
	}
	return $t;
}
/*
function countChinese($str){
	$c = 0;
	for($i=0;$i<strlen($str);$i++){
	    if(ord($str{$i})>128) $c++;
	}
	return $c / 2;
}
*/
function getFormLogCountingHtml($type, $form_no){
	global $form_count_title_list;
	$html = "";
	$html .= "<table border='1' cellspacing='0'>\n";
    $count_title = $form_count_title_list[$type];
    /*
    switch($type){
		case "std_enrol_dtl_upd": $count_title = array("NA", "CSSAS", "SSFRS(Full)", "SSFRS(Half)"); break;
		case "std_dep": $count_title = array("Continue", "Withdraw", "Unknow"); break;
	}
    */
	$html .= "<tr align='center'>";
	foreach($count_title as $title){
		$html .= "<td>".$title."</td>";
	}
	$html .= "<tr>\n<tr align='center'>";
	for($i = 1; $i <= count($count_title); $i++){
		$html .= "<td>".sql_exe("select c".$i." from form_log where form_no='"._addslashes($form_no)."'")."</td>";
	}
	$html .= "</tr>\n</table>\n";
	return $html;
}

function getSchoolInfoHtml($scrn, $width="760", $colWitdh = array("20%", "80%")){
	global $cohort;
	$sch_txt = str_replace("/", "", $scrn);
	$sch_no = substr($sch_txt, 0, 6);
	$sch_location_id = substr($sch_txt, 6, 4);
	$sch_level = substr($sch_txt, 10, 1);
	$sch_session = substr($sch_txt, 11, 1);
	$sch_language = substr($sch_txt, 12, 1);
	
	$html = "<table width='$width' cellpadding='2' cellspacing='1' border='0'>"
	."<tr><td width='".$colWitdh[0]."'>SCRN :</td><td width='".$colWitdh[1]."'>$scrn</td></tr>\n"
	."<tr><td>Shool No. :</td><td>$sch_no</td></tr>\n"
	."<tr><td>Location ID :</td><td>$sch_location_id</td></tr>\n"
	."<tr><td>School Level :</td><td>$sch_level</td></tr>\n"
	."<tr><td>Session:</td><td>$sch_level</td></tr>\n"
	."<tr><td>Language :</td><td>$sch_language</td></tr>\n"
	."<tr><td>School Name :</td><td>".sql_exe("select CONCAT(abbr_name, '<br>', sch_chn_name) from sch_con where cohort='$cohort' and scrn='$scrn'")."</td></tr>";
	
	$contact_persons = sql_to_array("select CONCAT(contact_eng_name, ' (Mode ', mode, ')<br>', contact_chn_name, ' (', post, ')<br>Tel: ', sch_tel, ' &nbsp; &nbsp; Fax: ', fax) from sch_con where cohort='$cohort' and scrn='$scrn' order by mode", 1);
	foreach($contact_persons as $p){
		$html .= "<tr><td>Contact Person :</td><td>$p</td></tr>\n";
	}
	$html .= "</table>\n";
	return $html;
}

function getEditContactInfoForm($form_no, $change_field_name, $field_value, $other_parameter, $scrn, $check_duplicate = true, $width="760", $colWitdh = array("20%", "80%")){
	global $cohort, $status_list;
	$sql = "select form_log.form_no, form_log.status_id from contact_info, form_log where form_log.cohort='$cohort' and form_log.scrn='"._addslashes($scrn)."' and form_log.form_no != '"._addslashes($form_no)."' and contact_info.form_no = form_log.form_no and form_log.status_id not in ('s95','s96','s97','s98','99')";
	$existing_change_array  = sql_to_array($sql);
	
	if(count($existing_change_array) > 0 && $check_duplicate){
		$html = "<font color='#ff00'><pre>Update Contact Information of this School in progress.\r\nPlease refer below Form:\r\n";
		foreach($existing_change_array as $a => $b){
			$html .= fl($a, 40).$status_list[$b]."\r\n";
			$c = sql_to_assoc("select * from contact_info where form_no='"._addslashes($a)."'");
			$html .= fl("Mode", 6).fl("Person", 8).fl("Eng Name", 35).fl("Chi Name", 35).fl("Post", 50).fl("Email", 60)."\r\n";
			$html .= fl("1", 6).fl("1", 8).fl($c['contact_info_eng_name_1'], 35).fl($c['contact_info_chn_name_1'], 35).fl($c['contact_info_post_1'], 50).fl($c['contact_info_email_1'], 60)."\r\n";
			$html .= fl("1", 6).fl("2", 8).fl($c['contact_info_eng_name_2'], 35).fl($c['contact_info_chn_name_2'], 35).fl($c['contact_info_post_2'], 50).fl($c['contact_info_email_2'], 60)."\r\n";
			$html .= fl("2", 6).fl("1", 8).fl($c['contact_info_eng_name_3'], 35).fl($c['contact_info_chn_name_3'], 35).fl($c['contact_info_post_3'], 50).fl($c['contact_info_email_3'], 60)."\r\n";
			$html .= fl("2", 6).fl("2", 8).fl($c['contact_info_eng_name_4'], 35).fl($c['contact_info_chn_name_4'], 35).fl($c['contact_info_post_4'], 50).fl($c['contact_info_email_4'], 60)."\r\n\r\n";
		}
		
		$html .= "</pre></font><br><br>";
	
	} else {
		$html = "<table width='$width' cellpadding='2' cellspacing='1' border='0'>"
		."<col width='".$colWitdh[0]."'>"
		."<col width='".$colWitdh[1]."'>";
		
		$html .= "<tr><td>Contact Changed:</td><td>"
		.mkradio($change_field_name, "Y", $field_value, $other_parameter)." Yes &nbsp; &nbsp; "
		.mkradio($change_field_name, "", $field_value, $other_parameter)." No"
		."</td></tr></table>\n";	
	
		if($field_value == "Y"){
			$html .= "<table cellpadding='2' cellspacing='1' border='0' bgcolor='#666666'>";
			// ."<col width='20%'><col width='20%'><col width='20%'><col width='20%'><col width='20%'>";
			$html .= "<tr align='center' bgcolor='#bbbbbb'><td>&nbsp;</td><td colspan='2'>Mode 1</td><td colspan='2'>Mode 2</td></tr>\n";
			$html .= "<tr align='center' bgcolor='#dddddd'><td>&nbsp;</td><td>Person 1</td><td>Person 2</td><td>Person 1</td><td>Person 2</td></tr>\n";
			$person_array = array();
			if(isset($_POST['contact_info_eng_name_1'])){
				for($i = 1; $i <= 4; $i++){
					$info = array();
					$info['contact_info_eng_name'] = getPost('contact_info_eng_name_'.$i);
					$info['contact_info_chn_name'] = getPost('contact_info_chn_name_'.$i);
					$info['contact_info_post'] = getPost('contact_info_post_'.$i);
					$info['contact_info_email'] = getPost('contact_info_email_'.$i);
					$person_array[] = getEditContactInfoRow(false, $i, $info);
				}
			} else {
				$count = 0;
				$temp_count = intval(sql_exe("select count(*) from contact_info where form_no='"._addslashes($form_no)."'"));
				if($temp_count == "1"){
					$contact_row = sql_to_assoc("select * from contact_info where form_no='"._addslashes($form_no)."'");
					for($i = 1; $i <= 4; $i++){
						$info = array(
						'contact_info_eng_name' => $contact_row['contact_info_eng_name_'.$i]
						,'contact_info_chn_name' => $contact_row['contact_info_chn_name_'.$i]
						,'contact_info_post' => $contact_row['contact_info_post_'.$i]
						,'contact_info_email' => $contact_row['contact_info_email_'.$i]
						);
						$person_array[] = getEditContactInfoRow(false, ++$count, $info);	
					}
					
				} else {
					for($mode = 1; $mode <= 2; $mode++){
						$contact_persons = sql_to_array_assoc("select contact_eng_name as contact_info_eng_name, contact_chn_name as contact_info_chn_name, post as contact_info_post, email as contact_info_email from sch_con where cohort='$cohort' and scrn='"._addslashes($scrn)."' and mode='$mode' order by sch_con_id limit 0, 2");
						
						if(count($contact_persons) > 2)	die("Contact persons more than 2 (scrn $scrn mode $mode)");
						
						foreach($contact_persons as $info){
							$person_array[] = getEditContactInfoRow(false, ++$count, $info);
						}
						
						for($i = count($contact_persons) + 1; $i <= 2; $i++){
							$person_array[] = getEditContactInfoRow(true, ++$count);
						}
					}
				}
			}
		
			$title_array = array("English Name", "Chinese Name", "Post", "Email");
			$col_count = 0;
			foreach($title_array as $title){
				$html .= "<tr align='center' bgcolor='#ffffff'><td align='left'>".$title.":</td>";
				foreach($person_array as $person){
					$html .= "<td>".$person[$col_count]."</td>";
				}
				$html .= "</tr>\n";
				$col_count++;
			}
			$html .= "</table>";
		}	
	}
	return $html;
}

function getEditContactInfoRow($isEmpty, $index, $info = array()){
	$a = array();
	if($isEmpty){
		$info['contact_info_eng_name'] = "";
		$info['contact_info_chn_name'] = "";
		$info['contact_info_post'] = "";
		$info['contact_info_email'] = "";
	}
	$a[] = mktext("contact_info_eng_name_".$index, 30, 30, $info['contact_info_eng_name']);
	$a[] = mktext("contact_info_chn_name_".$index, 30, 30, $info['contact_info_chn_name']);
	$a[] = mktext("contact_info_post_".$index, 30, 50, $info['contact_info_post']);
	$a[] = mktext("contact_info_email_".$index, 30, 60, $info['contact_info_email'], " onblur=\"ValidateEmail(this)\"");
	
	return $a;
}

/*
function checkLock($cohort, $scrn, $form_no){
	$locked = intval(sql_exe("select count(*) from scrn_lock where cohort='$cohort' and scrn='$scrn'"));
	$locked_form_no = intval(sql_exe("select form_no from scrn_lock where cohort='$cohort' and scrn='$scrn'"));
	if($locked > 0 && $locked_form_no != $form_no){
		return $locked_form_no;
	} else {
		return "";
	}
}

function addLock($cohort, $scrn, $form_no, $type){
	// lock_scrn
	$m_date = date("Y-m-d H:i:s",time());
	$sql = "insert into scrn_lock (cohort, scrn, type, form_no, lock_date) values ('$cohort', '"._addslashes($scrn)."', '$type', '"._addslashes($form_no)."', '$m_date')";
	mysql_query($sql);
	dbLog($sql);
}

function delLock($form_no){
	// lock_scrn
	$sql = "delete from scrn_lock where form_no='"._addslashes($form_no)."'";
	mysql_query($sql);
	dbLog($sql);
}
*/

function addStdPastSch($form_no){
	$c_date = date("Y-m-d H:i:s",time());
	$form_data = sql_to_assoc("select cohort, scrn, strn, sch_class_name, std_name, contact_phone from std_dep where form_no='"._addslashes($form_no)."'");
	
	$std_count = intval(sql_exe("select count(*) from std_past_sch where strn='"._addslashes($form_data['strn'])."'"));
	if($std_count > 0){
		// del old record if student already exists
		$sql = "delete from std_past_sch where strn='"._addslashes($form_data['strn'])."'";
		mysql_query($sql);
		dbLog($sql);
	}
	
	$sql = "insert into std_past_sch (cohort, strn, scrn, c_date, contact_phone, std_name, sch_class_name, form_no) values ("
	. "'"._addslashes($form_data['cohort'])."'"
	. ", '"._addslashes($form_data['strn'])."'"
	. ", '"._addslashes($form_data['scrn'])."'"
	. ", '$c_date'"
	. ", '"._addslashes($form_data['contact_phone'])."'"
	. ", '"._addslashes($form_data['std_name'])."'"
	. ", '"._addslashes($form_data['sch_class_name'])."'"
	. ", '"._addslashes($form_no)."'"
	.")";
	mysql_query($sql);
	dbLog($sql);
}

function delStdPastSch($cohort, $form_no){
	$sql = "select strn from std_adm where form_no='"._addslashes($form_no)."'";
	$strn = sql_exe($sql);
		
	$sql = "select count(*) from std_past_sch where cohort='$cohort' and strn='"._addslashes($strn)."'";
	$past_count = intval(sql_exe($sql));
	if($past_count != 1){
		echo "Student Past School Information not during delete past record (cohort = $cohort , strn = $strn )";
	} else {
		$sql = "select * from std_past_sch where cohort='$cohort' and strn='"._addslashes($strn)."'";
		$db_row = sql_to_assoc($sql);
		$db_row['d_date'] = date("Y-m-d H:i:s",time());
		$sql_f = array("std_past_sch_id", "cohort", "strn", "scrn", "c_date", "d_date", "contact_phone", "std_name", "sch_class_name", "form_no");
		$sql_v = array();
		foreach($sql_f as $f){
			$sql_v[] = $db_row[$f];
		}
		$sql = "insert into std_past_sch_deleted ".gen_insert_sql($sql_f, $sql_v);
		mysql_query($sql);
		dbLog($sql);
		
		$sql = "delete from std_past_sch where cohort='$cohort' and strn='"._addslashes($strn)."'";
		mysql_query($sql);
		dbLog($sql);
	}
}

function getPost($var, $default=''){
	return (isset($_POST[$var]) ? $_POST[$var] : $default);
}

function showInProgressFormInfo($cohort, $scrn, $form_no = ""){
	global $form_type_list, $status_list;
	$sql = "select * from form_log where scrn='"._addslashes($scrn)."' and status_id in ('s1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 's10') and form_no != '"._addslashes($form_no)."' and cohort='$cohort' order by form_no";
	$forms = sql_to_array_assoc($sql);
	$h = "";
	if(count($forms) > 0){
		$h .= "<font color='#ff0000'><pre>"
		. "There are ".count($forms)." form(s) of this school still in process:\r\n";
		foreach($forms as $form){
			$h .= fl($form_type_list[$form['type']], 35)
			.fl($status_list[$form['status_id']], 40)
			.fl($form['form_no'], 50)
			."\r\n";
		}
		
		$h .= "<pre></font><br>";
	}
	return $h;
}

class HKID {
  var $HKID_str;
  var $digits;
  
        function HKID()
        {
          $this->digits=array();
          $this->HKID_str='';
          $this->digits['a2']=' ';
          $this->digits['a1']=' ';
          $this->digits['d6']=0;
          $this->digits['d5']=0;
          $this->digits['d4']=0;
          $this->digits['d3']=0;
          $this->digits['d2']=0;
          $this->digits['d1']=0;
          $this->digits['c']='';
        }
        
        function set($HKID_str)
        {
          $matches=array();
          if (preg_match('/([A-Z,a-z,\s])?([A-Z,a-z])\s*(\d{6})\s*(\([0-9,A,a]\))?/', $HKID_str, $matches)) {
                if((isset($matches[1]))&&($matches[1]!='')) $this->digits['a2']=strtoupper($matches[1]);
                if((isset($matches[2]))&&($matches[2]!='')) $this->digits['a1']=strtoupper($matches[2]);
                if((isset($matches[3]))&&($matches[3]!='')) {
                  $this->digits['d6']=substr($matches[3],0,1);
                  $this->digits['d5']=substr($matches[3],1,1);
                  $this->digits['d4']=substr($matches[3],2,1);
                  $this->digits['d3']=substr($matches[3],3,1);
                  $this->digits['d2']=substr($matches[3],4,1);
                  $this->digits['d1']=substr($matches[3],5,1);
                }
                if((isset($matches[4]))&&($matches[4]!='')) $this->digits['c']=strtoupper(substr($matches[4],1,1));
          };
          $this->HKID_str=$this->digits['a2'].$this->digits['a1']
                         .$this->digits['d6'].$this->digits['d5'].$this->digits['d4'].$this->digits['d3'].$this->digits['d2'].$this->digits['d1'];
          if ($this->digits['c']!='') {
                $this->HKID_str .= "(".$this->digits['c'].")";
          }
        }
        
        function alphamap($ch)
        {
          if ($ch==' ') return 36;
          return (ord(strtoupper($ch))-ord('A')+10);
        }
        
        function check_digit()
        { 
          $o_chk=0;
          $sum=0;
          $sum=
            (($this->alphamap($this->digits['a2']))*9)
          + (($this->alphamap($this->digits['a1']))*8)
          + (($this->digits['d6'])*7)
          + (($this->digits['d5'])*6)
          + (($this->digits['d4'])*5) 
          + (($this->digits['d3'])*4) 
          + (($this->digits['d2'])*3) 
          + (($this->digits['d1'])*2);
          $o_chk = (11 - ($sum % 11))%11;
          if ($o_chk==10) $o_chk='A';
          return $o_chk;
        }
        
        function validate()
        {
          return (($this->check_digit())==($this->digits['c']));
        }
}

function isStrnValid($strn){
	$isValid = false;
	if(strlen($strn) == 8){
		$hkid_str = substr($strn, 0, 7)."(".substr($strn, 7,1).")";
		$hkid = new HKID();
		$hkid->set($hkid_str);
		if($hkid->validate()) $isValid = true;
	}
	
	if(!$isValid){
		$p = "/^\d{8}$/";
		if(preg_match ($p , $strn)){
			$checkDigit = intval(substr($strn, 7, 1));
			$total = 0;
			$total2 = 0;
			for($i = 0; $i < 7; $i++){
				$total += intval(substr($strn, $i, 1)) * ($i + 3);
				$total2 += intval(substr($strn, $i, 1)) * (9 - $i);
			}
			if(($total % 10) == $checkDigit || ($total2 % 10) == $checkDigit) $isValid = true;
		}
	}
	return $isValid;
}

function getCourseCodeWithoutAplCode($code, $delimiter = ","){
	$pos = strrpos($delimiter, $code);
	if($pos !== FALSE){
		$code = substr($code, 0, $pos + 1);
	}
	return $code;
}
?>