<?php


$classUser = new User();
if (isset($_POST['user_id']) && !empty($_POST['user_id']) && $_POST['user_id'] <> $so['user']['user_id']) {
    $so['chat'] = $classUser->So_UserData($_POST['user_id']);
    if ($so['chat']['user_id'] > 0) {
        
        $html = So_GetPage('chat/emoticons-chat');
        
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
?>