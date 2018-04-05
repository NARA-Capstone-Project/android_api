<?php

    require_once 'include/cict_db_users_functions.php';
    $db = new cict_db_users_functions();

    $response = array();

    if(isset($_POST['user_id'])){
        if($db->deactivateUser($_POST['user_id'])){
            $response['error'] = false;
            $response['message'] = "Redirecting to login";
        }else{
            $response['error'] = true;;
            $response['message'] = "Redirecting to login";
        }
    }

    echo json_encode($response);
?>