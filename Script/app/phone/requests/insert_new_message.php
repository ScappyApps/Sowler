<?php


$classMessage = new Message();
$classUpload = new Upload();

$messageText = NULL;
$messageFile = NULL;
$messageImage = NULL;
$messageVideo = NULL;
$messageGif = NULL;
$emoticon = '';

$classUser = new User();
$data = array();
if (isset($_POST['to_id']) && $_POST['to_id'] <> $so['user']['user_id'] && count($classUser->So_UserData(So_Secure($_POST['to_id']))) > 0) {

    if (isset($_POST['postText']) && !empty($_POST['postText'])) {
        $messageText = So_Secure($_POST['postText']);
    }
    $to_id = So_Secure($_POST['to_id']);
    if (isset($_POST['postGif']) && !empty($_POST['postGif'])) {
        $messageGif = So_Secure($_POST['postGif']);
    }

    if (isset($_FILES['media_empty']['name'])) {

        $upload = $classUpload->So_UploadPicture($_FILES["media_empty"]["tmp_name"], $_FILES['media_empty']['name'], 'post', $_FILES['media_empty']['type'], $so['user']['user_id']);

        if ($upload) {
            if ($upload['extension'] == 'jpeg' || $upload['extension'] == 'jpg' || $upload['extension'] == 'gif' || $upload['extension'] == 'png') {
                $messageImage = So_Secure($upload['filename']);
            }
            if ($upload['extension'] == 'mp4') {
                $messageVideo = So_Secure($upload['filename']);
            }
            $messageGif = NULL;
        }
    }

    $data_r = array(
        'from_id' => $so['user']['user_id'],
        'to_id' => $to_id,
        'postText' => $messageText,
        'postFile' => $messageFile,
        'postImage' => $messageImage,
        'postVideo' => $messageVideo,
        'postGif' => $messageGif,
        'time' => time(),
        'day' => date('d'),
        'month' => date('m'),
        'year' => date('Y'),
        'hour' => date('h:m')
    );
    if (isset($_POST['emoticon']) && !empty($_POST['emoticon'])) {
        $emoticon = So_Secure($_POST['emoticon']);
    }

    $insert_message = $classMessage->So_RegisterMessage($data_r, $emoticon);

    if ($insert_message) {
        $so['message'] = $classMessage->So_MessageData($insert_message);

        $html = So_GetPage('messages/message');

        $data[] = array(
            'status' => 200,
            'html' => $html
        );
    } else {
        $data[] = array(
            'status' => 400
        );
    }
} else {
    $data[] = array(
        'status' => 400
    );
}

echo json_encode($data);
?>