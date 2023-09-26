<?php

// ini_set('display_errors',1);

require_once('bootstrap.php');

use Panel\Server\Classes\Config\Response;
use Panel\Server\Classes\Controller\RenewalController;

if(!empty($_POST['_action']) && $_POST['_action'] == 'getRenewals'){
    
    $page = !isset($_POST['page']) ? 1  :  $_POST['page'];
    $limit = !isset($_POST['limit']) ? 10 :  $_POST['limit'];
    $filters = json_decode($_POST['filters']);
    $userId = $_SESSION['uId'];

    $renewalController = new RenewalController();
    if($_SESSION['utype'] == 16){
        $result = $renewalController->showForSupport($userId,$page,$limit);
    }else if($_SESSION['utype'] == 14){
        $result = $renewalController->showForMarketing($userId,$page,$limit);
    }else{
        $marketingId = '';
        $supportId = '';
        if(!empty($filters->marketingId)){
            $marketingId = $filters->marketingId;
        }
        if(!empty($filters->supportId)){
            $supportId = $filters->supportId;
        }
        $result = $renewalController->show($supportId,$marketingId,$page,$limit);
    }

    return Response::json($result,200);
}