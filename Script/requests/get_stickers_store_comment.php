<?php



$classPost = new Post();
$so['story'] = $classPost->So_PostData($_POST['post_id']);

$data = array(
    'status' => 200,
    'html' => So_GetPage('comment/stickers-store')
);
header("Content-type: application/json");
echo json_encode($data);
exit();

