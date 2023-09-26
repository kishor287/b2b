<? 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


if(isset($cp_action)){
$sub_domain = explode('.', $_SERVER['HTTP_HOST']);
$sub_domain[0] = 'beta';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://api.innerxcrm.com/v1/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"_action=".$cp_action."&sub_domain=".$sub_domain[0]."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close($ch);
$cp = json_decode($server_output,true);
}

if(isset($_POST['request'])){
 $param='';   
  $param.=(isset($_POST['id']))?',&id='.$_POST['id']:'';
 
$sub_domain = explode('.', $_SERVER['HTTP_HOST']);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://api.innerxcrm.com/v1/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"_action=".$_POST['request']."&sub_domain=".$sub_domain[0]."".$param);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = json_decode(curl_exec($ch),true);  


if(isset($_POST['action']) && $_POST['action']=='rate_api'){


/*
if($api!=''){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch),true);
curl_close($ch);
$data=round($response['data']['INR']['value']/$response['data']['CAD']['value'],2);
}*/


}

echo json_encode(array("status"=>1,"data"=> $server_output['data'][0]));


}
//APi
 if (isset($_POST['action']) && ($_POST['action']=='ticket')) {
 
   if (isset($_POST['ticket_action']) && ($_POST['ticket_action']=='create')){

 $params="action=".$_POST['action']."&subject=".$_POST['subject']."&chat=".$_POST['chat']."&ticket_domain_id=".$_POST['ticket_id']."&ticket_status=".$_POST['ticket_status']."&sub_domain=" .$sub_domain[0]."&ticket_action=".$_POST['ticket_action']." ";
  $url ="https://api.innerxcrm.com/ticket_handling/tickets.php?key=create_ticket_5923b6b208db11eebe560242ac120002";
    }
    print_r($params); 
    if (isset($_POST['ticket_action']) && ($_POST['ticket_action']=='reply')) {
    $params=
    "action=".$_POST['ticket_action']."&"."ticket_id=".$_POST['ticket_id']."&"."chat=".$_POST['chat']."&"."sub_domain=" . $sub_domain[0] . "
    ";
     $url ="https://api.innerxcrm.com/ticket_handling/tickets.php?key=reply_ticket_5923b34208db11eebe560242ac120002";
    
    }
    
  //    if(isset($_POST['ticket_action']) && ($_POST['ticket_action']=='reopen')) {
  //   $params=
  //   "action=".$_POST['ticket_action']."&
  //   sub_domain=" . $sub_domain[0] . "
  //   ";
  //   }

  //  if(isset($_POST['ticket_action']) && ($_POST['ticket_action']=='close')) {
  //   $params=
  //   "action=".$_POST['ticket_action']."&
  //   sub_domain=" . $sub_domain[0] . "
  //   ";
    //}=

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $server_output = curl_exec($ch);
  
  echo $server_output;
     

//   echo json_encode(array("status" => 1, "data" => $server_output['data']));

} 
