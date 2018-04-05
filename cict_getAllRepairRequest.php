<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

require_once 'include/cict_db_users_functions.php';
$db_users = new cict_db_users_functions();

$response = array();
if (isset($_POST['id'])) {

    $id = $_POST['id'];

    $stmt = $con->prepare("SELECT * FROM request_repair WHERE technician = ? or custodian = ? ORDER BY date_requested DESC, time_requested DESC");
    $stmt->bind_param("ss", $id, $id);
    $stmt->execute();
    $stmt->bind_result($req_id, $comp_id, $rep_id, $msg, $custodian, $technician, $date, $time, $image, $date_req, $time_req, $status, $req_details);

    while ($stmt->fetch()) {
        $temp = array();

        $cust_name              = $db_users->getUser($custodian);
        $tech_name              = $db_users->getUser($technician);
        $temp['req_id']         = $req_id;
        $temp['rep_id']         = $rep_id;
        $temp['comp_id']        = $comp_id;
        $temp['custodian']      = $custodian;
        $temp['technician']     = $technician;
        $temp['cust_name']      = $cust_name['name'];
        $temp['tech_name']      = $tech_name['name'];
        $temp['date']           = $date;
        $temp['time']           = $time;
        $temp['msg']            = $msg;
        $temp['req_status']     = $status;
        $temp['date_requested'] = $date_req;
        $temp['time_requested'] = $time_req;
        $temp['req_details']    = $req_details;
        $temp['image']          = $image;

        array_push($response, $temp);
    }
}
echo json_encode($response);
