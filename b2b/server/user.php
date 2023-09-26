<?


if ($action == 'get_roles') {
    $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "roles", "where" => "", "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
}
/*
Save
*/
if (!function_exists('qupdate')) {
    function qupdate($param, $data, $remove)
    {
        include('../_con.php');

        if ($remove == 1) {
            $stmt = $con->query('update  ' . $tb_pre . $param['tb'] . ' set ' . $param['col'] . '  ' . $param['where'] . ' limit ' . $param['limit'] . '  ');
            $count = $stmt->rowCount();
            $result = array("status" => 1, "msg" => "success", "data" => "Removed successfully", "count" => $count);
            return $result;
        } else {
            $values = array();
            $cols = '';
            foreach ($data as $name => $value) {
                $cols .= ' ' . $name . ' = :' . $name . ',';
                $values[':' . $name] = $value;
            }
            $cols = substr($cols, 0, -1);

            $stmt = $con->prepare('update ' . $tb_pre . $param['tb'] . ' set  ' . $cols . '  ' . $param['where'] . ' limit ' . $param['limit'] . '  ');
            if ($stmt->execute($values)) {
                $result = array("status" => 1, "msg" => "success", "data" => "Data updated successfully.");
            } else {
                $result = array("status" => 0, "msg" => "error", "data" => "Failed to save the data.");
            }
            return $result;
        }
    }
}


if ($action == 'get_reporting_roles') {
    $param = select('cp_roles', '*', ' where type=2', '', '');
    http_response_code(200);
    echo json_encode($param);
}

if ($action == 'get') {
    $filter = 'where 1=1';

    $appliedFilter = json_decode($_POST['filter'], true);
    if (!empty($appliedFilter)) {
        $getFilters = $appliedFilter['filter'];

        if (isset($appliedFilter['id']) && ($appliedFilter['id'] != '')) {
            $filter .= " and id='" . $appliedFilter['id'] . "'  ";
        }

        if (isset($getFilters['usertype']) && ($getFilters)) {
            $param = array("pagination" => "", "col" => "id", "tb" => "roles", "where" => "where roles_type='" . $getFilters['usertype'] . "' ", "limit" => "1");
            $res = qselect($param);

            $filter .= " and role='" . $res['data']['id'] . "'  ";
        }

        if (isset($getFilters['statusFilter']) && $getFilters['statusFilter'] !== "") {
            $status = $getFilters['statusFilter'];
            $filter .= " AND status='$status' ";
        }

        if (isset($getFilters['search']) && $getFilters['search'] !== "") {
            $status = $getFilters['search'];
            $filter .= " AND fname='$status' ";
        }

        if (isset($getFilters['daterange']) && $getFilters['daterange'] !== "") {
            $daterange = $getFilters['daterange'];
            $dates = explode(" - ", $daterange);

            $startDate = DateTime::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
            $endDate = DateTime::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');
            //   print_r($endDate);
            //   die;
            $filter .= " AND created BETWEEN '$startDate' AND '$endDate' ";
        }
    }
    $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "users", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
}

/*
Save
*/


if ($action == 'save') {


    $param = array("pagination" => "", "col" => "id", "tb" => "users", "where" => "where username='" . $_POST['user_name'] . "' ", "limit" => "1");
    $res = qselect($param);
    if ($res['count'] == 1) {
        $result = array("status" => 0, "msg" => "error", "data" => "Username already exists");
        echo json_encode($result);
        exit();
    }


    if ($_FILES['pimage']['name'] != '') {
        $file['image'] = $_FILES['pimage'];
        $file = upload_img($file, 'images');

        if ($file['status'] == 0) {
            echo json_encode($file);
            exit();
        }
    } else {
        $file['data'] = '';
    }

    if (!empty($_POST['reporting_manager'])) {
        $data = array(
            "fname" => $_POST['first_name'],
            "lname" => $_POST['last_name'],
            "username" => $_POST['user_name'],
            "password" => $_POST['password'],
            "phone" => $_POST['phone'],
            "role" => $_POST['role'],
            "email" => $_POST['email'],
            "image" => $file['data'],
            "meeting_link" => $_POST['meeting_link'],
            "dob" =>  $_POST['dob'],
            "campaign" => $_POST['campaign'],
            "reporting_manager" => $_POST['reporting_manager']
        );
    } else {
        $data = array(
            "fname" => $_POST['first_name'],
            "lname" => $_POST['last_name'],
            "username" => $_POST['user_name'],
            "password" => $_POST['password'],
            "phone" => $_POST['phone'],
            "role" => $_POST['role'],
            "email" => $_POST['email'],
            "image" => $file['data'],
            "meeting_link" => $_POST['meeting_link'],
            "dob" =>  $_POST['dob'],
            "campaign" => $_POST['campaign'],
        );
    }

    $param = array("tb" => "users");




    $res = qinsert($param, $data);
    $userId = $_SESSION['uId'];
    $username = userName($_SESSION['uId']);
    notify(getAdminId(), "User created by $username:$userId", 'user', '/user', 'User Created');
    echo json_encode($res);
}

/*
Remove
*/

if ($action == 'remove') {
    $param = array("col" => "", "tb" => "users", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
    $res = qdelete($param, '', 1);
    echo json_encode($res);
}



if ($action == 'update') {

    /*
File Insert
*/
    if ($_FILES['pimage']['name'] != '') {
        $file['image'] = $_FILES['pimage'];
        $file = upload_img($file, 'images');

        if ($file['status'] == 0) {
            echo json_encode($file);
            exit();
        }
    } else {
        $file['data'] = '';
    }
    /*
Data Update
*/
    $param = array("tb" => "users", "where" => "where id=" . $_POST['id'] . " ", "limit" => "1");
    $data = array(
        "fname" => $_POST['first_name'],
        "lname" => $_POST['last_name'],
        "username" => $_POST['user_name'],
        "password" => $_POST['password'],
        "phone" => $_POST['phone'],
        "role" => $_POST['role'],
        "email" => $_POST['email'],
        "image" => $file['data'],
        "meeting_link" => $_POST['meeting_link'],
        "dob" =>  $_POST['dob'],
        "campaign" => $_POST['campaign'],
    );

    if (is_null($data['image']) || $data['image'] == '') {
        unset($data['image']);
    }

    $res = qupdate($param, $data, 0);
    echo json_encode($res);
}


if ($action == 'get_users') {
    $filter = 'where 1=1';
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $array = json_decode($_POST['filter'], true);
        if (isset($array['id']) && ($array['id'] != '')) {
            $filter .= " and id='" . $array['id'] . "'  ";
        }
    }
    $param = array("pagination" => $_POST['pagination'], "col" => "*", "tb" => "users", "where" => "" . $filter, "limit" => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit']);
    $res = qselect($param);
    echo json_encode($res);
}

if (isset($_POST['action'])) {
    $col = 'status';
    $val = $_POST['val'] == 0 ? 0 : 1;

    $param = array(
        'tb' => 'users',
        'col' => $col,
        'where' =>  'WHERE `id` = ' . $_POST['id'],
        'limit' => '1'
    );

    $data = array(
        $col => $val
    );

    $result = qupdate($param, $data, 0);
    echo json_encode($result);
}
