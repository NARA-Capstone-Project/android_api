<?php
	require_once 'include/cict_db_connect.php';
	$response = array();
		$db = new cict_db_connect();
		$con = $db->connect();
	
	if(isset($_POST['serial'])){
	
		$serial = $_POST['serial'];
		$stmt = $con->prepare("INSERT INTO temporary VALUES(?);");
		$stmt->bind_param("s", $serial);
		if($stmt->execute()){
			$response['error'] = false;
			$response['message'] = "Insert in db";
		}else{
			$response['error'] = true;
			$response['message'] = "Can't insert";
		}
			
	}else{
			$response['error'] = true;
			$response['message'] = "not Post missing text";
			
	}
	
	echo json_encode($response);


?>