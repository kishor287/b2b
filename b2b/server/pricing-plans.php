<?php

// ini_set('display_errors',1);

if ($action == 'savePricingPlan') {
    include('../_con.php');

    $title = $_POST['title'];
    $price = $_POST['price'];
    $maxDiscount = $_POST['maximum_discount'];
    $description = $_POST['description'];
    $paymentMethod = $_POST['payment_method'];

    $stmt = $con->prepare('INSERT INTO cp_pricing_plans (title,price,max_discount,description,payment_method) VALUES(:title,:price,:maximumDiscount,:description,:paymentMethod) ');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':maximumDiscount', $maxDiscount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':paymentMethod', $paymentMethod);
    $stmt->execute();

    if ($stmt) {
        http_response_code(200);
        echo json_encode(['statusCode' => 200, 'message' => "New plan added successfully"]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['statusCode' => 500, 'message' => "Failed to add new plan"]);
        exit;
    }
}
if ($action == 'getPricingPlans') {
    include('../_con.php');

    $stmt = $con->prepare('SELECT * FROM cp_pricing_plans');
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode(['statusCode' => 200, 'data' => $result]);
    exit;
}
if ($action == 'deletePricingPlan') {   
    include('../_con.php');
    $id = $_POST['id'];
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['statusCode' => 400, 'message' => 'Record not found', 'status' => 0]);
        exit;
    }
    $stmt = $con->prepare('DELETE FROM cp_pricing_plans WHERE id="' . $id . '"');
    $stmt->execute();
    if ($stmt) {
        http_response_code(200);
        echo json_encode(['status' => 1, 'statusCode' => 200, 'message' => 'Plan Deleted!']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['status' => 0, 'statusCode' => 500, 'message' => 'Failed to delete the plan']);
        exit;
    }
}
if ($action == 'updatePricingPlan') {
    include('../_con.php');
    $title = $_POST['title'];
    $price = $_POST['price'];
    $maxDiscount = $_POST['maximum_discount'];
    $description = $_POST['description'];
    $planId = $_POST['id'];
    // Assuming you have a PDO connection $DB_con
    $sql = "UPDATE cp_pricing_plans SET
     title = :title,
     price = :price,
     max_discount = :maxDiscount,
     description = :description
     WHERE id = :planId";

    $stmt = $con->prepare($sql);

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':maxDiscount', $maxDiscount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':planId', $planId);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 1, 'statusCode' => 200, 'message' => 'Plan Updated!']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['status' => 0, 'statusCode' => 500, 'message' => 'Failed to update the plan']);
        exit;
    }
}
