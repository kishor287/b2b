<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(APP_PATH . 'Classes/Config/WhatsaapApi.php');
require_once(APP_PATH . 'Classes/Config/SendMail.php');
require_once(APP_PATH . 'Classes/Controller/LeadPaymentController.php');
require_once(APP_PATH . '../vendor/autoload.php');

use Panel\Server\Classes\Controller\LeadPaymentController;

const TABLE = 'cp_leads';

if ($action == 'get_users') {
  $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "users", "where" => "", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
  $res = qselect($param);
  echo json_encode($res);
}

/*
Email
*/
if ($action == 'email') {

  $filePath = getAgreementPdfPath($_POST['id']);
  $filter = 'where 1=1';
  if (isset($_POST['filter']) && !empty($_POST['filter'])) {
    $array = json_decode($_POST['filter'], true);
    if (isset($array['id']) && ($array['id'] != '')) {
      $filter .= " and id='" . $array['id'] . "'  ";
    }
  }
  $param = array("pagination" => "", "col" => "*", "tb" => "leads", "where" => "" . $filter, "limit" => "1");
  $res = qselect($param);
  $param = array("pagination" => "", "col" => "*", "tb" => "users", "where" => "where id=" . $_SESSION['uId'], "limit" => " 1");
  $r = qselect($param);
  $username = $r['data']['fname'] . ' ' . $r['data']['lname'];

  $to = explode(",", $_POST['emails']);
  if (count($to) > 0) {
    $subject = "Agreement | InnerxCRM";
    // $content = file_get_contents('https://innerxcrm.com/agreement');
    // $content = str_replace("{{date}}", date('d M Y'), $content);
    $param = array("pagination" => "", "col" => "*", "tb" => "users", "where" => "where id=" . $res['data']['marketing_id'], "limit" => " 1");
    $r = qselect($param);
    $username = $r['data']['fname'] . ' ' . $r['data']['lname'];
    $content = "Please see the attached document";
    $param = array("to" => $to, "subject" => $subject, "content" => $content, "attachment" => $filePath);

    // mailer($param);
    if(is_array($to)){
      foreach($to as $val){
        sendMail($val,[],'Agreement | Innerxcrm',$content,$filePath);
      }
    }else{
      sendMail($_POST['emails'],[],'Agreement | Innerxcrm',$content,$filePath);
    }
  }

  echo json_encode(array("status" => 1, "msg" => "success", "data" => "Mail Sent successfully."));
}

/* Save */

if ($action == 'get') {
  $condition = ' 1=1 ';
  $filterVar = $_POST['filter'];

  if (isset($filterVar) && !empty($filterVar)) {
    $array = json_decode($filterVar, true);
    $decoded = json_decode($filterVar, true);
    $filters = array_shift($array);
    if (isset($filters['contract']) && !empty($filters['contract'])) {
      if ($filters['contract'] == 1) {
        $condition .= ' AND uploaded_contract_path IS NOT NULL ';
      } else {
        $condition .= ' AND uploaded_contract_path IS NULL ';
      }
    }

    if (!empty($decoded['id'])) {
      $id = $decoded['id'];
      $condition .= " AND id=$id ";
    }
    if (isset($filters['search']) && !empty($filters['search'])) {
      $condition .= " AND organization LIKE '%" . $filters['search'] . "%' ";
    }
    if ($_SESSION['u_type'] == '1') {
      $condition .= " AND marketing_id <> 0 ";
    } else {
      $condition .= ' AND marketing_id="' . intval($_SESSION['uId']) . '" ';
    }
    if (isset($filters['daterange']) && $filters['daterange'] !== "") {
      $daterange = $filters['daterange'];
      $dates = explode(" - ", $daterange);

      $startDate = DateTime::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
      $endDate = DateTime::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');
      $condition .= " AND  created BETWEEN '$startDate' AND '$endDate' ";
    }

    if (isset($filters['trainer']) && !empty($filters['trainer'])) {
      $trainerId = $filters['trainer'];
      $condition .= " AND user_id='$trainerId'  ";
    }
    if (isset($filters['manager']) && !empty($filters['manager'])) {
      $manager = $filters['manager'];
      $condition .= " AND  marketing_id='$manager' ";
    }
    if (isset($filters['source']) && !empty($filters['source'])) {
      $source = $filters['source'];
      $condition .= " AND source='$source' ";
    }
    if(isset($filters['type']) && $filters['type'] == 'clients'){
      $condition.= " AND uploaded_contract_path IS NOT NULL AND reward !='' OR forex !=''";
    }
  }
  $clientPlan = [];
  if (!empty($_POST['getPlan']) && isset($id)) {
    $today = date('Y-m-d');

    $param = [
      'tb' => 'cp_lead_payments lp',
      'where' => "where lp.lead_id='$id' AND DATE(lp.to_date) > $today ",
      'col' => 'lp.*,pp.title',
      'pagination' => '',
      'sort' => ' ORDER BY id DESC ',
      'group' => '',
      'limit' => '1',
      'join' => [
        [
          'table' => 'cp_pricing_plans pp',
          'condition' => 'lp.plan_id = pp.id',
          'type' => 'LEFT JOIN'
        ]
      ]
    ];
    $selectPlan = joinSelect($param);
    $clientPlan = $selectPlan['data'];
  }

  $condition = rtrim($condition, 'AND');
  $param = array("sort" => " order by id desc ", "pagination" => $_POST['pagination'], "col" => "*", "tb" => "leads", "where" => "where " . $condition, "limit" => ($_POST['pagination'] - 1) * $_POST['limit'] . "," . $_POST['limit']);
  $res = qselect($param);
  echo json_encode($res + array('date' => date('d M Y'), 'plan' => $clientPlan));
  exit;
}

/* Save */
if ($action == 'save') {
  $param = array("pagination" => "", "col" => "*", "tb" => "users", "where" => "where id=" . $_SESSION['uId'], "limit" => " 1");
  $r = qselect($param);
  $username = $r['data']['fname'] . ' ' . $r['data']['lname'];

  $param = array("tb" => "leads");
  $extra_location = (isset($_POST['location_radio'])) ? $_POST['location_radio'] : 'no';
  $visa = isset($_POST['visa']) ? '1' : '0';
  $ielts = isset($_POST['ielts']) ? '1' : '0';
  $vistor_visa = isset($_POST['vistor_visa']) ? '1' : '0';
  $work_visa = isset($_POST['work_visa']) ? '1' : '0';
  $pr = isset($_POST['pr']) ? '1' : '0';

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

  $clientCategory = $_POST['client_category'];
  $agreementDate = $_POST['agreement_date'];
  $data = array(
    "lead_type" => $_POST['payment'],
    "agreement_signed_at" => $agreementDate,
    "name" => $_POST['c_name'],
    "client_category" => $clientCategory,
    "phone" => $_POST['c_phone'],
    "marketing_id" => $_SESSION['uId'],
    "account_email_id" => $_SESSION['account_email_id'],
    "email" => $_POST['c_email'],
    "address" => $_POST['address'],
    "companytype" => $_POST['companytype'],
    "companyowner" => $_POST['companyowner'],
    "companyownerphone" => $_POST['companyphone'],
    "companyowneremail" => $_POST['companyemail'],
    "committed" => $_POST['committed'],
    "reward" => $_POST['reward'],
    "credit_card" => $_POST['credit_card'],
    "benefits" => $_POST['benefits'],
    "forex" => $_POST['forex'],
    "source" => $_POST['source'],
    "other" => $_POST['fintech_otherservice'],
    "organization" => $_POST['organization'],
    "organization_phone" => $_POST['organization_phone'],
    "organization_email" => $_POST['organization_email'],
    "website" => $_POST['website'],
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
    "crm_users" => $_POST['crm_users'],
  );

  $validate = validateLead($_POST['organization'] ?? '', $_POST['c_phone'] ?? '');
  if ($validate) {
    http_response_code(500);
    $response = array(
      "error" => true,
      "message" => $validate,
      'status' => 0,
      'statusCode' => 500
    );
    $jsonResponse = json_encode($response);
    echo $jsonResponse;
    exit;
  }
  $fpath = '';
  if (isset($_FILES['contract_file']) && !empty($_FILES['contract_file'] && !empty($_FILES['contract_file']['name']))) {
    $uploaded = upload_pdf(['file' => $_FILES['contract_file']], 'contract');
    $contractFilename = $uploaded['filename'];
    $filePath = $uploaded['path'];
    $fpath = $uploaded['path'];
    $data['uploaded_contract_path'] = $filePath;
    $data['contract_filename'] = $contractFilename;
    $data['contracted_at'] = date('Y-m-d h:i:s');
  }
  if (isset($_FILES['organization_logo']) && !empty($_FILES['organization_logo'] && !empty($_FILES['organization_logo']['name']))) {
    $d = ['image' => $_FILES['organization_logo']];
    $uploadedLogo = upload_img($d, 'logo');
    $companylogo = $uploadedLogo['filename'];
    $companyLogoPath = $uploadedLogo['path'];
    $data['org_logo_path'] = $companyLogoPath;
    $data['org_logo_filename'] = $companylogo;
  }
  if (isset($_POST['availing-crm']) && !empty($_POST['availing-crm'])) {
    $availingCrmServices = $_POST['availing-crm'] == 'yes' ? 1 : 0;
    $data['availing_crm'] = $availingCrmServices;
  }
  if (isset($_POST['prefered_bank_name']) && !empty($_POST['prefered_bank_name'])) {
    $data['prefered_bank_name'] = $_POST['prefered_bank_name'];
  }
  if (!empty($_POST['committed_forex'])) {
    $data['number_of_forex_commited'] = $_POST['committed_forex'];
  }
  $adminId = getAdminId();
  $username = userName($_SESSION['uId']);
  notify($adminId, "An agreement has been added By $username", 'file', '/agreement', 'New Agreement');
  $res = qinsert($param, $data);
  $last_id = $res['success_id'];
  if ($fpath) {
    notify($adminId, "An agreement:$last_id  has been done By $username", 'file', '/agreement', 'Agreement Done');
    notify($_SESSION['uId'], "An agreement:$last_id has been done By you", 'file', '/agreement', 'Agreement Done');
  }
  //  Storing data to table name meetings
  if (!empty($_POST['schedule_date'])) {
    $param = array("tb" => "meetings");
    $status = "scheduled";
    $procedure = "Training";
    $data = array(
      "lead_id" => $last_id,
      "meeting_date" => $_POST['schedule_date'],
      "assigned_by" => $_SESSION['uId'],
      "procedure" => $procedure,
      "remarks" => $_POST['remarks'],
      "status" => $status
    );
    $res = qinsert($param, $data);
  }
  $date_time = $_POST['schedule_date'];
  $date_time_parts = explode(' ', $date_time);
  $date = substr($date_time, 0, 10);
  $time = substr($date_time, 11);
  $con = mysqli_connect("localhost", "innerxcrm_internal", "innerxcrm_internal@77", "innerxcrm_team_panel");

  $sales_manager_name = getUserName($con);
  $response = meetingScheduled($_POST['c_name'], $_POST['organization_phone'], $_POST['organization'], $sales_manager_name, $date, $time);

  if (count($_POST['location_city']) > 0) {

    $data = array();
    foreach ($_POST['location_city'] as $key => $a) {

      $data[] = array("lead_id" => $last_id, "state" => $_POST['location_state'][$key], "city" => $a);
    }

    $param = array("tb" => "lead_locations");
    $res = qminsert($param, $data);
  }

  if (!empty($_POST['payment']) && $_POST['payment'] == 'Paid') {

    $planData = [
      'leadId' => $last_id,
      'planId' => $_POST['plan_id'],
      'amountPaid' => $_POST['paid'],
      'givenDiscount' => $_POST['discount'],
      'paymentMode' => $_POST['mode'],
      'paymentType' => $_POST['type'],
      'fromDate' => $fromDate,
      'toDate' => $toDate,
      'remarks' => $_POST['remarks']
    ];
    $planData = array_merge($planData, $_FILES);
    (new LeadPaymentController())->store($planData);
  }
  captureLeadActivity($last_id, "Agreement Created On $agreementDate By $username", 'Agreement', '');
  echo json_encode($res);
  exit;
}

/* Remove */
if ($action == 'remove') {


  $param = array("col" => "", "tb" => "leads", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
  $res = qdelete($param, '', 1);
  echo json_encode($res);
  exit;
}

if ($action == 'deleteContract') {
  $id = $_POST['id'];
  update('cp_leads', ['uploaded_contract_path' => NULL, 'contract_filename' => NULL, 'contracted_at' => NULL], "id=$id");
  echo json_encode(['status' => 'success']);
}
if ($action == 'deleteLogo') {
  $id = $_POST['id'];
  update('cp_leads', ['org_logo_path' => NULL, 'org_logo_filename' => NULL], "id=$id");
  echo json_encode(['status' => 'success']);
}


if ($action == 'update') {

  $param = array("tb" => "leads", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");

  $extra_location = (isset($_POST['location_radio'])) ? $_POST['location_radio'] : 'no';

  $visa = isset($_POST['visa']) ? '1' : '0';
  $ielts = isset($_POST['ielts']) ? '1' : '0';
  $vistor_visa = isset($_POST['vistor_visa']) ? '1' : '0';
  $work_visa = isset($_POST['work_visa']) ? '1' : '0';
  $pr = isset($_POST['pr']) ? '1' : '0';

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

  $clientCategory = $_POST['client_category'];
  $data = array(
    "client_category" => $clientCategory,
    "name" => $_POST['c_name'],
    "phone" => $_POST['c_phone'],
    "account_email_id" => $_SESSION['account_email_id'],
    "email" => $_POST['c_email'],
    "address" => $_POST['address'],
    "companytype" => $_POST['companytype'],
    "companyowner" => $_POST['companyowner'],
    "companyownerphone" => $_POST['companyphone'],
    "companyowneremail" => $_POST['companyemail'],
    "committed" => $_POST['committed'],
    "reward" => $_POST['reward'],
    "credit_card" => $_POST['credit_card'],
    "benefits" => $_POST['benefits'],
    "forex" => $_POST['forex'],
    "source" => $_POST['source'],
    "other" => $_POST['fintech_otherservice'],
    "organization" => $_POST['organization'],
    "organization_phone" => $_POST['organization_phone'],
    "organization_email" => $_POST['organization_email'],
    "website" => $_POST['website'],
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
    "crm_users" => $_POST['crm_users'],

  );

  $validate = validateLead($_POST['organization'] ?? '', $_POST['c_phone'] ?? '', $_POST['id']);
  if ($validate) {
    http_response_code(500);
    $response = array(
      "error" => true,
      "message" => $validate
    );
    $jsonResponse = json_encode($response);
    echo $jsonResponse;
    exit;
  }
  if (isset($_FILES['contract_file']) && !empty($_FILES['contract_file'] && !empty($_FILES['contract_file']['name']))) {
    $uploaded = upload_pdf(['file' => $_FILES['contract_file']], 'contract');
    $contractFilename = $uploaded['filename'];
    $filePath = $uploaded['path'];
    $data['uploaded_contract_path'] = $filePath;
    $data['contract_filename'] = $contractFilename;
    $data['contracted_at'] = date('Y-m-d h:i:s');
  }
  if (isset($_FILES['organization_logo']) && !empty($_FILES['organization_logo'] && !empty($_FILES['organization_logo']['name']))) {
    $d = ['image' => $_FILES['organization_logo']];
    $uploadedLogo = upload_img($d, 'logo');
    $companylogo = $uploadedLogo['filename'];
    $companyLogoPath = $uploadedLogo['path'];
    $data['org_logo_path'] = $companyLogoPath;
    $data['org_logo_filename'] = $companylogo;
  }
  if (isset($_POST['availing_crm']) && !empty($_POST['availing_crm'])) {
    $availingCrmServices = $_POST['availing_crm'] == 'yes' ? 1 : 0;
    $data['availing_crm'] = $availingCrmServices;
  }
  if (isset($_POST['prefered_bank_name']) && !empty($_POST['prefered_bank_name'])) {
    $data['prefered_bank_name'] = $_POST['prefered_bank_name'];
  }
  if (isset($_POST['forex_commited'])) {
    $data['number_of_forex_commited'] = $_POST['forex_commited'];
  }
  $res = qupdate($param, $data, 0);
  $adminId = getAdminId();
  $username = userName($_SESSION['uId']);
  $userid = $_SESSION['uId'];
  $agreementId = $_POST['id'];

  // if(!empty($_POST['payment']) && $_POST['payment'] == 'Paid'){
  //   $planData = [
  //     'leadId' => $_POST['id'],
  //     'planId' => $_POST['plan_id'],
  //     'amountPaid' => $_POST['paid'],
  //     'givenDiscount' => $_POST['discount'],
  //     'paymentMode' => $_POST['mode'],
  //     'paymentType' => $_POST['type'],
  //     'fromDate' => $fromDate,
  //     'toDate' => $toDate,
  //     'remarks' => $_POST['remarks']
  //   ];
  //   $planData = array_merge($planData,$_FILES);
  //   (new LeadPaymentController())->store($planData);
  // }

  notify($adminId, "An agreement:$agreementId has been updated By $username:$userid", 'file', '/agreement', 'Agreement Updated');
  echo json_encode($res);
}

if ($action == 'getContractCount') {
  ini_set('display_errors', 1);
  $condition = ' where 1=1 ';
  $filterVar = $_POST['filter'];

  if (isset($filterVar) && !empty($filterVar)) {
    $array = json_decode($filterVar, true);
    if(!empty($array['contract'])){
      $contract = $array['contract'];
      if ($contract == 1 ) {
        $condition .= ' AND uploaded_contract_path IS NOT NULL ';
      } else {
        $condition .= ' AND uploaded_contract_path IS NULL ';
      }
    }else{
      $condition .= ' AND uploaded_contract_path IS NOT NULL OR uploaded_contract_path <> 0 OR uploaded_contract_path !=""';
    }
  }
  if ($_SESSION['uId'] == '12') {
    $condition .= " AND marketing_id <> 0 ";
  } else {
    $condition .= ' AND marketing_id="' . intval($_SESSION['uId']) . '" ';
  }
  $res = select(TABLE,' COUNT(id) as count',$condition,'','');
  $res = $res[0];
  echo json_encode(['statusCode' => 200, $res]);
}
