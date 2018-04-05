<?php

require_once 'include/cict_db_comp_functions.php';
$db = new cict_db_comp_functions();
$response = array();
$response = $db->getComputers();
echo json_encode($response);
?>