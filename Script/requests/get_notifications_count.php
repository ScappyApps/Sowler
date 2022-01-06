<?php


$classMessage = new Message();
$conversations = $classMessage->So_GetMessagesOpen(200);

$users_online = '';

if (count($conversations) > 1) {
    foreach ($conversations as $so['conversation']) {
        if ($so['conversation']['user_id'] <> $so['user']['user_id']) {
            $users_online .= So_GetPage('chat/list-conversation');
        }
    }
} else {
    $users_online = 0;
}

$update = mysqli_query($Connect, "UPDATE " . T_USERS . " SET `lastseen` = '" . time() . "' WHERE `user_id` = '{$so['user']['user_id']}'");

$data = array();

$data[] = array(
    'status' => 200,
    'count_notifications' => So_CountNotifications(),
    'count_messages' => So_CountMessages(),
    'users_online' => $users_online
);

echo json_encode($data);
?>