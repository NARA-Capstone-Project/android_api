<?php
			require_once 'include/cict_db_connect.php';
			$db = new cict_db_connect();
			$con = $db->connect();
			
			
			require_once 'include/cict_db_comp_functions.php';
			$db_comp = new cict_db_comp_functions();
			
			require_once 'include/cict_db_users_functions.php';
			$db_users = new cict_db_users_functions();
			
			require_once 'include/cict_db_room_functions.php';
			$db_rooms = new cict_db_room_functions();

			require_once 'include/cict_db_report_functions.php';
			$db_report = new cict_db_report_functions();
			
			$stmt = $con->prepare("SELECT * FROM room");
			//room_id, dept_id, building, room_custodian_id, room_technician_id, room_name,floor, room_image
			$stmt->bind_result($room_id, $dept_id, $room_custodian_id, $room_technician_id, $room_name,$building,$floor, $room_image);
			
			$stmt->execute();
			$response = array();
			
			while($stmt->fetch()){
				$temp= array();
				$custodian_name = $db_users->getUser($room_custodian_id);
				$technician_name = $db_users->getUser($room_technician_id);
				$pc_count = $db_comp->pc_count($room_id);
				$pc_working = $db_comp->pc_working_count($room_id);
				$lastAssess = $db_report->getLastAssessmentDate($room_id);
				
				$temp['room_id'] = $room_id;
				$temp['room_name'] = $room_name;
				$temp['dept_name'] = $dept_id;
				$temp['building'] = $building;
				$temp['floor'] = $floor;
				$temp['cust_id'] = $room_custodian_id;
				$temp['tech_id'] = $room_technician_id;
				$temp['room_custodian'] = $custodian_name['name'];
				$temp['room_technician'] = $technician_name['name'];
				$temp['room_image'] = $room_image;
				$temp['lastAssess'] = $lastAssess['date'];
				
				$temp['pc_count'] = $pc_count;
				$temp['pc_working'] = $pc_working;
				

				array_push($response, $temp);
			}
			$stmt->close();
			for($i = 0; $i < count($response); $i++){
				$id= $response[$i]{"dept_name"};
				$dept_name = $db_rooms->getDeptName($id);
				$response[$i]{"dept_name"} = $dept_name;
			}

			echo json_encode($response);
?>