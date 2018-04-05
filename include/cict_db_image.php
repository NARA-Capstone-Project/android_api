<?php

/**
 *
 */
class cict_db_image
{
    private $con;

    public function __construct()
    {
        require_once 'include/cict_db_connect.php';
        $db        = new cict_db_connect();
        $this->con = $db->connect();

    }

    public function uploadImage($image, $image_path)
    {
        if(file_put_contents($image_path, base64_decode($image))){
        	return true;
        }else{
        	return false;
        }
    }
}
