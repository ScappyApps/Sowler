<?php


$classPost = new Post();
if (isset($_GET['post_id'])) {
    $post_id = So_Secure($_GET['post_id']);
    $user_id = So_Secure($_GET['user_id']);
    
    $html = '';
    $postData = $classPost->So_GetPosts($post_id, $user_id);
    if (count($postData) > 0) {
        foreach ($postData as $so['story']) {
            $html .= So_GetPage('story/content');
        }
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


