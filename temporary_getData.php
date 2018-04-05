<?php

    require_once 'include/cict_db_connect.php';
	$db = new cict_db_connect();
    $con = $db->connect();

    $response = array();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['serial'])){
            $stmt = $con->prepare("SELECT * FROM temporary WHERE pc_serial = ?");
            $stmt->bind_param("s", $_POST['serial']);
            $stmt->bind_result($pc_serial, $mb_serial, $mon_serial, $processor, $ram, $hdd, $kboard, $mouse);
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows() > 0){
                    $response['error'] = false;
                    $response['id_serial'] = $pc_serial;
                    $response['mb'] = $mb_serial;
                    $response['mon'] = $mon_serial;
                    $response['pr'] = $processor;
                    $response['ram'] = $ram;
                    $response['hdd'] = $hdd;
                    $response['kb'] = $kboard;
                    $response['mouse'] = $mouse;
                }else{
                    $response['error'] = true;
                    echo 'no data';
                }
            }else{
                $response['error'] = true;
                echo 'error executing query';
            }

        }else{
            $response['error'] = true;
            echo 'invalid request method';
        }

    }else{
        $response['error'] = true;
        echo 'invalid request';
    }

    echo json_encode($response);
?>