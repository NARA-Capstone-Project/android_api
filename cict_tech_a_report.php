
<?php
require_once 'include/cict_db_report_functions.php';
$response = array();
require_once 'cict_send_sms.php';
$db_sms = new cict_send_sms();
require_once 'include/cict_db_users_functions.php';
$db_users = new cict_db_users_functions();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['tech_id']) and
        isset($_POST['room_id']) and
        isset($_POST['remarks']) and
        isset($_POST['category'])) {

        $db = new cict_db_report_functions();
        date_default_timezone_set('Asia/Manila');
        $date     = date('Y-m-d');
        $time     = date('H:i:s');
        $tech_id  = $_POST['tech_id'];
        $room_id  = $_POST['room_id'];
        $remarks  = $_POST['remarks'];
        $category = $_POST['category'];

        require_once 'include/cict_db_room_functions.php';
        $db_rooms = new cict_db_room_functions();

        $getCust   = $db_rooms->getRoom($room_id);
        $cust_id   = $getCust['custodian_id'];
        $room_name = $getCust['room_name'];

        $result             = $db->saveTechReport($cust_id, $tech_id, $room_id, $date, $time, $remarks, $category);
        $response['rep_id'] = $result;

        //SMS
        $tech_details = $db_users->getUser($tech_id);
        $cust_details = $db_users->getUser($cust_id);

        $recipient_phone = $cust_details['phone'];
        $recipient_name  = $cust_details['name'];
        $technician      = $tech_details['name'];

        if ($category == 'Inventory Report') {
            $msg_body = "Good Day! Technician $technician conducted an Inventory Report on $room_name room today.";
        }else{
            $msg_body = "Good Day! Technician $technician conducted a Repair Report on $room_name room today.";
        }

        if (strlen($remarks) > 0) {
            $msg_body .= " \n\nRemarks:\n$remarks";
        }

        //phone

        if ($recipient_phone[0] == '0') {
            $phone = preg_replace('/^0/', '63', $recipient_phone);
        } else {
            $phone = $recipient_phone;
        }

        if ($result > 0) {
            //if success irereturn ung rep id
            $response['error']   = false;
            $response['message'] = "Report saved successfully";

            //sms
            if ($db_sms->send_sms($phone, $msg_body)) {
                $response['body']    = $msg_body;
                $response['message'] = "SMS Message sent";
            } else {
                $response['message'] = "SMS Message not sent";
            }

        } else {
            $response['error']   = true;
            $response['message'] = "Unsuccessful";

        }
    } else {
        $response['error']   = true;
        $response['message'] = "Required field missing";

    }

} else {
    $response['error']   = false;
    $response['message'] = "Invalid Request";

}

echo json_encode($response);
?>