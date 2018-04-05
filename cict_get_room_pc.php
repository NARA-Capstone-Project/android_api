<?php

	require_once 'include/cict_db_comp_functions.php';
	$db = new cict_db_comp_functions();
	
	$response = array();
	
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
	
		if(isset($_POST['room_id'])){
			$room_id = (int)$_POST['room_id'];
			$response = $db->getAllPc($room_id);
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