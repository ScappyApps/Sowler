<?php


if (isset($_GET['comment_id'])) {
    $classComment = new Comment();
    $comment_id = So_Secure($_GET['comment_id']);

    if ($classComment->So_RegisterCommentLike($comment_id)) {

        $so['comment'] = $classComment->So_CommentData($comment_id);
        $html = So_GetPage('buttons/like-comment');

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
