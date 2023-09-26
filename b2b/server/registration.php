<?php

include('../_con.php');

ini_set('display_errors', 1);
/*
Save
*/
if ($action == 'get') {
  $filter = 'where 1=1';
  if (isset($_POST['filter']) && !empty($_POST['filter'])) {
    $array = json_decode($_POST['filter'], true);
    if (isset($array['id']) && ($array['id'] != '')) {
      $filter .= " and id='" . $array['id'] . "'  ";
    }
  }
  $param = array("sort" => "order by id desc", "pagination" => $_POST['pagination'], "col" => "*", "tb" => "registration", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
  $res = qselect($param);
  echo json_encode($res);
}
/*
AllAgreement
*/
if ($action == 'allagreements') {


  $param = array("pagination" => "", "col" => "organization", "tb" => "agreements", "where" => "", "limit" => 10000);
  $res = qselect($param);
  $dirs = array();
  foreach ($res['data'] as $d) {
    $dirs[] = $d['organization'];
  }

  $api = array("status" => 1, "msg" => "success", "data" => $dirs);
  echo json_encode($api);
}


/*
Agreement
*/
if ($action == 'agreements') {

  $param = array("pagination" => "", "col" => "organization", "tb" => "registration", "where" => "", "limit" => 10000);
  $res = qselect($param);
  $organization = array();
  foreach ($res['data'] as $d) {
    $organization[] = $d['organization'];
  }

  $param = array("pagination" => "", "col" => "organization", "tb" => "agreements", "where" => "", "limit" => 10000);
  $res = qselect($param);
  $dirs = array();
  foreach ($res['data'] as $d) {
    $dirs[] = $d['organization'];
  }

  $data = array();
  foreach ($dirs as $d) {
    if (!in_array($d, $organization)) {
      $data[] = $d;
    }
  }
  $api = array("status" => 1, "msg" => "success", "data" => $data);
  echo json_encode($api);
}


/*
Dir
*/
if ($action == 'dir') {

  $param = array("pagination" => "", "col" => "sub_domain", "tb" => "registration", "where" => "", "limit" => 10000);
  $res = qselect($param);
  $sub_domain = array();
  foreach ($res['data'] as $d) {
    $sub_domain[] = '../../' . $d['sub_domain'];
  }

  $dirs = array_filter(glob('../../*'), 'is_dir');
  if (($key = array_search('../../control-panel', $dirs)) !== false) {
    unset($dirs[$key]);
  }
  if (($key = array_search('../../feedback', $dirs)) !== false) {
    unset($dirs[$key]);
  }
  if (($key = array_search('../../website', $dirs)) !== false) {
    unset($dirs[$key]);
  }
  if (($key = array_search('../../crm', $dirs)) !== false) {
    unset($dirs[$key]);
  }
  if (($key = array_search('../../backup', $dirs)) !== false) {
    unset($dirs[$key]);
  }
  if (($key = array_search('../../team-panel', $dirs)) !== false) {
    unset($dirs[$key]);
  }

  $data = array();
  foreach ($dirs as $d) {
    if (!in_array($d, $sub_domain)) {
      $data[] = $d;
    }
  }
  $api = array("status" => 1, "msg" => "success", "data" => $data);
  echo json_encode($api);
}


/*
Save
*/
if ($action == 'save') {
  if ($_FILES['logo']['name'] != '') {
    $file['image'] = $_FILES['logo'];
    $file = storeImg($file, 'logo');

    if ($file['status'] == 0) {
      echo json_encode($file);
      exit();
    }
  } else {
    $file['data'] = '';
  }
  $param = array("tb" => "registration");
  $data = array(
    "sub_domain" => $_POST['sub_domain'],
    "organization" => $_POST['organization'],
    "phone" => $_POST['phone'],
    "email" => $_POST['email'],
    "website" => $_POST['website'],
    "logo" => $file['data'],
    "uid" => $_POST['uid'],
    "qr_code" => $_POST['qrdata'],
  );
  $emailId = $_POST['email']?? 'info@innerxcrm.com';
  $subject = "Login Credentials for your CRM";
  $content = file_get_contents('../view/email-templates/register.html');
  $content = str_replace('{{company_name}}', $_POST['organization'], $content);
  $content = str_replace('{{url}}', 'https://' . $_POST['sub_domain'] . ".innerxcrm.com", $content);
  $content = str_replace('{{username}}', 'admin', $content);
  $content = str_replace('{{password}}', 'innerx@77', $content);

  // send mail to organization with credentials
  sendMail($emailId,['info@innerxcrm.com','training@innerxcrm.com','gurpreet@innerxcrm.com'],$subject,$content);
  
  $res = qinsert($param, $data);
  echo json_encode($res);
}

/*
Remove
*/

if ($action == 'remove') {
  $select = select('cp_registration','*'," where id=" . $_POST['id'] . "",'',' limit 1 ');
  logMessage('Deleted Organization by IP: '. getClientIP() .' Organization Details :'.json_encode($select[0]));
  $param = array("col" => "", "tb" => "registration", "where" => " where id=" . $_POST['id'] . " ", "limit" => "1");
  $res = qdelete($param, '');
  echo json_encode($res);
}



if ($action == 'update') {

  if ($_FILES['logo']['name'] != '') {
    $file['image'] = $_FILES['logo'];
    // $file = upload_img(['image' => $file], 'logo');
    $file = storeImg($file, 'logo');
    if ($file['status'] == 0) {
      echo json_encode($file);
      exit();
    }
  } else {
    $file['data'] = '';
  }
 
  $param = array("tb" => "registration", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
  $data = array(
    "organization" => $_POST['organization'],
    "phone" => $_POST['phone'],
    "email" => $_POST['email'],
    "website" => $_POST['website'],
    "logo" => $file['data'],
  );

  if (is_null($data['logo']) || $data['logo'] == '') {
    unset($data['logo']);
  }

  $res = qupdate($param, $data, 0);
  echo json_encode($res);
}

if ($action == 'generateQrCode') {
  $qrCodeData = $_POST['qrdata'];
  $uid = $_POST['uid'];
  $update = update('cp_registration', ['qr_code' => $qrCodeData], 'uid="' . $uid . '"', false);
  http_response_code(200);
  echo json_encode(['statusCode' => 200, 'message' => 'Qr Code Generated']);
  exit;
}

if ($action == 'getUid') {
  $id = $_POST['id'];
  $uid = $_POST['uid'];
  $result = select('cp_registration', 'uid,organization', 'where id="' . $id . '"', '', '');
  $organizationName = str_replace(' ', '_', $result[0]['organization'] ?: '_');
  $uid = $uid . '_' . $organizationName;
  if (!empty($result[0]['uid'])) {
    http_response_code(200);
    echo json_encode(['uid' => $result[0]['uid'], 'statusCode' => 200, 'message' => 'Uid Generated']);
    exit;
  } else {
    $data = ['col' => ['uid' => $uid]];
    $update = update('cp_registration', ['uid' => $uid], ' id="' . $id . '"', false);
    if ($update) {
      http_response_code(200);
      echo json_encode(['statusCode' => 200, 'uid' => $uid, 'message' => 'Uid Generated']);
      exit;
    } else {
      http_response_code(500);
      echo json_encode(['statusCode' => 500, 'message' => 'Failed to generate Uid']);
    }
  }
}
