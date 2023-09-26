<?php

// ini_set("display_errors",1);

require_once("bootstrap.php");

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

const TRAVEL_PLAN_TABLE = 'cp_travel_plan';
if($action == 'addTravelPlan'){
    $userId = $_SESSION['uId'];
    $data = [
        // 'organization_id' => $_POST['organization_id'],
        'agent_name' => $_POST['agent_name'],
        'user_id' => $userId,
        'city' => $_POST['city'],
        'location' => $_POST['location'],
        'revisit' => $_POST['revisit'],
        'silver' => $_POST['silver'],
        'remarks' => $_POST['remarks'],
    ];
    try {
        $builder = new Builder();
        $builder->table(TRAVEL_PLAN_TABLE)->insert($data);
        return Response::success('Travel Plan details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details'. $th->getMessage());
    }
}

if($action == 'getTravelPlanData'){
    $userId = $_SESSION['uId'];
    if($_SESSION['utype'] == 13){
        $userId = '';
    }
    $limit = !empty($_POST['limit']) ? $_POST['limit'] : 10;
    $page = !empty($_POST['page']) ? $_POST['page'] : 1;
    $filter = !empty($_POST['filter']) ? $_POST['filter'] : '';
    $formattedFirstDate = '';
    $formattedLastDate = '';
    if($filter){
        $filter = json_decode($filter);
        if(!empty($filter->dateRange)){
            $dateRange = $filter->dateRange;
            $dateParts = explode(' - ', $dateRange);
            $firstDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[0]));
            $lastDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[1]));
            $formattedFirstDate = $firstDate->format('Y-m-d');
            $formattedLastDate = $lastDate->format('Y-m-d');
        }
    }
    $primaryTable = TRAVEL_PLAN_TABLE;
    $page = empty($_POST['page']) ? 1 : $_POST['page'];
    $limit = empty($_POST['limit']) ? 10 : $_POST['limit'];
    try {
        if(!empty($formattedFirstDate) && !empty($formattedLastDate)){
            $builder = new Builder();
            if ($userId) {
                $builder->where("user_id", '=', $userId);
            }
            $res = $builder->table($primaryTable)
                        ->select("*")
                        ->setPage($page)
                        ->where("DATE(created_at)", '>=', $formattedFirstDate)
                        ->where("DATE(created_at)", '<=', $formattedLastDate, '')
                        ->take($limit)
                        ->get();
            $totalCount = $builder->count();
            $pagination = new Pagination();
        }else{
            $builder = new Builder();
            if ($userId) {
                $builder->where("user_id", '=', $userId,'');
            }
            $res = $builder->table($primaryTable)
                        ->select("*")
                        ->setPage($page)
                        // ->join('cp_leads', "$primaryTable.organization_id", '=', 'cp_leads.id', 'LEFT')
                        // ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                        ->take($limit)
                        ->get();
            $totalCount = $builder->count();
            $pagination = new Pagination();

        }
        $pages = $pagination->getAllPageLinks($totalCount, '', $limit, $page);
        return Response::json(['data' => $res, 'pagination' => $pages, 'statusCode' => 200], 200);
    } catch (\Throwable $th) {
        return Response::error('Failed to get the data '. $th->getMessage());
    }
}

if($action == 'updateTravelPlan'){

    unset($_POST['_action'],$_POST['payload']);
    $id = $_POST['id'];
    if(empty($id)){
        return Response::error('Record not found');
    }
    try {
        update(TRAVEL_PLAN_TABLE,$_POST,"id='$id'");
        return Response::success('Changes saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to make changes ' . $th->getMessage());
    }

}

if($action == 'deleteTravelPlan'){
     $id  = $_POST['id'];
     if(empty($id)){
        return Response::error('Record not found');
    }
    try {
        $builder = new Builder();
        $builder->table(TRAVEL_PLAN_TABLE)->where('id','=',$id , '');
        $builder->delete();
        return Response::success('Record Deleted');
    } catch (\Throwable $th) {
        return Response::error('Failed to delete record ' . $th->getMessage());
    }
}