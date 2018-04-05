<?php

	require_once 'include/cict_db_room_functions.php';
	$db_room = new cict_db_room_functions();
	require_once 'include/cict_db_users_functions.php';
	$db_user = new cict_db_users_functions();
	
	$response = array();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['room_id']) and 
		isset($_POST['day'])){
			$room_id = $_POST['room_id'];
			$day = $_POST['day'];
			
			if($db_room->checkRoomSched($room_id, $day)){
				$response = $db_room->getRoomSched($room_id, $day);
			}
			
		}else{
			$response['error']= true;
			$response['message'] = "missing required input";
		}
	}else{
		$response['error']= true;
		$response['message'] = "Invalid Request";
	}

	echo json_encode($response);
?>