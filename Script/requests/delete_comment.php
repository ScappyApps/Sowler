<?php


$classComment = new Comment();
if (isset($_GET['comment_id'])) {
    $comment_id = So_Secure($_GET['comment_id']);
    
    if ($classComment->So_DeleteComment($comment_id)) {
        $data = array(
            'status' => 200,
            'message' => $so['lang']['deleted_post_successfuly']
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

