<?php
	class cict_db_room_functions{
		private $con;
	
		//constructor
		function __construct(){
			require_once 'include/cict_db_connect.php';
			$db = new cict_db_connect();
			$this->con = $db->connect();
		}
		
		//RETURN ROOM that belong to CUSTODIAN
		public function getCustodianRoom($user_id){
			require_once 'include/cict_db_users_functions.php';
			$db_users = new cict_db_users_functions();
			require_once 'include/cict_db_comp_functions.php';
			$db_comp = new cict_db_comp_functions();
			
			
			$stmt = $this->con->prepare("SELECT * FROM room WHERE room_custodian_id = ?");
			//room_id, dept_id, building, room_custodian_id, room_technician_id, room_name,floor, room_image
			$stmt->bind_param("s", $user_id);
			$stmt->bind_result($room_id, $dept_id, $room_custodian_id, $room_technician_id, $room_name,$building,$floor, $room_image);
			$stmt->execute();
			$response = array();
			
			while($stmt->fetch()){
				$temp= array();
				$custodian_name = $db_users->getUser($room_custodian_id);
				$technician_name = $db_users->getUser($room_technician_id);
				$pc_count = $db_comp->pc_count($room_id);
				$pc_working = $db_comp->pc_working_count($room_id);
				
				$temp['room_id'] = $room_id;
				$temp['room_name'] = $room_name;
				$temp['dept_name'] = $dept_id;
				$temp['building'] = $building;
				$temp['floor'] = $floor;
				$temp['room_custodian'] = $custodian_name['name'];
				$temp['room_technician'] = $technician_name['name'];
				$temp['room_image'] = $room_image;
				$temp['pc_count'] = $pc_count;
				$temp['pc_working'] = $pc_working;
				
				array_push($response, $temp);
			}
			$stmt->close();
			for($i = 0; $i < count($response); $i++){
				$id= $response[$i]{"dept_name"};
				$dept_name = $this->getDeptName($id);
				$response[$i]{"dept_name"} = $dept_name;
			}
			
			
			return $response;
		}
		
		public function getDeptName($dept_id){
			$stmt = $this->con->prepare("SELECT * FROM department WHERE dept_id = ?");
			$stmt->bind_param("i", $dept_id);
			
			$stmt->execute();
			$result =  $stmt->get_result()->fetch_assoc();
			$dept_name = $result['dept_name'];
			$stmt->close();
			return $dept_name;
		}
		
		public function getRoom($room_id){
			
			require_once 'include/cict_db_comp_functions.php';
			$db_comp = new cict_db_comp_functions();
			
			require_once 'include/cict_db_users_functions.php';
			$db_users = new cict_db_users_functions();
			
			$response = array();
		
			//room_id, dept_id, building, room_custodian_id, room_technician_id, room_name,floor, room_image
			$stmt = $this->con->prepare("SELECT * FROM room where room_id = ?");
			$stmt->bind_param("i", $room_id);
			$stmt->execute();
			$data = $stmt->get_result()->fetch_assoc();
			
			$room_no = $data['room_name'];
			$room_custodian_id = $data['room_custodian_id'];
			$room_technician_id = $data['room_technician_id'];
			
			$custodian_name = $db_users->getUser($room_custodian_id);
			$technician_name = $db_users->getUser($room_technician_id);
			$pc_count = $db_comp->pc_count($room_id);
			$pc_working = $db_comp->pc_working_count($room_id);
			
			$response['building'] = $data['building'];
			$response['floor'] = $data['floor'];
			$response['custodian_id'] = $data['room_custodian_id'];
			$response['room_custodian'] = $custodian_name['name'];
			$response['technician_id'] = $data['room_technician_id'];
			$response['room_technician'] = $technician_name['name'];
			$response['pc_count'] = $pc_count;
			$response['pc_working'] = $pc_working;
			
			
			$stmt->close();
			
			$dept_name = $this->getDeptName($data['dept_id']);
			$response['room_name'] = $dept_name . " " . $room_no;
			
			return $response;

		}
		
		public function getRoomSched($room_id, $day){
			require_once 'include/cict_db_users_functions.php';
			$db_users = new cict_db_users_functions();
			$response = array();
			
			$stmt= $this->con->prepare("SELECT * FROM room_schedule WHERE room_id = ? and day = ?");
			$stmt->bind_param("is", $room_id, $day);
			$stmt->bind_result($room_id, $room_user_id, $day, $from, $to);
			$stmt->execute();
			
			//room_id	room_user	day	from_time	to_time
			while($stmt->fetch()){
				$temp = array();
				$user_name = $db_users->getUser($room_user_id);
				
				$temp['day'] = $day;
				$temp['room_user'] = $user_name['name'];
				$temp['from'] = $from;
				$temp['to'] = $to;
				
				array_push($response, $temp);
			}
			
			return $response;
		}
		
		public function checkRoomSched($room_id, $day){
			$stmt= $this->con->prepare("SELECT * FROM room_schedule WHERE room_id = ? and day = ?");
			$stmt->bind_param("is", $room_id, $day);
			$stmt->execute();
			$stmt->store_result();
			
			return $stmt->num_rows() > 0;
		}
	}

?>