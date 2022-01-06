<?php


if (isset($_GET['post_id'])) {
    $classPost = new Post();
    $postData  = $classPost->So_PostData(So_Secure($_GET['post_id']));
    if (count($postData) > 0) {
        $so['story'] = $postData;
        $html = So_GetPage('story/form-share');
        
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

