<?php
	require_once 'include/cict_db_report_functions.php';
	$response = array();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if(isset($_POST['user_id'])){
			
			$user_id = $_POST['user_id'];
				$db = new cict_db_report_functions();
				$rows = $db->getReportCount($user_id);
				
				
				require_once 'include/cict_db_users_functions.php';
				$db_users = new cict_db_users_functions();
				
				
				if($rows){
					$result = $db->getLastReport($user_id);
					$cust_name = $db_users->getUser($result['custodian_id']);
					$tech_name = $db_users->getUser($result['technician_id']);
					
					$response['error'] = false;
					$response['rep_id'] = $result['rep_id'];
					$response['room_id'] = $result['room_id'];
					$response['custodian'] = $cust_name['name'];
					$response['technician'] = $tech_name['name'];
					$response['date'] = $result['date'];
					$response['time'] = $result['time'];
					$response['remarks'] = $result['remarks'];
				}else{
					$response['error'] = true;
					$response['message'] = "No Record";
				}
			}
		else{
			$response['error'] = true;
			$response['message'] = "Required field missing";
				
		}
	
	}else{
			$response['error'] = true;
					$response['message'] = "Invalid Request";
				
	}
	
	echo json_encode($response);
?>