<?php

$today = date('Y-m-d');
$userid = $_SESSION['uId'];
if ($action === 'get') {
    $condition = ' WHERE 1=1 ';
    if (!empty($_POST['filter'])) {
        $jsonData = $_POST['filter'];
        $filter = json_decode($jsonData, true);
        if (!empty($filter['search'])) {
            $condition .= ' AND (l.followup_status LIKE "%' . $filter['search'] . '%" OR l.followup_substatus LIKE "%' . $filter['search'] . '%" OR l.organization LIKE "%' . $filter['search'] . '%" OR l.phone LIKE "%' . $filter['search'] . '%" OR l.id LIKE "%' . $filter['search'] . '%")';
        }
        if (!empty($filter['dateRange'])) {
            $dateRange = explode(' - ', $filter['dateRange']);

            $startDate = date('Y-m-d', strtotime($dateRange[0]));
            $endDate = date('Y-m-d', strtotime($dateRange[1]));

            $filter['dateRange'] = array($startDate, $endDate);

            $condition .= ' AND DATE(m.meeting_date) BETWEEN "' . $filter['dateRange'][0] . '" AND "' . $filter['dateRange'][1] . '"';
        }
        if (!empty($filter['salemanager'])) {
            $condition .= ' AND l.assigned_by = ' . $filter['saleManager'];
        }
        if (!empty($filter['status'])) {
            $status = $filter['status'];
            $condition .= ' AND m.status = "' . $status . '"';
        }
    }
    if (empty($filter['dateRange'])) {
        $condition .= ' AND DATE(m.meeting_date)="' . $today . '"';
    }
    if ($_SESSION['utype'] == 1) {
        $param = [
            'col' => 'l.*,m.status,DATE_FORMAT(m.meeting_date, "%Y-%m-%d %h:%i %p") as meetingDate',
            'tb' => 'cp_meetings AS m',
            'join' => array(
                array(
                    'type' => 'JOIN',
                    'table' => 'cp_leads AS l',
                    'condition' => 'm.lead_id = l.id'
                ),
            ),
            'where' => $condition,
            'sort' => 'ORDER BY m.meeting_date DESC',
            'limit' => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
            'pagination' => $_POST['pagination']
        ];
    } else {
        $condition .= ' AND m.assigned_by = ' . $userid;
        $param = [
            'col' => 'l.organization,l.name,l.organization_phone,m.status,m.meeting_date as meetingDate',
            'tb' => 'cp_meetings AS m',
            'join' => array(
                array(
                    'type' => 'JOIN',
                    'table' => 'cp_leads AS l',
                    'condition' => 'm.lead_id = l.id'
                ),
            ),
            'where' => $condition,
            'sort' => 'ORDER BY m.meeting_date DESC',
            'limit' => (($_POST['pagination'] - 1) * $_POST['limit']) . "," . $_POST['limit'],
            'pagination' => $_POST['pagination']
        ];
    }
    $marketing = select('cp_users', 'fname,id', 'where role=14', '', '');
    http_response_code(200);
    $result = joinSelect($param);

    echo json_encode(['data' => $result, 'marketers' => $marketing]);

}