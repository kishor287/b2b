<?php
function meetingScheduled($to_name, $to_phone, $t_client_name, $t_manager_name, $date, $time)
{

    $data = array(
        "channelId" => "63e206b7fce0e80b431d639d",
        "channelType" => "whatsapp",
        "recipient" => array(
            "name" => $to_name,
            "phone" => "91" . $to_phone
        ),
        "whatsapp" => array(
            "type" => "template",
            "template" => array(
                "templateName" => "meeting_scheduled",
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


function getUserName($con)
{
    $user_id = $_SESSION['uId'];
    $query = "SELECT fname FROM cp_users WHERE id = '$user_id'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['fname'];
    } else {
        return "Unknown";
    }
}

// function getUserRole( $con) {
//    $user_id = $_SESSION['uId'];
//    $query = "SELECT role FROM cp_users WHERE id = '$user_id'";
//    $result = mysqli_query($con, $query);

//    if ($result && mysqli_num_rows($result) > 0) {
//        $row = mysqli_fetch_assoc($result);
//        return $row['role'];
//    } else {
//        return -1; 
//    }
// }

function trainingScheduled($to_name, $to_phone, $t_client_name, $trainer, $date, $time, $link)
{

    $data = array(
        "channelId" => "63e206b7fce0e80b431d639d",
        "channelType" => "whatsapp",
        "recipient" => array(
            "name" => $to_name,
            "phone" => "91" . $to_phone
        ),
        "whatsapp" => array(
            "type" => "template",
            "template" => array(
                "templateName" => "training_scheduled",
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
    )
    );

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


function clientNotInterested($to_name, $to_phone, $t_client_name)
{
    $data = array(
        "channelId" => "63e206b7fce0e80b431d639d",
        "channelType" => "whatsapp",
        "recipient" => array(
            "name" => $to_name,
            "phone" => "91" . $to_phone
        ),
        "whatsapp" => array(
            "type" => "template",
            "template" => array(
                "templateName" => "client_not_interested",
                "bodyValues" => array(
                    "client_organization_name" => $t_client_name,
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

function phoneSwitchedOff($to_name, $to_phone, $t_client_name)
{
    $data = array(
        "channelId" => "63e206b7fce0e80b431d639d",
        "channelType" => "whatsapp",
        "recipient" => array(
            "name" => $to_name,
            "phone" => "91" . $to_phone
        ),
        "whatsapp" => array(
            "type" => "template",
            "template" => array(
                "templateName" => "phone_switched_off",
                "bodyValues" => array(
                    "client_organization_name" => $t_client_name,
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


function getLeadInfo($lead_id, $con)
{

    $query = "SELECT name, organization_phone ,user_id,marketing_id,user_id, organization FROM cp_leads WHERE id = '$lead_id'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    } else {
        return -1;
    }
}

// function trainingRescheduled($to_name, $to_phone, $t_client_name, $date, $time, $link)
// {

//   $data = array(
//    "channelId" => "63e206b7fce0e80b431d639d",
//    "channelType" => "whatsapp",
//    "recipient" => array(
//        "name" => $to_name,
//        "phone" => "91".$to_phone
//    ),
//    "whatsapp" => array(
//        "type" => "template",
//        "template" => array(
//            "templateName" => "training_rescheduled",
//            "bodyValues" => array(
//                "client_organization_name" => $t_client_name,
//                "date" => $date,
//                "time" => $time,
//                "link" => $link,
//            )
//        )
//    )
// );


//   $curl = curl_init();
//   curl_setopt_array($curl, array(
//     CURLOPT_URL => 'server.gallabox.com/devapi/messages/whatsapp',
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => 'POST',
//     CURLOPT_POSTFIELDS => json_encode($data),
//     CURLOPT_HTTPHEADER => array(
//      'apiKey: 63e22876583a7318bbc19e96',
//      'apiSecret: edab7b1e312543b894d708f5a9526ef3',
//      'Content-Type: application/json',
// ),
//   ));


//   $response = curl_exec($curl);
//   curl_close($curl);
//    return $response;
// }

function trainingReminder($to_name, $to_phone, $t_client_name, $trainer, $date, $time, $link)
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
    )
    );

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


function calculateAverage($data)
{
    $sum = 0;
    $count = 0;

    foreach ($data as $field => $value) {
        if (is_numeric($value)) {
            $sum += $value;
            $count++;
        }
    }

    if ($count > 0) {
        $average = ($sum / $count) * 100;
        return $average;
    } else {
        return 0;
    }



}

function fintechServicesTargetNotAchievedClient($to_name, $to_phone, $t_client_name, $month)
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
                "templateName" => "fintech_services_target_not_achieved_client",
                "bodyValues" => array(
                    "client_organization_name" => $t_client_name,
                    "month" => $month,
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


function meetingRescheduled($to_name, $to_phone, $t_client_name, $t_manager_name, $date, $time)
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
                "templateName" => "meeting_rescheduled",
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


function procedure($to_name, $to_phone, $t_client_name, $procedure, $trainer, $date, $time, $link)
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
                "templateName" => "procedure",
                "bodyValues" => array(
                    "client_organization_name" => $t_client_name,
                    "procedure" => $procedure,
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
    )
    );

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;

}