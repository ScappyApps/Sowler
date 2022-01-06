<?php


$classLive = new Live();

$postText = NULL;
$postFile = NULL;
$postImage = NULL;
$postVideo = NULL;
$postGif = NULL;
$emoticon = '';

if (isset($_POST['postText']) && !empty($_POST['postText'])) {
    $postText = So_Secure($_POST['postText']);
}

$data_r = array(
    'user_id' => $so['user']['user_id'],
    'postText' => $postText,
    'postFile' => $postFile,
    'postImage' => $postImage,
    'postVideo' => $postVideo,
    'postGif' => $postGif,
	'postType' => 'started_live_stream',
    'time' => time(),
    'day' => date('d'),
    'month' => date('m'),
    'year' => date('Y'),
    'hour' => date('h:m')
);

$insert_live = $classLive->So_CreateLiveStream($data_r);

if ($insert_live) {
	
	$so['live']['post_id'] = $insert_live;
	$html = So_GetPage('live/player');
	
    $data = array(
        'status' => 200,
		'message' => $so['lang']['live_stream_created_successfully'],
		'html' => $html,
		'chat' => So_GetPage('live/chat/content')
    );
} else {
    $data = array(
        'status' => 400
    );
}

header("Content-type: application/json");
echo json_encode($data);
exit();
?>