<?php
 ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
 
 $POST = file_get_contents('php://input');
 $_POST = json_decode($POST,true);
 
//print_r($_POST);
//exit(); 

if((isset($_POST['phone']))&&($_POST['phone']!='')){
include('../crm/connection.php'); 


$name=(isset($_POST['name']))&&($_POST['name']!='')?$_POST['name']:'';
$phone=(isset($_POST['phone']))&&($_POST['phone']!='')?$_POST['phone']:'';
$service=(isset($_POST['service']))&&($_POST['service']!='')?$_POST['service']:'';
$country=(isset($_POST['country']))&&($_POST['country']!='')?$_POST['country']:'';
$branch=(isset($_POST['branch']))&&($_POST['branch']!='')?$_POST['branch']:'';
$branch_city=(isset($_POST['branch_city']))&&($_POST['branch_city']!='')?$_POST['branch_city']:'';
$booking_time=(isset($_POST['booking_time']))&&($_POST['booking_time']!='')?$_POST['booking_time']:'';

$stmt = $DB_con->prepare('Insert into crm_leads (name,phone,category,country,location2,location,given_date) values(:name,:phone,:service,:country,:branch,:branch_city,:booking_time)');
$stmt->bindParam(':name', $name);
$stmt->bindParam(':phone', $phone);
$stmt->bindParam(':service', $service);
$stmt->bindParam(':country', $country);
$stmt->bindParam(':branch', $branch);
$stmt->bindParam(':branch_city', $branch_city);
$stmt->bindParam(':booking_time', $booking_time);

if($stmt->execute()){
$msg='lead inserted succesfully.'; $code='succces';  $status='200'; 
}else{
$msg='some error occurred.'; $code='error';  $status='200'; 	
}
}else{
	$msg='phone number missing.'; $code='missing';  $status='400'; 	
}


$values = array("status"=>$status,"msg"=>$msg,"code"=>$code);
$result =array("response"=>$values);
echo json_encode($result);	