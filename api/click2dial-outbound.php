<?php

	if($_GET['fpn']!=''){
    include('../crm/connection.php');

	if(isset($_GET['fpn'])){ $fpn=trim($_GET['fpn']); } else { $fpn=""; }
	if(isset($_GET['spn'])){ $spn=trim($_GET['spn']); } else { $spn=""; }
	if(isset($_GET['sbs'])){ $duration=gmdate("H:i:s",trim($_GET['sbs'])); } else { $duration=""; }
	if(isset($_GET['fps'])){ $fpn_status=rawurlencode($_GET['fps']); } else { $fpn_status=""; }
	if(isset($_GET['sps'])){ $spn_status=rawurlencode($_GET['sps']); } else { $spn_status=""; }
	if(isset($_GET['audio'])){ $recording=trim($_GET['audio']); } else { $recording=""; }
	$fpn = substr($fpn, 1); 
	date_default_timezone_set('Asia/Kolkata');
	$date = date("Y-m-d");

	
	$stmt = $DB_con->query("SELECT username,location FROM crm_counselor WHERE phone='$fpn' limit 1 "); 
	$t= $stmt->fetch(PDO::FETCH_ASSOC);
	$counselor=$t['username'];
	$location=$t['location'];

	$string=$_SERVER['QUERY_STRING'];
	$stmt = $DB_con->prepare("INSERT INTO crm_click2call_response (fpn,spn,fpn_status,spn_status,recording,counselor,location,created,duration,string)VALUES(:fpn,:spn,:fpn_status,:spn_status,:recording,:counselor,:location,:date,:duration,:string) ");
	
	$stmt->bindParam(':fpn',$fpn);
	$stmt->bindParam(':spn',$spn);
	$stmt->bindParam(':fpn_status',$fpn_status);
	$stmt->bindParam(':spn_status',$spn_status);
	$stmt->bindParam(':recording',$recording);
	$stmt->bindParam(':counselor',$counselor);
	$stmt->bindParam(':location',$location);
	$stmt->bindParam(':date',$date);
	$stmt->bindParam(':duration',$duration);
	$stmt->bindParam(':string',$string);
	if($stmt->execute()){ 
	    
	    
$DB_con->query('Insert into crm_agent_call (user,username,phone) values ("Counselor","'.$counselor.'","'.$spn.'") ');
echo "Success"; } else { echo  "Error"; } 

	} else { echo  "fpn empty"; } 