<?php

// ini_set("display_errors", 1);

require_once('bootstrap.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

// define table at once 
const SALES_TABLE = 'cp_sales';


if ($action == 'saveSalesDetails') {
    unset($_POST['_action'], $_POST['payload']);
    try {
        $_POST['user_id'] = $_SESSION['uId'];
        $builder = new Builder();
        $builder->table(SALES_TABLE)->insert($_POST);
        return Response::success('Sales details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}
if ($action == 'updateSalesDetails') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    unset($_POST['_action'], $_POST['payload']);
    try {
        update(SALES_TABLE, $_POST, " id='$id'");
        return Response::success('Changes saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}


if ($action == 'getSalesDetails') {
    $userId = $_SESSION['uId'];
    if ($_SESSION['utype'] == 13) {
        $userId = '';
    }
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
        $pT = 'cp_leads'; #{$pT - PRIMARY TABLE }
        $select = "$pT.*,$pT.organization as agent_name,
        cp_users.fname,
        CASE
        WHEN $pT.organization_phone IS NULL THEN '0'
        ELSE '1'
        END as contact_status,
        CASE
        WHEN $pT.contracted_at <> '' THEN 'Contracted' 
        ELSE ''
        END as contracted_status";
        if (!empty($formattedFirstDate) && !empty($formattedLastDate)) {
            if ($userId) {
                $builder->where("$pT.marketing_id", '=', $userId);
            }
            $res = $builder->table('cp_leads')
                ->select($select)
                ->setPage($page)
                ->join('cp_users', "$pT.marketing_id", '=', 'cp_users.id', 'LEFT')
                ->where("DATE($pT.created)", '>=', $formattedFirstDate)
                ->where("DATE($pT.created)", '<=', $formattedLastDate, '')
                ->take($limit)
                ->get();
            $totalCount = $builder->count();
        } else {
            if ($userId) {
                $builder->where("$pT.marketing_id", '=', $userId,'');
            }
            $res = $builder->table('cp_leads')
                ->select($select)
                ->setPage($page)
                ->join('cp_users', "$pT.marketing_id", '=', 'cp_users.id', 'LEFT')
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

if ($action == 'deleteSales') {
    $id = $_POST['id'];
    if (empty($id)) {
        Response::error('Record not found');
    }
    try {
        //code...
        $builder = new Builder();
        $builder->table(SALES_TABLE)->where('id', '=', $id, '');
        $builder->delete();

        Response::success('Record has been deleted');
    } catch (\Throwable $th) {
        Response::error('Failed to delete record' . $th->getMessage());
    }
}
