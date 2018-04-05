<?php
	require_once 'include/cict_db_connect.php';
	$db = new cict_db_connect();
	$con = $db->connect();


	if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$id = $_GET['id'];
			
			$stmt = $con->prepare("SELECT room_image FROM room WHERE room_id = ?");
			$stmt->bind_param("i", $id);
			$stmt->execute();

			$result = $stmt->get_result()->fetch_assoc();

			header('content-type: image/jpeg');

			echo base64_decode($result['room_image']);
	}
	
?>