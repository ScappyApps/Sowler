<?php



$data = array(
    'status' => 200,
    'html' => So_GetPage('story/stickers-store')
);
header("Content-type: application/json");
echo json_encode($data);
exit();

