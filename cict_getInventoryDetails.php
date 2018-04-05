
<?php
	require_once 'include/cict_db_report_functions.php';
	$response = array();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if(isset($_POST['rep_id'])){
		
			$rep_id = $_POST['rep_id'];
				$db = new cict_db_report_functions();
			    $response = $db->getInventoryReportDetails($rep_id);
                   
	}}
	
	echo json_encode($response);
?>