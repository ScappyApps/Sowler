<?php


$html = So_GetPage('messages/search-user');
$data = array(
    'status' => 200,
    'html' => $html
);

header("Content-type: application/json");
echo json_encode($data);
exit();

