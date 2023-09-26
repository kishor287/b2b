<?php

// ini_set("display_errors",1);

include('bootstrap.php');

use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\Response;

if ($action == 'addRemittance') {
    unset($_POST['payload'], $_POST['_action']);
    $_POST['user_id'] = $_SESSION['uId'];
    try {
        $builder = new Builder();
        $builder->table('cp_remittance')->insert($_POST);
        return Response::success('Agent details saved successfully');
    } catch (\Throwable $th) {
        return Response::error('Failed to save details' . $th->getMessage());
    }
}