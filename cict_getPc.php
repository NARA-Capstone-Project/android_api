<?php

	require_once 'include/cict_db_comp_functions.php';
	$db = new cict_db_comp_functions();
	
	
	require_once 'include/cict_db_connect.php';
	$db_connect = new cict_db_connect();
	$con = $db_connect->connect();
	
	$response= array();
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
	
		if(isset($_POST['room_id'])){
			$room_id = $_POST['room_id'];
			$response = $db->getPcByRoomId($room_id);
		}else{
				$response['error'] = true;
				$response['message'] = "Missing required details";
					
		}
		
	}else{
		$response['error'] = true;
		$response['message'] = "Invalid Request";
		}
	
echo json_encode($response);
?>