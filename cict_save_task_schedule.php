
<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();

//send din ng sms

if (isset($_POST['tech_id']) and
    isset($_POST['category']) and
    isset($_POST['description']) and
    isset($_POST['room_comp_id']) and
    isset($_POST['tech_name'])and
    isset($_POST['date'])and
    isset($_POST['time'])) {

    $tech_id      = $_POST['tech_id'];
    $category     = $_POST['category'];
    $description  = $_POST['description'];
    $room_comp_id = $_POST['room_comp_id'];
    $tech_name    = $_POST['tech_name'];
    $sched_date = $_POST['date'];
    $sched_time = $_POST['time'];


    require_once 'include/cict_db_room_functions.php';
    $db_rooms = new cict_db_room_functions();

    if ($category == 'Schedule Inventory') {
        $room_details = $db_rooms->getRoom($room_comp_id);
        $room_name = $room_details['room_name'];

    } else {
        //search ung comp details
        require_once 'include/cict_db_comp_functions.php';
        $db_comps = new cict_db_comp_functions();
        //comp
        $comp_details = $db_comps->getComputersWithId($room_comp_id);
        $pc_no        = $comp_details['pc_no'];
        $pc_room_id   = $comp_details['room_id'];
        //rooms
        $room_details = $db_rooms->getRoom($pc_room_id);
        $room_name = $room_details['room_name'];

    }

    //save to mysql
    $stmt = $con->prepare("INSERT INTO task_schedule VALUES(null,?,?,?,?,?,?)");
    $stmt->bind_param("ssisss", $category, $description, $room_comp_id, $tech_id, $sched_date, $sched_time);
    if($stmt->execute()){
    	$response['error'] = false;
    }else{
    	$response['error'] = true;
    }

    //send sms

    echo json_encode($response);
}
?>