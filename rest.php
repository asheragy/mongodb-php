<?php
require_once('database.php');

//Get parameters
$method = $_SERVER['REQUEST_METHOD'];
$collection = str_replace('/', '', $_SERVER['PATH_INFO']);
$data = json_decode(file_get_contents("php://input"),true);

//Result
$code = 200;
$response = array();

if($collection == "") 
{
	$code = 400; //bad request
	$response['error'] = "invalid path";
	
} 
else 
{
	$db = new Database();

	if(!verifyCollection($collection))
	{
		$code = 400; //bad request
		$response['error'] = "collection '$collection' does not exist";
	}
	else
	{
		if($method == "GET") 
		{
			$results = $db->get($collection,null);
			$response[$collection] = $results;
		} 
		else if($method == "POST") //Insert
		{
			$records = $data[$collection];
			$result = array();
		
			foreach($records as $record) {
				$userid = $record[FIELD_USERID];
				$id = $db->insert($collection,$userid,$record);
				$result[] = $id;
			}
			
		} 
		else if($method == "PUT") //Update
		{
			$records = $data[$collection];
			$result = array();
		
			foreach($records as $record) {
				$id = $record[FIELD_ID];
				$result[FIELD_ID] = $db->update($collection,$id,$record);
			}	
		} 
		else if($method == "DELETE")
		{
			$records = $data[$collection];
			$result = array();
		
			foreach($records as $record) {
				$id = $record[FIELD_ID];
				$result[FIELD_ID] = $db->delete($collection,$id);
			}	
			
		} 
		else 
		{
			$code = 405; //Method Not Allowed
			$response['error'] = "method not allowed";
		}
	}

	
}

http_response_code($code);
echo json_encode($response);

?>