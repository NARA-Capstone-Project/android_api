<?php
	require_once 'include/cict_db_connect.php';
	$db = new cict_db_connect();
	$con = $db->connect();


	$response = array();

	if(isset($_POST['serial'])){

		$stmt = $con->prepare("SELECT * FROM temporary WHERE pc_serial = ?");
		$stmt->bind_param("s", $_POST['serial']));

		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();

		if(counct($result) > 0){
			$response['mb_serial'] = $result['mb_serial'];
			$response['mon_serial'] = $result['mon_serial'];
			$response['processor'] = $result['processor'];
			$response['ram'] = $result['ram'];
			$response['hdd'] = $result['hdd'];
			$response['keyboard'] = $result['keyboard'];
			$response['mouse'] = $result['mouse'];

		}else{
			$response["error"] = false;
			$response["count"] = 0; 
		}
	}else {
        $response['error'] = true;
        $response['message'] = "An error occured";
    }

    echo json_encode($response);


?>