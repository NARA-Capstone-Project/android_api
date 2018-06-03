<?php
require_once 'include/cict_db_connect.php';
$db = new cict_db_connect();

$con = $db->connect();

$response = array();

if(isset($_POST['comp_id'])){
$comp_id               = $_POST['comp_id'];
    $checkRepairRequest = $con->prepare("SELECT * FROM request_repair WHERE comp_id = ? AND (req_status = 'Pending' or req_status = 'Accepted') ORDER BY date DESC, time DESC LIMIT 1");
    $checkRepairRequest->bind_param("i", $comp_id);
    // $checkInventoryRequest->execute();
    // $checkInventoryRequest->store_result();

    if ($checkRepairRequest->execute()) {
        $result = $checkRepairRequest->get_result()->fetch_assoc();

        if (count($result) > 0) {
        	$response['error'] = false;
            $response['pending']    = true;
            $response['req_id']     = $result['req_id'];
            $response['rep_id']     = $result['rep_id'];
            $response['comp_id']    = $result['comp_id'];
            $response['custodian']  = $result['custodian'];
            $response['technician'] = $result['technician'];
            $response['date']       = $result['date'];
            $response['time']       = $result['time'];
            $response['msg']        = $result['message'];
            $response['req_status'] = $result['req_status'];
            $response['date_requested'] = $result['date_requested'];
            $response['time_requested'] = $result['time_requested'];
            $response['image'] = $result['images'];
            $response['req_details'] = $result['report_details'];
        }else{
        	$response['pending'] = false;
        	$response['error'] = false;
            $response['technician'] = $result['technician'];
        }
    } else {
        $response['error'] = true;
        $response['message'] = "An error occured";
    }
}

echo json_encode($response);
?>