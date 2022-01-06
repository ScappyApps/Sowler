<?php


$classPost = new Post();
$classUpload = new Upload();

$postText = NULL;
$postFile = NULL;
$postImage = NULL;
$postVideo = NULL;
$postGif = NULL;
$emoticon = '';

$html_title = NULL;
$html_image = NULL;
$html_body = NULL;
$html_url = NULL;
$background = NULL;

if (isset($_POST['postText']) && !empty($_POST['postText'])) {
    $postText = So_Secure($_POST['postText']);
}
if (isset($_POST['postGif']) && !empty($_POST['postGif'])) {
    $postGif = So_Secure($_POST['postGif']);
}
if (isset($_POST['background']) && !empty($_POST['background'])) {
	$background = So_Secure($_POST['background']);
}

if (!empty($_POST['titleMoment']) && !empty($_POST['postText'])) {

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

	if (!empty($_POST['html_url']) && empty($_FILES['media_empty']['name']) && empty($_POST['postGif'])) {
		$html_title = So_Secure($_POST['html_title']);
		$html_image = So_Secure($_POST['html_image']);
		$html_body = So_Secure($_POST['html_body']);
		$html_url = So_Secure($_POST['html_url']);
	}

	$data_r = array(
		'user_id' => $so['user']['user_id'],
		'postText' => $postText,
		'postFile' => $postFile,
		'postImage' => $postImage,
		'postVideo' => $postVideo,
		'postType' => 'moments',
		'postGif' => $postGif,
		'time' => time(),
		'day' => date('d'),
		'month' => date('m'),
		'year' => date('Y'),
		'hour' => date('h:m'),
		'html_title' => $html_title,
		'html_image' => $html_image,
		'html_body' => $html_body,
		'html_url' => $html_url,
		'background' => $background,
		'titleMoment' => So_Secure($_POST['titleMoment']),
		'categoryMoment' => So_Secure($_POST['categoryMoment'])
	);
	if (isset($_POST['emoticon']) && !empty($_POST['emoticon'])) {
		$emoticon = So_Secure($_POST['emoticon']);
	}

	$insert_post = $classPost->So_RegisterPost($data_r, $emoticon);

	if ($insert_post) {
		$so['story'] = $classPost->So_PostData($insert_post);
				
		$html = So_GetPage('story/content');
		
		$data = array(
			'status' => 200,
			'html' => $html,
			'message' => $so['lang']['published_success']
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