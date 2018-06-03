<?php
    require_once 'include/cict_db_connect.php';
    $db = new cict_db_connect();
    $con = $db->connect();

    $response = array();

    if(isset($_POST['user_id']) and
    isset($_POST['username']) and
    isset($_POST['password'])){

        date_default_timezone_set('Asia/Manila');
        $date_request = date('Y-m-d');
        
		$id = $_POST['user_id'];
		$username = $_POST['username'];
	    $password = $_POST['password'];
        
        require_once 'include/cict_db_users_functions.php';
        $db_user = new cict_db_users_functions();

        //check muna kung nakapagrequest na ng reactivation
        $requested = $db_user->isUserSentRequest($id,'reactivate');

        if($requested){
            $response['error'] = true;
            $response['message'] = "You already sent a request for reactivation.";
        }else{
            $result = $db_user->request_account($id, $username, $password, $date_request, 'reactivate');
            if($result){
                $response['error'] = false;
                $response['message'] = "Request has been submitted! Please wait for the approval before logging in";
            }else{						
                $response['error'] = true;
                $response['message'] = "An error occured while processing your request, please try again later";
            }
        }  
    }

    echo json_encode($response);
?>