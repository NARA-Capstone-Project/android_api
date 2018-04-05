<?php
require_once 'include/cict_db_connect.php';
$db = new cict_db_connect();
$con = $db->connect();

$response = array();

if (isset($_POST['query']) and
    isset($_POST['rep_id'])) {

    $rep_id = $_POST['rep_id'];

	$stmt = $con->prepare($_POST['query']);
	$stmt->bind_param("i", $rep_id);

	if($stmt->execute()){
		$response['error'] = false;
	}else{
		$response['error'] = true;
	}
	echo json_encode($response);
}
