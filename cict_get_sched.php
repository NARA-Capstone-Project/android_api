<?php
	require_once 'include/cict_db_connect.php';
	require_once 'include/cict_db_users_functions.php';
	$db_users = new cict_db_users_functions();
	$db = new cict_db_connect();
	$con = $db->connect();
	$response = array();

	$stmt = $con->prepare("SELECT * FROM room_schedule");
	$stmt->bind_result($room_id, $room_user, $day,$from, $to);
	$stmt->execute();

	while($stmt->fetch()){
		$temp = array();
		$user_name = $db_users->getUser($room_user);

		$temp['room_id'] = $room_id;
		$temp['day'] = $day;
		$temp['room_user'] = $user_name['name'];
		$temp['from'] = $from;
		$temp['to'] = $to;
				
		array_push($response, $temp);
	}

	echo json_encode($response);
?>
