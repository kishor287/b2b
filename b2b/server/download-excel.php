<?php
ini_set('display_errors', 1);
if (isset($_GET['daterange']) && !empty($_GET['daterange'])) {
    include('function.php');
    $filter = '';
    if (!empty($_GET['daterange'])) {
        $dateRange = $_GET['daterange'];
        $dateParts = explode(' - ', $dateRange);
        $firstDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[0]));
        $lastDate = DateTime::createFromFormat('d/m/Y', trim($dateParts[1]));
        $formattedFirstDate = $firstDate->format('Y-m-d');
        $formattedLastDate = $lastDate->format('Y-m-d');
        $filter .= " WHERE DATE(created) BETWEEN '$formattedFirstDate' AND '$formattedLastDate' ";
        if ($firstDate == $lastDate) {
            $filter = " WHERE DATE(created) = '$formattedFirstDate'";
        }
    }
    $res = select('cp_fintech_requests', '*', $filter, ' order by id desc ', '',false);
    $tableData = array(
        [
            'Sr. No.',
            'Organization',
            'Student Name',
            'Phone',
            'Email',
            'SIM Number',
            'SIM Attachment',
            'Passport Number',
            'Passport File',
            'Visa Copy',
            'Offer Letter',
            'Travel Date',
            'City',
            'Address',
            'Course',
            'College',
        ]
    );

    foreach ($res as $key => $value) {
        $passportFile = json_decode($value['passport_file'], true);
        $passportDocuments = '';
        if (!empty($passportFile) && count($passportFile) > 0) {
            foreach ($passportFile as $file) {
                $passportDocuments .= 'https://innerxcrm.com/website/' . $file . ' || ';
            }
        }
        array_push($tableData, [
            $key++,
            $value['organization'],
            $value['sname'],
            $value['phone'],
            $value['email'],
            $value['sim_number'],
            $value['sim_path'] ? 'https://innerxcrm.com/website/' . $value['sim_path'] : '',
            $value['passport'],
            $passportDocuments,
            $value['visa_copy'] ? 'https://innerxcrm.com/website/' . $value['visa_copy'] : '',
            $value['offer_letter'] ? 'https://innerxcrm.com/website/' . $value['offer_letter'] : '',
            $value['arrival_date'],
            $value['city'],
            $value['address'],
            $value['course'],
            $value['college']
        ]);
    }
    // Create the Excel file
    $filename = 'sim_request_data_report.xls';

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