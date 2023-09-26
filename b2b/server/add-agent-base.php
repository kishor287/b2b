<?php

require_once('bootstrap.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Pagination;
use Panel\Server\Classes\Config\Response;

// define table at once 
const AGENT_BASE_DETAILS = 'cp_agent_base_details';


if ($action == 'saveAgentBaseDetails') {
    $data = [
        // 'date' => $_POST['date'],
        'agent_name' => $_POST['agent_name'],
        'location' => $_POST['location'],
        'email_id' => $_POST['email_id'],
        'contact_number' => $_POST['contact_number'],
        'organization_id' => $_POST['organization_id'],
        // 'meeting' => $_POST['meeting'],
        // 'visit_type' => $_POST['visit_type'],
        'remarks' => $_POST['remarks'],
        'user_id' => $_SESSION['uId']
    ];
    try {
        $builder = new Builder();
        $builder->table(AGENT_BASE_DETAILS)->insert($data);
        return Response::success('Agent details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}
if ($action == 'updateAgentBaseDetails') {
    $id = $_POST['id'];
    if (empty($id)) {
        return Response::error('Record not found');
    }
    $data = [
        'agent_name' => $_POST['agent_name'],
        'location' => $_POST['location'],
        'email_id' => $_POST['email_id'],
        'contact_number' => $_POST['contact_number'],
        'remarks' => $_POST['remarks'],
        'organization_id' => $_POST['organization_id'],
        'updated_at' => date('Y-m-d h:i:s'),
    ];
    try {
        update(AGENT_BASE_DETAILS,$data," id='$id'");
        return Response::success('Changes saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}


if ($action == 'getAgentBaseDetails') {
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
        $builder = new Builder();
        $primaryTable = AGENT_BASE_DETAILS;
        if(!empty($formattedFirstDate) && !empty($formattedLastDate)){
            if($userId){
                $builder->where("$primaryTable.user_id", '=', $userId);
            }
            $res = $builder->table($primaryTable)
                ->select("cp_users.fname,$primaryTable.*,cp_leads.organization,cp_leads.city,cp_leads.state,cp_leads.companyowner as contactPerson,cp_leads.companyowneremail as contactPersonEmail,cp_leads.companyownerphone as contactPersonPhone, cp_leads.city as contactPersonCity, cp_leads.state as contactPersonState")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->join('cp_leads', "$primaryTable.organization_id", '=', 'cp_leads.id', 'LEFT')
                ->where("DATE($primaryTable.created_at)", '>=', $formattedFirstDate)
                ->where("DATE($primaryTable.created_at)", '<=', $formattedLastDate, '')
                ->orderBy("$primaryTable.id",'DESC')
                ->take($limit)
                ->get();
            $totalCount = $builder->count();
        }else{
            if($userId){
                $builder->where("$primaryTable.user_id", '=', $userId,'');
            }
            $res = $builder->table($primaryTable)
            ->select("cp_users.fname,$primaryTable.*,cp_leads.organization,cp_leads.city,cp_leads.state,cp_leads.companyowner as contactPerson,cp_leads.companyowneremail as contactPersonEmail,cp_leads.companyownerphone as contactPersonPhone, cp_leads.city as contactPersonCity, cp_leads.state as contactPersonState")
                ->setPage($page)
                ->join('cp_users', "$primaryTable.user_id", '=', 'cp_users.id', 'LEFT')
                ->join('cp_leads', "$primaryTable.organization_id", '=', 'cp_leads.id', 'LEFT')
                ->orderBy("$primaryTable.id",'DESC')
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

if($action == 'deleteAgentBaseDetail'){
    $id = $_POST['id'];
    if(empty($id)){
        Response::error('Record not found');
    }
    try {
        //code...
        $builder = new Builder();
        $builder->table(AGENT_BASE_DETAILS)->where('id','=',$id,'');
        $builder->delete();

        Response::success('Record has been deleted');
    } catch (\Throwable $th) {
        Response::error('Failed to delete record'. $th->getMessage());
    }

}