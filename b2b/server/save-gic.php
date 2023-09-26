<?php

require_once('bootstrap.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

const GIC_TABLE = 'cp_gic';

if ($action == 'getOrganizations') {
    $userId = $_SESSION['uId'];
    try {
        $queryBuilder = new Builder();
        $queryBuilder->table('cp_leads')
            ->select('id,organization')
            ->where('organization', '!=', 'null');
        if ($_SESSION['u_type'] !== 1) {
            $queryBuilder->where('marketing_id', '=', $userId, '');
        }
        $res = $queryBuilder->groupBy('organization')->get();

        return Response::json(['statusCode' => 200, 'data' => $res], 200);
    } catch (Exception $e) {
        return Response::json(['statusCode' => 500, 'status' => 0, 'data' => [], 'message' => 'Failed to get data' . $e->getMessage()], 500);
    }
}

if ($action == 'getGicDetails') {
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
        $primaryTable = GIC_TABLE;
        if (!empty($formattedFirstDate) && !empty($formattedLastDate)) {
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*,cp_leads.organization,cp_leads.organization_email,cp_leads.reward as commision")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->join('cp_leads', "$primaryTable.organization_id", '=', 'cp_leads.id', 'LEFT');
            if (!empty($userId)) {
                $builder->where("$primaryTable.user_id", '=', $userId);
            }
            $builder->where("DATE($primaryTable.created_at)", '>=', $formattedFirstDate)
                ->where("DATE($primaryTable.created_at)", '<=', $formattedLastDate, '')
                ->take($limit)
                ->get();
            $totalCount = $builder->count();
        } else {
            $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*,cp_leads.organization,cp_leads.organization_email,cp_leads.reward as commision")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->join('cp_leads', "$primaryTable.organization_id", '=', 'cp_leads.id', 'LEFT');
            if (!empty($userId)) {
                $builder->where("$primaryTable.user_id", '=', $userId, '');
            }
            $res = $builder->orderBy("$primaryTable.created_at", 'desc')
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

if ($action == 'saveGic') {
    $data = [
        'organization_id' => $_POST['organization_id'],
        'user_id' => $_SESSION['uId'],
        'student_name' => $_POST['student_name'],
        'passport_number' => $_POST['passport_number'],
        'bank' => $_POST['bank'],
        'gic_acc_number' => $_POST['gic_account_number'],
        'gic_reference_number_for_simpli' => $_POST['gic_reference_number_simpli'],
        'amount' => $_POST['amount'],
        // 'commision' => $_POST['commission'],
    ];
    try {
        $builder = new Builder();
        $builder->table('cp_gic')
            ->insert($data);
        return Response::success('Gic details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}

if ($action == 'updateGic') {
    unset($_POST['_action'], $_POST['payload']);
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    try {
        update('cp_gic', $_POST, "id='$id'", false);
        return Response::success('Record updated successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to update the record because: ' . $th->getMessage());
    }
}

if ($action == 'deleteGicDetail') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    try {
        $builder = new Builder();
        $builder->table(GIC_TABLE)->where('id', '=', $id, '');
        $builder->delete();
        return Response::success('Record deleted successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to delete the record, Check logs for more information');
    }
}
