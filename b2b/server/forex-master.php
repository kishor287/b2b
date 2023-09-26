<?php

$userid = $_SESSION['uId'];
if ($action == 'save') {
    if (validateBankName($_POST['bank_name'],$_POST['currency'])) {
        $res = [
            'status' => 'error',
            'message' => 'Bank name and currency already exists'
        ];
        http_response_code(400);
        echo json_encode($res);
        exit;
    }
    $currency = $_POST['currency'];
    $bank_name = $_POST['bank_name'];
    $today_rate = $_POST['today_rate'];

    $data = [
        'currency' => $currency,
        'name' => $bank_name,
        'rate' => $today_rate,
        'created_by' => $userid,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $table = ['tb' => 'vendors'];
    $res = qinsert($table, $data);
    echo json_encode($res);
}

if ($action == 'get') {
    $param = [
        'col' => '*',
        'tb' => 'vendors',
        'where' => 'WHERE 1=1',
        'sort' => 'ORDER BY id DESC',
        'limit' => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
        'pagination' => $_POST['pagination']
    ];
    $res = qselect($param);
    echo json_encode($res);
}

if ($action == 'update') {
    $id = $_POST['id'];
    $data = [];
    if (!empty($_POST['currency'])) {
        $data['currency'] = $_POST['currency'];
    }
    if (!empty($_POST['today_rate'])) {
        $data['rate'] = $_POST['today_rate'];
    }
    if (isset($_POST['status'])) {
        $data['status'] = $_POST['status'];
    }

    try {
        $table = 'cp_vendors';
        $condition = 'id=' . $id;

        $res = update($table, $data, $condition);

        if ($res) {
            http_response_code(200);
            $response = [
                'status' => 'success',
                'message' => 'Successfully updated'
            ];
        } else {
            http_response_code(400);
            $response = [
                'status' => 'error',
                'message' => 'Update failed'
            ];
        }
    } catch (Exception $e) {
        http_response_code(400);
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($response);
}

function validateBankName($bankName,$currency)
{
    $res = select('cp_vendors', ' COUNT(id) as count', ' where name = "' . $bankName . '" AND currency = "' . $currency . '"', '', ' LIMIT 1');
    return $res[0]['count'];
}

// if($action == 'deleteRecord')
// {
//     $id = $_POST['id'];
//     $data = [
//         'tb' => ' vendors',
//         'where' => ' where id=' . $id,
//         'limit' => ' limit 1 '
//     ];
    
//     $res = qdelete($data,$data);

//     if ($res['status'] == 1) {
//         http_response_code(200);
//         $response = [
//             'status' => 'success',
//             'message' => 'Successfully deleted'
//         ];
//     } else {
//         http_response_code(400);
//         $response = [
//             'status' => 'error',
//             'message' => 'Delete failed'
//         ];
//     }
//     echo json_encode($response);
// }

if ($action == 'deleteRecord') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $data = [
            'tb' => 'vendors',
            'where' => 'WHERE id=' . $id,
            'limit' => '1'
        ];

        $res = qdelete($data, $data);

        if ($res['status'] == 1) {
            http_response_code(200);
            $response = [
                'status' => 'success',
                'message' => 'Successfully deleted'
            ];
        } else {
            http_response_code(400);
            $response = [
                'status' => 'error',
                'message' => 'Delete failed'
            ];
        }
    } else {
        http_response_code(400);
        $response = [
            'status' => 'error',
            'message' => 'Invalid request. Missing id parameter.'
        ];
    }
    echo json_encode($response);
}
