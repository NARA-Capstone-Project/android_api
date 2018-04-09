<?php
	require_once 'include/cict_db_users_functions.php';
	$response = array();
	

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if(isset($_POST['username']) and
			isset($_POST['password'])){
				
			$db = new cict_db_users_functions();
			
			//INITIALIZE
			$username = $_POST['username'];
			$password = $_POST['password'];
		
			//check kung existing
			if($db->userLogin($username, $password)){
				
				//check kung active pa
				if($db->checkAccStatus($username)){
					//CHECK EXPIRATION
					if(!$db->checkExpiration($username)){ //expired
						$user_acc_data = $db->getAccount($username);
						$id = $user_acc_data['user_id'];

						$response['error'] = true;
						$response['user_id'] = $id;
						$response['message'] = "Account is deactivated";
					}else{
						$user_acc_data = $db->getAccount($username);
						//account = user_id, username, password, date_created, date_expire, acc_status 
						$user_data = $db->getUser($user_acc_data['user_id']);
						//users = user_id,email, name, phone, role
						//sesave sa sqlite
						$response['error'] = false;
						$response['user_id'] = $user_acc_data['user_id'];
						$response['email'] = $user_data['email'];
						$response['username'] = $user_acc_data['username'];
						$response['name'] = $user_data['name'];
						$response['phone'] = $user_data['phone'];
						$response['role'] = $user_data['role'];
						$response['date_expire'] = $user_acc_data['date_expire'];
						$response['acc_status'] = $user_acc_data['acc_status'];
					}
				}else{
						$user_acc_data = $db->getAccount($username);
						$id = $user_acc_data['user_id'];

						$response['error'] = true;
						$response['user_id'] = $id;
						$response['message'] = "Account is deactivated";	
				}
			}else{
				$response['error'] = true;
				$response['message'] = "Invalid username or password";
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