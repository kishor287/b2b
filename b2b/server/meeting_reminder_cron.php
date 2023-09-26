<?php
ini_set('display_errors', 1);

require_once('Classes/Config/SendMail.php');

use Panel\Server\Classes\Config\SendMail;

meetingScheduleReminder();
function meetingScheduleReminder()
{
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
    session_start();
    $leadsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $trainerId  = '';
    foreach ($leadsData as $lead) {
        $meetingDate = $lead['meeting_date'];
        $marketingId = $lead['marketing_id'];
        $trainerId = $lead['user_id'];
        $leadId = $lead['id'];
    }
    $leadId = $lead['id'];
    $leadPhone = $lead['phone'];

    $marketingId = $lead['marketing_id'];
    $sales_manager_name = getUserNamed($marketingId, $conn);
    $userData = getUserData($leadId, $conn);

    if (isset($userData['name'])) {
        $name = $userData['name'];
    } else {
        $name = '';
    }
    $organization = $userData['organization'];
    $organizationPhone = $userData['organization_phone'];
    $email = $userData['organization_email'];

    $date_time = $meetingDate;
    $date_time_parts = explode(' ', $date_time);
    $date = substr($date_time, 0, 10);
    $time = substr($date_time, 11);
    $currentDate = date('Y-m-d');
    $convertedTime = $currentDate . ' ' . $time;


    $currentDateTime = date('Y-m-d H:i');

    $scheduledDateTime = new DateTime($meetingDate);
    $scheduleDate = $scheduledDateTime->modify('-1 hour');
    $formattedScheduleDate = $scheduleDate->format('Y-m-d H:i');

    $selectTrainerQuery = "SELECT meeting_link FROM cp_users WHERE id='$trainerId' LIMIT 1";
    $prepareQuery = $conn->prepare($selectTrainerQuery);
    $exec = $prepareQuery->execute();
    $result = $prepareQuery->fetch(PDO::FETCH_ASSOC);
    if(!empty($result['meeting_link']))
    {
        $link = $result['meeting_link']??'';
        $meetinTemplate = '../view/email-templates/meeting-reminder-template.html';
        $tempate = file_get_contents($meetinTemplate);
        $tempate = str_replace('{{client_name}}',$organization,$tempate);
        $tempate =str_replace('{{date}}',$date,$tempate);
        $tempate = str_replace('{{time}}',$time,$tempate);
        $tempate = str_replace('{{duration}}',$time,$tempate);
        $tempate = str_replace('{{link}}',$link,$tempate);

        $mail = new SendMail();
        $mail->setTo($email);
        $mail->setSubject("Demo Booked for $organization");
        $mail->setContent($tempate);
        $mail->sendMail();
    }

    echo $currentDateTime . '____' . $formattedScheduleDate;

    if ($currentDateTime === $formattedScheduleDate) {
        $response = meetingReminder($name, $organizationPhone, $organization, $sales_manager_name, $date, $time);
        if ($leadPhone) {
            $responses = meetingReminder($name, $leadPhone, $sales_manager_name, $organization, $date, $time);
        }
    } else {
        echo 'false';
    }
}
function getUserNamed($marketingId, $conn)
{
    $query = "SELECT fname FROM cp_users WHERE id = '$marketingId'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return null;
    }
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

function meetingReminder($to_name, $to_phone, $t_client_name, $t_manager_name, $date, $time)
{

    $data = array(
        "channelId" => "63e206b7fce0e80b431d639d",
        "channelType" => "whatsapp",
        "recipient" => array(
            "name" => $to_name,
            "phone" => "91" . $to_phone,
        ),
        "whatsapp" => array(
            "type" => "template",
            "template" => array(
                "templateName" => "meeting_reminder",
                "bodyValues" => array(
                    "client_organization_name" => $t_client_name,
                    "sales_manager_name" => $t_manager_name,
                    "date" => $date,
                    "time" => $time,
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
    )
    );

    

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}