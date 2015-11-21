<?php

require_once('database.php');
require_once('events.php');

define("TOTAL_USERS", 10);
define("EVENTS_PER_USER", 10);
define("COLLECTION", "calendar");

$db = new Database();


$result = test_addAll($db);
//if($result)
//	$result = test_updateUser($db,5);
//if($result)
//	$result = test_deleteUser($db,7);
//if($result)
//	$result = test_deleteAll($db);
	
if($result)
	echo "Success";
else
	echo "Failed";

function test_addAll($db)
{
	//Insert X events per user
	$result = true;
	$count = 0;
	$t1 = microtime(true);
	for($i = 0; $i < TOTAL_USERS; $i++) {

		$events = Event::getEvents(EVENTS_PER_USER);
		foreach($events as $event) {
			$userid = $i + 1;
			if($db->insert("calendar",$userid,$event) == false)
				$result = false;
			else
				$count++;
		}
	}
	
	$t2 = microtime(true);
	$diff = ($t2 - $t1) . "s";

	if($result)
		echo "Insert $count records ($diff)<br>";
	else
		echo "Failed addAll";
		
	return $result;
}

function test_updateUser($db,$userid)
{
	//Get all events for 1 user, in a single batch
	$query = array(FIELD_USERID => $userid);
	$events = $db->get(COLLECTION,$query,EVENTS_PER_USER,0);

	$result = true;
	$count = 0;
	//Modify the subject of each event
	foreach($events as $event) {
		$updated = array();
		$updated['subject'] = $event['subject'] . " MODIFIED";
		
		if($db->update(COLLECTION, $event[FIELD_ID], $updated))
			$count++;
		else
			$result = false;
	}
	
	if($result)
		echo "Updated $count records<br>";
	else
		echo "Failed updateUser";
	
	return $result;
}

function test_deleteUser($db,$userid)
{
	//Get all events for 1 user, in a single batch
	$query = array(FIELD_USERID => $userid);
	$events = $db->get(COLLECTION,$query,EVENTS_PER_USER,0);

	$result = true;
	$count = 0;

	foreach($events as $event) {
		if($db->delete(COLLECTION, $event[FIELD_ID]))
			$count++;
		else
			$result = false;
	}
	
	if($result)
		echo "Deleted $count records<br>";
	else
		echo "Failed deleteUser";
	
	return $result;
}

function test_deleteAll($db)
{
	$result = true;
	$count = 0;
	$t1 = microtime(true);
	
	while(true)
	{
		//Get event in default batch size and delete all
		$events = $db->get(COLLECTION,null);
		if(sizeof($events) == 0)
			break;
			
		foreach($events as $event) {
			$id = $event[FIELD_ID];
			if($db->delete(COLLECTION,$id) == false)
				$result = false;
			else
				$count++;
		}
	}
	
	$t2 = microtime(true);
	$diff = ($t2 - $t1) . "s";
	
	if($result)
		echo "Deleted $count records ($diff)<br>";
	else
		echo "Failed deleteAll";
	
	return $result;
}

?>