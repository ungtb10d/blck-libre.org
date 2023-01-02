<?php

# only non-dependent functions here...basically re-usable helper functions
# might make this a helper class later.
#
class Common{
	static function checkForMagicWord($string){
		global $wgParser;
		
		$ret = $string;
		$string = str_replace("{{","",$string);
		$string = str_replace("}}","",$string);
		$string = strtolower($string);

		$string = $wgParser->getVariableValue($string);
		
		if(isset($string)) $ret = $string;
		
		return $ret;
	}

	static function datemath($dayOffset, $month, $day, $year){

		$seconds = $dayOffset * 86400;
		$arr = getdate(mktime(12, 0, 0, $month, $day, $year) + $seconds);

		return $arr;
	}

	static function cleanWiki($text){

		$text = self::swapWikiToHTML($text, "'''", "b");
		$text = self::swapWikiToHTML($text, "''", "i");
		$text = self::swapWikiToHTML($text, "<pre>", "");
		$text = self::swapWikiToHTML($text, "</pre>", "");

		return $text;
	}

	//basic tage changer for common wiki tags
	static function swapWikiToHTML($text, $tagWiki, $tagHTML){

		$ret = $text;

		$lenWiki = strlen($tagWiki);
		$pos = strpos($text, $tagWiki);
		if($pos !== false){
			if($tagHTML != ""){
				$ret = substr_replace($text, "<$tagHTML>", $pos, $lenWiki);
					$ret = str_replace($tagWiki, "</$tagHTML>", $ret);
			}
			else
				$ret = str_replace($tagWiki, "", $ret);
		}

		return $ret;
	}	

	static function limitText($text,$max) { 
		global $wgParser;

		if($max == "") return;
		
		$text = trim($text);
		
		if(strlen($text) > $max)
			$ret = substr($text, 0, $max) . "...";
		else
			$ret = $text;


		return $ret;
	} 


	static function getDaysInMonth($month, $year) {
		
		// 't' = Number of days in the given month	
		return date('t', mktime(12, 0, 0, $month, 1, $year)); 
	}

	static function getDateArr($month, $day, $year, $hour=0, $minutes=0, $seconds=0, $add_seconds=0){

		return getdate(mktime($hour, $minutes, $seconds, $month, $day, $year) + $add_seconds);
	}

	static function getNextValidDate(&$month, &$day, &$year){

		$seconds = 86400; //1 day
		$arr = getdate(mktime(12, 0, 0, $month, $day, $year) + $seconds);
		
		$day = $arr['mday'];
		$month = $arr['mon'];
		$year = $arr['year'];
		
		return $arr;
	}

	static function day_diff($date1, $date2){

		if(!isset($date2)) return 0;

		$start = mktime($date1['hours'], $date1['minutes'], $date1['seconds'], $date1['mon'], $date1['mday'], $date1['year']);
		$end = mktime($date2['hours'], $date2['minutes'], $date2['seconds'], $date2['mon'], $date2['mday'], $date2['year']);

		return ($end - $start) / 86400; //seconds
		
	}

	// get the offset info based on the 1st of the month
	static function wdayOffset($month, $year, $weekday){

		$timestamp = mktime(12, 0, 0, $month, 1, $year);
		$max_days = date('t', $timestamp);	
		$the_first = getdate($timestamp);
		$wday = $the_first["wday"];	
		
		$offset = ($weekday - $wday) +1; //relate $wday as a negative number
		$month_offset = (7 + $offset);
		
		$weeks = 4;
		
		// this $weekday is before the 1st
		if($offset <= 0 )
			if( ($month_offset + 28) <= $max_days)  $weeks = 5;
		
		// this $weekday is after the 1st
		if($offset > 0 )
			if( ($month_offset + 21) <= $max_days)  $weeks = 5;

		$arr['offset'] = $offset; // delta between the 1st and the $weekday parameter(0-sun, 1-mon, etc)
		$arr['maxdays'] = $max_days; //days in month
		$arr['weeks'] = $weeks; //max weeks this weekday has
		
		return $arr;
	}

	static function translate($value, $key=""){
		global $wgLang;
		
		switch($key){
		case 'month':
			return $wgLang->getMonthName($value);
			
		case 'month-gen': //genitive case or possessive case
			return $wgLang->getMonthNameGen($value);
			
		case 'month_short':
			return $wgLang->getMonthAbbreviation($value);
			
		case 'weekday':
			return $wgLang->getWeekdayName($value);
			
		default:
			//return $wgLang->iconv("", "UTF-8", Common::translate($value));
			return utf8_encode(wfMsg($value));
		}
		return "";
	}
	
	//kludge to display an image event link... this MUST be cleaned up!
	static function getImageURL( $image ){

		// stripe brackets... cant use them in the lookup
		$image = str_replace('[[', '',$image);
		$image = str_replace(']]', '',$image);
		
		// check to see if they passed in Image: or File: and add if needed
		if( (stripos($image, 'Image:') === false) && (stripos($image, 'File:') === false) ){
			$image = "File:" . $image;
		}
		
		$titleObj = Title::newFromText( $image );
		if( !$titleObj ) return null;
		if( !$titleObj->exists() ) return null;
		
		$img = new ImagePage($titleObj); 
		
		// we're good, return the URL
		return $img->getDisplayedFile()->getUrl();  
	}
	
	static function isWeekend($month, $day, $year){
		$dayOfWeek = date('N', mktime(12, 0, 0, $month, $day, $year));
		
		if($dayOfWeek > 5) return true;

		return false;
	}
	
	function right($value, $count){
		$value = substr($value, (strlen($value) - $count), strlen($value));
		return $value;
	}

	function left($string, $count){
		return substr($string, 0, $count);
	}
}







