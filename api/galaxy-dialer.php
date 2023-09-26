<?php 
if((isset($_GET['phone']))&&(($_GET['phone']!=''))){
include('../crm/connection.php');

date_default_timezone_set("Asia/Calcutta");
$date_time=date('Y-m-d H:i:s');
$c_date=date('Y-m-d');


$string=$_SERVER['QUERY_STRING'];

if(isset($_GET['followup_date'])){ $followup_date_g=$_GET['followup_date']; }else{ $followup_date_g=''; }
if(isset($_GET['sub_status'])){ $sub_status=$_GET['sub_status']; }else{ $sub_status=''; }
if(isset($_GET['audio'])){ $recording=$_GET['audio']; }else{ $recording=''; }
if(isset($_GET['prefer_intake'])){ $prefer_intake=substr($_GET['prefer_intake'],0, 4); }else{ $prefer_intake=''; }
if(isset($_GET['status'])){ $status=$_GET['status']; }else{ $status=''; }
if(isset($_GET['remarks'])){ $remarks=$_GET['remarks']; }else{ $remarks=''; }
if(isset($_GET['branch'])){ $branch=$_GET['branch']; }else{ $branch=''; }
if(isset($_GET['exptd_walkins_date'])){ $exptd_walkins_date=substr($_GET['exptd_walkins_date'],0, 10); }else{ $exptd_walkins_date=''; }
if(isset($_GET['passing_year'])){ $passing_year=substr($_GET['passing_year'],0, 4); }else{ $passing_year=''; }
if(isset($_GET['exptd_sale_date'])){ $exptd_sale_date=substr($_GET['exptd_sale_date'],0, 10); }else{ $exptd_sale_date=''; }
if(isset($_GET['user'])){ $user=$_GET['user']; }else{ $user=''; }
if(isset($_GET['name'])){$fullname=ucwords($_GET['name']);}else{ $fullname=''; }
if(isset($_GET['source'])){$source=ucwords($_GET['source']);}else{ $source=''; }
if(isset($_GET['over_all'])){ $over_all=str_replace(' ', '.', trim($_GET['over_all'])); }else{ $over_all=''; }
if(isset($_GET['minimum'])){ $minimum=str_replace(' ', '.', trim($_GET['minimum'])); }else{ $minimum=''; }
if(isset($_GET['pte'])){ $pte=str_replace(' ', '-', trim($_GET['pte'])); }else{ $pte=''; }
if(isset($_GET['sib_age'])){ $sib_age=$_GET['sib_age']; }else{ $sib_age=''; }
if(isset($_GET['sib_name'])){ $sib_name=$_GET['sib_name']; }else{ $sib_name=''; }
$followup_date=substr($followup_date_g,0, 10);
$followup_time= substr($followup_date_g, -5);
	 
	 
	if(isset($_GET['source2'])){  
	$source2=str_replace('%20', ' ',$_GET['source2']); 
	$arrm = explode("|", $source2);
	if(isset($arrm[0]) && !empty($arrm[0])){   $f_source2 = $arrm[0];   }else{ $f_source2='Direct'; }  
	if(isset($arrm[1]) && !empty($arrm[1]) ){   $s_source2 = $arrm[1];    }else{ $s_source2=$source; } 
	if(isset($arrm[2])){   $lead_camp = $arrm[2];    }else{ $lead_camp=''; }  
  
	}else{ 
	$f_source2='Direct';
	$s_source2 =$source;
	$lead_camp ='';
	$source2= ''; 
	}
	
  

 $agent_id=0;
$stmt = $DB_con->query('SELECT agent_id,location,fname from crm_agent where username="'.$user.'" and status="Active" limit 1');
$av =$stmt->fetch(PDO::FETCH_ASSOC);
$agent_id=$av['agent_id'];
$agent_location=$av['location'];
$fname=ucwords($av['fname']);
 
 $DB_con->query('UPDATE crm_agent set on_call=0 WHERE username="'.$user.'" ');


 
/////////////////////////visa////////////////////////////////////////
if((isset($_GET['phone']))&&($_GET['phone']!='')){
    
if((isset($_GET['services']))&&(($_GET['services']=='StudyVisa'))){
    
    



$stmt = $DB_con->query('SELECT visa_form_id,counselor_id,followup_counselour from crm_visa_form where phone="'.$_GET['phone'].'" or rphone="'.$_GET['phone'].'" limit 1');
$counts = $stmt->rowCount();
$x =$stmt->fetch(PDO::FETCH_ASSOC);

if($counts == 0 ){

$counselor_id=0; 
$username=''; 



if($sub_status=='RNG' || $sub_status=='NW'){
$stmt = $DB_con->query("SELECT template_name from crm_whatsapp_master where is_active=1 and place='Call Not Answer' limit 1");
$f =$stmt->fetch(PDO::FETCH_ASSOC);
if($stmt->rowCount()>0){

$param=array("name"=>$fullname);

include('../crm/whatsapp_function.php');
WA($fullname,$_GET['phone'],$f['template_name'],$param);
}
}
    
    
if(($sub_status=='INT') || ($sub_status=='PTV') || ($sub_status=='WVL') ){
$stmt = $DB_con->query("SELECT o.counselor,c.counselor_id FROM crm_online_users as o inner join crm_counselor as c on o.counselor=c.username WHERE o.counselor!='' AND o.date='$c_date' AND o.location='$branch' AND c.dept = 'visa' AND timestamp BETWEEN timestamp(DATE_SUB(NOW(), INTERVAL 30 SECOND)) AND timestamp(NOW()) order by rand() limit 1");
$t =$stmt->fetch(PDO::FETCH_ASSOC);
$counselor_id=$t['counselor_id']; 
$username=$t['counselor'];

 if($username!=''){
     
$stmt = $DB_con->query("SELECT template_name from crm_whatsapp_master where is_active=1 and place='Counselor Assigned' limit 1");
$f =$stmt->fetch(PDO::FETCH_ASSOC);
if($stmt->rowCount()>0){
$param=array("name"=>$fullname,"counselor"=>$username);
include('../crm/whatsapp_function.php');
WA($fullname,$_GET['phone'],$f['template_name'],$param);
}


$stmt = $DB_con->query("SELECT subject,template_name,tags from crm_email_master where is_active=1 and place='Counselor Assigned' limit 1");
$f =$stmt->fetch(PDO::FETCH_ASSOC);
if($stmt->rowCount()>0 && isset($_GET['email']) && $_GET['email']!=''){
include('../crm/email_function.php');
$param=array("name"=>$fullname,"counselor"=>$username);
EM($f['subject'],$_GET['email'],$f['template_name'],$param);
}




}


if((!isset($username))||($username=='')){    $counselor_id=0; $username='';  }
}

	
	
	
	
 $followup_by='Agent';
 $stmt = $DB_con->prepare("INSERT INTO crm_visa_form (s_status,min_score,stream,last_qual,passingyear,status,country	,name,email,phone,rphone,score,followup_counselour,followup_date2,remarks,prefer_city,followup_time2,agent_id,resi_city,intakeyear,resultdate,pte,typeofexam,percentage,data_type,ielts_from,ielts_reason,heared_from,datenew,counselor_id,created,score_type,expected_walkin,expected_payment,followup_by2,string,followup_date_time2,source2,sub_source2,lead_camp)
 VALUES (:s_status,:min_score,:stream,:last_qual,:passingyear,:status,:country	,:name,:email,:phone,:rphone,:score,:followup_counselour,:followup_date,:remarks,:prefer_city,:followup_time,:agent_id,:resi_city,:intake,:resultdate,:pte,:typeofexam,:percentage,:data_type,:ielts_from,:ielts_reason,:heared_from,:datenew,:counselor_id,:created,:score_type,:expected_walkin,:expected_payment,:followup_by,:string,:followup_date_time2,IF('$f_source2' = '', NULL, '$f_source2'),IF('$s_source2' = '', NULL, '$s_source2'),IF('$lead_camp' = '', NULL, '$lead_camp'))");	
 
    $stmt->bindParam(':expected_walkin',$exptd_walkins_date);
	$stmt->bindParam(':expected_payment',$exptd_sale_date);
    $stmt->bindParam(':score_type',$_GET['test_given']);
	$stmt->bindParam(':s_status',$status);
	$stmt->bindParam(':min_score',$minimum);
	$stmt->bindParam(':stream',$_GET['stream']);
	$stmt->bindParam(':last_qual',$_GET['last_qualification']);
	$stmt->bindParam(':passingyear',$passing_year);
	$stmt->bindParam(':status',$sub_status);
	$stmt->bindParam(':country',$_GET['prefer_country']);
	$stmt->bindParam(':name',$fullname);
	$stmt->bindParam(':email',$_GET['email']);
	$stmt->bindParam(':phone',$_GET['phone']);
	$stmt->bindParam(':rphone',$_GET['alt_number']);
	$stmt->bindParam(':score',$over_all);
	$stmt->bindParam(':followup_counselour',$username);
	$stmt->bindParam(':followup_date',$followup_date);
	$stmt->bindParam(':remarks',$remarks);
	$stmt->bindParam(':prefer_city',$branch);
	$stmt->bindParam(':followup_time',$followup_time);
	$stmt->bindParam(':agent_id',$agent_id);
	$stmt->bindParam(':resi_city',$_GET['resi_city']);
	$stmt->bindParam(':intake',$prefer_intake);
	$stmt->bindParam(':resultdate',$_GET['result_date']);
	$stmt->bindParam(':pte',$pte);
		$stmt->bindParam(':typeofexam',$_GET['type_exam']);
			$stmt->bindParam(':percentage',$_GET['percentage']);
				$stmt->bindParam(':data_type',$_GET['data_type']);
					$stmt->bindParam(':ielts_from',$_GET['ielts_institute']);
						$stmt->bindParam(':ielts_reason',$_GET['why_from']);
								$stmt->bindParam(':heared_from',$_GET['heard_abut_daffodils']);
									$stmt->bindParam(':datenew',$c_date);
										$stmt->bindParam(':counselor_id',$counselor_id);
										$stmt->bindParam(':followup_by',$followup_by);
										$stmt->bindParam(':followup_date_time2',$followup_date_g);
											$stmt->bindParam(':created',$c_date);
											$stmt->bindParam(':string',$string);
												
						
    $stmt->execute();
	$last_id = $DB_con->lastInsertId();
    $type="Visa";
    
   
    
    if($_GET['branch']!=''){
      $stmt = $DB_con->query('SELECT address,tid from crm_branch where branch_name="'.trim($_GET['branch']).'"  limit 1');
      $countss = $stmt->rowCount();
      $aa =$stmt->fetch(PDO::FETCH_ASSOC);  
      if($countss==1){
          $tid=$aa['tid'];
          $sms=$aa['address'];
          $phone=$_GET['phone'];
         // include("sms.php");
      }
    }
    
	
$stmt = $DB_con->prepare("INSERT INTO crm_remark (remark,status,sub_status,next_to_follow,agent,form_type,form_id,date,time,recording)
VALUES (:remark,:status,:sub_status,:next_to_follow,:agent,:form_type,:form_id,:date,:time,:recording) ");
$stmt->bindParam(':remark',$remarks);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':sub_status',$status);
$stmt->bindParam(':next_to_follow',$followup_date);
$stmt->bindParam(':agent',$user);
$stmt->bindParam(':form_type',$type);
$stmt->bindParam(':form_id',$last_id);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':time',$followup_time);
$stmt->bindParam(':recording',$recording);
$stmt->execute();




if((isset($_GET['sib_phone']))&&($_GET['sib_phone']!='')){	 
$DB_con->query('INSERT into crm_siblings_detail (form_type,form_id,silbing_name,silbing_age,silbing_phone) VALUES
("Visa","'.$last_id.'","'.$sib_name.'","'.$sib_age.'","'.$_GET['sib_phone'].'")');	
}


$DB_con->query('INSERT INTO crm_alerts (user,username,type,alert,link,date,location,icon)
values ("manager","","Lead","Agent '.ucwords($user).' created new visa lead.","visa.php?visa_form_id='.$last_id.'&tab=main","'.$date_time.'","'.$branch.'","fa fa-wpforms"),
("counselor","'.$username.'","Lead","Agent '.ucwords($user).' assigned new visa lead.","visa.php?visa_form_id='.$last_id.'&tab=main","'.$date_time.'","'.$branch.'","fa fa-wpforms")
');

$DB_con->query('UPDATE crm_leads set removed=1 WHERE phone="'.$_GET['phone'].'" and category="Visa" ');
 
echo "success";


}else{

////////////////Already present Visa//////////////////////////

	$last_id = $x['visa_form_id'];
    $type="Visa";
	
	
	
	
$DB_con->query('UPDATE crm_remark SET done="1" WHERE form_id="'.$last_id .'" AND form_type="Visa" AND counselor="Na" ');

if($x['counselor_id']==0 && $x['followup_counselour']=="" ) {
	
if(($sub_status=='INT') || ($sub_status=='PTV') || ($sub_status=='WVL') ){
$stmt = $DB_con->query("SELECT o.counselor,c.counselor_id FROM crm_online_users as o inner join crm_counselor as c on o.counselor=c.username WHERE o.counselor!='' AND o.date='$c_date' AND o.location='$branch' AND c.dept = 'visa' AND timestamp BETWEEN timestamp(DATE_SUB(NOW(), INTERVAL 30 SECOND)) AND timestamp(NOW()) order by rand() limit 1");
$t =$stmt->fetch(PDO::FETCH_ASSOC);
$counselor_id=$t['counselor_id']; 
$username=$t['counselor']; 
}
if(!isset($username)||$username=='' || !isset($counselor_id)|| $counselor_id==''  ){    $counselor_id=0; $username='';  }
$exter='counselor_id="'.$counselor_id.'",followup_counselour="'.$username.'",';
}else{
$exter='';	
}


    

$stmt = $DB_con->prepare('UPDATE crm_visa_form SET '.$exter.'  expected_payment=:expected_payment,expected_walkin=:expected_walkin,s_status=:s_status,status=:status,followup_date2=:followup_date,remarks=:remarks,followup_time2=:followup_time,followup_by2="Agent",followup_date_time2=:followup_date_time2 WHERE visa_form_id="'.$last_id.'" ');
   
	$stmt->bindParam(':status',$sub_status); 
    $stmt->bindParam(':s_status',$status);
	$stmt->bindParam(':followup_date',$followup_date);
	$stmt->bindParam(':remarks',$remarks);
	$stmt->bindParam(':followup_time',$followup_time);
    $stmt->bindParam(':followup_date_time2',$followup_date_g);
    $stmt->bindParam(':expected_walkin',$exptd_walkins_date);
    $stmt->bindParam(':expected_payment',$exptd_sale_date);

	$stmt->execute();




	
	
	
$stmt = $DB_con->prepare("INSERT INTO crm_remark (remark,status,sub_status,next_to_follow,agent,form_type,form_id,date,time,recording)
VALUES (:remark,:status,:sub_status,:next_to_follow,:agent,:form_type,:form_id,:date,:time,:recording) ");
$stmt->bindParam(':remark',$remarks);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':sub_status',$status);
$stmt->bindParam(':next_to_follow',$followup_date);
$stmt->bindParam(':agent',$user);
$stmt->bindParam(':form_type',$type);
$stmt->bindParam(':form_id',$last_id);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':time',$followup_time);
$stmt->bindParam(':recording',$recording);
if($stmt->execute()){ echo "success"; }



//////////////////////////////////////////
}}

/////////////////////////Ielts////////////////////////////////////////
elseif((isset($_GET['services']))&&($_GET['services']=='Ielts')){
	
if(isset($_GET['course'])){ $course=$_GET['course']; }else{ $course=''; }
if(isset($_GET['name'])){ $name=$_GET['name']; }else{ $name=''; }
if(isset($_GET['phone'])){ $phone=$_GET['phone']; }else{ $phone=''; }
if(isset($_GET['resi_city'])){ $city=$_GET['resi_city']; }else{ $city=''; }
if(isset($_GET['last_qualification'])){ $last_qualification=$_GET['last_qualification']; }else{ $last_qualification=''; }
if(isset($_GET['email'])){ $email_id=$_GET['email']; }else{ $email_id=''; }
if(isset($_GET['alt_number'])){ $alt_number=$_GET['alt_number']; }else{ $alt_number=''; } 
if(isset($_GET['followup_time'])){ $followup_time=$_GET['followup_time']; }else{ $followup_time=''; } 


$stmt = $DB_con->query('SELECT ielts_form_id from crm_ielts_form where phone="'.$_GET['phone'].'" or rphone="'.$_GET['phone'].'" limit 1');
$counts = $stmt->rowCount();
$x =$stmt->fetch(PDO::FETCH_ASSOC);

if($counts == 0 ){





$counselor_id=0; 
$username='';; 

if(($sub_status=='INT') || ($sub_status=='PTV') || ($sub_status=='WVL') ){
$stmt = $DB_con->query("SELECT o.counselor,c.counselor_id FROM crm_online_users as o inner join crm_counselor as c on o.counselor=c.username WHERE o.counselor!='' AND o.date='$c_date' AND o.location='$branch' AND c.dept = 'ielts' AND timestamp BETWEEN timestamp(DATE_SUB(NOW(), INTERVAL 30 SECOND)) AND timestamp(NOW()) order by rand() limit 1");
$t =$stmt->fetch(PDO::FETCH_ASSOC);
$counselor_id=$t['counselor_id']; 
$username=$t['counselor']; 
if((!isset($username))||($username=='')){    $counselor_id=0; $username='';  }
}
	

$stmt = $DB_con->prepare("INSERT INTO crm_ielts_form (name,phone,city,qual,email,prefer_city,s_status,status,date,followup_time,remarks,course,rphone,agent_id,followup_counselour,counselor_id,datenew,location2,created,expected_walkin,expected_payment,source2,sub_source2,lead_camp) VALUES (:name,:phone,:city,:qual,:email,:prefer_city,:s_status,:status,:date,:followup_time,:remarks,:course,:rphone,:agent_id,:followup_counselour,:counselor_id,:datenew,:location2,:created,:expected_walkin,:expected_payment,IF('$f_source2' = '', NULL, '$f_source2'),IF('$s_source2' = '', NULL, '$s_source2'),IF('$lead_camp' = '', NULL, '$lead_camp')) ");

$stmt->bindParam(':expected_walkin',$exptd_walkins_date);
$stmt->bindParam(':expected_payment',$exptd_sale_date);
$stmt->bindParam(':name',$name);
$stmt->bindParam(':phone',$phone);
$stmt->bindParam(':city',$city);
$stmt->bindParam(':qual',$last_qualification);
$stmt->bindParam(':email',$email_id);
$stmt->bindParam(':prefer_city',$branch);
$stmt->bindParam(':course',$course);
$stmt->bindParam(':s_status',$status);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':followup_time',$followup_time);
$stmt->bindParam(':remarks',$remarks);
$stmt->bindParam(':rphone',$alt_number);
$stmt->bindParam(':agent_id',$agent_id);
$stmt->bindParam(':followup_counselour',$username);
$stmt->bindParam(':counselor_id',$counselor_id);
$stmt->bindParam(':datenew',$c_date);
$stmt->bindParam(':location2',$branch);
$stmt->bindParam(':created',$c_date);


	$stmt->execute();
	$last_id = $DB_con->lastInsertId();	
	$type="Ielts";
	
	
	   
    if($branch!=''){
      $stmt = $DB_con->query('SELECT address,tid from crm_branch where branch_name="'.trim($branch).'"  limit 1');
      $countss = $stmt->rowCount();
      $aa =$stmt->fetch(PDO::FETCH_ASSOC);  
      if($countss==1){
          $tid=$aa['tid'];
          $sms=$aa['address'];
          $phone=$_GET['phone'];
         // include("sms.php");
      }
    }
	
$stmt = $DB_con->prepare("INSERT INTO crm_remark (remark,status,sub_status,next_to_follow,agent,form_type,form_id,date,time,recording)
VALUES (:remark,:status,:sub_status,:next_to_follow,:agent,:form_type,:form_id,:date,:time,:recording) ");
$stmt->bindParam(':remark',$remarks);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':sub_status',$status);
$stmt->bindParam(':next_to_follow',$followup_date);
$stmt->bindParam(':agent',$user);
$stmt->bindParam(':form_type',$type);
$stmt->bindParam(':form_id',$last_id);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':time',$followup_time);
$stmt->bindParam(':recording',$recording);
$stmt->execute();

	
$DB_con->query('INSERT INTO crm_alerts (user,username,type,alert,link,date,location,icon)
values ("manager","","Lead","Agent '.ucwords($user).' created new ielts lead.","ielts.php?ielts_form_id='.$last_id.'&tab=main","'.$date_time.'","'.$branch.'","fa fa-wpforms"),
("counselor","'.$username.'","Lead","Agent '.ucwords($user).' assigned new ielts lead.","ielts.php?ielts_form_id='.$last_id.'&tab=main","'.$date_time.'","'.$branch.'","fa fa-wpforms")
');
 
 $DB_con->query('UPDATE crm_leads set removed=1 WHERE phone="'.$_GET['phone'].'" and category="Ielts" ');
 
	echo "success";
	
	
}else{
    
    
	$last_id = $x['ielts_form_id'];
    $type="Ielts";
	
	
 $DB_con->query('UPDATE crm_remark SET done="1" WHERE form_id="'.$last_id .'" AND form_type="Ielts" AND counselor="Na" ');

	
$stmt = $DB_con->prepare("INSERT INTO crm_remark (remark,status,sub_status,next_to_follow,agent,form_type,form_id,date,time,recording)
VALUES (:remark,:status,:sub_status,:next_to_follow,:agent,:form_type,:form_id,:date,:time,:recording) ");
$stmt->bindParam(':remark',$remarks);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':sub_status',$status);
$stmt->bindParam(':next_to_follow',$followup_date);
$stmt->bindParam(':agent',$user);
$stmt->bindParam(':form_type',$type);
$stmt->bindParam(':form_id',$last_id);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':time',$followup_time);
$stmt->bindParam(':recording',$recording);
if($stmt->execute()){ echo "success"; }
    
    
    
    
    
    
    
}


}


/////////////////////////Visitor////////////////////////////////////////
elseif((isset($_GET['services']))&&($_GET['services']=='VisitorVisa')){
	
	if(isset($_GET['pass_no'])){ $pass_no=$_GET['pass_no']; }else{ $pass_no=''; } 
		if(isset($_GET['address'])){ $address=$_GET['address']; }else{ $address=''; } 
			if(isset($_GET['occupation'])){ $occupation=$_GET['occupation']; }else{ $occupation=''; } 
				if(isset($_GET['income'])){ $income=$_GET['income']; }else{ $income=''; } 
					if(isset($_GET['visa_earlier'])){ $visa_earlier=$_GET['visa_earlier']; }else{ $visa_earlier=''; } 
						if(isset($_GET['refused_visa'])){ $refused_visa=$_GET['refused_visa']; }else{ $refused_visa=''; } 
							if(isset($_GET['approved_visa'])){ $approved_visa=$_GET['approved_visa']; }else{ $approved_visa=''; } 
							if(isset($_GET['departed_stopped'])){ $departed_stopped=$_GET['departed_stopped']; }else{ $departed_stopped=''; } 
							if(isset($_GET['invitaion'])){ $invitaion=$_GET['invitaion']; }else{ $invitaion=''; } 
							if(isset($_GET['sponser'])){ $sponser=$_GET['sponser']; }else{ $sponser=''; } 
								if(isset($_GET['relation'])){ $relation=$_GET['relation']; }else{ $relation=''; } 
								if(isset($_GET['source'])){ $source=$_GET['source']; }else{ $source=''; } 
									if(isset($_GET['dob'])){ $dob=$_GET['dob']; }else{ $dob=''; } 
									
									if(isset($_GET['rphone'])){ $rphone=$_GET['rphone']; }else{ $rphone=''; } 
								
								
	
	
	
$counselor_id=0; 
$username='';; 


$stmt = $DB_con->query('SELECT id from crm_visitorvisa where phone="'.$_GET['phone'].'" or rphone="'.$_GET['phone'].'" limit 1');
$counts = $stmt->rowCount();
$x =$stmt->fetch(PDO::FETCH_ASSOC);

if($counts == 0 ){
	
	
	
if(($sub_status=='INT') || ($sub_status=='PTV') || ($sub_status=='WVL') ){
$stmt = $DB_con->query("SELECT o.counselor,c.counselor_id FROM crm_online_users as o inner join crm_counselor as c on o.counselor=c.username WHERE o.counselor!='' AND o.date='$c_date' AND o.location='$branch' AND c.dept = 'visa' AND timestamp BETWEEN timestamp(DATE_SUB(NOW(), INTERVAL 30 SECOND)) AND timestamp(NOW()) order by rand() limit 1");
$t =$stmt->fetch(PDO::FETCH_ASSOC);
$counselor_id=$t['counselor_id']; 
$username=$t['counselor']; 
if((!isset($username))||($username=='')){    $counselor_id=0; $username='';  }
}
		
$stmt = $DB_con->prepare("INSERT INTO crm_visitorvisa(name,dob,pass_no,phone,rphone,email,address,occ,an_income,apply_visa,refuse_visa,appr_visa,entry_clr,invt_from,spnsr_prsn,rel,remarks,created,status,s_status,followup_date,country,source,prefer_city,agent_id,followup_counselour,expected_walkin,expected_payment,source2,sub_source2,lead_camp) VALUES (:name,:dob,:pass_no,:phone,:rphone,:email,:address,:occ,:an_income,:apply_visa,:refuse_visa,:appr_visa,:entry_clr,:invt_from,:spnsr_prsn,:rel,:remarks,:created,:status,:s_status,:followup_date,:country,:source,:prefer_city,:agent_id,:followup_counselour,:expected_walkin,:expected_payment,IF('$f_source2' = '', NULL, '$f_source2'),IF('$s_source2' = '', NULL, '$s_source2'),IF('$lead_camp' = '', NULL, '$lead_camp'))");
	
	$stmt->bindParam(':name',$_GET['name']);
	$stmt->bindParam(':dob',$dob);
	$stmt->bindParam(':pass_no',$pass_no);
	$stmt->bindParam(':phone',$_GET['phone']);
	$stmt->bindParam(':rphone',$rphone);
	$stmt->bindParam(':email',$_GET['email']);
	$stmt->bindParam(':address',$address);
	$stmt->bindParam(':occ',$occupation);
	$stmt->bindParam(':an_income',$income);
	$stmt->bindParam(':apply_visa',$visa_earlier);
	$stmt->bindParam(':refuse_visa',$refused_visa);
	$stmt->bindParam(':appr_visa',$approved_visa);
	$stmt->bindParam(':entry_clr',$departed_stopped);
	$stmt->bindParam(':invt_from',$invitaion);
	$stmt->bindParam(':spnsr_prsn',$sponser);
	$stmt->bindParam(':rel',$relation);
	$stmt->bindParam(':remarks',$remarks);
	$stmt->bindParam(':created',$c_date);
	$stmt->bindParam(':status',$sub_status);
	$stmt->bindParam(':s_status',$status);
	$stmt->bindParam(':followup_date',$followup_date);
	$stmt->bindParam(':country',$_GET['prefer_country']);
	$stmt->bindParam(':source',$source);
	$stmt->bindParam(':prefer_city',$branch);
	$stmt->bindParam(':agent_id',$agent_id);
	$stmt->bindParam(':followup_counselour',$username);
	$stmt->bindParam(':expected_walkin',$exptd_walkins_date);
	$stmt->bindParam(':expected_payment',$exptd_sale_date);
	$stmt->execute();
	$id = $DB_con->lastInsertId();
	$type="Visitor";
	
	 if($branch!=''){
      $stmt = $DB_con->query('SELECT address,tid from crm_branch where branch_name="'.trim($branch).'"  limit 1');
      $countss = $stmt->rowCount();
      $aa =$stmt->fetch(PDO::FETCH_ASSOC);  
      if($countss==1){
          $tid=$aa['tid'];
          $sms=$aa['address'];
          $phone=$_GET['phone'];
         // include("sms.php");
      }
    }
	
	
$stmt = $DB_con->prepare("INSERT INTO crm_remark (remark,status,sub_status,next_to_follow,agent,form_type,form_id,date,time,recording)
VALUES (:remark,:status,:sub_status,:next_to_follow,:agent,:form_type,:form_id,:date,:time,:recording) ");
$stmt->bindParam(':remark',$remarks);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':sub_status',$status);
$stmt->bindParam(':next_to_follow',$followup_date);
$stmt->bindParam(':agent',$user);
$stmt->bindParam(':form_type',$type);
$stmt->bindParam(':form_id',$id);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':time',$followup_time);
$stmt->bindParam(':recording',$recording);
$stmt->execute();
	
$DB_con->query('INSERT INTO crm_alerts (user,username,type,alert,link,date,location,icon)
values ("manager","","Lead","Agent '.ucwords($user).' created new visitor visa lead.","visitor-form-edit.php?id='.$id.'","'.$date_time.'","'.$branch.'","fa fa-wpforms"),
("counselor","'.$username.'","Lead","Agent '.ucwords($user).' assigned new visitor visa lead.","visitor-form-edit.php?id='.$id.'","'.$date_time.'","'.$branch.'","fa fa-wpforms")');

echo "success"; 
 
 
}else{
	
    
	$last_id = $x['id'];
    $type="Visitor";
	
	
$DB_con->query('UPDATE crm_remark SET done="1" WHERE form_id="'.$last_id .'" AND form_type="Visitor" AND counselor="Na" ');
	
$stmt = $DB_con->prepare("INSERT INTO crm_remark (remark,status,sub_status,next_to_follow,agent,form_type,form_id,date,time,recording)
VALUES (:remark,:status,:sub_status,:next_to_follow,:agent,:form_type,:form_id,:date,:time,:recording) ");
$stmt->bindParam(':remark',$remarks);
$stmt->bindParam(':status',$sub_status);
$stmt->bindParam(':sub_status',$status);
$stmt->bindParam(':next_to_follow',$followup_date);
$stmt->bindParam(':agent',$user);
$stmt->bindParam(':form_type',$type);
$stmt->bindParam(':form_id',$last_id);
$stmt->bindParam(':date',$c_date);
$stmt->bindParam(':time',$followup_time);
$stmt->bindParam(':recording',$recording);
if($stmt->execute()){ 
    
    echo "success"; }
    
    	
	
	
}	
}else{ echo 'Service Missing'; }


$DB_con->query('Insert into crm_agent_call (user,username,phone) values ("Agent","'.$user.'","'.$_GET['phone'].'") ');

}else{ echo 'Phone Number Missing'; }

}else{
    
  
    
    
    echo 'success'; }