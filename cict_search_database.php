<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();

if (isset($_POST['query'])) {

    $query      = $_POST['query'];

    $stmt = $con->prepare($query);
    $stmt->execute();
    $stmt->bind_result($id, $dept_name, $room_name);

    while ($stmt->fetch()) {
    	$temp = array();

    	$temp['id'] = $id;
    	$temp['dept_name'] = $dept_name;
    	$temp['room_name'] = $room_name;


    	array_push($response, $temp);
    }
}

echo json_encode($response);
