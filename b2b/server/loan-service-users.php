<?php


if ($action == 'get_loan_users') {
    $queryParams = [
        'col' => 'cp_fintech_request_services.*,cp_fintech_request_services.id as service_id,cp_fintech_request_services.status as requeststatus,cp_user_has_services.*,cp_registration.organization',
        'tb' => 'cp_fintech_request_services',
        'join' => [
           [
              'table' => 'cp_user_has_services',
              'condition' => 'cp_fintech_request_services.service_user_id = cp_user_has_services.id'
           ],
           [
              'table' => 'cp_registration',
              'condition' => 'cp_fintech_request_services.sub_domain = cp_registration.sub_domain'
           ]
        ],
        "pagination" => $_POST['pagination'],
        "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
        "where"=> ' where cp_fintech_request_services.service = "loan" OR cp_fintech_request_services.service = "LOAN"',
        "sort" => " ORDER BY cp_fintech_request_services.id DESC ",
     ];
    $res = joinSelect($queryParams);
    echo json_encode($res);
  }
  