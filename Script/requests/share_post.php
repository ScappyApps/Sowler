<?php


$classPost = new Post();
if (isset($_POST['share_id']) && is_numeric($_POST['share_id']) && count($classPost->So_PostData(So_Secure($_POST['share_id']))) > 0) {

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
        'time' => time(),
        'day' => date('d'),
        'month' => date('m'),
        'year' => date('Y'),
        'hour' => date('h:m'),
        'share_id' => So_Secure($_POST['share_id'])
    );

    $insert_post = $classPost->So_RegisterPost($data_r);

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
        'status' => 400,
		'error' => true
    );
}
header("Content-type: application/json");
echo json_encode($data);
exit();
?>