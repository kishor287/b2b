<?php 
session_start();
ini_set('display_errors',1);

trainingScheduleReminder();

function trainingScheduleReminder(){
    date_default_timezone_set("Asia/Calcutta");
    $dbHost = 'localhost';
    $dbName = 'innerxcrm_team_panel';
    $dbUser = 'innerxcrm_internal';
    $dbPass = 'innerxcrm_internal@77';
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT cp_leads.*, cp_meetings.meeting_date
              FROM cp_leads
              INNER JOIN cp_meetings ON cp_meetings.lead_id = cp_leads.id";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    

// Retrieve the user ID from the session


    $leadsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($leadsData as $lead) {
        $meetingDate = $lead['meeting_date'];
        $leadUserId = $lead['user_id'];
        $leadId = $lead['id'];

    }
     $leadId = $lead['id'];
     $leadUserId = $lead['user_id'];
     $sales_manager_name = getUserNamed($leadUserId, $conn);
     $userData = getUserData($leadId, $conn);

    $name = $userData['name'];
    $organization = $userData['organization'];
    $organizationPhone = $userData['organization_phone'];

     if( $lead['user_id'] == 32){ 
    $link ="https://meet.google.com/skw-xujh-ppc";
    } 
    if($lead['user_id'] == 33){
        $link ="https://meet.google.com/uog-kacq-nug";
    }
    if( $lead['user_id'] == 30){
        $link ="https://meet.google.com/ior-fvxu-apb";
    }
    if( $lead['user_id'] == 31){
        $link ="https://meet.google.com/jef-sowe-aqi";
    }
    if($lead['user_id'] == 38){
        $link ="https://meet.google.com/kjc-hpss-aaj";
    }
    if($lead['user_id'] == 54){
        $link ="https://meet.google.com/qcu-aqri-urv";
    }
    if($lead['user_id'] == 55){
        $link ="https://meet.google.com/tph-hkcn-yhm";
    }
    if($lead['user_id'] == 56){
        $link ="https://meet.google.com/xkr-esgz-zqg";
    }
    if($lead['user_id'] == 64){
        $link ="https:/xkr-esgz-zqg";
    }
    


    $date_time = $meetingDate;
    $date_time_parts = explode(' ', $date_time);
    $date = substr($date_time, 0, 10);
    $time = substr($date_time, 11);

    $currentDateTime = date('Y-m-d H:i');
        

    $scheduledDateTime = new DateTime($meetingDate);
    $scheduleDate = $scheduledDateTime->modify('-1 hour');
    $formattedScheduleDate = $scheduleDate->format('Y-m-d H:i');
    echo $currentDateTime . '____'. $formattedScheduleDate;


    if($currentDateTime === $formattedScheduleDate){
       $response =  trainingReminder($name,$organizationPhone, $organization, $sales_manager_name, $date, $time, $link);

      echo $response;
      
      $message = "A gentle reminder regarding upcoming meeting with $organization at $date_time";
      $userid = $lead['user_id'];
      $icon = "calendar";
      $notification_data = array(
      'user_id' => $userid,
      "message" => $message, // the message content of the notification
      "icon" => $icon, // the name of the icon to display with the notification
      "tag" => "Training Reminder",
      "created_at" => date('Y-m-d H:i:s'), // the current datetime for the created_at field
      "updated_at" => date('Y-m-d H:i:s') // the current datetime for the updated_at field
   );
   $query = "INSERT INTO cp_notifications (user_id, message, icon, url, tag, created_at, updated_at) 
          VALUES (:user_id, :message, :icon, :url, :tag, :created_at, :updated_at)";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $notification_data['user_id']);
    $stmt->bindParam(':message', $notification_data['message']);
    $stmt->bindParam(':icon', $notification_data['icon']);
    $stmt->bindParam(':url', $notification_data['url']);
    $stmt->bindParam(':tag', $notification_data['tag']);
    $stmt->bindParam(':created_at', $notification_data['created_at']);
    $stmt->bindParam(':updated_at', $notification_data['updated_at']);

    $stmt->execute();
    }else{
        echo'false';
     
    }
    
}

function getUserNamed($leadUserId, $conn)
{
    $query = "SELECT fname FROM cp_users WHERE id = '$leadUserId'";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['fname'];
}


function getUserData($leadId, $conn)
{
    $query = "SELECT name, organization, organization_phone FROM cp_leads WHERE id = '$leadId'";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function trainingReminder($to_name, $to_phone, $t_client_name, $trainer, $date, $time, $link)
{
$data = array(
"channelId" => "63e206b7fce0e80b431d639d",
"channelType" => "whatsapp",
"recipient" => array(
    "name" => $to_name,
    "phone" => "91".$to_phone,
),
"whatsapp" => array(
    "type" => "template",
    "template" => array(
        "templateName" => "training_reminder",
        "bodyValues" => array(
            "client_organization_name" => $t_client_name,
            "trainer" => $trainer,
            "date" => $date,
            "time" => $time,
            "link" => $link,
        )
    )
)
);

$curl = curl_init();
curl_setopt_array($curl, array(
 CURLOPT_URL => 'server.gallabox.com/devapi/messages/whatsapp',
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_ENCODING => '',
 CURLOPT_MAXREDIRS => 10,
 CURLOPT_TIMEOUT => 0,
 CURLOPT_FOLLOWLOCATION => true,
 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 CURLOPT_CUSTOMREQUEST => 'POST',
 CURLOPT_POSTFIELDS => json_encode($data),
 CURLOPT_HTTPHEADER => array(
  'apiKey: 63e22876583a7318bbc19e96',
  'apiSecret: edab7b1e312543b894d708f5a9526ef3',
  'Content-Type: application/json',
),
));

$response = curl_exec($curl);
curl_close($curl);
return $response;
}


