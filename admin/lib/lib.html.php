<?php
final class Html
{
	public static function BreakLine($no = 1){
		return str_repeat("<br />\n",$no);
	}
	public static function Span($id, $content, $otherParam = '')
	{
		if(!empty($otherParam)) $otherParam = " ".$otherParam;
	    return ('<span id="'.$id.'"'.$otherParam.' />'.$content.'</span>');
	}
	public static function Section($id, $title,$content,$width = 0, $otherParam = '')
	{
		if(!$otherParam) $otherParam = " ".$otherParam;
		$html = '
		<div'.($width > 0 ? ' style="width:'.$width.'px"' : "").'>
			<div class="sectionHead">'.$title.'</div>
			<div id="'.$id.'">'.$content.'</div>
		</div>';
		
		return $html;
		//<div class="sectionHead" onclick="showSection(\''.$id.'\')">'.$title.'</div>
	}
	public static function PageTitle($title){
		return '<table class="pages_title"><tr><td class="pages_title_l"></td><td class="pages_title_c">'.$title.'</td><td class="pages_title_r"></td></tr></table>';
		//return "<h2>$title</h2>\n";
	}
	public static function LoadingBox($title, $content)
	{
	  	$html =  '<div class="LoadingBox"><table align="center" width="300" height="100">';
		$html .= '	<tr><td>';
	  	$html .= '		<p><img alt="Loading" src="'.GlobalConfig::PATHS_IMAGE.'loading_32_2.gif" /> '.$title.'</p>';
		$html .= '	</td></tr>';
		$html .= '	<tr><td>';
	  	$html .= $content;
		$html .= '	</td></tr>';
	  	$html .= '</table></div>';	  	
	  	return $html;
	}
	public static function WarningMessage($msg) {
		return "<span class=\"WarnMsg\">${msg}</span>\n";
	}
	public static function ErrorMessage($msg) {
		return "<span class=\"ErrorMsg\">${msg}</span>\n";
	}
	public static function FormatFileSize ($bytes, $precision = 1){
		$suffix = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$index = floor (log ($bytes + 1, 1024)); // + 1 to prevent -INF
		return sprintf ("%0.{$precision}f %s", $bytes / pow (1024, $index), $suffix[$index]);
	}
}
class JavaScript
{
	const BEGIN = "\n<script type=\"text/javascript\">\n";
	const END = "\n</script>\n";
	public static function onReady($script){
		return self::BEGIN ."$(function(){ $script });".self::END;
	}
	public static function setFocus($query){
		echo self::onReady("setTimeout(\"$('$query').focus();\", 0)");
	}
	public static function redirect($url="index.php",$stopPHP=true){
		echo self::BEGIN."window.location='$url';".self::END;
		if($stopPHP)die("Redirect to $url");
	}
	public static function replaceEmpty($query,$replace="-"){
		if (is_array($query)) {	$query = join(",",$query); }
		echo self::onReady('FillEmpty("'.$query.'","'.$replace.'");');
	}
	public static function jqDatePicker($query,$opt = ""){
		if (is_array($query)) {	$query = join(",",$query); }
		echo self::onReady("$('$query').datepicker({ $opt });");
	}
	public static function jqTableSorter($query="#mainTable",$nonsort=false,$opt=false){
		$h="";
		if ($nonsort !== false){
			if (!is_array($nonsort)){$nonsort = array($nonsort);}
			$h=array();
			foreach ($nonsort as $c){$h[]="$c:{sorter:false}";}
			$h="headers: {".implode(",",$h)."}";
		}
		echo self::onReady("$('$query').tablesorter({ $h".($opt?",$opt":"")."})");
	}
}
final class JS extends JavaScript {}
?>