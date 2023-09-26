<?php

if ($action === 'getFintechServiceUsers') {
    $filters = json_decode($_POST['filter']);
    $condition = ' where 1=1 ';

    if (!empty($filters->filter)) {
        if (!empty($filters->filter->organization)) {
            $organization = $filters->filter->organization;
            $sub_domain = select('cp_registration', 'sub_domain', " where id='$organization'", '', '');
            if (isset($sub_domain[0]['sub_domain'])) {
                $sub_domain = $sub_domain[0]['sub_domain'];
                $condition .= " AND s.sub_domain= '$sub_domain'";
                $month = ' ';
                $year = ' ';
            }
        }
        if (!empty($filters->filter->month) && !empty($filters->filter->year)) {
            $month = $filters->filter->month;
            $year = $filters->filter->year;
            $condition .= " AND MONTH(s.created_at) = $month and YEAR(s.created_at) = $year";
        }else{
            $month = date('m');
            $year = date('Y');
            $condition .= " AND MONTH(s.created_at) = $month and YEAR(s.created_at) = $year";
        }
    }

    $pagination = $_POST['pagination'];
    $limit = $_POST['limit'];

    // SUM(CASE WHEN s.service = 'FOREX' THEN 1 ELSE 0 END) AS forex_count,
    $param = array(
        "col" => "r.id as org_id, r.organization, s.sub_domain,s.id as id,
        SUM(CASE WHEN s.service = 'credit_card' THEN 1 ELSE 0 END) AS credit_card_count,
        SUM(CASE WHEN s.service = 'LOAN' THEN 1 ELSE 0 END) AS loan_count,
        SUM(CASE WHEN s.service = 'INSURANCE' THEN 1 ELSE 0 END) AS insurance_count,
        (SELECT COUNT(*) FROM cp_forex_requests WHERE cp_forex_requests.sub_domain = s.sub_domain) AS forex_count,
        SUM(CASE WHEN s.service = 'SIM' THEN 1 ELSE 0 END) AS sim_count,
        SUM(CASE WHEN s.service = 'GIC' THEN 1 ELSE 0 END) AS gic_count",
        "tb" => "cp_fintech_request_services s",
        "join" => array(
            array(
                "table" => "cp_registration r",
                "condition" => "s.sub_domain = r.sub_domain",
            ),
        ),
        "group" => "s.sub_domain",
        "where" => $condition,
        "sort" => "",
        "limit" => "",
        "pagination" => "",
        // 'debug' => ''
    );
    // removed status is null  from the above query
    $organizations = select('cp_registration', 'id,organization', " ", '', '');
    //print_r($param);
    $subdomains = joinSelect($param);
    $todayRequestsQueryParams = array(
        "col" => "r.id as org_id, r.organization, s.sub_domain,s.id as id,
        SUM(CASE WHEN s.service = 'credit_card' THEN 1 ELSE 0 END) AS credit_card_count,
        SUM(CASE WHEN s.service = 'LOAN' THEN 1 ELSE 0 END) AS loan_count,
        SUM(CASE WHEN s.service = 'INSURANCE' THEN 1 ELSE 0 END) AS insurance_count,
        (SELECT COUNT(*) FROM cp_forex_requests WHERE cp_forex_requests.sub_domain = s.sub_domain) AS forex_count,
        SUM(CASE WHEN s.service = 'SIM' THEN 1 ELSE 0 END) AS sim_count,
        SUM(CASE WHEN s.service = 'GIC' THEN 1 ELSE 0 END) AS gic_count",
        "tb" => "cp_fintech_request_services s",
        "join" => array(
            array(
                "table" => "cp_registration r",
                "condition" => "s.sub_domain = r.sub_domain",
            ),
        ),
        "group" => "s.sub_domain",
        "where" => ' where created_at >= CURDATE()',
        "sort" => "",
        "limit" => "",
        "pagination" => "",
        //'debug' => ''
    );
    //print_r($param);
    $todayRequests = joinSelect($todayRequestsQueryParams);
    echo json_encode(compact('subdomains', 'organizations', 'todayRequests','year','month'));
}

if ($action == 'getServices') {
    $service = $_POST['service'];
    // $service = strtoupper(trim(str_replace('_', ' ', $service)));
    $sub_domain = $_POST['sub_domain'];
    $sub_domain = strtolower(trim($sub_domain));
    if ($service == 'FOREX' || $service == 'forex') {
        $param = [
            'tb' => 'cp_forex_requests frs',
            'where' => "where frs.sub_domain = '$sub_domain'",
            'col' => 'frs.*,uhs.*',
            'pagination' => '',
            'sort' => '',
            'group' => '',
            'limit' => '',
            'join' => [
                [
                    'table' => 'cp_user_has_services uhs',
                    'condition' => 'frs.user_id = uhs.id'
                ]
            ]
        ];
        $getServices = joinSelect($param);
    } else {

        $param = [
            'tb' => 'cp_fintech_request_services frs',
            'where' => "where frs.service = '$service' AND frs.sub_domain = '$sub_domain'",
            'col' => 'frs.*,uhs.*,frs.status as fintech_status',
            'pagination' => '',
            'sort' => '',
            'group' => '',
            'limit' => '',
            'join' => [
                [
                    'table' => 'cp_user_has_services uhs',
                    'condition' => 'frs.service_user_id = uhs.id'
                ]
            ]
        ];
        $getServices = joinSelect($param);
    }
    http_response_code(200);
    echo json_encode($getServices);
}

if ($action === 'deleteRecord') {
    $id = $_POST['id'];
    $param = [
        "tb" => "user_has_services",
        "where" => "WHERE id = $id",
        'limit' => '1'
    ];
    $delete = qdelete($param, '');
    if ($delete) {
        http_response_code(200);
        echo json_encode([
            'message' => 'Record deleted successfully',
            'status' => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'message' => 'Failed to delete record',
            'status' => true
        ]);
    }
}

if ($action == 'addRemarks') {

    $remarks = $_POST['remarks'];
    $remarks = trim(str_replace('\'', ' ', $remarks));
    $status = $_POST['status'];
    $service = $_POST['service'];
    $id = $_POST['id'];

    if ($service == "FOREX") {
        $getCrmRequestId = select('cp_forex_requests', 'crm_forex_id,sub_domain', " where id = $id", '', '');
        $uuid = $getCrmRequestId[0]['crm_forex_id'];
    } else {
        $getCrmRequestId = select('cp_fintech_request_services', 'crm_request_id,sub_domain', " where id = $id", '', '');
        $uuid = $getCrmRequestId[0]['crm_request_id'];
    }
    $subDomain = $getCrmRequestId[0]['sub_domain'];
    if (empty($subDomain)) {
        http_response_code(400);
        echo json_encode([
            'message' => 'subdomain not found',
            'status' => false,
            'statusCode' => 404,
        ]);
        exit();
    }
    if (empty($uuid)) {
        http_response_code(400);
        echo json_encode([
            'message' => 'Record Not Found in ' . $subDomain,
            'status' => false,
            'statusCode' => 404,
        ]);
        exit();
    }

    if (!empty($_POST['loan_disbursed_amount'])) {
        $loanAmount = $_POST['loan_disbursed_amount'];
    } else {
        $loanAmount = 0;
    }
    $apiKey = "9f0GShaM285IEpeKU4YxcovFQhT9LoRZ"; // Replace with your API key
    $service = str_replace(' ', '_', $service);
    if ($subDomain == 'innerxfintech') {
        $url = "https://" . $subDomain . ".com/api/fintech-followups?key={$apiKey}&action=remarks";
    } else {
        $url = "https://" . $subDomain . ".innerxcrm.com/api/fintech-followups?key={$apiKey}&action=remarks";
    }
    // Initialize cURL
    $data = [
        'remarks' => $remarks,
        'status' => $status,
        'service' => $service,
        'uuid' => $uuid,
        'loanamount' => $loanAmount,
        'key' => $apiKey,
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        $error = curl_error($curl);
        http_response_code(500);
        echo json_encode(['message' => $error, 'status' => false, 'response' => $response, 'statusCode' => 500]);
    } else {
        if ($service == "FOREX") {
            $res = update('cp_forex_requests', ['remarks' => $remarks, 'status' => $status], "id='$id' and crm_forex_id='$uuid' ");
        } else {
            $res = update('cp_fintech_request_services', ['remarks' => $remarks, 'status' => $status, 'loan_disbursed_amount' => $loanAmount], "id='$id' and crm_request_id='$uuid' and service='$service'");
        }
        http_response_code(200);
        echo json_encode(['message' => 'Remarks added successfully', 'status' => true, 'statusCode' => 200, 'response' => $response]);
    }
    // Close cURL
    curl_close($curl);
}