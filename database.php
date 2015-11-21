<?php

define("DATABASE_NAME", "cloud");
define("FIELD_ID", "_id");
define("FIELD_USERID", "user_id");
define("FIELD_CREATED", "created");
define("FIELD_UPDATED", "updated");

class Database
{
	private $db;

	public function __construct() {
		$mc = new MongoClient();
		$this->db = $mc->selectDB(DATABASE_NAME);
	}


	public function verifyCollection($name) {
		$collections = $this->db->getCollectionNames();
		if(in_array($name,$collections))
			return true;
	
		return false;
	}
	
	
	public function get($collectionName,$query,$limit = 100,$offset = 0) {
	
		if(!$this->verifyCollection($collectionName))
			return false;
	
		//Set to specified collection
		$collection = $this->db->selectCollection($collectionName);
		if($query == null)
			$cursor = $collection->find();
		else
			$cursor = $collection->find($query);
			
		$cursor->limit($limit);
		$cursor->skip($offset);
		$results = array();
		
		foreach ($cursor as $id=>$value)
		{
			foreach($value as $key=>$val)
			{
				$type = gettype($val);
				if($type == "object")
					$type = get_class($val);
				
				//Convert mongo types to native php types
				if($type == "MongoId")
					$val = (string)$val;
				else if($type == "MongoTimestamp" || $type == "MongoDate") {
					$val = (new DateTime())->setTimestamp($val->sec);
				}
				
				$value[$key] = $val;
			}
			
			$results[] = $value;
		}


		return $results;
	}
	
	public function insert($collectionName, $userid, $record)
	{
		if(!$this->verifyCollection($collectionName))
			return false;
		$collection = $this->db->selectCollection($collectionName);
			
		//Set core fields
		$newRec = array();
		$newRec[FIELD_USERID] = $userid;
		$newRec[FIELD_CREATED] = new MongoTimestamp();
		$newRec[FIELD_UPDATED] = new MongoTimestamp();
		
		//Check type of fields to convert to mongo types
		foreach($record as $key=>$value) {
		
			$type = gettype($value);
			if($type == "object")
				$type = get_class($value);
				
			if($type == "DateTime") {
				$value = new MongoDate($value->getTimestamp());
			}
			
			$newRec[$key] = $value;
		}
		
		//Insert to dataase
		$result = $collection->insert($newRec);
		if($result['ok'] == 1)
			return (string)$newRec['_id'];
		
		return false;
	}
	
	public function update($collectionName, $id, $record)
	{
		if(!$this->verifyCollection($collectionName))
			return false;
		$collection = $this->db->selectCollection($collectionName);
		
		//Unable to reset values to these fields
		unset($record[FIELD_ID]);
		unset($record[FIELD_CREATED]);
		$record[FIELD_UPDATED] = new MongoTimestamp();
				
		//Criteria
		$criteria = array();
		$criteria[FIELD_ID] = new MongoId($id);

		$result = $collection->update($criteria, array('$set' => $record));
		return ($result['ok'] == 1 ? true : false);
	}
		
		
	public function delete($collectionName, $id) {
	
		if(!$this->verifyCollection($collectionName))
			return false;
		$collection = $this->db->selectCollection($collectionName);
		
		$result = $collection->remove(array(FIELD_ID => new MongoId($id)), array("justOne" => true) );
		return ($result['ok'] == 1 ? true : false);
	}
	
	




}




?>