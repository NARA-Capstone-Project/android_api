<?php

	require_once 'include/cict_db_comp_functions.php';
	$db = new cict_db_comp_functions();
	
	$response= array();
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
	
		if(isset($_POST['comp_id'])){
			$result = $db-> getPcDetails($_POST['comp_id']);
			
			//model,processor,motherboard,monitor ram kboard mouse vga hdd
			$response['comp_id'] = $_POST['comp_id'];
			$response['model'] = $result['model'];
			$response['processor'] = $result['processor'];
			$response['motherboard'] = $result['motherboard'];
			$response['monitor'] = $result['monitor'];
			$response['kboard'] = $result['kboard'];
			$response['mouse'] = $result['mouse'];
			$response['ram'] = $result['ram'];
			$response['vga'] = $result['vga'];
			$response['hdd'] = $result['hdd'];
			
		}else{
				$response['error'] = true;
				$response['message'] = "Missing required details";
					
		}
		
	}else{
		$response['error'] = true;
		$response['message'] = "Invalid Request";
		}
	
echo json_encode($response);
?>