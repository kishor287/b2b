<?php

ini_set('display_errors',1);
$userId = $_SESSION['uId'];
$today = date('Y-m-d 00:00:00');

if ($action == 'get_count') {
   $filter = 'where 1=1';
   if (isset($_POST['filter']) && !empty($_POST['filter'])) {
      $array = json_decode($_POST['filter'], true);
      if (isset($array['id']) && ($array['id'] != '')) {
         $filter .= " and id='" . $array['id'] . "'  ";
      }
   }
   $param = array("pagination" => $_POST['pagination'], "col" => "count(id) as count", "tb" => "leads", "where" => "", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
   $leads = qselect($param);


   $filter .= " and next_followup_date BETWEEN  '" . date('Y-m-d 00:00:00') . "' and '" . date('Y-m-d 23:59:59') . "'  ";
   $param = array("sort" => "order by id desc", "pagination" => $_POST['pagination'], "col" => "count(id) as count", "tb" => "leads", "where" => $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);

   $followup = qselect($param);

   $meetings = query_foreign_table(
      'cp_meetings',
      'lead_id',
      'cp_leads',
      ['cp_users.fname as sales_manager', 'cp_leads.phone', 'cp_leads.organization', 'cp_meetings.meeting_date'],
      'cp_meetings.meeting_date',
      ["cp_meetings.assigned_by='$userId'", "cp_meetings.meeting_date >= CONCAT(DATE(NOW()),' 00:00:00')"],
      'cp_users',
      'marketing_id'
   );
   $dashboard = dashboard();
   $data = array("total_leads" => $leads['data'][0]['count'], "today_followups" => $followup['data'][0]['count'], 'meetings' => $meetings, 'dashboard' => $dashboard);
   echo json_encode($data);
}


function dashboard()
{
   require(APP_PATH.'../_con.php');
   $userId = $_SESSION['uId'];
   $today = date('Y-m-d 00:00:00');
   $yesterday = date("Y-m-d", strtotime("-1 day"));
   $startOfWeek = date("Y-m-d", strtotime("last Monday"));
   $month = date('m');
   if ($_SESSION['utype'] !== 1) {
      $followUpCondition = " AND user_id='$userId'";
   } else {
      $followUpCondition = '';
   }
   $agreementCondition = '';
   if ($_SESSION['utype'] == 14) {
      $agreementCondition = " AND marketing_id='$userId'";
   }
   $contractCondition = ' AND uploaded_contract_path IS NOT NULL OR uploaded_contract_path <> 0 ';
   if ($_SESSION['utype'] == 14) {
      $contractCondition = " AND marketing_id='$userId'";
   }
   $leads = select('cp_leads', 'COUNT(id) as total_leads', '', '', '');
   $todayLeads = select('cp_leads', 'COUNT(id) as newLeads', "WHERE DATE(created) = '$today'", '', '');
   // meeting_date >= '$today' and 
   $totalMeetings = select('cp_meetings', 'COUNT(id) as total_meetings', "WHERE assigned_by='$userId'", '', '');
   // WHERE meeting_date >= '$today' AND 
   $totalRescheduledMeetings = select('cp_meetings', 'COUNT(id) as total_rescheduled_meetings', "WHERE status='rescheduled' and assigned_by='$userId'", '', '');
   // meeting_date <= '$today' and
   $totalDoneMeetings = select('cp_meetings', 'COUNT(id) as total_done_meetings', "WHERE meeting_date <= '$today' and assigned_by='$userId'", '', '');
   $totalFollowups = select('cp_followups', 'COUNT(id) as total_followups', "WHERE DATE(followup_date) = '$today' $followUpCondition", '', '');
   $totalDoneFollowups = select('cp_followups', 'COUNT(id) as total_done_followups', "WHERE followup_date = '$today' $followUpCondition", '', '');
   $totalPendingFollowups = select('cp_followups', 'COUNT(id) as total_pending_followups', "WHERE  DATE(followup_date) = '$today' $followUpCondition", '', '');
   $monthlyAgreementsResult = select('cp_leads', 'COUNT(id) as total_agreements', " WHERE DATE_FORMAT(created, '%m') = '$month' $agreementCondition", '', '');
   $todayAgreementsResult = select('cp_leads', 'COUNT(id) as total_agreements', "WHERE created >= '$today' $agreementCondition", '', '');
   $yesterdayAgreementsResult = select('cp_leads', 'COUNT(id) as total_agreements', " WHERE created >= '$yesterday' AND created < '$today' $agreementCondition", '', '');
   $weeklyAgreementsResult = select('cp_leads', 'COUNT(id) as total_agreements', " WHERE created >= '$startOfWeek' $agreementCondition", '', '');
   
   // contracts if contract uploaded then agreement becomes contract
   $monthlyContracts = select('cp_leads', 'COUNT(id) as total_contracts', " WHERE DATE_FORMAT(created, '%m') = '$month' $contractCondition", '', '');
   $todayContractsResult = select('cp_leads', 'COUNT(id) as total_contracts', "WHERE created >= '$today' $contractCondition", '', '');
   $yesterdayContractsResult = select('cp_leads', 'COUNT(id) as total_contracts', " WHERE created >= '$yesterday' AND created < '$today' $contractCondition", '', '');
   $weeklyContractsResult = select('cp_leads', 'COUNT(id) as total_contracts', " WHERE created >= '$startOfWeek' $contractCondition", '', '');
   // $todayTrainings = select('cp_leads', 'COUNT(id) as total_contracts', " WHERE created >= '$startOfWeek' $contractCondition", '', '');
   $todayTraining = $con->query("SELECT cp_users.fname,COUNT(cp_meetings.id) as meetings FROM cp_meetings LEFT JOIN cp_users ON cp_meetings.assigned_by = cp_users.id WHERE cp_meetings.procedure = 'Training' AND DATE(cp_meetings.created) = '$today' GROUP BY cp_meetings.assigned_by ORDER BY cp_meetings.id");
   $todayTraining = $todayTraining->fetchAll(PDO::FETCH_ASSOC);
   $leads = array_shift($leads);
   $totalDoneMeetings = array_shift($totalDoneMeetings);
   $todayLeads = array_shift($todayLeads);
   $totalMeetings = array_shift($totalMeetings);
   $totalFollowups = array_shift($totalFollowups);
   $totalDoneFollowups = array_shift($totalDoneFollowups);
   $totalPendingFollowups = array_shift($totalPendingFollowups);
   $todayAgreementsResult = array_shift($todayAgreementsResult);
   $yesterdayAgreementsResult = array_shift($yesterdayAgreementsResult);
   $weeklyAgreementsResult = array_shift($weeklyAgreementsResult);
   $monthlyAgreementsResult = array_shift($monthlyAgreementsResult);
   $totalRescheduledMeetings = array_shift($totalRescheduledMeetings);
   $monthlyContracts = array_shift($monthlyContracts);

   $todayLeads = $todayLeads['newLeads'] ?? 0;
   $totalDoneMeetings = $totalDoneMeetings['total_done_meetings'] ?? 0;
   $totalRescheduledMeetings = $totalRescheduledMeetings['total_rescheduled_meetings'] ?? 0;
   $leads = $leads['total_leads'] ?? 0;
   $totalMeetings = $totalMeetings['total_meetings'] ?? 0;
   $followups = $totalFollowups['total_followups'] ?? 0;
   $totalDoneFollowups = $totalDoneFollowups['total_done_followups'] ?? 0;
   $totalPendingFollowups = $totalPendingFollowups['total_pending_followups'] ?? 0;
   $todayAgreementsResult = $todayAgreementsResult['total_agreements'] ?? 0;
   $yesterdayAgreementsResult = $yesterdayAgreementsResult['total_agreements'] ?? 0;
   $weeklyAgreementsResult = $weeklyAgreementsResult['total_agreements'] ?? 0;
   $monthlyAgreementsResult = $monthlyAgreementsResult['total_agreements'] ?? 0;
   $monthlyContracts = $monthlyContracts['total_contracts'] ?? 0;

   $vars = compact(
      'leads',
      'todayLeads',
      'totalMeetings',
      'followups',
      'totalDoneFollowups',
      'monthlyAgreementsResult',
      'todayAgreementsResult',
      'yesterdayAgreementsResult',
      'weeklyAgreementsResult',
      'totalRescheduledMeetings',
      'totalDoneMeetings',
      'monthlyContracts',
      'todayTraining'
   );
   return $vars;
}