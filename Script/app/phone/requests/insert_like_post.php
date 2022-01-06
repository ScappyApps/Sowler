<?php


if (isset($_GET['post_id'])) {
    $classPost = new Post();
    $post_id = So_Secure($_GET['post_id']);

    if ($classPost->So_RegisterPostLike($post_id)) {

        $so['story'] = $classPost->So_PostData($post_id);
        $html = So_GetPage('buttons/like');

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
