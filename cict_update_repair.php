<?php
require_once 'include/cict_db_connect.php';
$db  = new cict_db_connect();
$con = $db->connect();

if (isset($_POST['req_id']) and
    isset($_POST['msg']) and
    isset($_POST['date']) and
    isset($_POST['time']) and
    isset($_POST['image']) and
    isset($_POST['old_path']) and
    isset($_POST['rep_details'])) {

    $response = array();

    $image = $_POST['image'];

    $image_dir = 'images/repair_photos';
    if (!file_exists($image_dir)) {
        mkdir($image_dir, 0777, true);
    }

    $image_path = $image_dir . "/" . rand() . "_" . time() . ".jpeg";

    $query = "UPDATE request_repair SET date = ?, time = ?, images = '$image_path', report_details = ?, message = ? WHERE req_id = ?";
//check if may image
    if (strlen($image) == 0) {
        $query = "UPDATE request_repair SET date = ?, time = ?, report_details = ?, message = ?  WHERE req_id = ?";
    } else {
        if (file_exists($_POST['old_path'])) {
            unlink($_POST['old_path']);
        }
    }

    $stmt = $con->prepare($query);
    $stmt->bind_param("ssssi", $_POST['date'], $_POST['time'], $_POST['rep_details'], $_POST['msg'], $_POST['req_id']);

    if ($stmt->execute()) {
        require_once 'include/cict_db_image.php';
        $db_image = new cict_db_image();

        $response['error'] = false;

        if ($db_image->uploadImage($image, $image_path)) {
            $response['image'] = "Upload Successful";
        } else {
            $response['image'] = "Upload not Successful";
        }
    } else {
        $response['error'] = true;
    }
} else {
    $response['error'] = true;
    $response['msg']   = 'missing requirements';
}

echo json_encode($response);
