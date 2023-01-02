<?php
 /*
 Class:          CalendarArticle
 Purpose:        Stucture to hold article/event data and 
                         then store into an array for future retrieval
 
 */
 require_once ("common.php");
 
 class CalendarArticle
 {
         var $day = "";
         var $month = "";
         var $year = "";
         var $page = ""; //full wiki page name
         var $eventname = ""; //1st line of body; unformated plain text
         var $body = ""; // everything except line 1 in the event page
         var $html = ""; // html link displayed in calendar
		 var $isImage = false;
         
         function CalendarArticle($month, $day, $year){
                 $this->month = $month;
                 $this->day = $day;
                 $this->year = $year;    
         }
 }
 
 /*
 Class:          CalendarArticles
 Purpose:        Contains most of the functions to retrieve article 
                         information. It also is the primary container for
                         the main array of class::CalendarArticle articles
 
 */
 class CalendarArticles
 {       
         private $arrArticles = array();
         public $wikiRoot = "";
         private $arrTimeTrack = array();
         private $arrStyle = array();

	// build an event based on the 1st line or ==event== type
	public function addArticle($month, $day, $year, $page){
		$lines = array();
		$temp = "";		
		$head = array();

		$article = new Article(Title::newFromText($page));
		if(!$article->exists()) return "";

		$redirectCount = 0;
		
		if( $article->isRedirect() && $this->setting('disableredirects') ) return '';
		
		 while($article->isRedirect() && $redirectCount < 10){
			 $redirectedArticleTitle = Title::newFromRedirect($article->getContent());
			 $article = new Article($redirectedArticleTitle);
			 $redirectCount += 1;
		 }

		$body = $article->fetchContent(0,false,false);
	
		if(strlen(trim($body)) == 0) return "";
		
		$lines = split("\n",$body);
		$cntLines = count($lines);
	
		// dont use section events... only line 1 of the page
		if($this->setting('disablesectionevents')){
			$key = $lines[0]; //initalize the key
			$head[$key] = ""; 
			$cntLines = 0;
		}
	
		for($i=0; $i<$cntLines; $i++){
			$line = $lines[$i];
			if(substr($line,0,2) == '=='){
				$arr = split("==",$line);
				$key = $arr[1];
				$head[$key] = ""; $temp = "";
			}
			else{
				if($i == 0){ // $i=0  means this is a one event page no (==event==) data
					$key = $line; //initalize the key
					$head[$key] = ""; 
				}
				else{
					$temp .= "$line\n";
					$head[$key] = Common::cleanWiki($temp);
				}
			}
		}

		while (list($event,$body) = each($head)){
			$this->buildEvent($month, $day, $year, trim($event), $page, $body);
		}
	}
 
	// this is the main logic/format handler; the '$event' is checked for triggers here...
	public function buildEvent($month, $day, $year, $event, $page, $body, $eventType='addevent', $bRepeats=false){	
	
		// user triggered yearly repeat event...
		if(substr($event,0,2) == '##'){
			$event = trim(str_replace("##", "", $event));
			$this->buildRecurrenceEvent($month, $day, $year, $event, $page);
		}

		//check for repeating events
		$arrEvent = split("#",$event);
		if( isset($arrEvent[1]) && ($arrEvent[0] != 0) && $this->setting('enablerepeatevents') ){
			for($i=0; $i<$arrEvent[0]; $i++) {
				$this->add($month, $day, $year, $arrEvent[1], $page, $body, false, true);
				Common::getNextValidDate($month, $day, $year);
			}
		}else{
			$this->add($month, $day, $year, $event, $page, $body, $eventType, $bRepeats);	
		}
	}

	// this is the MAIN function that returns the events to the calendar...
	// there shouldn't be ANY formatting or logic done here....
	public function getArticleLinks($month, $day, $year){
		global $wgParser;
		
		$ret = $list = "";
		$bFound = false;

		// not using 'templates' array, but the purpose was to put all these events above other events...
		if(isset($this->arrArticles['templates'])){	
			foreach($this->arrArticles['templates'] as $cArticle){
				if($cArticle->month == $month && $cArticle->day == $day && $cArticle->year == $year){
					$ret .= $cArticle->html;
				}
			}	
		}
		
		// we want to format the normal 'add event' items in 1 table cell
		// this creates less spacing and creates a better <ul>
		$head = "<tr cellpadding=0 cellspacing=0 ><td class='calendarTransparent singleEvent'>";
		$head .= "<ul class='bullets'>";
		$foot = "</ul></td></tr>";
		
		if(isset($this->arrArticles['events'])){		
			foreach($this->arrArticles['events'] as $cArticle){
				if($cArticle->month == $month && $cArticle->day == $day && $cArticle->year == $year){
					$image = Common::getImageURL($cArticle->eventname);
					
					if( $image ) {
						$list .= '<a href="'. $this->wikiRoot . $cArticle->page . '"><img src="' . $image . '"></a>';
					}
					else {
						$list .= "<li>" . $cArticle->html . "</li>";	
					}
					
					$bFound = true;
				}	
			}
		}
		
		if($bFound) 
			$ret .= $head . $list . $foot;
		
		return $ret;
	}
	
	public function buildSimpleEvent($month, $day, $year, $event, $body, $page){
		
		$cArticle = new CalendarArticle($month, $day, $year);
		$temp = $this->checkTimeTrack($month, $day, $year, $event, '');
		$temp = trim($temp);
		$summaryLength = $this->setting('enablesummary',false);
		
		$html_link = $this->articleLink('', $temp, true);
		
		// format for different event types
		$class = "baseEvent ";
		//if($bRepeats) $class .= "repeatEvent ";
		//if($eventType == "recurrence") $class .= "recurrenceEvent ";
		$class = trim($class);		
		
		$cArticle->month = $month;	
		$cArticle->day = $day;	
		$cArticle->year = $year;	
		$cArticle->page = $page;	
		$cArticle->eventname = $event;
		$cArticle->body = $body;		
		
		// this will be the main link displayed in the calendar....
		$cArticle->html = "<span class='$class'>$html_link</span><br/>" . Common::limitText($cArticle->body, $summaryLength);

		$this->arrArticles['events'][] = $cArticle;	
	}
	
	// when the calendar loads, we want to put all the template events into memory
	// so we dont have to read the wiki db for every day
	public function addTemplate($month, $year, $pagename){
		$displayText = "";
		$arrEvent = array();
	
		$articleName = $pagename . "/" . $month . "-" . $year . " -Template";
		$article = new Article(Title::newFromText($articleName));

		if (!$article->exists()) return "";
		
		$displayText  = $article->fetchContent(0,false,false);
	
		$arrAllEvents=split(chr(10),$displayText);
		if (count($arrAllEvents) > 0){
			for($i=0; $i<count($arrAllEvents); $i++){
				$arrEvent = split("#",$arrAllEvents[$i]);
				
				if(!isset($arrEvent[1])) continue;//skip 
				
				if(strlen($arrEvent[1]) > 0){
					$day = $arrEvent[0];
					$arrRepeat = split("-",$arrEvent[0]);
					
					if(count($arrRepeat) > 1){
						$day = $arrRepeat[0];
						while($day <= $arrRepeat[1]){
							$this->buildEvent($month, $day, $year,  $arrEvent[1], $articleName, "", "templates", true);
							$day++;
						}
					}else{
						$this->buildEvent($month, $day, $year, $arrEvent[1], $articleName, "", "templates");
					}
				}
			}
		}	
	}

	// this is the FINAL stop; the events are stored here then pulled out
	// and displayed later via "getArticleLinks()"... 
	private function add($month, $day, $year, $eventname, $page, $body, $eventType='addevent', $bRepeats=false){
		// $eventType='default' -- addevent
		// $eventType='recurrence'
		// $eventType='template'
		global $wgParser;
		
		$cArticle = new CalendarArticle($month, $day, $year);
		$temp = $this->checkTimeTrack($month, $day, $year, $eventname, $eventType);
		$temp = trim($temp);
		
		// lets get the body char limit
		$summaryLength = $this->setting('enablesummary',false);

		$html_link = $this->articleLink($page, $temp);
		
		// format for different event types
		$class = "baseEvent ";
		if($bRepeats) $class .= "repeatEvent ";
		if($eventType == "recurrence") $class .= "recurrenceEvent ";
		$class = trim($class);		
		
		$cArticle->month = $month;	
		$cArticle->day = $day;	
		$cArticle->year = $year;	
		$cArticle->page = $page;	
		$cArticle->eventname = $temp;
		$cArticle->body = $body;	

		$cArticle->isImage = $eventType;		

		// wik-a-fi the $body; however, cut off text could cause html issues... so try to 
		// keep all required body wiki/html to the top
		$parsedBody = $wgParser->recursiveTagParse( Common::limitText($cArticle->body, $summaryLength) );

		// this will be the main link displayed in the calendar....
		$cArticle->html = "<span class='$class'>$html_link</span><br/>" . $parsedBody;

		$this->arrArticles['events'][] = $cArticle;
	}
	
	// this function checks a template event for a time trackable value
	private function checkTimeTrack($month, $day, $year, $event, $eventType){
	
		if((stripos($event,"::") === false) || $this->setting('disabletimetrack'))
			return $event;
		
		$arrEvent = split("::", $event);
		
		$arrType = split(":",$arrEvent[1]);
		if(count($arrType) == 1)
			$arrType = split("-",$arrEvent[1]);
		
		if(count($arrType) != 2) return $event;
		
		$type = trim(strtolower($arrType[0]));

		// we only want the displayed calendar year totals
		if($this->year == $year){
			if($eventType=='templates')
				$this->arrTimeTrack[$type.' (y)'][] = $arrType[1];
			else
				$this->arrTimeTrack[$type.' (m)'][] = $arrType[1];
		}
		
		//piece together any prefixes that the code may have added - like (r) for repeat events
		$ret = $arrType[0] . " <i>-(track)</i>"; 
		
		return $ret;
	}
	
	public function buildTrackTimeSummary(){
	
		if($this->setting('disabletimetrack')) return "";
	
		$ret = "";
		$cntValue = count($this->arrTimeTrack);

		if($cntValue == 0) return "";
	
		$cntHead = split(",", $this->setting('timetrackhead',false));
		$linktitle = "Time summaries of time specific enties. Prefix events with :: to track time values.";
		
		$html_head = "<hr><table title='$linktitle' width=15% border=1 cellpadding=0 cellspacing=0><th>$cntHead[0]</th><th>$cntHead[1]</th>";
		$html_foot = "</table><small>"
			. "(m) - total month only; doesn't add to year total <br/>"
			. "(y) - total year; must use monthly templates<br/></small><br>";

		while (list($key,$val) = each($this->arrTimeTrack)) {
			$ret .= "<tr><td align='center'>$key</td><td align='center'>" . array_sum($this->arrTimeTrack[$key]) . "</td></tr>";
		}

		return $html_head . $ret . $html_foot;
	}
	
	//find the number of current events and "build" the <add event> link
    public function buildAddEventLink($month, $day, $year, $text="") {
		
		if($day < 1) return "";
		$section_new = '';
		
		if($this->setting('disableaddevent') && strlen($text) == 0) return "";
		if($this->setting('disableaddevent') && strlen($text) > 0) return $day;
		
    	$articleName = "";    	// the name of the article to check for
    	$articleCount = 1;    	// the article count
		
		if($text == "")
			$text = "<u>" . Common::translate("add_event") . "</u>";
			
		$tip = Common::translate('add_event_tip');
		
		if($this->setting('weekofyear')){
			$tip .= " (wk:" . $this->getWeekOfTheYear($month,$day,$year,true) . ")";
		}
		
		//$date = "$month-$day-$year";
		$date = $this->userDateFormat($month, $day, $year);

		$articleName = $this->getNextAvailableArticle($this->calendarPageName, $date);
		
		// if we're using multi-event mode, then default to section=new
		if( $this->setting('usesectionevents') )
			$section_new = "&section=new";
		
		$newURL = "<a title='$tip' href='" . $this->wikiRoot . wfUrlencode($articleName) . "&action=edit$section_new'>$text</a>";
		return $newURL;
	}

	public function getNextAvailableArticle($page, $date, $event_zero=false){
		$stop = false;
		$page = "$page/$date -Event "; 
		$articleCount = 1;
		
		// for ical option and setting all icals to Event -0 (== event ==) style
		if($event_zero)
			return $page . "0"; 
		
		$max_articles = $this->setting('maxdailyevents',false);
		
		// bump up the max for iCal imports...but not to much in case of a runaway
		// we also want to ignore the inforced 'usesectionevents'..however, the 
		// calendar will still only display the 'maxdailyevents' value
		if($this->setting('ical')){
			$max_articles += 5; 
		}
		else{
			if($this->setting('usesectionevents') && !$this->setting('ical'))
				return $page . $articleCount;
		}
		
		$article = new Article(Title::newFromText($page . $articleCount));
		
		// dont care about the articles here, just need to get next available article
		while ($article->exists() && !$stop) {
			$displayText  = $article->fetchContent(0,false,false);
			if(strlen($displayText) > 0){
				$articleCount++;
				$article = new Article(Title::newFromText($page . $articleCount));
				
				if($articleCount == $max_articles)
					$stop = true;
			}
			else $stop = true;
		}

		return  $page . $articleCount;
	}
	
	function readStylepage(){
		$articleName = $this->calendarPageName . "/" . "style";	
		$article = new Article(Title::newFromText($articleName));

		if ($article->exists()){
			$displayText  = $article->fetchContent(0,false,false);	
			$this->arrStyle = split(chr(10), $displayText);
		}
	}	
	
	public function getConfig($pagename){
	
		$params = array();	
		
		$articleName = "$pagename/config";
		$article = new Article(Title::newFromText($articleName));

		if ($article->exists()){
			$body  = $article->fetchContent(0,false,false);
			$body = str_replace("\"", "", $body);	

			$arr = split("\n", $body);
			$cnt = count($arr);

			for($i=0; $i<$cnt; $i++){
				$arrParams = split("=", $arr[$i]);
				$key = trim($arrParams[0]);
				
				if($key != 'useconfigpage'){		// we dont want users to lock themselves out of the config page....		
					if(count($arrParams) == 2) 
						$params[$key] = trim($arrParams[1]); // we have both $key and $value
					else
						$params[$key] = $key; // init the value with itself if $value is null
				}
			}
		}
		return $params;
	}
	
    // returns the link for an article, along with summary in the title tag, given a name
    private function articleLink($title, $text, $noLink=false){
		global $wgParser;
		
		if(strlen($text)==0) return "";
		//$text = $wgParser->recursiveTagParse( $text );
		$arrText = $this->buildTextAndHTMLString($text);
		$style = $arrText[2];

		//locked links
		if($this->setting('disablelinks') || $noLink)
			$ret = "<span $style>" . $arrText[1] . "</span>";
		else
			if($this->setting('defaultedit'))
				$ret = "<a $style title='$arrText[0]' href='" . $this->wikiRoot . wfUrlencode($title) . "&action=edit'>$arrText[1]</a>";
			else
				$ret = "<a $style title='$arrText[0]' href='" . $this->wikiRoot . wfUrlencode($title)  . "'>$arrText[1]</a>";
		
		
		
		return $ret;
    }
	
	private function buildTextAndHTMLString($string){

		$string = Common::cleanWiki($string);	
		$htmltext = $string;
		$plaintext = strip_tags($string);
		$charlimit = $this->setting('charlimit',false);
		
		if(strlen($plaintext) > $charlimit) {
			$temp = substr($plaintext,0,$charlimit) . "..."; //plaintext
			$ret[0] = $plaintext; //full plain text
			$ret[1] = str_replace($plaintext, $temp, $htmltext); //html
			$ret[2] = ""; //styles
		}
		else{
			$ret[0] = $plaintext; //full plain text
			$ret[1] = $htmltext;	
			$ret[2] = ""; //styles
		}
		
		if(!$this->setting('disablestyles'))
			$ret[2] = $this->buildStyleBySearch($plaintext);
		
		return $ret;
	}	
	

	
	private function buildStyleBySearch($text){

		$stylePage = '';
	
		// set default style if available, if not... use std windows defaults
		$defaultStyle = $this->setting('style', false);
		
		for($i=0; $i < count($this->arrStyle); $i++){
			$arr = split("::", $this->arrStyle[$i]);
			$cnt = count($arr);
			
			if(stripos($text, $arr[0]) !== false) {
				$stylePage = trim($arr[1]);
			}
		}

		//return "style='" . str_replace("'", "", "$defaultStyle;$stylePage") . "' ";
		return "style='$defaultStyle;$stylePage' ";
	}
	
	// creates a new page and populates it as required
	function createNewPage($page, $event, $description, $summary){
		$article = new Article(Title::newFromText($page));

		$event = $event . "\n\n" . $description;

		$article->doEdit($event, EDIT_NEW);
	}
	
	function createNewMultiPage($page, $event, $description, $summary, $overwrite=false){
		$article = new Article(Title::newFromText($page));
		$bExists = $article->exists();

		$event = "==$event==\n\n" . $description;
		
		if($bExists){
			if($overwrite){
				$article->doEdit($body.$event, $summary, EDIT_UPDATE);
			}
			else{
				$body  = trim($article->fetchContent(0,false,false));
				if(strlen($body) > 0) $body = "$body\n\n";
				$article->doEdit($body.$event, $summary, EDIT_UPDATE);
			}
		}
		else{
			$article->doEdit($event, $summary, EDIT_NEW);
		}
	}
	
	private function buildRecurrenceEvent($month, $day, $year, $event, $page){
		$this->debug->set('buildRecurrenceEvent started');
		
		$recurrence_page = "$this->calendarPageName/recurrence";
		
		$article = new Article(Title::newFromText($page));
		$bExists = $article->exists();
		
		if($bExists){
			$article->doEdit('', 'recurrence event moved...', EDIT_UPDATE);
			unset($article);
		}

		$rrule = "RRULE:FREQ=YEARLY;INTERVAL=1"
			. ";BYMONTH=$month"
			. ";DAY=$day"
			. ";SUMMARY=$event";
		
		$this->updateRecurrence($recurrence_page, $rrule, $event, 'recurrence update');	
		$this->invalidateCache = true;
	}
	
	function updateRecurrence($page, $rrule, $event, $summary, $overwrite=false){
		$article = new Article(Title::newFromText($page));
		$bExists = $article->exists();

		$ret = 0;
		$rrule = trim($rrule);
		
		if($bExists){
			if($overwrite){
				$article->doEdit("$rrule", $summary, EDIT_UPDATE);
				$ret = 1;
			}
			else{
				$body  = trim($article->fetchContent(0,false,false));
				if((stripos($body, $rrule) === false)){ 	// lets not re-add duplicate rrule lines
					$article->doEdit("$body\n" . "$rrule", $summary, EDIT_UPDATE);
					$ret = 1;
				}
			}
		}
		else{
			$article->doEdit("$event", $summary, EDIT_NEW);
			$ret = 1;
		}
		
		return $ret;
	}
	
	// RRULE:FREQ=YEARLY;INTERVAL=1;BYMONTH=10;BYDAY=2MO;MONTH=10;DAY=14;SUMMARY=Columbus Day 
	public function addVCalEvents($page, $year, $month){	
		$arrRRULES = array();
		
		$ical_short_day = array("SU"=>0,"MO"=>1,"TU"=>2,"WE"=>3,"TH"=>4,"FR"=>5,"SA"=>6);	
		
		$articleName = "$page/recurrence";
		$article = new Article(Title::newFromText($articleName));
		$bExists = $article->exists();
		
		if($bExists){
			$body  = trim($article->fetchContent(0,false,false));
			$arrRRULES = $this->convertRRULEs($body);
		} else return;

		foreach($arrRRULES as $rules){

			$bExpired = false;
			if(isset($rules['UNTIL'])){
				$bExpired = $this->checkExpiredRRULE($rules['UNTIL']);
			}
			
			if($bExpired) continue; // skip the rest of the current loop iteration
		
			if($rules['FREQ'] == 'YEARLY' && !isset($rules['BYDAY']) && $rules['BYMONTH'] == $month){ //std sameday recurrence
				$this->buildEvent($month, $rules['DAY'], $year, $rules['SUMMARY'], $articleName, "", 'recurrence');
			}
			else if ($rules['FREQ'] == 'YEARLY' && isset($rules['BYDAY']) && $rules['BYMONTH'] == $month){
				$num = $rules['BYDAY'];
		
				// parse the ical format for BYDAY (1MO, 4TH, 2WE, etc)
				settype($num, 'integer'); //get the numeric value of BYDAY
				$ical_weekday = str_replace($num, "", $rules['BYDAY']); //get the weekday text value of BYDAY
				$day = $ical_short_day[$ical_weekday]; // take the text and get the 0-6 numeric value (SU=0, MO=1, etc)

				$wday_info = Common::wdayOffset($month,$year,$day);
				$offset = $wday_info['offset'];
				
				// if we have something like -1SU; then handle it here...
				if($num < 0)
					$num = ($wday_info['weeks'] +1) + $num;
					
				// kick back the week count if needed
				if($offset > 0 && $num != 0) $num--;

				$theday = $offset + (7 * $num);
				$this->buildEvent($month, $theday, $year, $rules['SUMMARY'], $articleName, "", 'recurrence');
			}	
		}
		unset($rules);
	}

	// filter out RRULE-'UNTIL' expired events
	function checkExpiredRRULE($date){
		
		$bRet = false;
		
		$expire_year = substr($date,0,4);
		$expire_month = substr($date,4,2);

		if($this->year > $expire_year){
			$bRet = true;
		}
		else if($this->year == $expire_year){
			if($this->month > $expire_month){
				$bRet = true;
			}
		}
		
		return $bRet;
	}
	
	// converts an RRULE line into an easy to use 2d-array
	function convertRRULEs($rrules){
		$arr_rrules = split("RRULE:", $rrules);

		$events = array();
		array_shift($arr_rrules); //1st array[0] is garbage because RRULE: in position 0(1st)
		
		foreach($arr_rrules as $rule){
			$arr_properties = split(";", $rule);
			foreach($arr_properties as $property){
				$arr_rule = split("=", $property);
				$rules[$arr_rule[0]] = $arr_rule[1]; //key and value
			}
			
			if(isset($rules['FREQ'])) //make sure we add valid rows
				$events[] = $rules;

			unset($rules); //clear array
		}

		return $events;
	}
	
	// any custom MW tags or code can be filtered out here...
	// this is only for calendar event display and doesn't edit the article itself
	private function cleanEventData($content){
		
		$ret = $content;
		
		// remove [[xyz]] type strings...
		$ret = preg_replace('[(\[\[)+.+(\]\])]', '', $ret); 
		
		// remove  __xyz__   type strings...
		$ret = preg_replace('[(__)+.+(__)]', '', $ret); 
		
		// remove  {{xyz}}  type strings...
		$ret = preg_replace('[({{)+.+(}})]', '', $ret); 
		
		return $ret;
	}
/*	
	public function chkConfigToolPage($configToolPage){
	
		$article = new Article(Title::newFromText($configToolPage));
		
		if(!$article->exists())
			$article->doEdit("<config/>", "", EDIT_NEW);
		elseif(trim($article->fetchContent()) == "")
			$article->doEdit("<config/>", "", EDIT_UPDATE);
	}	
*/
}
