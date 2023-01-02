<?php

class debugger{

	private $debugData = "";
	private $markTime = 0;
	private $startTime = 0;
	private $enabled = false;

	function debugger($type){
		$this->type = $type;
		
		$this->startTime = $this->markTime = microtime(1);
	}
	public function getTotalTime() {return round(microtime(1) - $this->startTime,2);}
	public function enabled($bEnabled) {$this->enabled = $bEnabled;}
	
	public function set($e){
		if(!$this->enabled) return;		
		
		// recorded time in seconds
		$steptime = round(microtime(1) - $this->markTime,2);
		$totaltime = round(microtime(1) - $this->startTime,2);	
		
		if($this->type = 'html')
			return $this->setHTML($e, $steptime, $totaltime);

		//must be last
		$this->markTime = microtime(1);
	}
	
	// return debug data in HTML format
	public function get() {
		if(!$this->enabled) return;
		
		if($this->type = 'html')
			return $this->getHTML();
	}	
	
	private function setHTML($e, $steptime, $totaltime){
		$e = trim(htmlspecialchars($e));
		return $this->debugData .= "<tr><td><pre>$e</pre></td><td align=center>$steptime</td><td align=center>$totaltime</td></tr>";
	}
	private function getHTML(){
	
		return "<table border=1 cellpadding=5 cellspacing=0 >"
		. "<tr><th>DebugName</th><th>StepTime<br>(sec)</th><th>TotalTime<br>(sec)</th></tr>"
		. "$this->debugData</table>";
	
	}
}

?>