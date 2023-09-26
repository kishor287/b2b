<?php

ini_set("display_errors", 1);

require_once('bootstrap.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

// define table at once 
const REMITTANCE_TABLE = 'cp_remittance';

if ($action == 'getRemittance') {
    $limit = !empty($_POST['limit']) ? $_POST['limit'] : 10;
    $page = !empty($_POST['page']) ? $_POST['page'] : 1;
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
    try {

        $table = REMITTANCE_TABLE;
        $builder = new Builder();
        if(!empty($formattedFirstDate) && !empty($formattedLastDate)){
            if($userId){
                $builder->where("$table.user_id", '=', $userId);
            }
            $res = $builder->table($table)
                ->select("cp_users.fname,cp_leads.organization,cp_leads.reward as agent_margin,cp_leads.organization_phone as contact_number,cp_leads.organization_email as email_id,$table.*")
                ->setPage($page)
                ->join('cp_users', "$table.user_id", '=', 'cp_users.id', 'LEFT')
                ->join('cp_leads', "$table.organization_id", '=', 'cp_leads.id', 'LEFT')
                ->where("DATE($table.created_at)",'>=',$formattedFirstDate)
                ->where("DATE($table.created_at)",'<=',$formattedLastDate,'')
                ->orderBy("$table.created_at",'DESC')
                ->take($limit)
                ->get();
                $totalCount = $builder->count();
        }else{
            if($userId){
                $builder->where("$table.user_id", '=', $userId,'');
            }
            $res = $builder->table($table)
                ->select("cp_users.fname,cp_leads.organization,cp_leads.reward as agent_margin,cp_leads.organization_phone as contact_number,cp_leads.organization_email as email_id,$table.*")
                ->setPage($page)
                ->join('cp_users', "$table.user_id", '=', 'cp_users.id', 'LEFT')
                ->join('cp_leads', "$table.organization_id", '=', 'cp_leads.id', 'LEFT')
                ->orderBy("$table.created_at",'DESC')
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

if ($action == "updateRemittance") {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        return Response::error('Record not found');
    }
    unset($_POST['payload'], $_POST['_action']);
    $request = $_POST;
    try {
        update(REMITTANCE_TABLE, $request, " id='$id'");
        return Response::success('Details updated successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to update details' . $th->getMessage());
    }
}