<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();
if (isset($_POST['id'])) {

    $stmt = $con->prepare("SELECT * FROM request_inventory WHERE technician = ? or custodian = ? ORDER BY date_requested DESC, time_requested DESC");
    $stmt->bind_param("ss", $_POST['id'],$_POST['id']);
    $stmt->execute();
    $stmt->bind_result($req_id, $rep_id, $room_id, $custodian, $technician, $date, $time, $msg, $date_req, $time_req, $status);

    while ($stmt->fetch()) {
        $temp = array();

        $temp['req_id']         = $req_id;
        $temp['rep_id']         = $rep_id;
        $temp['room_id']        = $room_id;
        $temp['custodian']      = $custodian;
        $temp['technician']     = $technician;
        $temp['date']           = $date;
        $temp['time']           = $time;
        $temp['msg']            = $msg;
        $temp['req_status']     = $status;
        $temp['date_requested'] = $date_req;
        $temp['time_requested'] = $time_req;

        array_push($response, $temp);
    }
}
echo json_encode($response);
