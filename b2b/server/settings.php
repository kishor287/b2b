<?

/*
User
*/
if ($action == 'logout') {
  unset($_SESSION['uId']);
  unset($_SESSION['uType']);

  // unset($_COOKIE['uId']); 
  // setcookie('uId', null, -1, '/'); 

  //   unset($_COOKIE['uType']); 
  // setcookie('uType', null, -1, '/');  

  $api = array("status" => 1, "msg" => "success");

  echo json_encode($api);
}



/*
login
*/
if ($action == 'login') {
  $username = str_replace('--', '**', trim($_POST['username'], "'"));
  $password = str_replace('--', '**', trim($_POST['password'], "'"));
  $param = array(
    "pagination" => "",
    "col" => "id, username, role,status",
    "tb" => "users",
    "where" => "WHERE username='" . $username . "' AND password='" . $password . "' AND status = 1",
    "limit" => "1"
  );
  $res = qselect($param);
  if ($res['count'] == 1 && $res['data']['username'] == $_POST['username']) {
    $_SESSION['uId'] = $res['data']['id'];
    $_SESSION['utype'] = $res['data']['role'];

    $api = array("status" => 1, "msg" => "success", "data" => "LoggedIn");
  } else {
    $api = array("status" => 0, "msg" => "success", "data" => "Invalid");
  }
  echo json_encode($api);
}


/*
login 
*/
if ($action == 'profile') {

  $param = array("pagination" => '', "col" => "*", "tb" => "users", "where" => " where id=" . $_SESSION['uId'], "limit" => "1");
  $res = qselect($param);
  echo json_encode($res);
}

/*
Navbar
*/

if ($action == 'navbar') {
  $param = array("pagination" => '', "col" => "navbar_id", "tb" => "navbar_assign", "where" => "where role_id=" . $_SESSION['utype'] . "", "limit" => "1000");
  $res = qselect($param);
  $nav_ids = array();

  foreach ($res['data'] as $v) {
    $nav_ids[] = $v['navbar_id'];
  }

  $filter = "";
  if ($_SESSION['utype'] != 1) {
    $filter .= "where id in (" . implode(', ', $nav_ids) . ") ";
  }
  $param = array("pagination" => '', "col" => "*", "tb" => "navbar", "where" => $filter, "limit" => "1000", "sort" => "order by sorting");
  $res = qselect($param);
  echo json_encode($res);
}

if($action == 'getRole'){
  echo json_encode(['role' => $_SESSION['utype']]);
}