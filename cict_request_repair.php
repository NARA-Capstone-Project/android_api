<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();
require_once 'cict_send_sms.php';
$db_sms = new cict_send_sms();
require_once 'include/cict_db_users_functions.php';
$db_users = new cict_db_users_functions();
require_once 'include/cict_db_room_functions.php';
$db_room = new cict_db_room_functions();
require_once 'include/cict_db_comp_functions.php';
$db_comp = new cict_db_comp_functions();

if (isset($_POST['comp_id']) and
    isset($_POST['msg']) and
    isset($_POST['cust_id']) and
    isset($_POST['tech_id']) and
    isset($_POST['date']) and
    isset($_POST['time']) and
    isset($_POST['image']) and
    isset($_POST['rep_details'])) {

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $response = array();

    $image = $_POST['image'];

    $image_dir = 'images/repair_photos';
    if (!file_exists($image_dir)) {
        mkdir($image_dir, 0777, true);
    }

    $image_path = $image_dir . "/" . rand() . "_" . time() . ".jpeg";

    $query = "INSERT INTO request_repair VALUES(null, ?,null,?,?,?,?,?,'$image_path','$date', '$time','Pending',?)";
//check if may image
    if (strlen($image) == 0) {
        $query = "INSERT INTO request_repair VALUES(null, ?,null,?,?,?,?,?,null,'$date', '$time','Pending',?)";
    }

//SMS
    $comp_details = $db_comp->getComputersWithId($_POST['comp_id']);
    $room_details = $db_room->getRoom($comp_details['room_id']);
    $cust_room    = $room_details['room_name'];
    $comp_name    = $comp_details['pc_no'];

    $tech_details = $db_users->getUser($_POST['tech_id']);
    $cust_details = $db_users->getUser($_POST['cust_id']);

    $recipient_phone = $tech_details['phone'];
    $recipient_name  = $tech_details['name'];
    $requestor       = $cust_details['name'];

    $msg_body = "Good Day! Sir/Ma'am $requestor of " . $cust_room . " room requests for repair of PC $comp_name on the following date and time: \n\nDate: " . $_POST['date'] . " \nTime: " . $_POST['time'];

    if (strlen($_POST['msg']) > 0) {
        $msg_body .= " \n\nMessage from the Custodian:\n" . $_POST['msg'];
    }

    //phone

    if ($recipient_phone[0] == '0') {
        $phone = preg_replace('/^0/', '63', $recipient_phone);
    } else {
        $phone = $recipient_phone;
    }

    $stmt = $con->prepare($query);
    $stmt->bind_param("issssss", $_POST['comp_id'], $_POST['msg'], $_POST['cust_id'], $_POST['tech_id'], $_POST['date'], $_POST['time'], $_POST['rep_details']);

    if ($stmt->execute()) {
        require_once 'include/cict_db_image.php';
        $db_image = new cict_db_image();

        $response['error'] = false;

        if ($db_image->uploadImage($image, $image_path)) {
            $response['image'] = "Upload Successful";
        } else {
            $response['image'] = "Upload not Successful";
        }
        if ($db_sms->send_sms($phone, $msg_body)) {
            $response['body']    = $msg_body;
            $response['message'] = "SMS Message sent";
        } else {
            $response['message'] = "SMS Message not sent";
        }
    } else {
        $response['error'] = true;
    }
} else {
    $response['error'] = true;
    $response['msg']   = 'missing requirements';
}

echo json_encode($response);
