<?php

if($action == 'get-monetary-reward'){
    $where = "WHERE 1=1";
    $month = date('m');
    $year = date('Y');
    if(!empty($_POST['filter'])){
        $filters = json_decode($_POST['filter'],true);
        $filters = array_shift($filters);
        if(!empty($filters['year'])){
            $year = $filters['year'];
            $where .= " AND YEAR(s.created_at) = '$year'";
        }
        if(!empty($filters['month'])){
            $month =  $filters['month'];
            $where .= " AND MONTH(s.created_at) = '$month'";
        }
    }
    // SUM(CASE WHEN s.gic_bank <> 0  THEN l.reward ELSE 0 END) as total_gic,
    $param = array(
        "col" => "l.reward,r.organization,s.sub_domain,s.payment_status,
        SUM(CASE WHEN s.gic_acc_certificate <> '0' THEN CAST(l.reward AS DECIMAL) ELSE 0 END) AS total_gic,
        COUNT(CASE WHEN s.service = 'SIM' THEN 1 END) AS sim_count,
        SUM(s.total_fees * s.margin ) AS forex ",
        "tb" => "cp_fintech_request_services s",
        "join" => array(
            array(
                "table" => "cp_registration r",
                "condition" => "s.sub_domain = r.sub_domain"
            ),
            array(
                "table" => "cp_leads l",
                "condition" => "l.organization = r.organization"
            ),
        ),
        "group" => "s.sub_domain",
        "where" => $where,
        "sort" => "",
        "limit" => "",
        "pagination" => "",
        // 'debug' => ''
    );
  
    $students = joinSelect($param);
    echo json_encode(compact('students','month','year'));
}

if($action == 'change-payment-status')
{
    if(!empty($_POST['payment_status']) && !empty($_POST['sub_domain']) && !empty($_POST['year']) && !empty($_POST['month']))
    {
        $condition = " sub_domain = '".$_POST['sub_domain']."' AND YEAR(created_at) = '".$_POST['year']."' AND MONTH(created_at) = '".$_POST['month']."'";
        $update = update('cp_fintech_request_services',['payment_status' => $_POST['payment_status']], $condition);
        if($update){
            http_response_code(200);
            echo json_encode(['message' => 'Payment Status Updated']);
        }
    }else{
        http_response_code(401);
        echo json_encode(['message' => 'Invalid Parameters']);
    }
}