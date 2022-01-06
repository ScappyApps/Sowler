<?php


$classPost = new Post();
if (isset($_GET['post_id'])) {
    $post_id = So_Secure($_GET['post_id']);
    
    if ($classPost->So_DeletePost($post_id, true)) {
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

