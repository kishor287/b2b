<?php

ini_set('display_errors', 1);
session_start();
if (isset($_GET['daterange']) && !empty($_GET['daterange'])) {
    include('function.php');
    $userId = $_SESSION['uId'];
    $filter = '';
    if (!empty($_GET['daterange'])) {
        $dateRange = $_GET['daterange'];
        $dateParts = explode(' - ', $dateRange);
        $firstDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[0]));
        $lastDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[1]));
        $formattedFirstDate = $firstDate->format('Y-m-d');
        $formattedLastDate = $lastDate->format('Y-m-d');
        $filter .= " WHERE DATE(created_at) BETWEEN '$formattedFirstDate' AND '$formattedLastDate' ";
        if ($firstDate == $lastDate) {
            $filter = " WHERE DATE(created_at) = '$formattedFirstDate'";
        }
    }
    // $page = !empty($_POST['pagination']) ? $_POST['pagination'] : 1;
    // $limit = !empty($_POST['limit']) ?  $_POST['limit'] : 
    // 'cp_gic', '*', $filter, ' order by id desc ', '',false
    $queryParams = [
        'col' => 'cp_gic.*,cp_leads.organization as organization_name',
        'tb' => 'cp_gic',
        'join' => [
           [
              'table' => 'cp_leads',
              'condition' => 'cp_gic.organization_id = cp_leads.id',
              'type' => 'LEFT JOIN'
           ]
        ],
        // "pagination" => $page,
        "limit" => '',
        "where"=> " where cp_gic.user_id='$userId'",
        "sort" => " ORDER BY cp_gic.id DESC ",
     ];
    $res = joinSelect($queryParams);
    if(!empty($res['data'])){
        $tableData = array(
            [
                'Sr. No.',
                'Organization',
                'Student Name',
                // 'Student Email Id',
                'Passport Number',
                'Bank',
                'Gic Account Number',
                'Gic Reference Number For SIMPLI',
                'Amount',
                // 'Comission',
            ]
        );
        foreach ($res['data'] as $key => $value) {
            array_push($tableData, [
                $key++,
                $value['organization_name'],
                $value['student_name'],
                // $value['email_id'],
                $value['passport_number'],
                $value['bank'],
                $value['gic_acc_number'],
                $value['gic_reference_number_for_simpli'],
                $value['amount'],
                // $value['commision']
            ]);
        }
        // Create the Excel file
        $filename = 'gic_report.xls';
    
        // Start the Excel file content
        $excelContent = '<?xml version="1.0" encoding="UTF-8"?>
        <ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
        <ss:Worksheet ss:Name="Sheet1">
            <ss:Table>';
    
        // Loop through the table data and generate rows and columns
        foreach ($tableData as $rowData) {
            $excelContent .= '<ss:Row>';
            foreach ($rowData as $value) {
                $excelContent .= '<ss:Cell>';
                $excelContent .= '<ss:Data ss:Type="String">' . htmlspecialchars($value) . '</ss:Data>';
                $excelContent .= '</ss:Cell>';
            }
            $excelContent .= '</ss:Row>';
        }
    
        // End the Excel file content
        $excelContent .= '</ss:Table>
        </ss:Worksheet>
        </ss:Workbook>';
    
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
    
        // Output the Excel content
        echo $excelContent;
    
        // Exit the script
        exit;
    }
}