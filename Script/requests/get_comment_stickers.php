<?php


$classPost = new Post();
$so['story'] = $classPost->So_PostData($_POST['post_id']);
$html = So_GetPage('comment/stickers-box');

$data = array(
    'status' => 200,
    'html' => $html
);
header("Content-type: application/json");
echo json_encode($data);
exit();
?>