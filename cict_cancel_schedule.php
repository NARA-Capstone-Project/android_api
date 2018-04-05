<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

if (isset($_POST['id']) and
	isset($_POST['req_type'])) {

	$response = array();

	$type = $_POST['req_type'];
	
	if($type == 'inventory'){
		$query = "UPDATE request_inventory SET req_status ='Cancel' WHERE req_id = ?";
	}elseif ($type == 'repair') {
		$query = "UPDATE request_repair SET req_status ='Cancel' WHERE req_id = ?";
	}elseif ($type == 'schedule') {
		$query = "DELETE FROM task_schedule WHERE sched_id = ?";
	}

	$stmt = $con->prepare($query);
	$stmt->bind_param("i", $_POST['id']);

	if($stmt->execute()){
		$response['error'] = false;
	}else{
		$response['error'] =true;
	}

	echo json_encode($response);
}
