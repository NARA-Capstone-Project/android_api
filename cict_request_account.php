<?php
	require_once 'include/cict_db_users_functions.php';
	$response = array();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if(isset($_POST['id']) and
			isset($_POST['username']) and
			isset($_POST['password'])){
				
			$db = new cict_db_users_functions();
			
			date_default_timezone_set('Asia/Manila');
			$date  = date('Y-m-d');
			
			//INITIALIZE
			$id = $_POST['id'];
			$username = $_POST['username'];
			$password = $_POST['password'];
			$date_request = $date;
		
			//check kung may account na sa accounts
			$isExist = $db->isExisted($id, $username);
			if($isExist){
				//check kung active pa
				if($db->checkAccStatus){ // active pa
					$response['error'] = true;
					$response['message'] = "User already exist";
				}else{
					$response['error'] = false; //kung deactivated/expired na
					$response['message'] = "expired";
				}
			}else{
				//check kung nakapagrequest na
				$alreadyReq = $db->isUserSentRequest($id, 'account');
				if($alreadyReq){
					$response['error'] = true;
					$response['message'] = "User already sent a request";
				}else{
					//check naman kung may existing na ung username
					$isUsernameExist = $db->isUsernameAlreadyExists($username);
					if($isUsernameExist){
						$response['error'] = true;
						$response['message'] = "Username already exist";
					}else{
						//insert to request_account
						$result = $db->request_account($id, $username, $password, $date_request, 'account');

						if($result){
							$response['error'] = false;
							$response['message'] = "Request has been submitted! Please wait for the approval via SMS before logging in";
						}else{						
							$response['error'] = true;
							$response['message'] = "An error occured while processing your request, please try again";
						}
					}
				}
			}
		}else{
			$response['error'] = true;
			$response['message'] = "Required Fields are missing";
		}
		
	}else{
			$response['error'] = true;
			$response['message'] = "Invalid Request";
			
	}
	
	echo json_encode($response);
	
?>