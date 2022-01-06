<?php


$html = So_GetPage('story/stickers-box');

$data = array(
    'status' => 200,
    'html' => $html
);
header("Content-type: application/json");
echo json_encode($data);
exit();
?>