<?php



if (isset($_POST['user_id']) && $_POST['user_id'] <> $so['user']['user_id']) {
    
    $classMessage = new Message();
    
    $message_id = 0;
    if (isset($_POST['message_id']) && !empty($_POST['message_id']) && is_numeric($_POST['message_id'])) {
        $message_id = So_Secure($_POST['message_id']);
    }
    
    $messages = $classMessage->So_GetMessages(array('user_id' => So_Secure($_POST['user_id'])), 50);
    
    if (count($messages) > 0) {
        $html = '';

        foreach ($messages as $so['message']) {
            $html .= So_GetPage('chat/message');
        }
        
        $data = array('status' => 200, 'messages' => $html);
        
    } else {
        $classUser = new User();
        $so['chat'] = $classUser->So_UserData(So_Secure($_POST['user_id']));
        
        $data = array(
            'status' => 200,
            'messages' => So_GetPage('chat/none-message')
        );
        
    }
} else {
    $data = array('status' => 400, 'error' => true);
}
header("Content-type: application/json");
echo json_encode($data);
exit();
