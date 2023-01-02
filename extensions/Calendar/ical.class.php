<?php

class ical_calendar{

	var $data = "";

	// get an array of vcal elements
	public function getData(){return $this->parse();}
	
	//set and validate the vcal file
	public function setFile($file){	
		$bOK = file_exists($file);
		if($bOK){
			$this->data = file_get_contents($file);
			$bOK = $this->validate();
		}
		
		return $bOK;
	}
	
		//set and validate the vcal file
	public function setData($data){	
		$bOK = false;
		$this->data = $data;
		$bOK = $this->validate();
		
		return $bOK;
	}
	
	// take: BEGIN and END (20090116T150000Z) and determine the true repeat counter
	public function getRepeatDays($date_time1, $date_time2){
	
	
	}
	
//******************************* PRIVATE FUNCTIONS ********************************************************************

	//verify if this is truely a vcal file
	private function validate(){
	
		$lines = split("\n", $this->data);
		if(stripos($lines[0],"BEGIN:VCALENDAR") !== false)
			return true;
		
		return false;
	}
	
	//takes a successfully loaded file and returns a parsed file
	private function parse(){
		$arrSection = array();
		$arrEvents = array();

		$sections = split("BEGIN:VEVENT", $this->data);
		
		for($sec=0; $sec<count($sections); $sec++){
			$lines = split("\n", $sections[$sec]);

			for($i=0; $i< count($lines); $i++){
				$line = $lines[$i];
				$event = split(":",$line);
				
				if(substr($line,0,7) == 'DTSTART'){
					$arrSection['DTSTART'] = $this->convertToPHPDate($event[1]);
				}
				if(substr($line,0,5) == 'DTEND'){
					$arrSection['DTEND'] = $this->convertToPHPDate($event[1]);
				}
				if(substr($line,0,7) == 'SUMMARY'){
					$arrSection['SUMMARY'] = trim($event[1]);
				}
				if(substr($line,0,11) == 'DESCRIPTION'){
					$arrSection['DESCRIPTION'] = trim($event[1]);
				}
				if(substr($line,0,5) == 'RRULE'){
					$arrSection['RRULE'] = trim($event[1]);
				}
			}
			
			if(count($arrSection) > 0){
				$arrEvents[] = $arrSection;
				unset($arrSection);
			}
		}
		
		return $arrEvents;
	}
	
	private function filterExpiredRRULE($rrule){
	
	}
	
	//ex: 20090116T150000Z
	public function convertToPHPDate($date){
		$date = trim($date);
	
		$date_time = split("T", $date);
		$date = $date_time[0];
	
		$arr['year'] = substr($date,0,4);
		$arr['mon'] = substr($date,4,2) +0;
		$arr['mday'] = substr($date,6,4) +0;
		
		if(isset($date_time[1])){	
			$time = $date_time[1];
			$arr['hours'] = substr($time,0,2) +0;
			$arr['minutes'] = substr($time,2,2) +0;
			$arr['seconds'] = substr($time,4,2) +0;
		}
		else{
			$arr['hours'] = 12;
			$arr['minutes'] = 0;
			$arr['seconds'] = 0;
		}
		
		return $arr;
	}
}

?>