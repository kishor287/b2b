<?php

ini_set('display_errors',1);

require_once('Classes/Controller/LeadPaymentController.php');
require_once('Classes/Config/Response.php');

use Panel\Server\Classes\Config\Response;
use Panel\Server\Classes\Controller\LeadPaymentController;

if(!empty($_POST['_action']) && $_POST['_action'] == 'getPayments'){
    
    $paymentController = new LeadPaymentController();
    $result = $paymentController->show();
    return Response::json($result,200);
}