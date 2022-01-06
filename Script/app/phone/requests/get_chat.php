<?php


if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
    $user_id = So_Secure($_POST['user_id']);
    $classUser = new User();
    $user = $classUser->So_UserData($user_id);
    if (count($user) > 0) {
        $so['chat'] = $user;
        $html = So_GetPage('messages/chat');
        
        $data = array('status' => 200, 'html' => $html);
    } else {
        $data = array('status' => 400);
    }
} else {
    $data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

