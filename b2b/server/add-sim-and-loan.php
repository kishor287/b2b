<?php

require_once('bootstrap.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

// define table at once 
const SIM_AND_LOAN_TABLE = 'cp_sim';


if ($action == 'saveSimAndLoan') {
    unset($_POST['_action'], $_POST['payload']);
    try {
        $_POST['user_id'] = $_SESSION['uId'];
        $barCode = $_FILES['sim_card_barcode'];
        $img = storeImg(['image' => $barCode], 'sim-barcodes');
        $barCodePath = '';
        if ($img['status'] = 1) {
            $barCodeName = $img['filename'];
            $barCodePath = $img['path'];
        } else {
            return Response::error('BarCode Image has some errors, Please try again');
        }
        $_POST['sim_card_barcode'] = $barCodePath;
        $builder = new Builder();
        $builder->table(SIM_AND_LOAN_TABLE)->insert($_POST);
        return Response::success('Details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}
if ($action == 'updateSimAndLoan') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    $barCode = $_FILES['sim_card_barcode'];
    $img = storeImg(['image' => $barCode], 'sim-barcodes');
    $barCodePath = '';
    if ($img['status'] = 1) {
        $barCodeName = $img['filename'];
        $barCodePath = $img['path'];
    } else {
        return Response::error('BarCode Image has some errors, Please try again');
    }
    $_POST['sim_card_barcode'] = $barCodePath;
    unset($_POST['_action'], $_POST['payload']);
    try {
        update(SIM_AND_LOAN_TABLE, $_POST, " id='$id'");
        return Response::success('Changes saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}


if ($action == 'getLoanSimDetails') {
    $userId = $_SESSION['uId'];
    if($_SESSION['utype'] == 13){
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
        $primaryTable = SIM_AND_LOAN_TABLE;
        if (!empty($formattedFirstDate) && !empty($formattedLastDate)) {
            if($userId){
                $builder->where("$primaryTable.user_id", '=', $userId);
            }
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->orderBy("$primaryTable.created_at", 'desc')
                ->where("DATE($primaryTable.created_at)", '>=', $formattedFirstDate)
                ->where("DATE($primaryTable.created_at)", '<=', $formattedLastDate, '')
                ->take($limit)
                ->get();
            $totalCount = $builder->count();
        } else {
            if($userId){
                $builder->where("$primaryTable.user_id", '=', $userId, '');
            }
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
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

if ($action == 'deleteSimLoan') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    try {
        //code...
        $builder = new Builder();
        $builder->table(SIM_AND_LOAN_TABLE)->where('id', '=', $id, '');
        $builder->delete();

        Response::success('Record has been deleted');
    } catch (\Throwable $th) {
        Response::error('Failed to delete record' . $th->getMessage());
    }

}