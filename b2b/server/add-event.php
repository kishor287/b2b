<?php

if ($action == 'get_meetings') {

    $startDate = $_POST['start'];
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate = $_POST['end'];
    $endDate = date('Y-m-d', strtotime($endDate));
    $userid = $_SESSION['uId'];
    $where = " WHERE meeting_date BETWEEN '$startDate' AND '$endDate' AND assigned_by='$userid'";
    $orderBy = " ORDER BY meeting_date ASC";
    $table = "cp_meetings";
    $columns = "*";

    $result = select($table, $columns, $where, $orderBy);
    
    $events = array();
    foreach ($result as $row) {
        $event = array(
            "id" => $row['id'],
            "title" => $row['remarks'],
            "start" => $row['meeting_date'],
            "end" => $row['meeting_date'],
            "allDay" => false,
            // "url" => $row['url'],
            // "backgroundColor" => "green",
            // "borderColor" => "black"
        );
        array_push($events, $event);
    }
    
    echo json_encode($events);
}
