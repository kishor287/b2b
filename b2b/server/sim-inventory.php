<?php

ini_set('display_errors', 1);
require_once '../vendor/autoload.php'; // Include the Composer autoloader

require_once('Classes/Config/Builder.php');
require_once('Classes/Config/Response.php');
require_once('Classes/Config/Pagination.php');
require_once('Classes/Config/DatabaseSeeder.php');
require_once('Classes/Seeder/SimInventorySeeder.php');


use server\Classes\Seeder\SimInventorySeeder;
use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

const SIM_INVENTORY_TABLE = 'cp_sim_inventory';
if ($action == 'saveSimInventory') {

    unset($_POST['_action'], $_POST['payload']);
    $barCodePath = '';
    if (!empty($_FILES['barcode']['name'])) {
        $file['image'] = $_FILES['barcode'];
        $file = storeImg($file, 'sim-barcodes');
        $barCodePath = $file['path'];
        if ($file['status'] == 0) {
            Response::error('Image has some errors');
        }
    }
    try {
        $db = new Builder();
        $_POST['bar_code'] = $barCodePath;
        $_POST['user_id'] = $_SESSION['uId'];
        $db->table('cp_sim_inventory')->insert($_POST);
        Response::success('Data saved successfully');
    } catch (\Throwable $th) {
        Response::error('Failed to save the data');
    }
}

if ($action == 'getSimInventory') {
    $userId = $_SESSION['uId'];
    $limit = !empty($_POST['limit']) ? $_POST['limit'] : 10;
    $page = !empty($_POST['page']) ? $_POST['page'] : 1;
    $filter = !empty($_POST['filter']) ? $_POST['filter'] : '';
    $formattedFirstDate = '';
    $formattedLastDate = '';
    if ($filter) {
        $filter = json_decode($filter);
        if (!empty($filter->dateRange)) {
            $dateRange = $filter->dateRange;
            $dateParts = explode(' - ', $dateRange);
            $firstDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[0]));
            $lastDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[1]));
            $formattedFirstDate = $firstDate->format('Y-m-d');
            $formattedLastDate = $lastDate->format('Y-m-d');
        }
    }

    try {
        $builder = new Builder();
        $primaryTable = SIM_INVENTORY_TABLE;
        if (!empty($formattedFirstDate) && !empty($formattedLastDate)) {
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->orderBy("$primaryTable.created_at", 'desc')
                ->where("$primaryTable.user_id", '=', $userId)
                ->where("DATE($primaryTable.created_at)", '>=', $formattedFirstDate)
                ->where("DATE($primaryTable.created_at)", '<=', $formattedLastDate)
                ->where("$primaryTable.is_assigned", '=', 0, '')
                ->take($limit)
                ->get();
            $totalCount = $builder->count();
        } else {

            $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->where("$primaryTable.user_id", '=', $userId)
                ->where("$primaryTable.is_assigned", '=', 0, '')
                ->orderBy("$primaryTable.created_at", 'desc')
                ->take($limit)
                ->get();

            $totalCount = $builder->count();
        }

        $pagination = new Pagination();
        $pages = $pagination->getAllPageLinks($totalCount, '', $limit, $page);
        return Response::json(['data' => $res, 'pagination' => $pages, 'statusCode' => 200], 200);
    } catch (\Throwable $th) {
        return Response::error('Failed to get the data' . $th->getMessage());
    }
}

if ($action == 'getMarketingPeople') {
    $db = new Builder();
    $result = $db->table('cp_users')->where('role', '=', '14')->where('fname', '!=', 'null', '')->get();
    Response::json(['status' => 1, 'data' => $result], 200);
}

if ($action == 'getAgents') {
    $db = new Builder();
    $result = $db->table('cp_leads')
        ->select('organization,id')
        ->groupBy('organization')
        ->where('organization', '!=', 'null', '')
        ->get();
    Response::json(['status' => 1, 'data' => $result], 200);
}

if ($action == 'getStudents') {
    $db = new Builder();
    $result = $db->table('cp_sim_inventory')
        ->select('student_name')
        ->where('student_name', '!=', 'null', '')
        ->groupBy('student_name')
        ->get();
    Response::json(['status' => 1, 'data' => $result], 200);
}

if ($action == 'assignSimNumbers') {
    $data = json_decode($_POST['data'], true);
    if (!is_array($data)) {
        return Response::error(' data format is not valid');
    }
    try {
        foreach ($data as $item) {
            $id = $item['id'];
            $organizationName = $item['organizationName'];
            $marketingManager = $item['marketingManager'];
            $backendTeam = $item['backendTeam'];
            $studentName = $item['studentName'];

            update(SIM_INVENTORY_TABLE, [
                'company_name' => $organizationName,
                'marketing_id' => $marketingManager,
                'backend_team' => $backendTeam,
                'is_assigned' => true,
                'student_name' => $studentName
            ], "id='$id'");
        }
        Response::success('Done');
    } catch (\Throwable $th) {

        Response::error('Failed' . $th->getMessage());
    }
}

if ($action == 'getAssignedSimInventory') {
    $userId = $_SESSION['uId'];
    $limit = !empty($_POST['limit']) ? $_POST['limit'] : 10;
    $page = !empty($_POST['page']) ? $_POST['page'] : 1;
    $filter = !empty($_POST['filter']) ? $_POST['filter'] : '';
    $formattedFirstDate = '';
    $formattedLastDate = '';
    if ($filter) {
        $filter = json_decode($filter);
    } else {
        $filter = new stdClass();
    }

    try {
        $builder = new Builder();
        $primaryTable = SIM_INVENTORY_TABLE;
        $builder->table($primaryTable)
            ->select("cp_users.fname as marketingPersonFname,cp_users.lname as marketingPersonLname,$primaryTable.*")
            ->setPage($page)
            ->join('cp_users', "$primaryTable.marketing_id", '=', 'cp_users.id', 'LEFT')
            ->where("$primaryTable.user_id", '=', $userId)
            ->where("is_assigned", '=', 1)
            ->orderBy("$primaryTable.created_at", 'desc')
            ->take($limit);

        if (!empty($filter->dateRange)) {
            $dateRange = $filter->dateRange;
            $dateParts = explode(' - ', $dateRange);
            $firstDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[0]));
            $lastDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[1]));
            $formattedFirstDate = $firstDate->format('Y-m-d');
            $formattedLastDate = $lastDate->format('Y-m-d');
            $builder->where("DATE($primaryTable.created_at)", '>=', $formattedFirstDate);
            $builder->where("DATE($primaryTable.created_at)", '<=', $formattedLastDate);
        }
        
        if (!empty($filter->marketing_id)) {
            $builder->where("$primaryTable.marketing_id", '=', $filter->marketing_id);
        }
        if (!empty($filter->backend_team)) {
            $builder->where("$primaryTable.backend_team", '=', $filter->backend_team);
        }
        if (!empty($filter->company_name)) {
            $builder->where("$primaryTable.company_name", '=', $filter->company_name);
        }
        if (!empty($filter->student_name)) {
            $builder->search("$primaryTable.student_name", $filter->student_name);
        }
        $res = $builder->where('1', '=', '1', '')->get();
        $totalCount = $builder->count();
        $pagination = new Pagination();
        $pages = $pagination->getAllPageLinks($totalCount, '', $limit, $page);
        return Response::json(['data' => $res, 'pagination' => $pages, 'statusCode' => 200], 200);
    } catch (\Throwable $th) {
        return Response::error('Failed to get the data' . $th->getMessage());
    }
}