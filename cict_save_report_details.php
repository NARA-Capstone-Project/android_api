<?php
require_once 'include/cict_db_report_functions.php';
$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'include/cict_db_connect.php';
    $db_con = new cict_db_connect();
    $db     = new cict_db_report_functions();

    $con = $db_con->connect();

    $details = json_decode(file_get_contents("php://input"), true);

    foreach ((array) $details as $key) {
        $rep_id      = $key['rep_id'];
        $comp_id     = $key['comp_id'];
        $pc_no       = $key['pc_no'];
        $model       = $key['model'];
        $pr          = $key['pr'];
        $mb          = $key['mb'];
        $mb_serial   = $key['mb_serial'];
        $monitor     = $key['monitor'];
        $mon_serial  = $key['mon_serial'];
        $ram         = $key['ram'];
        $kboard      = $key['kb'];
        $mouse       = $key['mouse'];
        $vga         = $key['vga'];
        $hdd         = $key['hdd'];
        $comp_status = $key['comp_status'];

        $result = $db->saveTechReportDetails($rep_id, $comp_id, $pc_no, $model, $pr, $mb, $mb_serial, $monitor, $mon_serial, $ram, $kboard, $mouse, $vga, $hdd, $comp_status);

        if ($result) {
            $detailsSaved = $db->updateComputers($comp_status, $comp_id, $kboard, $mouse, $vga);
            if ($detailsSaved) {
                $response[0]['error']   = false;
                $response[0]['message'] = "Success!";
            } else {
                $response[0]['error']   = true;
                $response[0]['message'] = "Cant update computers";
            }

        } else {
            $response[0]['error']   = true;
            $response[0]['message'] = "Not saved";
        }
    }
} else {
    $response[0]['error']   = true;
    $response[0]['message'] = "Invalid Request";
}

echo json_encode($response);
