<?php


if ($action == 'save') {

    $vendor_id = $_POST['vendors'];
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    $ifsc_code = $_POST['ifsc_code'];
    $micr_code = $_POST['micr_code'];
    $branch_name = $_POST['branch_name'];
    $bank_url = $_POST['bank_url'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $data = array(
        'vendor_id' => $vendor_id,
        'name' => $bank_name,
        'account_no' => $account_number,
        'ifsc' => $ifsc_code,
        'micr' => $micr_code,
        'branch' => $branch_name,
        'created' => $created_at,
        'updated' => $updated_at,
        'url' => $bank_url
    );
    $result = qinsert(['tb' => 'vendor_banks'], $data);
    if ($result['status'] == 1) {
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Vendor Bank Details Added Successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Vendor Bank Details Added Failed'));
    }
}
if ($action == 'get') {

    $vendors = select('cp_vendors', ' id, name', ' WHERE status = 1', ' order by id ASC');
    $params = [
        'col' => 'cp_vendor_banks.*,cp_vendors.id as vendor_id,cp_vendors.name as vendor_name',
        'tb' => 'cp_vendor_banks',
        'join' => array(
            array(
                'table' => 'cp_vendors',
                'condition' => 'cp_vendor_banks.vendor_id = cp_vendors.id',
                'type' => 'LEFT  JOIN'
            )
        ),
        'where' => ' WHERE cp_vendor_banks.is_active = 1',
        'sort' => ' ORDER BY cp_vendor_banks.id DESC',
        'limit' => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
        'pagination' => $_POST['pagination']
    ];
    $bankDetails = joinSelect($params);
    http_response_code(200);
    echo json_encode(['vendors' => $vendors, 'bankDetails' => $bankDetails['data']]);
}

if ($action == 'update') {


    $vendor_id = $_POST['vendors'];
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    $ifsc_code = $_POST['ifsc_code'];
    $micr_code = $_POST['micr_code'];
    $branch_name = $_POST['branch_name'];
    // $bank_url = $_POST['bank_url'];
    $updated_at = date('Y-m-d H:i:s');
    $data = array(
        'vendor_id' => $vendor_id,
        'name' => $bank_name,
        'account_no' => $account_number,
        'ifsc' => $ifsc_code,
        'micr' => $micr_code,
        'branch' => $branch_name,
        'updated' => $updated_at,
        // 'url' => $bank_url
    );
    $table = 'cp_vendor_banks';
    $result = update($table, $data, ' id = ' . $_POST['id']);
    if ($result == 1) {
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Vendor Bank Details Updated Successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Vendor Bank Details Updated Failed'));
    }
}

if($action == 'delete'){
    
    $table = 'cp_vendor_banks';
    $data = array(
        'is_active' => 0
    );
    $result = update($table, $data, ' id = ' . $_POST['id']);
    if ($result == 1) {
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Vendor Bank Details Deleted Successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Vendor Bank Details Deleted Failed'));
    }
}