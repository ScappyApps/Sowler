<?php



$classUser = new User();
if (isset($_POST['user_id']) && $_POST['user_id'] <> $so['user']['user_id']) {
    $so['chat'] = $classUser->So_UserData(So_Secure($_POST['user_id']));
    $data = array(
        'status' => 200,
        'html' => So_GetPage('chat/extra/stickers-store')
    );
} else {
    $data = array('status' => 400, 'error' => true);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

