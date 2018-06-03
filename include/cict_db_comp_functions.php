<?php

class cict_db_comp_functions
{
    private $con;

    //constructor
    public function __construct()
    {
        require_once 'include/cict_db_connect.php';
        $db        = new cict_db_connect();
        $this->con = $db->connect();
    }

    //FOR RETRIEVING COMPUTERS BASE ON THE ROOM ID -> used kapag titingnan ung mga pc pag may sinelect na room
    public function getPcByRoomId($room_id)
    {
        $stmt = $this->con->prepare("SELECT comp_id, pc_no, comp_status FROM comp_details WHERE room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        $stmt->bind_result($comp_id, $pc_no, $status);

        $computers = array();

        while ($stmt->fetch()) {

            $temp            = array();
            $temp['comp_id'] = $comp_id;
            $temp['pc_no']   = $pc_no;
            $temp['status']  = $status;

            array_push($computers, $temp);
        }
        $stmt->close();
        for ($i = 0; $i < count($computers); $i++) {
            $id                     = $computers[$i]{"comp_id"};
            $details                = $this->getPcDetails($id);
            $computers[$i]{"model"} = $details['model'];
        }
        return $computers;
    }

    //for syncing
    public function getAllPc($room_id)
    {

        $response = array();
        $response = $this->getPcByRoomId($room_id);
        for ($i = 0; $i < count($response); $i++) {
            $id   = $response[$i]{"comp_id"};
            $stmt = $this->con->prepare("SELECT * FROM computers where comp_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $details = $stmt->get_result()->fetch_assoc();

            $response[$i]{'processor'} = $details['processor'];
            $response[$i]{'mb'}        = $details['motherboard'];
            $response[$i]{'monitor'}   = $details['monitor'];
            $response[$i]{'kboard'}    = $details['kboard'];
            $response[$i]{'mouse'}     = $details['mouse'];
            $response[$i]{'ram'}       = $details['ram'];
            $response[$i]{'vga'}       = $details['vga'];
            $response[$i]{'hdd'}       = $details['hdd'];
        }
        return $response;
    }

    public function getPcDetails($comp_id)
    {
        $stmt = $this->con->prepare("SELECT * from computers where comp_id = ?");
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function pc_count($room_id)
    {
        $stmt = $this->con->prepare("SELECT * from comp_details where room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        $stmt->store_result();
        return $stmt->num_rows();
    }

    public function pc_working_count($room_id)
    {
        $stat = "Working";
        $stmt = $this->con->prepare("SELECT * from comp_details where room_id = ? and comp_status = ?");
        $stmt->bind_param("is", $room_id, $stat);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows();
    }

    public function getComputers()
    {
        $computers = array();
        $stmt      = $this->con->prepare("SELECT * FROM computers");
        $stmt->execute();
        $stmt->bind_result($comp_id, $os, $model, $pr, $mb, $monitor, $ram, $kboard, $mouse, $vga, $hdd);

        while ($stmt->fetch()) {
            $temp            = array();
            $temp['comp_id'] = $comp_id;
            $temp['os']      = $os;
            $temp['model']   = $model;
            $temp['mb']      = $mb;
            $temp['pr']      = $pr;
            $temp['monitor'] = $monitor;
            $temp['ram']     = $ram;
            $temp['kboard']  = $kboard;
            $temp['mouse']   = $mouse;
            $temp['vga']     = $vga;
            $temp['hdd']     = $hdd;

            array_push($computers, $temp);
        }
        $stmt->close();
        for ($i = 0; $i < count($computers); $i++) {
            $id   = $computers[$i]{'comp_id'};
            $stmt = $this->con->prepare("SELECT * from comp_details where comp_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $details                      = $stmt->get_result()->fetch_assoc();
            $computers[$i]{'pc_no'}       = $details['pc_no'];
            $computers[$i]{'room_id'}     = $details['room_id'];
            $computers[$i]{'comp_status'} = $details['comp_status'];

        }

        return $computers;

    }
    public function getComputersWithId($comp_id)
    {
        $stmt      = $this->con->prepare("SELECT * FROM comp_details WHERE comp_id = ?");
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
