
<?php
require_once 'include/cict_db_report_functions.php';
$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['user_id'])) {

        $user_id = $_POST['user_id'];
        require_once 'include/cict_db_users_functions.php';
        $users        = new cict_db_users_functions();
        $user_details = $users->getUser($user_id);
        $user_role    = $user_details['role'];

        $db = new cict_db_report_functions();

        if ($user_role == 'Admin' or $user_role == 'Main Technician') {
            $response = $db->getInventoryReportsForHighUps();
        } else {
            $response = $db->getInventoryReports($_POST['user_id']);

        }

    }}

echo json_encode($response);
?>