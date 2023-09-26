<?php

// ini_set('display_errors',1);

require_once('Classes/Config/Builder.php');
require_once('Classes/Config/Response.php');
require_once('Classes/Config/Pagination.php');


use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

// define table at once 
const LOAN_TABLE = 'cp_loan';


if ($action == 'saveLoan') {
    unset($_POST['_action'], $_POST['payload']);
    try {
        $_POST['user_id'] = $_SESSION['uId'];
        $builder = new Builder();
        $builder->table(LOAN_TABLE)->insert($_POST);
        return Response::success('Details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}



if ($action == 'getLoanDetails') {
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
        $primaryTable = LOAN_TABLE;
        if (!empty($formattedFirstDate) && !empty($formattedLastDate)) {
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->orderBy("$primaryTable.created_at", 'desc')
                ->where("$primaryTable.user_id", '=', $userId)
                ->where("DATE($primaryTable.created_at)", '>=', $formattedFirstDate)
                ->where("DATE($primaryTable.created_at)", '<=', $formattedLastDate, '')
                ->take($limit)
                ->get();
            $totalCount = $builder->count();
        } else {
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->where("$primaryTable.user_id", '=', $userId, '')
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


if ($action == 'deleteLoan') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    try {
        $builder = new Builder();
        $builder->table(LOAN_TABLE)->where('id', '=', $id, '');
        $builder->delete();

        Response::success('Record has been deleted');
    } catch (\Throwable $th) {
        Response::error('Failed to delete record' . $th->getMessage());
    }

}

if ($action == 'updateLoan') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    unset($_POST['_action'], $_POST['payload']);
    try {
        update(LOAN_TABLE, $_POST, " id='$id'");
        return Response::success('Changes saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}
