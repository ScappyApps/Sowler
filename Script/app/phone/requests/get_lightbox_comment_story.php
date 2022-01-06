<?php


if (isset($_GET['post_id']) && $_GET['comment_id']) {
    $classPost = new Post();
    $postData  = $classPost->So_PostData(So_Secure($_GET['post_id']));
    
    $classComment = new Comment();
    $commentData  = $classComment->So_CommentData(So_Secure($_GET['comment_id']));
    
    if (count($postData) > 0) {
        $so['story'] = $postData;
        $so['comment'] = $commentData;
        $html = So_GetPage('story/content-view-comment');
        
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

