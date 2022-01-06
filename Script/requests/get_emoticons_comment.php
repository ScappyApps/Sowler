<?php



$data = array(
	'status' => 200,
	'emoticons' => So_GetPage('comment/emoticons')
);
header("Content-type: application/json");
echo json_encode($data);
exit();
