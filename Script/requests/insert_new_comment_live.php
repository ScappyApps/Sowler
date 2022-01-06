<?php


$classComment = new Comment();
$classUpload = new Upload();
$classPost = new Post();
$classUser = new User();

$postText = NULL;
$postFile = NULL;
$postImage = NULL;
$postVideo = NULL;
$postGif = NULL;
$emoticon = '';
if (isset($_POST['post_id']) && count($classPost->So_PostData(So_Secure($_POST['post_id']))) > 0) {
    if (isset($_POST['postText']) && !empty($_POST['postText'])) {
        $postText = So_Secure($_POST['postText']);
    }
    
    if (isset($_POST['postGif']) && !empty($_POST['postGif'])) {
        $postGif = So_Secure($_POST['postGif']);
    }

    if (isset($_FILES['media_empty']['name'])) {

        $upload = $classUpload->So_UploadPicture($_FILES["media_empty"]["tmp_name"], $_FILES['media_empty']['name'], 'post', $_FILES['media_empty']['type'], $so['user']['user_id']);

        if ($upload) {
            if ($upload['extension'] == 'jpeg' || $upload['extension'] == 'jpg' || $upload['extension'] == 'gif' || $upload['extension'] == 'png') {
                $postImage = So_Secure($upload['filename']);
            }
            if ($upload['extension'] == 'mp4') {
                $postVideo = So_Secure($upload['filename']);
            }
            $postGif = NULL;
        }
    }

    $data_r = array(
        'post_id' => So_Secure($_POST['post_id']),
        'user_id' => $so['user']['user_id'],
        'postText' => $postText,
        'postFile' => $postFile,
        'postImage' => $postImage,
        'postVideo' => $postVideo,
        'postGif' => $postGif,
        'time' => time(),
        'day' => date('d'),
        'month' => date('m'),
        'year' => date('Y'),
        'hour' => date('h:m')
    );
    if (isset($_POST['emoticon']) && !empty($_POST['emoticon'])) {
        $emoticon = So_Secure($_POST['emoticon']);
    }

    $insert_comment = $classComment->So_RegisterComment($data_r, $emoticon);

    if ($insert_comment) {
        
        $so['comment'] = $classComment->So_CommentData($insert_comment);
        $html          = So_GetPage('live/comment/content');
        
		if ($so['config']['websocket'] == 1) {
			
			$data[] = array(
				'status' => 200,
				'message' => $so['lang']['published_success'],
				'from_id' => $so['user']['user_id'],
				'room_id' => $so['comment']['post_id'],
				'html' => $html
			);
		} else {
			$data[] = array(
				'status' => 200,
				'message' => $so['lang']['published_success'],
				'html' => $html
			);
		}
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
echo json_encode($data);
?>