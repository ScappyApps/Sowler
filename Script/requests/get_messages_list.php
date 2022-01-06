<?php


$classMessage = new Message();
$messages = $classMessage->So_GetMessagesOpen(200);
if (count($messages) > 0) {
    $html = '';

    foreach ($messages as $so['conversation']) {
        if ($so['conversation']['user_id'] <> $so['user']['user_id']) {
            $html .= So_GetPage('header/list-conversation');
        }
    }

    $data = array(
        'status' => 200,
        'html' => $html
    );
} else {
    $data = array(
        'status' => 200,
        'html' => '<p>' . $so['lang']['none_messages'] . '</p>'
    );
}

header("Content-type: application/json");
echo json_encode($data);
exit();
