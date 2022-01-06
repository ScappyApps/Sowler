<?php



$classMessage = new Message();
if (!empty($_POST['user_id']) && $_POST['user_id'] <> $so['user']['user_id']) {
    $messages = $classMessage->So_GetMessages(array('user_id' => So_Secure($_POST['user_id'])), 50);
    if (count($messages) > 0) {
        $html = '';
        foreach ($messages as $so['message']) {
            $html .= So_GetPage('messages/message');
        }

        $data = array(
            'status' => 200,
            'html' => $html
        );
    } else {
        $data = array(
            'status' => 400
        );
    }
} else {
    $data = array(
        'status' => 400
    );
}

header("Content-type: application/json");
echo json_encode($data);
exit();
