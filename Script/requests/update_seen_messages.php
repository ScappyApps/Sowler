<?php


$classMessage = new Message();
if (isset($_POST['user_id']) && $_POST['user_id'] <> $so['user']['user_id']) {
    $user_id = So_Secure($_POST['user_id']);
    if ($classMessage->So_UpdateSeenMessages($user_id)) {
        $data = array('status' => 200);
    } else {
        $data = array('status' => 400);
    }
} else {
    $data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data);
exit();
