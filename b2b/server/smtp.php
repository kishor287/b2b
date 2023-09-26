<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

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
  $param = array("sort" => "order by id desc", "pagination" => $_POST['pagination'], "col" => "*", "tb" => "smtp", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
  $res = qselect($param);
  echo json_encode($res);

}

/*
Dir
*/
if ($action == 'dir') {

  $param = array("pagination" => "", "col" => "organization", "tb" => "smtp", "where" => "", "limit" => 10000);
  $res = qselect($param);
  $organization = array();
  foreach ($res['data'] as $d) {
    $organization[] = $d['organization'];
  }

  $param = array("pagination" => "", "col" => "organization", "tb" => "registration", "where" => "", "limit" => 10000);
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
Save
*/
if ($action == 'save') {


  /*
Data Insert
*/
  $param = array("tb" => "smtp");
  $data = array(
    "organization" => $_POST['organization'],
    "p_host" => $_POST['p_host'],
    "p_email" => $_POST['p_email'],
    "p_pass" => $_POST['p_pass'],
    "p_type" => $_POST['p_type'],
    "p_port" => $_POST['p_port'],
    "s_host" => $_POST['s_host'],
    "s_email" => $_POST['s_email'],
    "s_pass" => $_POST['s_pass'],
    "s_type" => $_POST['s_type'],
    "s_port" => $_POST['s_port'],
  );


  $res = qinsert($param, $data);
  echo json_encode($res);
}

/*
Remove
*/

if ($action == 'remove') {
  $param = array("col" => "", "tb" => "smtp", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
  $res = qdelete($param, '', 1);
  echo json_encode($res);
}



if ($action == 'update') {

  /*
Data Update
*/
  $param = array("tb" => "smtp", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
  $data = array(
    "p_host" => $_POST['p_host'],
    "p_email" => $_POST['p_email'],
    "p_pass" => $_POST['p_pass'],
    "p_type" => $_POST['p_type'],
    "p_port" => $_POST['p_port'],

    "s_host" => $_POST['s_host'],
    "s_email" => $_POST['s_email'],
    "s_pass" => $_POST['s_pass'],
    "s_type" => $_POST['s_type'],
    "s_port" => $_POST['s_port'],

  );


  $res = qupdate($param, $data, 0);
  echo json_encode($res);
}





/*
Test Connection
*/
if ($action == 'test_connection') {
  
  $param = array("pagination" => "", "col" => "*", "tb" => "smtp", "where" => "where id=" . $_POST['id'], "limit" => 1);
  $res = qselect($param);
  $type = ($_POST['type'] == 'p_test') ? 'p' : 's';
  $mail['host'] = $res['data'][$type . '_host'];
  $mail['email'] = $res['data'][$type . '_email'];
  $mail['pass'] = $res['data'][$type . '_pass'];
  $mail['encryptt'] = $res['data'][$type . '_type'];
  $mail['port'] = $res['data'][$type . '_port'];
  //print_r($mail);
  $res = test_mailer($mail);
//   echo '<pre>';
//   print_r($res);
   //die;
  if ($res == 1) {
    $result = array("status" => 1, "data" => "Connected Successfully", "msg" => 'success');
  } else {
    $result = array("status" => 0, "data" => $res, "msg" => 'error');
  }
  echo json_encode($result);
}