<?php

if($action=='get_count'){
    $extra = '';
    $userId = $_SESSION['uId'];
   $today = date('Y-m-d 00:00:00');
  $startDate = date('Y-m-d H:i:s', strtotime('-7 days'));
  $endDate = date('Y-m-d H:i:s');
  
    $date_time = $startDate;
    $date_time_parts = explode(' ', $date_time);
    $startDate = substr($date_time, 0, 10);
  
   
   
   $date_time = $endDate;
    $date_time_parts = explode(' ', $date_time);
    $endDate = substr($date_time, 0, 10);
    
    
   $filter = 'where 1=1';
   if (isset($_POST['filter']) && !empty($_POST['filter'])) {
      $array = json_decode($_POST['filter'], true);
      if (isset($array['id']) && ($array['id'] != '')) {
         $filter .= " and id='" . $array['id'] . "'  ";
      }
   }
   
   $param = array("sort" => "order by id desc","pagination" => $_POST['pagination'], "col" => "count(id) as count", "tb" => "leads", "where" =>  $filter , "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
   $leads = qselect($param);
   
  $filter .= " and followup_date BETWEEN  '" . date('Y-m-d 00:00:00') . "' and '" . date('Y-m-d 23:59:59') . "'  ";
  $param = array("sort" => "order by id desc", "pagination" => $_POST['pagination'], "col" => "count(id) as count", "tb" => "followups",  "where" => $filter . " and user_id = '" . $userId . "'", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
  $followup = qselect($param);
   

    $param = array("sort" => "","pagination" => $_POST['pagination'],"col" => "count(id) as count","tb" => "leads","where" => "where marketing_id = '$userId' AND created BETWEEN '$startDate' AND '$endDate'","limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']
    );
    $newLeads = qselect($param);
    
    $param = array("sort" => "order by id desc","pagination" => $_POST['pagination'], "col" => "count(id) as count", "tb" => "meetings", "where" =>  "where assigned_by = '$userId' and meeting_date BETWEEN  '" . date('Y-m-d 00:00:00') . "' and '" . date('Y-m-d 23:59:59') . "'", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $meetings = qselect($param);

   $param = array("sort" => "","pagination" => $_POST['pagination'],"col" => "count(id) as count","tb" => "meetings","where" => "where assigned_by = '$userId' AND meeting_date BETWEEN  '" . date('Y-m-d 00:00:00') . "' and '" . date('Y-m-d 23:59:59') . "' ","limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']
    );
   $trainings = qselect($param);
   $param = array("sort" => "","pagination" => $_POST['pagination'],"col" => "count(id) as count","tb" => "leads","where" => "where marketing_id = '$userId' AND created BETWEEN  '" . date('Y-m-d 00:00:00') . "' and '" . date('Y-m-d 23:59:59') . "' ","limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']
    );
    $agreement = qselect($param);

 $data = array("total_leads" => $leads['data'][0]['count'],"today_followups" => $followup['data'][0]['count'],
 "today_total_leads" => $newLeads['data'][0]['count'], "today_meeting" => $meetings['data'][0]['count'], "today_training" => $trainings['data'][0]['count'],"today_agreement" =>$agreement['data'][0]['count']
);  
 echo json_encode($data);
}
















