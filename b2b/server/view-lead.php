<?php

require_once('bootstrap.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// require_once(APP_PATH . 'Classes/Config/WhatsaapApi.php');
// require_once(APP_PATH . 'Classes/Config/SendMail.php');
// require_once(APP_PATH . 'Classes/Config/Response.php');
// require_once(APP_PATH . 'Classes/Config/Builder.php');
// require_once(APP_PATH . '../vendor/autoload.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Controller\LeadActivityController;
use Panel\Server\Classes\Config\WhatsappApi;
use Panel\Server\Classes\Config\SendMail;
use Carbon\Carbon;
use Panel\Server\Classes\Config\Response;

if ($action == 'get') {
    $filter = 'where 1=1';
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $array = json_decode($_POST['filter'], true);
        if (isset($array['id']) && ($array['id'] != '')) {
            $filter .= " and id='" . $array['id'] . "'  ";
        }
    }
    $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "leads", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $data = qselect($param);

    $param = array("pagination" => "", "col" => "*", "tb" => "lead_locations", "where" => "where lead_id=" . $array['id'], "limit" => 1000000);
    $locations = qselect($param);

    $result = array("status" => 1, "msg" => "success", "main" => $data, "locations" => $locations);
    echo json_encode($result);
}

if ($action == 'save_followups') {
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
    $udpateData = [
        'next_followup_date' => $_POST['next_followup_date'] . ' ' . $_POST['next_followup_time'],
        'updated' => date('Y-m-d h:i:s')
    ];
    $updateLead = update('cp_leads', $udpateData, " id='$leadid'", false);
    $username = userName($userid);
    notify(adminId(), "A followup has been added by $username", 'calendar', "/view-lead/$leadid#navs-justified-followups", 'Followup Created');
    captureLeadActivity($leadid, "A followup has been added by $username", 'FOLLOWUP ADDED', "/view-lead/$leadid#navs-justified-followups");
    $con = mysqli_connect("localhost", "innerxcrm_internal", "innerxcrm_internal@77", "innerxcrm_team_panel");
    $lead_id = $_POST['lead_id'];
    $leadInfo = getLeadInfo($lead_id, $con);

    if ($leadInfo != -1) {
        $leadName = $leadInfo['name'];
        $leadPhone = $leadInfo['organization_phone'];
        $leadOrganization = $leadInfo['organization'];
        $userid = $leadInfo['user_id'];
        $marketingId = $leadInfo['marketing_id'];
    }

    $followUpStatus = $_POST['followup_substatus'];
    $statusCondition1 = ['Call Back', 'Ringing Not Responding', 'Line Busy', 'Switched Off'];
    if (in_array($followUpStatus, $statusCondition1)) {
        $whatsaap = new WhatsappApi();
        $result = $whatsaap->sendWhatsAppMessage(['name' => $leadName, 'phone' => $leadPhone], 'phone_switched_off', ['client_organization_name' => $leadInfo['organization']]);
    }
    $statusCondition2 = ['Not Interested', 'Invalid No', 'Not Enquired'];
    if (in_array($followUpStatus, $statusCondition2)) {
        $notInterestedQuery = select('cp_followups', 'COUNT(sub_status) as countNotInterested', " where lead_id='$lead_id' AND sub_status='Not Interested'", '', '');
        $count = $notInterestedQuery[0]['countNotInterested'];
        if ($count > 3) {
            http_response_code(200);
            echo json_encode(['statusCode' => 200, 'status' => 1, 'message' => 'Same status (Not Interested more than 3 times)']);
            exit;
        }
        $whatsaap = new WhatsappApi();
        $result = $whatsaap->sendWhatsAppMessage(['name' => $leadName, 'phone' => $leadPhone], 'client_not_interested', ['client_organization_name' => $leadInfo['organization']]);
    }
    if ($followUpStatus === 'Interested') {
        notify($userid, "Lead :$leadName followup added status: Interested", 'user', "/view-lead/$lead_id", 'Lead Interested Followup');
        notify($marketingId, "Lead :$leadName followup added", 'user', "/view-lead/$lead_id", 'Followup Lead Interested');
    }
    echo json_encode($res);
    exit;
}
if ($action == 'get_followups') {
    $filter = 'where 1=1';
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $array = json_decode($_POST['filter'], true);
        if (isset($array['id']) && ($array['id'] != '')) {
            $filter .= " and id='" . $array['id'] . "'  ";
        }
    }
    $param = array("sort" => " order by id desc", "pagination" => $_POST['pagination'], "col" => "*", "tb" => "followups", "where" => "where lead_id='" . $_POST['lead_id'] . "' ", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    $param = [
        'tb' => 'cp_followups f',
        'where' => "where f.lead_id='" . $_POST['lead_id'] . "' ",
        'col' => 'f.*,u.fname as created_by_fname,u.lname as created_by_lname,l.next_followup_date,un.fname as addedByName',
        'pagination' => $_POST['pagination'],
        'sort' => ' order by id desc',
        'group' => '',
        'limit' => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
        'join' => [
            [
                'table' => 'cp_leads l',
                'condition' => 'f.lead_id = l.id'
            ],
            [
                'type' => 'LEFT JOIN ',
                'table' => 'cp_users u',
                'condition' => 'l.user_id = u.id'
            ],
            [
                'type' => 'LEFT JOIN ',
                'table' => 'cp_users un',
                'condition' => 'f.user_id = un.id'
            ],
        ]
    ];
    $res = joinSelect($param);
    // dd($res);
    echo json_encode($res);
}
if ($action == 'save_meeting') {

    $param = array("tb" => "meetings");
    $data = array(
        "lead_id" => $_POST['id'],
        "assigned_by" => $_SESSION['uId'],
        "procedure" => $_POST['procedure'],
        "status" => 'scheduled',
        "meeting_date" => $_POST['meeting_date'],
        "remarks" => $_POST['remarks'],
    );

    $res = qinsert($param, $data);
    $mysqli = new mysqli("localhost", "innerxcrm_internal", "innerxcrm_internal@77", "innerxcrm_team_panel");
    // $mysqli = new mysqli($hostname, $username, $password, $database);
    $lead_id = $_POST['id'];
    $leadInfo = getLeadInfo($lead_id, $mysqli);
    if ($leadInfo != -1) {
        $leadName = $leadInfo['name'];
        $leadUserId = $leadInfo['user_id'] ?? '';
        $marketingId = $leadInfo['marketing_id'] ?? '';
        $leadPhone = $leadInfo['organization_phone'];
        $leadEmail = $leadInfo['organization_email'] ?? '';
        $leadOrganization = $leadInfo['organization'];
    }

    $query = "SELECT cp_leads.user_id, cp_users.fname, cp_users.phone 
          FROM cp_leads 
          INNER JOIN cp_users ON cp_leads.user_id = cp_users.id 
          WHERE cp_leads.id = $lead_id";
    $result = $mysqli->query($query);


    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $fname = $row['fname'];
            $phone = $row['phone'];
        }
    }
    $query = "SELECT  meeting_date , 'procedure' FROM cp_meetings WHERE lead_id = $lead_id";
    $result = $mysqli->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $procedure = $row['procedure'];
            $meeting_date = $row['meeting_date'];
        }
    }
    $date_time = $_POST['meeting_date'];
    $date_time_parts = explode(' ', $date_time);
    $date = substr($date_time, 0, 10);
    $time = substr($date_time, 11);

    if ($leadUserId == 32) {
        $link = "https://meet.google.com/skw-xujh-ppc";
    } else if ($leadUserId == 33) {
        $link = "https://meet.google.com/uog-kacq-nug";
    } else if ($leadUserId == 30) {
        $link = "https://meet.google.com/ior-fvxu-apb";
    } else if ($leadUserId == 31) {
        $link = "https://meet.google.com/jef-sowe-aqi";
    } else if ($leadUserId == 38) {
        $link = "https://meet.google.com/kjc-hpss-aaj";
    } else if ($leadUserId == 54) {
        $link = "https://meet.google.com/qcu-aqri-urv";
    } else if ($leadUserId == 55) {
        $link = "https://meet.google.com/tph-hkcn-yhm";
    } else if ($leadUserId == 56) {
        $link = "https://meet.google.com/xkr-esgz-zqg";
    } else {
        $userMeetingLink = "SELECT * FROM cp_users WHERE id='$leadUserId'";
        $getUser = select('cp_users', 'meeting_link', " where id='$leadUserId'", '', '');
        $link = $getUser[0]['meeting_link'] ?: 'd';
        if (empty($link)) {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to fetch the meeting link', 'statusCode' => 500, 'status' => 0]);
            exit;
        }
    }

    $user_id = $_SESSION['uId'];
    $query = "SELECT  fname FROM cp_users WHERE id = $user_id";
    $result = $mysqli->query($query);
    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row['fname'];
        }
    }
    $procedure = $_POST['procedure'];
    if ($procedure === "training") {
        $response = trainingScheduled($leadName, $leadPhone, $leadOrganization, $fname, $date, $time, $link);
        error_log(" {{$procedure}} whatsaap message log: " . $response);
        notify($leadUserId, "A training session has been scheduled with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Training Schedule');
        captureLeadActivity($lead_id, "A training session has been scheduled with $leadOrganization at $date", "TRAINING SESSION", "/view-lead/$lead_id#navs-justified-followups");
    }
    if ($procedure === "re-schedule" || $procedure === "meeting") {
        $msg = $procedure == 'meeting' ? 'scheduled' : 're-scheduled';
        if ($msg == 'scheduled') {
            $whatsaap = new WhatsappApi();
            $result = $whatsaap->sendWhatsAppMessage(['name' => $leadName, 'phone' => $leadPhone], 'meeting_scheduled', ['client_organization_name' => $leadInfo['organization'], 'sales_manager_name' => $fname, "date" => $date, "time" => $time]);

            $meetinTemplate = APP_PATH . '../view/email-templates/meeting-template1.html';
            $tempate = file_get_contents($meetinTemplate);
            $tempate = str_replace('{{client_name}}', $leadName, $tempate);
            $tempate = str_replace('{{date}}', $date, $tempate);
            $tempate = str_replace('{{time}}', $time, $tempate);
            $tempate = str_replace('{{link}}', $link, $tempate);

            $mail = new SendMail();
            $mail->setTo($leadEmail);
            $mail->setSubject("Demo Booking for $leadName on $date/$time");
            $mail->setContent($tempate);
            $mail->sendMail();
        } else {
            $whatsaap = new WhatsappApi();
            $result = $whatsaap->sendWhatsAppMessage(['name' => $leadName, 'phone' => $leadPhone], 'meeting_rescheduled', ['client_organization_name' => $leadInfo['organization'], 'sales_manager_name' => $fname, "date" => $date, 'time' => $time]);
        }
        $msgUpperCase = strtoupper($msg);
        if (!empty($leadUserId)) {
            captureLeadActivity($lead_id, "Meeting has been $msg with $leadOrganization at $date, $time", "MEETING $msgUpperCase", "/view-lead/$lead_id#navs-justified-followups");
            notify($leadUserId, "Meeting has been $msg with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Meeting Reschedule');
        }
        if (!empty($marketingId)) {
            notify($marketingId, "Meeting has been $msg with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Meeting Reschedule');
            captureLeadActivity($lead_id, "Meeting has been $msg with $leadOrganization at $date, $time", "MEETING $msgUpperCase", "/view-lead/$lead_id#navs-justified-followups");
        }
    }
    if ($procedure == "schedule-demo") {
        $response = procedure($leadName, $leadPhone, $leadOrganization, $procedure, $fname, $date, $time, $link);
        error_log(" {{$procedure}} whatsaap message log: " . $response);
        notify($leadUserId, "A schedule-demo session has been scheduled with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Training Schedule');
        captureLeadActivity($lead_id, "A schedule-demo session has been scheduled with $leadOrganization at $date, $time", 'DEMO SCHEDULED', "/view-lead/$lead_id#navs-justified-followups");
    }
    if ($procedure == "onboarding") {
        $response = procedure($leadName, $leadPhone, $leadOrganization, $procedure, $fname, $date, $time, $link);
        error_log(" {{$procedure}} whatsaap message log: " . $response);
        captureLeadActivity($lead_id, "A onboarding session has been scheduled with $leadOrganization at $date, $time", 'ONBOARDING SESSION', "/view-lead/$lead_id#navs-justified-followups");
        notify($leadUserId, "A onboarding session has been scheduled with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Training Schedule');
    }
    if ($procedure == "practice") {
        $response = procedure($leadName, $leadPhone, $leadOrganization, $procedure, $fname, $date, $time, $link);
        error_log(" {{$procedure}} whatsaap message log: " . $response);
        notify($leadUserId, "A practice session has been scheduled with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Training Schedule');
        captureLeadActivity($lead_id, "A practice session has been scheduled with $leadOrganization at $date", 'PRACTICE SESSION', "/view-lead/$lead_id#navs-justified-followups");
    }
    if ($procedure == "followup") {
        //     $response = procedure($leadName, $leadPhone, $leadOrganization, $procedure, $fname, $date, $time, $link);
        //    error_log(" {{$procedure}} whatsaap message log: ".$response);
        notify($leadUserId, "A followup-session has been scheduled with $leadOrganization at $date, $time", 'calendar', "/view-lead/$lead_id#navs-justified-followups", 'Training Schedule');
        captureLeadActivity($lead_id, "A followup-session has been scheduled with $leadOrganization at $date", 'FOLLOWUP SESSION', "/view-lead/$lead_id#navs-justified-followups");
    }
    echo json_encode($res);
    exit;
}
if ($action == 'get_meetings') {
    $filter = 'where 1=1';
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $array = json_decode($_POST['filter'], true);
        if (isset($array['id']) && ($array['id'] != '')) {
            $filter .= " and lead_id='" . $array['id'] . "'  ";
        }
    }
    $param = array("sort" => "order by id desc", "pagination" => $_POST['pagination'], "col" => '*,DATE_FORMAT(meeting_date, "%d,%b %y %h:%i%p") as new_meeting_date', "tb" => "meetings", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
}
if ($action == 'update') {

    if ($_FILES['contract']['name'] != '') {
        $file['file'] = $_FILES['contract'];
        $file = upload_pdf($file, 'contract');

        if ($file['status'] == 0) {
            echo json_encode($file);
            exit();
        }
    } else {
        $file['data'] = '';
    }


    $param = array("col" => "", "tb" => "lead_locations", "where" => "where lead_id=" . $_POST['lead_id'] . " ", "limit" => "1000");
    $res = qdelete($param, '');

    if (!empty($_POST['location_city']) && $_POST['location_radio'] == 'yes') {

        $data = array();
        foreach ($_POST['location_city'] as $key => $a) {
            if (!empty(trim($a))) {
                $data[] = array("lead_id" => $_POST['lead_id'], "state" => $_POST['location_state'][$key], "city" => $a);
            }
        }

        $param = array("tb" => "lead_locations");
        $res = qminsert($param, $data);
    }
    /* Data Insert */
    //  storing data to table name leads
    $param = array("tb" => "leads", "where" => "where id=" . $_POST['lead_id'] . " ", "limit" => "1");


    $extra_location = (isset($_POST['location_radio'])) ? $_POST['location_radio'] : 'no';

    // setting the values for the checkbox
    $visa = isset($_POST['visa']) ? '1' : '0';
    $ielts = isset($_POST['ielts']) ? '1' : '0';
    $vistor_visa = isset($_POST['vistor_visa']) ? '1' : '0';
    $work_visa = isset($_POST['work_visa']) ? '1' : '0';
    $pr = isset($_POST['pr']) ? '1' : '0';

    // setting the values for checkbox for sending mail through
    $email = isset($_POST['link_email']) ? '1' : '0';
    $whatsapp = isset($_POST['link_whatsapp']) ? '1' : '0';

    $crm_soft_text = $_POST['crm_soft_text'];
    $crm_soft = (isset($_POST['crm_soft']) && $_POST['crm_soft'] == "yes") ? $crm_soft_text : 'no';

    $calling_soft_text = $_POST['calling_soft_text'];
    $calling_soft = (isset($_POST['calling_soft']) && $_POST['calling_soft'] == "yes") ? $calling_soft_text : 'no';

    $sms_soft_text = $_POST['sms_soft_text'];
    $sms_soft = (isset($_POST['sms_soft']) && $_POST['sms_soft'] == "yes") ? $sms_soft_text : 'no';

    $whatsapp_soft_text = $_POST['whatsapp_soft_text'];
    $whatsapp_soft = (isset($_POST['whatsapp_soft']) && $_POST['whatsapp_soft'] == "yes") ? $whatsapp_soft_text : 'no';

    $email_soft_text = $_POST['email_soft_text'];
    $email_soft = (isset($_POST['email_soft']) && $_POST['email_soft'] == "yes") ? $email_soft_text : 'no';

    $companyType = $_POST['companytype'];
    $companyType = !empty($companyType) ?  $companyType : '';
    // $paid_ads_soft_text=$_POST['paid_ads_soft_text'];
    // $paid_ads_soft = (isset($_POST['paid_ads_soft']) && $_POST['paid_ads_soft']=="yes") ? $paid_ads_soft_text : 'no' ;

    $source = $_POST['source'];
    $data = array(
        "name" => $_POST['name'],
        // "user_id" => $_SESSION['uId'],
        "phone" => $_POST['phone'],
        "website" => $_POST['website'],
        "email" => $_POST['email'],
        "state" => $_POST['state'],
        "city" => $_POST['city'],
        "address" => $_POST['address'],
        "extra_location" => $extra_location,
        "visa" => $visa,
        "ielts" => $ielts,
        "vistor_visa" => $vistor_visa,
        "work_visa" => $work_visa,
        "pr" => $pr,
        "other_services" => $_POST['other_services'],
        "crm_soft" => $crm_soft,
        "calling_soft" => $calling_soft,
        "sms_soft" => $sms_soft,
        "whatsapp_soft" => $whatsapp_soft,
        "email_soft" => $email_soft,
        // "paid_ads_soft"=>$paid_ads_soft,
        "crm_users" => $_POST['crm_users'],
        // "followup_status"=>$_POST['followup_status'],
        // "followup_substatus"=>$_POST['followup_substatus'],
        // "next_followup_date"=>$_POST['next_followup_date'],
        // "remarks"=>$_POST['remarks'],
        "contract" => $file['data'],
        "link_email" => $email,
        "link_whatsapp" => $whatsapp,
        'companytype'=>$companyType,
        'source'=>$source
    );

    $res = qupdate($param, $data, 0);
    echo json_encode($res);
}
if ($action == 'email') {

    $filter = 'where 1=1';
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $array = json_decode($_POST['filter'], true);
        if (isset($array['id']) && ($array['id'] != '')) {
            $filter .= " and id='" . $array['id'] . "'  ";
        }
    }

    $param = array("pagination" => "", "col" => "*", "tb" => "leads", "where" => "" . $filter, "limit" => "1");
    $res = qselect($param);

    $to = explode(",", $_POST['emails']);
    //$to = explode(",",$res['data']['email']);
    $link = $_POST['meeting_link'];
    if (count($to) > 0) {
        $subject = "Innerx CRM Training Details";
        $content = file_get_contents('https://team.innerxcrm.com/view/meetingtemplate.html');
        $content = str_replace('{{Date}}', date('d M Y'), $content);
        $content = str_replace('{{Url}}', $link, $content);
        //$content=str_replace( "{email}", $res['data']['email'], $content);

        $param = array("to" => $to, "subject" => $subject, "content" => $content, "attachment" => "");
        mailer($param);
    }

    echo json_encode(array("status" => 1, "msg" => "success", "data" => "Mail Sent successfully."));
}
if ($action == 'pricingPlans') {
    
    $builder = new Builder();
    $res = $builder->table('cp_pricing_plans')->select('*')->get();
    echo json_encode($res);
}
if ($action == 'userRegistrationPlan') {
    include('../_con.php');

    $userId = $_POST['userId'];
    $query = $con->query("SELECT cp_lead_payments.*,cp_lead_payments.id as payment_id,cp_pricing_plans.*  FROM cp_lead_payments JOIN cp_pricing_plans ON cp_pricing_plans.id = cp_lead_payments.plan_id WHERE cp_lead_payments.lead_id='$userId' ORDER BY cp_lead_payments.id DESC");

    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($res);
}
if ($action == 'savePaymentDetails') {
    $leadId = $_POST['lead_id'];
    $planId = $_POST['plan_id'];
    $discountGiven = $_POST['discount'];
    $amountPaid = $_POST['paid'];
    $paymentType = $_POST['type'];
    $paymentMode = $_POST['mode'];
    $remarks = $_POST['remarks'];
    $today = Carbon::today();
    $getLastRenewDate = select('cp_lead_payments', 'to_date', " where lead_id='$leadId'", ' ORDER BY id DESC LIMIT 1');
    $existedfromDate = !empty($getLastRenewDate[0]['to_date']) ? $getLastRenewDate[0]['to_date'] : '';

    if ($paymentType == 'QUARTERLY' || $paymentType == 'quarterly') {
        $fromDate = $today->format('Y-m-d');
        $toDate = $today->copy()->addMonths(3)->format('Y-m-d');
        if (!empty($existedfromDate)) {
            $fromDate = Carbon::parse($existedfromDate)->addDay()->format('Y-m-d');
            $toDate = Carbon::parse($existedfromDate)->copy()->addMonths(3)->format('Y-m-d');
        }
    } else {
        $fromDate = $today->format('Y-m-d');
        $toDate = $today->copy()->addMonths(12)->format('Y-m-d');
        if (!empty($existedfromDate)) {
            $fromDate = Carbon::parse($existedfromDate)->addDay()->format('Y-m-d');
            $toDate = Carbon::parse($existedfromDate)->copy()->addMonths(12)->format('Y-m-d');
        }
    }
    if (empty($_FILES['image']['name'])) {
        return Response::error('Reciept Required');
    } else {
        $reciept = $_FILES['image'];
        $img = storeImg(['image' => $reciept], 'payment-attachments');

        if ($img['status'] = 1) {
            $recieptName = $img['filename'];
            $recieptPath = $img['path'];
        } else {
            return Response::error('Reciept Image has some errors, Please try again');
        }
    }

    $insert = qinsert(
        ['tb' => 'lead_payments'],
        [
            'lead_id' => $leadId,
            'plan_id' => $planId,
            'amount' => $amountPaid,
            'given_discount' => $discountGiven,
            'payment_mode' => $paymentMode,
            'payment_type' => strtoupper($paymentType),
            'reciept_attachment' => $recieptPath,
            'reciept_name' => $recieptName,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'remarks' => $remarks,
        ]
    );
    if (!empty($insert['success_id'])) {
        http_response_code(200);
        echo json_encode(['statusCode' => 200, 'status' => 1, 'message' => 'Payment Created successfully']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['statusCode' => 500, 'status' => 0, 'message' => 'Payment Created successfully']);
        exit;
    }
}
if ($action == 'deleteUserRegistredPlan') {
    include('../_con.php');
    $userPlanId = $_POST['id'];

    $query = "DELETE FROM cp_lead_payments WHERE id='$userPlanId'";
    $prep = $con->prepare($query);
    $res = $prep->execute();
    if (!empty($res)) {
        http_response_code(200);
        echo json_encode(['statusCode' => 200, 'status' => 1, 'message' => 'Plan Removed Successfully']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['statusCode' => 500, 'status' => 0, 'message' => 'Failed to remove the plan']);
        exit;
    }
}

if ($action == 'getActivities') {

    require_once(APP_PATH . 'Classes/Controller/LeadActivityController.php');

    $leadId = $_POST['lead_id'];
    if (empty($leadId)) {
        http_response_code(403);
        echo json_encode(['statusCode' => 403, 'status' => 0, 'message' => 'Lead Id Required']);
        exit;
    }
    try {
        $leadActivity = new LeadActivityController();
        $getActivities = $leadActivity->getActivities($leadId);
        http_response_code(200);
        echo json_encode(['statusCode' => 200, 'activities' => $getActivities]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['statusCode' => 500, 'message' => $e->getMessage(), 'status' => 0]);
    }
}

if ($action == 'getUserPlan') {
    $id = $_POST['user_id'];
    $qb = new Builder();
    $result = $qb->table('cp_lead_payments')
        ->select('cp_lead_payments.*,cp_pricing_plans.price')
        ->join('cp_pricing_plans', 'cp_lead_payments.plan_id', '=', 'cp_pricing_plans.id')
        ->where('cp_lead_payments.lead_id', '=', $id, '')
        ->orderBy('cp_lead_payments.id', 'DESC')
        ->take(1)->get();
    if (!empty($result[0]['id'])) {
        return Response::json(['statusCode' => 200, 'status' => 1, 'data' => $result], 200);
    } else {
        return Response::error('Failed to get the record');
    }
}

if ($action == 'saveChecklist') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Failed to find the record');
    }
    try {
        update('cp_leads', ['checklist_services' => json_encode($_POST['services'])], 'id="' . $id . '"');
        return Response::success('Checklist services saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save checklist services');
    }
}

if ($action == 'getChecklistServices') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Failed to fetch data');
    }
    $db = new Builder();
    $result = $db->select('checklist_services')->table('cp_leads')->where('id', '=', $id, '')->get();
    return Response::json(['data' => $result], 200);
}
