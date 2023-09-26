<?php

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL); 

if ($action == 'get') {
    $filter = 'where 1=1';
    $filterVar = $_POST['filter'];

    $condition = ' where 1=1 ';
    if (isset($filterVar) && !empty($filterVar)) {

        $array = json_decode($filterVar, true);
        if (!empty($array['trainer'])) {
            $trainer = $array['trainer'];
            $condition .= " AND user_id='$trainer' ";
        }
        if (!empty($array['manager'])) {
            $manager = $array['manager'];
            $condition .= " AND marketing_id='$manager' ";
        }
        if (!empty($array['source'])) {
            $source = $array['source'];
            $condition .= " AND source='$source' ";
        }
        if (!empty($array['status'])) {
            $status = $array['status'];
            $condition .= " AND followup_status = '$status' ";
        }
        if (!empty($array['subStatus'])) {
            $subStatus = $array['subStatus'];
            $condition .= " AND followup_substatus = '$subStatus' ";
        }
        if (!empty($array['search'])) {
            $searchTerm = str_replace("'", "\\'", $array['search']);
            $condition .= " AND organization LIKE '%$searchTerm%' ";
        }
        if (!empty($array['dateRange']) && $array['dateRange'] !== "") {
            $dateRange = $array['dateRange'];
            $separatedDateRange = explode('-', $dateRange);

            // First date format
            $firstDate = trim($separatedDateRange[0]); // Trim in case there are spaces
            $dateObject = DateTime::createFromFormat('d/m/Y', $firstDate);
            if ($dateObject instanceof DateTime) {
                $firstDateFormat = $dateObject->format('Y/m/d');
            }
            // else {
            //     $firstDateFormat = date('Y-m-d');
            // }

            // Last date format
            $lastDate = trim($separatedDateRange[1]); // Trim in case there are spaces
            $dateObject = DateTime::createFromFormat('d/m/Y', $lastDate);
            if ($dateObject instanceof DateTime) {
                $lastDateFormat = $dateObject->format('Y/m/d');
            }
            // else {
            //     $lastDateFormat = date('Y-m-d');
            // }
            if(!empty($firstDateFormat)){
            $condition .= " AND created BETWEEN '$firstDateFormat' AND '$lastDateFormat'";    
            }
            
        }
    }

    $pagination = !empty($_POST['pagination']) ? $_POST['pagination'] : 1;

    $param = array(
        "sort" => "order by id desc",
        "pagination" => $pagination,
        "col" => "*",
        "tb" => "leads",
        "where" => $condition,
        "limit" => (($pagination - 1) * $_POST['limit']) . "," . $_POST['limit'],
        // 'debug' => 1,
    );

    $res = qselect($param);
    echo json_encode($res);
}

if ($action == "get_managers") {
    include('../_con.php');
    $stmt = $con->query('SELECT fname,lname,id FROM cp_users WHERE role=14');
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($res);
}
if ($action == "get_trainers") {
    include('../_con.php');
    $stmt = $con->query('SELECT fname,lname,id FROM cp_users WHERE role=13');
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($res);
}

if ($action == 'get_users') {
    $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "users", "where" => "", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
}
/*
Call
*/
if ($action == 'call') {
    $param = array("pagination" => "", "col" => "phone", "tb" => "leads", "where" => " where id=" . $_POST['id'] . " ", "limit" => "1");
    $res = qselect($param);
    $phone = $res['data']['phone'];
    if ($phone == null || empty($phone)) {
        $result = array("status" => 0, "msg" => "error", "data" => "Phone Number is Missing.");
        echo json_encode($result);
        exit();
    }

    $param = array("pagination" => "", "col" => "username,campaign", "tb" => "users", "where" => "where id='" . $_SESSION['uId'] . "' ", "limit" => "1");
    $res = qselect($param);
    $campaign = $res['data']['campaign'];
    $username = $res['data']['username'];
    if ($campaign == null || empty($campaign)) {
        $result = array("status" => 0, "msg" => "error", "data" => "Campaign is Missing.");
        echo json_encode($result);
        exit();
    }

    $res = dialer($campaign, $username, $phone);
    $data = json_decode($res, true);
    if (isset($data['dialstatus']) && $data['dialstatus'] == 'success') {
        $result = array("status" => 1, "msg" => "success", "data" => "Call Connected.");
    } else {
        $result = array("status" => 0, "msg" => "error", "data" => $data['dialstatus']);
    }
    echo json_encode($result);
    exit();
}
/*
Updating the data in the database
*/

if ($action == 'update') {

    $leads = $_POST['lead_id'];
    $assigneeName = userName($_POST['user']);
    $assigneeId = $_POST['user'];
    $adminId = getAdminId();
    $param = array("tb" => "leads", "where" => "where id in (" . $_POST['lead_id'] . ") ", "limit" => "1000");
    $marketerIds = explode(',', $_POST['manager_lead_id']);
    $leadIds = explode(',', $_POST['lead_id']);

    if ($_POST['usertype'] == "Marketing") {
        $type = 'Sale Manager';
        foreach ($leadIds as $key => $lead_id) {
            $leadname = getLeadName($lead_id);
            notify($assigneeId, "Lead $leadname:$lead_id assigned to you", 'user', "/view-lead/$lead_id", 'Lead Assigned');
            notify($adminId, "Lead $leadname:$lead_id assigned to sales manager $assigneeName:$assigneeId", 'user', "/view-lead/$lead_id", 'Lead Assigned');
        }
    } else {
        $type = 'Trainer';
        foreach ($marketerIds as $key => $value) {
            $ids = explode(':', $value);
            $manager_id = $ids[0];
            $lead_id = $ids[1];
            $managerName = userName($manager_id);
            $leadname = getLeadName($lead_id);
            notify($manager_id, "Lead $leadname:$lead_id assigned to $type :$assigneeName,Id:$assigneeId", 'user', "/view-lead/$lead_id", 'Lead Assigned');
            notify($adminId, "Lead $leadname:$lead_id assigned to Trainer $assigneeName:$assigneeId sales manager $managerName:$manager_id", 'user', "/view-lead/$lead_id", 'Lead Assigned');
            notify($_POST['user'], "You have been assigned Lead: ($lead_id)", 'user', "/view-lead/$lead_id", 'Lead Assigned');
        }
    }
    if (isset($_POST['usertype']) && ($_POST['usertype'] == "Marketing")) {
        $data = array(
            "marketing_id" => $_POST['user']
        );
        $res = qupdate($param, $data, 0);
        echo json_encode($res);
        exit();
    } elseif (isset($_POST['usertype']) && ($_POST['usertype'] == "Trainer")) {
        $data = array("user_id" => $_POST['user']);
    }
    $res = qupdate($param, $data, 0);
    echo json_encode($res);
}
/*
Reassigning the lead in the database
*/
if ($action == 'reassign') {
    $assigneeId = $_POST['userss'];
    $lead_id = $_POST['leadid'];
    $adminId = getAdminId();
    if ($_POST['usertypeee'] == "Marketing") {
        $type = 'Sale Manager';
        $leadname = getLeadName($lead_id);
        $assigneeName = userName($assigneeId);
        notify($assigneeId, "Lead $leadname:$lead_id assigned to you", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
        notify($adminId, "Lead $leadname:$lead_id assigned to sales manager $assigneeName:$assigneeId", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
    } else {
        $type = 'Trainer';
        $manager_id = (int) $_POST['marketer_id']; // Convert to integer
        $managerName = userName($manager_id);
        $leadname = getLeadName($lead_id);
        $assigneeName = userName($assigneeId);

        notify($manager_id, "Lead $leadname:$lead_id assigned to $type :$assigneeName,Id:$assigneeId", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
        notify($adminId, "Lead $leadname:$lead_id assigned to Trainer $assigneeName:$assigneeId sales manager $managerName:$manager_id", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');

        notify($assigneeId, "You have been assigned Lead: ($lead_id)", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
    }
    $param = array("tb" => "leads", "where" => "where id in (" . $_POST['leadid'] . ") ", "limit" => "1000");
    if (isset($_POST['usertypeee']) && ($_POST['usertypeee'] == "Marketing")) {
        $data = array(
            "marketing_id" => $_POST['userss']
        );
        $res = qupdate($param, $data, 0);
        echo json_encode($res);
        exit();
    } elseif (isset($_POST['usertypeee']) && ($_POST['usertypeee'] == "Trainer")) {
        $data = array(
            "user_id" => $_POST['userss']
        );
    }
    $res = qupdate($param, $data, 0);
    echo json_encode($res);
}

