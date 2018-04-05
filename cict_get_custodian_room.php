<?php
	require_once 'include/cict_db_room_functions.php';
	$response = array();
	
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if(isset($_POST['user_id'])){
			$db = new cict_db_room_functions();
			$user_id = $_POST['user_id'];
			
			$response = $db->getCustodianRoom($user_id);
		}else{
			$response['error'] = true;
			$response['message'] = "Missing requirements";
		}
	}else{
		$response['error'] = true;
		$response['message'] = "Invalid request";
	}
	
			echo json_encode($response);
?>