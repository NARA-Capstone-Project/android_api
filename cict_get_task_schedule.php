<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

$response = array();

if (isset($_POST['tech_id'])) {

    $id = $_POST['tech_id'];

    $stmt = $con->prepare("SELECT * FROM task_schedule WHERE tech_id = ? ORDER BY date DESC, time DESC");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($sched_id, $category, $desc, $room_pc_id, $tech_id, $date, $time);

    while ($stmt->fetch()) {
        $temp = array();

        $temp['sched_id'] = $sched_id;
        $temp['category'] = $category;
        $temp['desc']     = $desc;
        $temp['id']       = $room_pc_id;
        $temp['date']     = $date;
        $temp['time']     = $time;

        array_push($response, $temp);
    }

    echo json_encode($response);
}
