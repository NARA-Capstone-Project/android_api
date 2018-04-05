<?php
	
	class cict_db_users_functions{
		private $con;
		
		//constructor
		function __construct(){
			require_once 'include/cict_db_connect.php';
			$db = new cict_db_connect();
			$this->con = $db->connect();
		}
		
		//DEACTIVATE USER
		public function deactivateUser($user_id){
			$stmt = $this->con->prepare("UPDATE accounts SET acc_status = 'DEACTIVATED' WHERE user_id = ?");
			$stmt->bind_param("s", $user_id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}
		//CHECK KUNG ACTIVE PA YUNG USER - MAGRERETURN NG TRUE KUNG ACTIVE PA
		public function checkAccStatus($username){
			$stmt = $this->con->prepare("SELECT acc_status FROM accounts WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc();
			$acc_status = $result['acc_status'];
			if($acc_status == "Active"){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}

		//CHECK EXPIRATION
		public function checkExpiration($username){
			$stmt = $this->con->prepare("SELECT date_expire FROM accounts where username = ?");
			$stmt->bind_param("s", $username);
			if($stmt->execute()){
				$date_expire = $stmt->get_result()->fetch_assoc()['date_expire'];
				date_default_timezone_set('Asia/Manila');
				$expire = date_create($date_expire);
				$today  = date_create(date('Y-m-d')); // get todays date
				$diff = date_diff($today, $expire); //positive kung hindi pa expired
				
				$result = (int)($diff->format("%R%a"));
			}
			return $result > 0; //true kapag hindi pa expired
		}
		
		//CREATE USER
		public function createUser($id,$username, $password, $date_created, $date_expire){
			$stmt = $this->con->prepare("INSERT INTO accounts VALUES (?,?,aes_encrypt(?, 'cictpassword'),NULL,?,?, 'Active');");
			$stmt->bind_param("sssss", $id,$username, $password, $date_created, $date_expire);
			
			if($stmt->execute()){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}
		
		//IF A USER REQUEST AN ACCOUNT
		public function request_account($user_id, $username, $password, $date_request, $request){
			
			$stmt = $this->con->prepare("INSERT INTO request_account VALUES (?,?,aes_encrypt(?,'cictpassword'),?, ?)");
			$stmt->bind_param("sssss",$user_id, $username, $password, $date_request, $request);
			
			if($stmt->execute()){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}
		
		//CHECK USERNAME ALREADY EXISTS IN REQUEST TABLE
		public function isUsernameAlreadyExists($username){
			$stmt = $this->con->prepare("SELECT * FROM request_account WHERE username = ?;");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows() > 0){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}
		//CHECK USER ALREADY SENT A REQUEST
		public function isUserSentRequest($id, $request){
			$stmt = $this->con->prepare("SELECT * FROM request_account WHERE id = ? and request = ?;");
			$stmt->bind_param("ss", $id, $request);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows() > 0){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}
		
		//CHECK USER IF EXISTS by id and username
		public function isExisted($id, $username){
			$stmt = $this->con->prepare("SELECT * FROM accounts WHERE username = ? OR user_id= ?;");
			$stmt->bind_param("ss", $username, $id);
			
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows() > 0){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		}
		
		//when log in, validate if user exists
		public function userLogin($username, $password){
			$stmt = $this->con->prepare("SELECT * FROM accounts WHERE username = ? and password = aes_encrypt(?, 'cictpassword')");

			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$stmt->store_result();
			
			return $stmt->num_rows > 0;
		}
		
		public function getUser($id){
			$stmt = $this->con->prepare("SELECT * FROM users where user_id = ?");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}
		
		public function getAccount($username){
			$stmt = $this->con->prepare("SELECT * FROM accounts WHERE username = ?;");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}
	}//class
?>