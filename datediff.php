<?php

    require_once 'cict_send_sms.php';
    $db = new cict_send_sms();


    $phone = $_POST['phone'];
    $message = "Send";

    $result = $db->send_sms($phone, $message);
    $status = $result->status;
    echo $status;
?>