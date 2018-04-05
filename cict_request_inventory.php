<?php

require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();

if (isset($_POST['room_id']) and
    isset($_POST['room_name']) and
    isset($_POST['cust_id']) and
    isset($_POST['tech_id']) and
    isset($_POST['date']) and
    isset($_POST['time']) and
    isset($_POST['message'])) {

    require_once 'cict_send_sms.php';
    $db_sms = new cict_send_sms();
    require_once 'include/cict_db_users_functions.php';
    $db_users = new cict_db_users_functions();

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $tech_details = $db_users->getUser($_POST['tech_id']);
    $cust_details = $db_users->getUser($_POST['cust_id']);

    $recipient_phone = $tech_details['phone'];
    $recipient_name  = $tech_details['name'];
    $custodian       = $cust_details['name'];

    $msg_body = "Good Day! Sir/Ma'am $custodian of " .$_POST['room_name']. " room requests for a room inventory on the following date and time: \n\nDate: " . $_POST['date'] . " \nTime: " . $_POST['time'];

    $stmt = $con->prepare("INSERT into request_inventory values(null, null, ?,?,?,?,?,?,?,?,'Pending');");
    $stmt->bind_param("ssssssss", $_POST['room_id'], $_POST['cust_id'], $_POST['tech_id'], $_POST['date'], $_POST['time'], $_POST['message'], $date, $time);

    $message = $_POST['message'];
    if (strlen($message) > 0) {
        $msg_body .= " \n\nAdditional Message from Custodian:\n$message";
    }

    if ($recipient_phone[0] == '0') {
        $phone = preg_replace('/^0/', '63', $recipient_phone);
    } else {
        $phone = $recipient_phone;
    }

    if ($stmt->execute()) {
        $response['error'] = false;
        if ($db_sms->send_sms($phone, $msg_body)) {
            $response['body'] = $msg_body;
            $response['message'] = "SMS Message sent";
        } else {
            $response['message'] = "SMS Message not sent";
        }
    } else {
        $response['error']   = true;
        $response['message'] = "Error";
        echo $stmt->error;
    }

    echo json_encode($response) . "\n";

} else {
    echo 'Missing requirements';
}
