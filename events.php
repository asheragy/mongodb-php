<?php


class Event 
{

	public $subject; //String
	public $start;   //DateTime
	public $end;
	
	public function __construct($subject, $start, $end, $tz) {
		$this->subject = $subject;
		$this->start = $start;
		$this->end   = $end;
	}

	public function __toString()
    {
        return $this->start->format('c') . " " . $this->end->format('c') . " " . $this->getTimezone()->getName() . " " . $this->subject . " ";
    }
	
	public function getTimezone()
	{
		return $this->start->getTimezone();
	}
	
	public static function getEvents($count)
	{
		$events = array();
		$start = new DateTime();
		$start->setTime(0,0,0);
		$duration = 30; //duration of single event
		$gap      = 60; //minutes between start time of next event (equal to duration means no time gaps)
		
		for($i = 0; $i < $count; $i++) {
			//Create new event
			$end = clone $start;
			$end->add( new DateInterval('PT'.$duration.'M') );
			
			$subject = "Event " . ($i + 1);
			$event = new Event($subject, clone $start, $end, $start->getTimezone());
			
			//Add to result
			$events[] = $event;
			
			//Next record
			$start->add( new DateInterval('PT'.$gap.'M') );

		}
		
		return $events;
	}
	
}


?>