<?php

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
if ($action == 'get') {
    include('../var.php');
    $tb_pre = ''; // Set the table prefix as needed
    $pagination = intval($_POST['pagination']);
    $limit = intval($_POST['limit']);
    $filter = json_decode($_POST['filter']);

    if($_SESSION['utype'] !== 1){
        $userId = $_SESSION['uId'];
        $condition = " AND f.user_id='$userId'";
     }else{
        $condition = '';
     }

     if(!empty($filter->search)){
        $search = $filter->search;
        $condition = " AND l.organization LIKE '%$search%'";
     }
    $pagination = max(1, $pagination);
    $limit = max(0, $limit);
    $today = date('Y-m-d');
    $limitClause = (($pagination - 1) * $limit) . "," . $limit;
    $param = array(
        'col' => 'f.*, u.fname, l.organization, l.marketing_id,f.user_id',
        'tb' => 'followups AS f LEFT JOIN cp_users AS u ON f.user_id = u.id LEFT JOIN cp_leads AS l ON f.lead_id = l.id',
        'where' => " where DATE(f.followup_date) = '$today' $condition",
        'sort' => ' ORDER BY f.id DESC',
        'limit' => $limitClause,
        'pagination' => $pagination,
    );

    $result = qselect($param);
    echo json_encode($result);
}

if ($action == 'get_users') {
    $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "users", "where" => "", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
}



// if ($action == 'reassign') {
//     $assigneeId = $_POST['userss'];
//     $lead_id = $_POST['leadid'];
//     $adminId = getAdminId();
//     if ($_POST['usertypeee'] == "Marketing") {
//         $type = 'Sale Manager';
//         $leadname = getLeadName($lead_id);
//         notify($assigneeId, "Lead $leadname:$lead_id assigned to you", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
//         notify($adminId, "Lead $leadname:$lead_id assigned to sales manager $assigneeName:$assigneeId", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
//     } else {
//         $type = 'Trainer';
//         $manager_id = $_POST['marketer_id'];
//         $managerName = userName($manager_id);
//         $leadname = getLeadName($lead_id);
//         $assigneeName = userName($assigneeId);
//         notify($manager_id, "Lead $leadname:$lead_id assigned to $type :$assigneeName,Id:$assigneeId", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
//         notify($adminId, "Lead $leadname:$lead_id assigned to Trainer $assigneeName:$assigneeId sales manager $managerName:$manager_id", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
//         notify($assigneeId, "You have been assigned Lead: ($lead_id)", 'user', "/view-lead/$lead_id", 'Lead Re-Assigned');
//     }
//     $param = array("tb" => "leads", "where" => "where id in (" . $_POST['leadid'] . ") ", "limit" => "1000");
//     if (isset($_POST['usertypeee']) && ($_POST['usertypeee'] == "Marketing")) {
//         $data = array(
//             "marketing_id" => $_POST['userss']
//         );
//         $res = qupdate($param, $data, 0);
//         echo json_encode($res);
//         exit();
//     } elseif (isset($_POST['usertypeee']) && ($_POST['usertypeee'] == "Trainer")) {
//         $data = array(
//             "user_id" => $_POST['userss']
//         );
//     }
//     $res = qupdate($param, $data, 0);
//     echo json_encode($res);
// }


if ($action == 'call') {
    $param = array("pagination" => "", "col" => "phone", "tb" => "leads", "where" => " where id=" . $_POST['id'] . " ", "limit" => "1");
    $res = qselect($param);
    $phone = !empty($res['data']['phone']) ? $res['data']['phone'] : '';
    if ($phone == null || empty($phone)) {
        http_response_code(500);
        $result = array("status" => 0, "msg" => "error", "message" => "Phone Number is Missing.");
        echo json_encode($result);
        exit();
    }

    $param = array("pagination" => "", "col" => "username,campaign", "tb" => "users", "where" => "where id='" . $_SESSION['uId'] . "' ", "limit" => "1");
    $res = qselect($param);
    $campaign = !empty($res['data']['campaign']) ? $res['data']['campaign'] : '';
    $username = !empty($res['data']['username']) ? $res['data']['username'] : '';
    if ($campaign == null || empty($campaign)) {
        http_response_code(500);
        $result = array("status" => 0, "msg" => "error", "message" => "Campaign is Missing.");
        echo json_encode($result);
        exit();
    }

    $res = dialer($campaign, $username, $phone);
    $data = json_decode($res, true);
    if (isset($data['dialstatus']) && $data['dialstatus'] == 'success') {
        http_response_code(200);
        $result = array("status" => 1, "msg" => "success", "message" => "Call Connected.");
    } else {
        http_response_code(500);
        $result = array("status" => 0, "msg" => "error", "message" => $data['dialstatus']);
    }
    echo json_encode($result);
    exit();
}


if ($action == 'save_followup') {
    $param = array("tb" => "followups");
    $userid = $_SESSION['uId'];
    $leadid = $_POST['lead_id'];
    $data = array(
        "lead_id" => $leadid,
        "user_id" => $userid,
        "status" => $_POST['followup_status'],
        "sub_status" => $_POST['followup_substatus'],
        "followup_date" => $_POST['next_followup_date'] . ' ' . $_POST['next_followup_time'],
        "remarks" => $_POST['remarks'],
    );

    $res = qinsert($param, $data);
    $udpateData  = [
        'next_followup_date' => $_POST['next_followup_date'] . ' ' . $_POST['next_followup_time'],
        'updated' => date('Y-m-d h:i:s')
    ];
    $updateLead = update('cp_leads', $udpateData , " id='$leadid'", false);
    $username = userName($userid);
    notify(adminId(), "A folowup has been added by $username", 'calendar', "/view-lead/$leadid#navs-justified-followups", 'Followup Created');
    echo json_encode(['statusCode' => 200,'message' => 'Followup Has been Added ']);
    exit;
}
