<?php


	require_once 'include/cict_db_connect.php';
	$db = new cict_db_connect();
	$con = $db->connect();
	
	$response = array();
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		
		if(isset($_POST['comp_id']) and
		isset($_POST['status']) and
		isset($_POST['kboard']) and
		isset($_POST['vga']) and
		isset($_POST['mouse'])){
			
			$stmt = $con->prepare("update comp_details set comp_status = ? where comp_id = ?");
			$stmt->bind_param("si", $_POST['status'],$_POST['comp_id']);
			if($stmt->execute()){
				$stmt->close();
				$stmt = $con->prepare("update computers set kboard = ?, mouse = ?, vga = ? where comp_id = ?");
				$stmt->bind_param("sssi", $_POST['kboard'],$_POST['mouse'],$_POST['vga'],$_POST['comp_id']);
				if($stmt->execute()){
				
					$response['error'] = true;
					$response['message'] = "Updated Successfully!";
							
				}else{
				
					$response['error'] = true;
					$response['message'] = "Unsuccessful";
						
				}
			}else{
				
			}
		}else{
			$response['error'] = true;
			$response['message'] = "Missing fields";
				
		}
	}else{
		$response['error'] = true;
		$response['message'] = "Invalid Request";
	}

	echo json_encode($response);
?>