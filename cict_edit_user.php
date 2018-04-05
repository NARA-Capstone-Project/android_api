<?php

    require_once 'include/cict_db_connect.php';
    $db = new cict_db_connect();
    $con = $db->connect();

    require_once 'include/cict_db_users_functions.php';
    $user_db = new cict_db_users_functions();

    $response = array();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['user_id']) and
            isset($_POST['column']) and
            isset($_POST['new_data']) and
            isset($_POST['table']) and 
            isset($_POST['password']) and 
            isset($_POST['username'])){

                $table = $_POST['table'];
                $id = $_POST['user_id'];
                $column = $_POST['column'];
                $new_data = $_POST['new_data'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $user = "user_id";

                $sql1 = "UPDATE $table SET $column = ? WHERE $user = ?";
                $sql2 = "UPDATE $table SET $column = aes_encrypt(?, 'cictpassword') WHERE $user = ?";



                $checkPassword = $user_db->userLogin($username, $password);

                if($checkPassword){ //check kung tama ung password
                    if($column == 'password'){
                            $stmt = $con->prepare($sql2);
                        }
                    else
                        {$stmt = $con->prepare($sql1);}
                

                    $stmt->bind_param("ss", $new_data, $id);

                    if($stmt->execute()){
                        $response['error'] = false;
                        $response['message'] = 'Successfully Updated';
                    }else{
                        $response['error'] = true;
                        $response['message'] = 'Something went wrong, try again later';
                    }
                }else{
                    $response['error'] = true;
                    $response['message'] = 'Incorrect Password';
                }
            }else{
                  $response['error'] = true;
                    $response['message'] = 'Missing requirements';
            }
    }else{
        $response['error'] = true;
        $response['message'] = 'Invalid method';
    }

    echo json_encode($response);
?>