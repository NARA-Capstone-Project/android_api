<?php

require_once 'include/cict_db_users_functions.php';
$db_user = new cict_db_users_functions();
$response = array();

if(isset($_POST['username'])){
	$username = $_POST['username'];
	$result = $db_user->getAccount($username);

	if(count($result) > 0 ){
		$response['error'] = false;
		$response['signature'] = $result['signature'];
	}else{
		$response['error'] = true;
		$response['msg'] = "Error";
	}
}
echo json_encode($response);

?>