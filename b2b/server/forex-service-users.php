<?php


if ($action == 'get_forex_users') {
    $queryParams = [
        'col' => 'cp_forex_requests.*,cp_forex_requests.id as service_id,cp_forex_requests.status as requeststatus,cp_user_has_services.*,cp_registration.organization',
        'tb' => 'cp_forex_requests',
        'join' => [
           [
              'table' => 'cp_user_has_services',
              'condition' => 'cp_forex_requests.user_id = cp_user_has_services.id'
           ],
           [
              'table' => 'cp_registration',
              'condition' => 'cp_forex_requests.sub_domain = cp_registration.sub_domain'
           ]
        ],
        "pagination" => $_POST['pagination'],
        "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
        "where"=> '',
        "sort" => " ORDER BY cp_forex_requests.id DESC ",
     ];
    $res = joinSelect($queryParams);
    echo json_encode($res);
  }
  