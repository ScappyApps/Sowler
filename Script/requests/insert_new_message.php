<?php


$classMessage = new Message();
$classUpload = new Upload();

$messageText = NULL;
$messageFile = NULL;
$messageImage = NULL;
$messageVideo = NULL;
$messageGif = NULL;
$emoticon = '';
$messageSticker = NULL;

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
	
	if (isset($_POST['postSticker'])) {
		$messageSticker = So_Secure($_POST['postSticker']);
	}

    if (isset($_FILES['media_empty']['name'])) {

        $upload = $classUpload->So_UploadPicture($_FILES["media_empty"]["tmp_name"], $_FILES['media_empty']['name'], 'message', $_FILES['media_empty']['type'], $so['user']['user_id']);

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
		'postSticker' => $messageSticker,
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
        $timeN = time() - 60;

        if (!empty($classUser->So_UserData($to_id)['auto_message']) && $classUser->So_UserData($to_id)['lastseen'] < $timeN) {
            $classMessage->So_RegisterMessage(array(
                'from_id' => $to_id,
                'to_id' => $so['user']['user_id'],
                'postText' => $classUser->So_UserData($to_id)['auto_message'],
                'time' => time(),
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y'),
                'hour' => date('h:m')
            ));
        }

        $so['message'] = $classMessage->So_MessageData($insert_message);

        $html = So_GetPage('messages/message');
        if ($so['config']['websocket'] == 1) {
            $dataMessage = $so['message'];

            $so['message']['from_id'] = $dataMessage['to_id'];
            $so['message']['to_id'] = $dataMessage['from_id'];

            $so['message']['from'] = $classUser->So_UserData($so['message']['to_id']);

            $html_to = So_GetPage('messages/message');
            $html_to_chat = So_GetPage('chat/message');
        }
        if ($so['config']['websocket'] == 1) {
            $data[] = array(
                'status' => 200,
                'html' => $html,
                'html_to' => $html_to,
                'html_to_chat' => $html_to_chat,
                'from_id' => $so['user']['user_id'],
                'to_id' => $to_id,
                'message_id' => $so['message']['id']
            );
        } else {
            $data[] = array(
                'status' => 200,
                'html' => $html
            );
        }
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