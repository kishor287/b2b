<?php


if($action == 'get')
{
    $userid = $_SESSION['uId'];
    $notifications = select('cp_notifications', '*', "WHERE user_id=$userid", 'ORDER BY id DESC', 'LIMIT 15');
    $totalNotifications = select('cp_notifications', 'COUNT(id) as totalNotifications', "WHERE user_id=$userid AND read_at IS NULL", '', '');
    $totalNotifications = array_shift($totalNotifications);
    echo json_encode(['notifications' => $notifications,'totalNotifications'=>$totalNotifications['totalNotifications']??0]);
}