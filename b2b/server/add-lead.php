<?php
if ($action == 'save') {

    if ($_FILES['contract']['name'] != '') {
        $file['file'] = $_FILES['contract'];
        $file = upload_pdf($file, 'contract');

        if ($file['status'] == 0) {
            echo json_encode($file);
            exit();
        }
    } else {
        $file['data'] = '';
    }
    $param = array("tb" => "leads");

    $extra_location = (isset($_POST['location_radio'])) ? $_POST['location_radio'] : 'no';

    // setting the values for the checkbox
    $visa = isset($_POST['visa']) ? '1' : '0';
    $ielts = isset($_POST['ielts']) ? '1' : '0';
    $vistor_visa = isset($_POST['vistor_visa']) ? '1' : '0';
    $work_visa = isset($_POST['work_visa']) ? '1' : '0';
    $pr = isset($_POST['pr']) ? '1' : '0';

    // setting the values for checkbox for sending mail through
    $email = isset($_POST['link_email']) ? '1' : '0';
    $whatsapp = isset($_POST['link_whatsapp']) ? '1' : '0';

    $crm_soft_text = $_POST['crm_soft_text'];
    $crm_soft = (isset($_POST['crm_soft']) && $_POST['crm_soft'] == "yes") ? $crm_soft_text : 'no';

    $calling_soft_text = $_POST['calling_soft_text'];
    $calling_soft = (isset($_POST['calling_soft']) && $_POST['calling_soft'] == "yes") ? $calling_soft_text : 'no';

    $sms_soft_text = $_POST['sms_soft_text'];
    $sms_soft = (isset($_POST['sms_soft']) && $_POST['sms_soft'] == "yes") ? $sms_soft_text : 'no';

    $whatsapp_soft_text = $_POST['whatsapp_soft_text'];
    $whatsapp_soft = (isset($_POST['whatsapp_soft']) && $_POST['whatsapp_soft'] == "yes") ? $whatsapp_soft_text : 'no';

    $email_soft_text = $_POST['email_soft_text'];
    $email_soft = (isset($_POST['email_soft']) && $_POST['email_soft'] == "yes") ? $email_soft_text : 'no';

    // $paid_ads_soft_text=$_POST['paid_ads_soft_text'];
    // $paid_ads_soft = (isset($_POST['paid_ads_soft']) && $_POST['paid_ads_soft']=="yes") ? $paid_ads_soft_text : 'no' ;



    $data = array(
        // "name" => $_POST['name'],
        "organization" => $_POST['organization'],
        "organization_phone" => $_POST['organization_phone'],

        "user_id" => $_SESSION['uId'],
        // "phone" => $_POST['phone'],
        "website" => $_POST['website'],
         "source" => $_POST['source'],
        // "email" => $_POST['email'],
        "state" => $_POST['state'],
        "city" => $_POST['city'],
        "address" => $_POST['address'],
        "extra_location" => $extra_location,
        "visa" => $visa,
        "ielts" => $ielts,
        "vistor_visa" => $vistor_visa,
        "work_visa" => $work_visa,
        "pr" => $pr,
        "other_services" => $_POST['other_services'],
        "crm_soft" => $crm_soft,
        "calling_soft" => $calling_soft,
        "sms_soft" => $sms_soft,
        "whatsapp_soft" => $whatsapp_soft,
        "email_soft" => $email_soft,
        // "paid_ads_soft"=>$paid_ads_soft,
        "crm_users" => $_POST['crm_users'],
        // "procedure" => $_POST['procedure'],
        // "followup_status" => $_POST['followup_status'],
        // "followup_substatus" => $_POST['followup_substatus'],
        // "next_followup_date" => $_POST['next_followup_date'],
        "remarks" => $_POST['remarks'],
        "contract" => $file['data'],
        "link_email" => $email,
        "link_whatsapp" => $whatsapp,
    );
    
    $validate = validateLead($_POST['organization']??'',$_POST['organization_phone']??'');
      if($validate){
        http_response_code(500);
        $response = array(
          "error" => true,
          "message" => $validate
        );
        echo json_encode($response);
        exit;
      }
    $res = qinsert($param, $data);
    $last_id = $res['success_id'];
    $userName = userName($_SESSION['uId']);
    notify(getAdminId(),"A new Lead:$last_id has been added by $userName",'user',"/view-lead/$last_id",'Lead Created');
    echo json_encode($res);
    
    //  Storing data to table name followups
    $param = array("tb" => "followups");
    $data = array(
        "lead_id" => $last_id,
        "user_id" => $_SESSION['uId'],
        // "status" => $_POST['followup_status'],
        // "sub_status" => $_POST['followup_substatus'],
        // "followup_date" => $_POST['next_followup_date'],
        "remarks" => $_POST['remarks'],
    );



    $res = qinsert($param, $data);
    //echo json_encode($res);


    //  Storing data to table name meetings

    if (!empty($_POST['schedule_date'])) {

        $param = array("tb" => "meetings");
        $status = "scheduled";
        $data = array(

            "lead_id" => $last_id,
            "meeting_date" => $_POST['schedule_date'],
            "assigned_by" => $_SESSION['uId'],
            "assigned_to" => $_SESSION['uId'],
            // "procedure" => $_POST['procedure'],
            "remarks" => $_POST['remarks'],
            "status" => $status

        );
    }

    $res = qinsert($param, $data);


    //  Storing data to table name lead_locations
    if (count($_POST['location_city']) > 0) {

        $data = array();
        foreach ($_POST['location_city'] as $key => $a) {

            $data[] = array("lead_id" => $last_id, "state" => $_POST['location_state'][$key], "city" => $a);
        }

        $param = array("tb" => "lead_locations");
        $res = qminsert($param, $data);
    }
    
    $date_time = $_POST['schedule_date'];
    $date_time_parts = explode(' ', $date_time);
    $date = substr($date_time, 0, 10);
    $time = substr($date_time, 11);
    
    $con = mysqli_connect("localhost", "innerxcrm_internal", "innerxcrm_internal@77", "innerxcrm_team_panel");
    
    $sales_manager_name = getUserName($con);
    $link = '';
    if( $_SESSION['uId'] == 32){ 
    $link ="https://meet.google.com/skw-xujh-ppc";
    } 
    if( $_SESSION['uId'] == 33){
        $link ="https://meet.google.com/uog-kacq-nug";
    }
    if( $_SESSION['uId'] == 30){
        $link ="https://meet.google.com/ior-fvxu-apb";
    }
    if( $_SESSION['uId'] == 31){
        $link ="https://meet.google.com/jef-sowe-aqi";
    }
    if($_SESSION['uId'] == 38){
        $link ="https://meet.google.com/kjc-hpss-aaj";
    }
    if($_SESSION['uId'] == 54){
        $link ="https://meet.google.com/qcu-aqri-urv";
    }
    if($_SESSION['uId'] == 55){
        $link ="https://meet.google.com/tph-hkcn-yhm";
    }
    if($_SESSION['uId'] == 56){
        $link ="https://meet.google.com/xkr-esgz-zqg";
    }
    if($_SESSION['uId'] == 64){
        $link ="https:/xkr-esgz-zqg";
    }

    if($_SESSION['uId'] != 12){
        $response = trainingScheduled($_POST['organization'], $_POST['organization_phone'], $_POST['organization'], $sales_manager_name, $date, $time,$link);
    }
    

      
}
