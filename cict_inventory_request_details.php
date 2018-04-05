<?php
require_once 'include/cict_db_connect.php';
$db = new cict_db_connect();

$con = $db->connect();

$response = array();

if (isset($_POST['room_id'])) {

    $room_id               = $_POST['room_id'];
    $checkInventoryRequest = $con->prepare("SELECT * FROM request_inventory WHERE room_id = ? AND req_status = 'Pending' ORDER BY date DESC, time DESC LIMIT 1");
    $checkInventoryRequest->bind_param("i", $room_id);
    // $checkInventoryRequest->execute();
    // $checkInventoryRequest->store_result();

    if ($checkInventoryRequest->execute()) {
        $result = $checkInventoryRequest->get_result()->fetch_assoc();

        if (count($result) > 0) {
        	$response['error'] = false;
            $response['pending']    = true;
            $response['req_id']     = $result['req_id'];
            $response['rep_id']     = $result['rep_id'];
            $response['room_id']    = $result['room_id'];
            $response['custodian']  = $result['custodian'];
            $response['technician'] = $result['technician'];
            $response['date']       = $result['date'];
            $response['time']       = $result['time'];
            $response['msg']        = $result['message'];
            $response['req_status'] = $result['req_status'];
            $response['date_requested'] = $result['date_requested'];
            $response['time_requested'] = $result['time_requested'];
        }else{
        	$response['pending'] = false;
        	$response['error'] = false;
        }
    } else {
        $response['error'] = true;
        $response['message'] = "An error occured";
    }

}

echo json_encode($response);
?>
