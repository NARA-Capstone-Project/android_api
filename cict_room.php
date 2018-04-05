<?php
	require_once 'include/cict_db_room_functions.php';
	$response = array();
	
	$db = new cict_db_room_functions();
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(isset($_POST['room_id'])){
			$response = $db->getRoom($_POST['room_id']);
			$response['error'] = false;
		}else{
			$response['error'] = true;
			$response['message'] = "Missing input requirements";			
		}
	}else{
		$response['error'] = true;
		$response['message'] = "Invalid Request";
	}
	echo json_encode($response);
?>