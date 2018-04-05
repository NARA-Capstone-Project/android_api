<?php

require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();

if (isset($_POST['query']) and
       isset($_POST['req_type'])) {

	$req = $_POST['req_type'];
	$query = $_POST['query'];

	require_once 'include/cict_db_report_functions.php';
	$report = new cict_db_report_functions();

	$response = $report->getRequestReports($req, $query);

}
echo json_encode($response);
