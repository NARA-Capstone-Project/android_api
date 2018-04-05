<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

if (isset($_POST['query']) and 
	isset($_POST['id'])) {

	$response = array();

	$query = $_POST['query'];

	$id = $_POST['id'];

	$stmt = $con->prepare($query);
	$stmt->bind_param("i", $id);

	if($stmt->execute()){
		$response['error'] = false;
	}else{
		$response['error'] =true;
	}

	echo json_encode($response);
}
?>