<?php
require_once 'inlude/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();

if (isset($_POST['sched_id'])
    isset($_POST['category']) and
    isset($_POST['description']) and
    isset($_POST['room_pc_id']) and
    isset($_POST['date']) and
    isset($_POST['time'])) {

    $sched_id = $_POST['sched_id'];
    $category     = $_POST['category'];
    $description  = $_POST['description'];
    $room_pc_id = $_POST['room_pc_id'];
    $sched_date = $_POST['date'];
    $sched_time = $_POST['time'];

	$stmt = $con->prepare("UPDATE task_schedule SET category =?, description = ? , room_pc_id = ? , date = ? , time = ? WHERE sched_id = ?");

	$stmt->bind_param("ssiss", $category, $description, $room_pc_id, $sched_date, $sched_time);

	if($stmt->execute()){
		$response['error'] = false;
	}else{
		$response['error'] = true;
	}

}
echo json_encode($response);