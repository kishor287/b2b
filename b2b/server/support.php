<?
 require("../var.php");
//   require("plugins/mailer/class.phpmailer.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($action == 'get_tickets') {
  $param = array(
    "pagination" => $_POST['pagination'],
    "col" => "*",
    "tb" => "tickets",
    "where" => "",
    "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']
  );
  $res = qselect($param);

  $params = array(
    "pagination" => $_POST['pagination'],
    "col" => "*",
    "tb" => "users",
    "where"=> "where role = '18'", 
    "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']
  );
  $res2 = qselect($params);

  $response = array(
    "tickets" => $res,
    "users" => $res2,
    "total" => count($res),
  );

  echo json_encode($response);
  
  
  
//   $data="&subject=".$_POST['subject']."&chat=".$_POST['chat']."&ticket_status=".$_POST['ticket_status']."&sub_domain=" .$sub_domain[0]."&ticket_action=".$_POST['ticket_action']." ";
}

  
  if ($_POST['_action'] == 'update') {
    $ticketId = $_POST['ticket_id'];
    $developerName = $_POST['developer_name'];
    $ticket_status = $_POST['ticket_status'];
    $to = $_POST['developerEmail'];
    $subject = 'Assigned Ticket Notification';
    $content = 'You have been assigned '.$ticket_status.' ticket with ID: ' . $ticketId . '  Please check this as soon as possible .';
    
    $param = array(
        "tb" => "tickets",
        "where" => "where ticket_id=" . $ticketId,
        "limit" => "1"
    );

    $data = array(
        "developer_name" => $developerName,
        "ticket_status" => $ticket_status,
    );
    
    $res = qupdate($param, $data, 0);
    echo json_encode($res);
  if ($ticket_status != 'Closed') {
        $data = array(
            'to' => $to,
            'subject' => $subject,
            'content' => $content
        );

        test2_mailer($data);
    }
}


// if ($_POST['_action'] == 'reply') {
    
//   $ticketId = $_POST['ticket_id'];
//   $reply = $_POST['reply'];

//   $param = array(
//     "tb" => "tickets",
//     "where" => "where ticket_id=" . $ticketId,
//     "limit" => "1"
//   );

//   $data = array(
//     "reply" => $reply
//   );

//   $res = qupdate($param, $data, 0);

//  echo json_encode($res);
// }


if (isset($_POST['_action']) && $_POST['_action'] === 'reply') {
  $ticketId = $_POST['ticket_id'];
  $reply = $_POST['reply'];

  $param = array(
    "tb" => "reply"
  );

  $data = array(
    array(
      "ticket_id" => $ticketId,
      "reply" => $reply
    )
  );

  $res = qminsert($param, $data);

  echo json_encode($res);
}



//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $url);
//   curl_setopt($ch, CURLOPT_POST, 1);
//   curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//   $server_output = curl_exec($ch);
  
//   echo $server_output;



