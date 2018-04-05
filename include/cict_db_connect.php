<?php

class cict_db_connect{
	private $conn;
	
	
	function __construct(){}
	
	
	//conecting to database
	public function connect(){
		require_once 'include/cict_db_config.php';
		
		//connecting to mysql database
	
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		if(mysqli_connect_errno()){
			echo "Failed to connect with database: " .mysqli_connect_err();
		}
		return $this->conn;
	}
}

?>