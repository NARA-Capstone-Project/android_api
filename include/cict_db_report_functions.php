<?php

class cict_db_report_functions
{
    private $con;

    //constructor
    public function __construct()
    {
        require_once 'include/cict_db_connect.php';
        $db        = new cict_db_connect();
        $this->con = $db->connect();
    }

    //get report count

    public function getReportCount($user_id)
    {
        $stmt = $this->con->prepare("SELECT * from assessment_reports where custodian_id = ? OR technician_id = ? ");
        $stmt->bind_param("ss", $user_id, $user_id);
        $stmt->execute();

        $stmt->store_result();
        return $stmt->num_rows() > 0;
    }

    public function getLastReport($user_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM assessment_reports WHERE custodian_id = ? OR technician_id = ? ORDER BY rep_id DESC LIMIT 1");
        $stmt->bind_param("ss", $user_id, $user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getLastAssessmentDate($room_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM assessment_reports WHERE room_id = ? ORDER BY date DESC LIMIT 1");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();

    }

    public function updateComputers($comp_status, $comp_id, $kboard, $mouse, $vga)
    {
        $stmt = $this->con->prepare("update comp_details set comp_status = ? where comp_id = ?");
        $stmt->bind_param("si", $comp_status, $comp_id);
        if ($stmt->execute()) {
            $stmt->close();
            $stmt = $this->con->prepare("update computers set kboard = ?, mouse = ?, vga = ? where comp_id = ?");
            $stmt->bind_param("sssi", $kboard, $mouse, $vga, $comp_id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //technician

    //get inventory report
    public function getInventoryReports($user_id)
    {

        require_once 'include/cict_db_users_functions.php';
        $db_users = new cict_db_users_functions();

        $response = array();
        $stmt     = $this->con->prepare("SELECT * from assessment_reports where (not exists (select null from request_repair where request_repair.rep_id = assessment_reports.rep_id) and not exists (select null from request_inventory where request_inventory.rep_id = assessment_reports.rep_id)) and (technician_id = ? or custodian_id = ?)ORDER BY date desc, time desc");
        $stmt->bind_param("sss", $user_id, $user_id);
        $stmt->bind_result($rep_id, $room_id, $cust_id, $tech_id, $category, $date, $time, $cust_signed, $htech_signed, $admin_signed, $remarks);
        $stmt->execute();

        //get report
        while ($stmt->fetch()) {
            $temp      = array();
            $cust_name = $db_users->getUser($cust_id);
            $tech_name = $db_users->getUser($tech_id);

            $temp['category']     = $category;
            $temp['rep_id']       = $rep_id;
            $temp['room_id']      = $room_id;
            $temp['custodian']    = $cust_name['name'];
            $temp['technician']   = $tech_name['name'];
            $temp['cust_signed']  = $cust_signed;
            $temp['htech_signed'] = $htech_signed;
            $temp['admin_signed'] = $admin_signed;
            $temp['cust_id']      = $cust_id;
            $temp['tech_id']      = $tech_id;
            $temp['date']         = $date;
            $temp['time']         = $time;
            $temp['remarks']      = $remarks;

            array_push($response, $temp);
        }
        $stmt->close();

        require_once 'include/cict_db_room_functions.php';
        $db_room = new cict_db_room_functions();

        //get Room name
        for ($x = 0; $x < count($response); $x++) {
            $r_id                      = $response[$x]{'room_id'};
            $room                      = $db_room->getRoom($r_id);
            $response[$x]{'room_name'} = $room['room_name'];
        }

        return $response;
    }

    public function getRequestReports($req_type, $query)
    {
        require_once 'include/cict_db_users_functions.php';
        $db_users = new cict_db_users_functions();

        $response = array();
        $stmt     = $this->con->prepare($query);
        $stmt->bind_result($rep_id, $room_id, $cust_id, $tech_id, $date, $time, $cust_signed, $htech_signed, $admin_signed, $remarks);
        $stmt->execute();

        //get report
        while ($stmt->fetch()) {
            $temp      = array();
            $cust_name = $db_users->getUser($cust_id);
            $tech_name = $db_users->getUser($tech_id);

            if ($req_type == 'Inventory') {
                $temp['category'] = "Request Inventory Report";
            } else {
                $temp['category'] = "Request Repair Report";
            }
            $temp['rep_id']  = $rep_id;
            $temp['room_id'] = $room_id;
            $temp['date']    = $date;
            $temp['time']    = $time;
            $temp['cust_signed']  = $cust_signed;
            $temp['htech_signed'] = $htech_signed;
            $temp['admin_signed'] = $admin_signed;
            $temp['custodian']    = $cust_name['name'];
            $temp['technician']   = $tech_name['name'];
            $temp['remarks']      = $remarks;

            array_push($response, $temp);
        }
        $stmt->close();

        require_once 'include/cict_db_room_functions.php';
        $db_room = new cict_db_room_functions();

        //get Room name
        for ($x = 0; $x < count($response); $x++) {
            $r_id                 = $response[$x]{'room_id'};
            $room                 = $db_room->getRoom($r_id);
            $response[$x]{'name'} = $room['room_name'];
        }
        if ($req_type == 'Repair') {
            for ($i = 0; $i < count($response); $i++) {
                $report_id = $response[$i]{'rep_id'};
                $get_pc_details = $this->getRequestRepairReportDetails($report_id);
                $pc_no = $get_pc_details['pc_no'];
                $name = 'PC ' .$pc_no. ' of room ' .$response[$i]{'name'};
                $response[$i]{'name'} = $name;
            }
        }

        return $response;
    }

    public function getRequestRepairReportDetails($rep_id)
    {
        $response = array();
        $stmt     = $this->con->prepare("SELECT * FROM assessment_details WHERE rep_id = ?");
        $stmt->bind_param("i", $rep_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    //rep_id    comp_id    model    processor    motherboard    monitor    ram    kboard    mouse    vga    hdd    status

    public function getInventoryReportsForHighUps()
    {

        require_once 'include/cict_db_users_functions.php';
        $db_users = new cict_db_users_functions();

        $response = array();
        $stmt     = $this->con->prepare("SELECT * from assessment_reports where (not exists (select null from request_repair where request_repair.rep_id = assessment_reports.rep_id) and not exists (select null from request_inventory where request_inventory.rep_id = assessment_reports.rep_id)) order by date desc, time desc;");
        $stmt->bind_result($rep_id, $room_id, $cust_id, $tech_id,$category, $date, $time, $cust_signed, $htech_signed, $admin_signed, $remarks);
        $stmt->execute();

        //get report
        while ($stmt->fetch()) {
            $temp      = array();
            $cust_name = $db_users->getUser($cust_id);
            $tech_name = $db_users->getUser($tech_id);

            $temp['category']     = "Inventory Report";
            $temp['rep_id']       = $rep_id;
            $temp['room_id']      = $room_id;
            $temp['custodian']    = $cust_name['name'];
            $temp['technician']   = $tech_name['name'];
            $temp['cust_signed']  = $cust_signed;
            $temp['htech_signed'] = $htech_signed;
            $temp['admin_signed'] = $admin_signed;
            $temp['cust_id']      = $cust_id;
            $temp['tech_id']      = $tech_id;
            $temp['date']         = $date;
            $temp['time']         = $time;
            $temp['remarks']      = $remarks;

            array_push($response, $temp);
        }
        $stmt->close();

        require_once 'include/cict_db_room_functions.php';
        $db_room = new cict_db_room_functions();

        //get Room name
        for ($x = 0; $x < count($response); $x++) {
            $r_id                      = $response[$x]{'room_id'};
            $room                      = $db_room->getRoom($r_id);
            $response[$x]{'room_name'} = $room['room_name'];
        }

        return $response;
    }

    public function getInventoryReportDetails($rep_id)
    {
        $response = array();
        $stmt     = $this->con->prepare("SELECT * FROM assessment_details WHERE rep_id = ?");
        $stmt->bind_param("i", $rep_id);
        $stmt->bind_result($rep_id, $comp_id, $pc_no, $model, $pr, $mb, $mb_serial,
            $mon, $mon_serial, $ram, $kb, $mouse, $vga, $hdd, $comp_status);
        $stmt->execute();

        while ($stmt->fetch()) {
            $temp = array();

            $temp['rep_id']      = $rep_id;
            $temp['comp_id']     = $comp_id;
            $temp['pc_no']       = $pc_no;
            $temp['model']       = $model;
            $temp['pr']          = $pr;
            $temp['mb']          = $mb;
            $temp['mb_serial']   = $mb_serial;
            $temp['mon']         = $mon;
            $temp['mon_serial']  = $mon_serial;
            $temp['ram']         = $ram;
            $temp['kb']          = $kb;
            $temp['mouse']       = $mouse;
            $temp['vga']         = $vga;
            $temp['hdd']         = $hdd;
            $temp['comp_status'] = $comp_status;

            array_push($response, $temp);
        }

        return $response;
    }

    public function userSendReport($user_id, $comp_id)
    {
        //tables user_report , cust_report, tech_report
        //bago ireport check muna kung nareport na -> pag nareport na disabled ung button
        //from custodian to tech, pag inupdate na ung status by tech clicking done sa report
        //ska plang mageenable ung report button para sa user
        //user_report = rep_id, comp_id, room_id, user_id(nagsend), cust_id(send to, depende sa room id), subject, problem, date, status(pending, done)
        //check user role

    }

    public function genRepId()
    {
        $rep_id = rand(1000, 99999);
        return $rep_id;
    }

    public function checkRepId($rep_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM assessment_reports WHERE rep_id = ?");
        $stmt->bind_param("i", $rep_id);
        $stmt->store_result();
        if ($stmt->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function saveTechReport($cust_id, $tech_id, $room_id, $date, $time, $remarks, $category)
    {
        //rep_id    room_id    custodian_id    technician_id    date    time    cust_signed    remarks
        //generate random number for rep id then check if meron na pag wala save
        while (true) {
            $rep_id = $this->genRepId();
            if ($this->checkRepId($rep_id)) {
                break;
            }
        }

        $stmt = $this->con->prepare("insert into assessment_reports values(?,?,?,?,?,?,?,0,0,0,?)");
        $stmt->bind_param("iissssss", $rep_id, $room_id, $cust_id, $tech_id,$category, $date, $time, $remarks);

        if ($stmt->execute()) {
            return $rep_id;
        } else {
            return 0;
        }

    }
    //rep_id    comp_id    model    processor    motherboard    monitor    ram    kboard    mouse    vga    hdd    status
    public function saveTechReportDetails($rep_id, $comp_id, $pc_no, $model, $processor, $mb, $mb_serial, $monitor, $mon_serial, $ram, $kboard, $mouse, $vga, $hdd, $status)
    {
        $stmt = $this->con->prepare("insert into assessment_details values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iiissssssssssss", $rep_id, $comp_id, $pc_no, $model, $processor, $mb, $mb_serial, $monitor, $mon_serial, $ram, $kboard, $mouse, $vga, $hdd, $status);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

    }
}
