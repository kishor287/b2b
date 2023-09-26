<?php

	if($_GET['customer']!=''){
    include('../crm/connection.php');

	if(isset($_GET['customer'])){ $fpn=trim($_GET['customer']); } else { $fpn=""; }
	if(isset($_GET['counselor'])){ $spn=trim($_GET['counselor']); } else { $spn=""; }
	if(isset($_GET['duration'])){ $duration=gmdate("H:i:s",trim($_GET['duration'])); } else { $duration=""; }
	if(isset($_GET['customer_status'])){ $fpn_status=rawurlencode($_GET['customer_status']); } else { $fpn_status=""; }
	if(isset($_GET['counselor_status'])){ $spn_status=rawurlencode($_GET['counselor_status']); } else { $spn_status=""; }
	if(isset($_GET['audio'])){ $recording=trim($_GET['audio']); } else { $recording=""; }
	$spn = substr($spn, 1); 
	date_default_timezone_set('Asia/Kolkata');
	$date = date("Y-m-d");
	$counselor=$location='';
	
	$stmt = $DB_con->query("SELECT username,location FROM crm_counselor WHERE phone='$spn' limit 1 "); 
	$get= $stmt->fetch(PDO::FETCH_ASSOC);
	if($stmt->rowCount()>0){
	$counselor=$get['username'];
	$location=$get['location'];
	}
	
	$string=	$_SERVER['QUERY_STRING'];
	$stmt = $DB_con->prepare("INSERT INTO crm_click2call_response_inbound (fpn,spn,fpn_status,spn_status,recording,counselor,location,created,duration,string)VALUES(:fpn,:spn,:fpn_status,:spn_status,:recording,:counselor,:location,:date,:duration,:string) ");
	
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
	    

	    
	    echo "Success"; } else { echo  "Error"; } 

	unset($DB_con);
	unset($stmt);
	
	} else { echo  "Customer No. Empty"; } 