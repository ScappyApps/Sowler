<?php



$data = array(
	'status' => 200,
	'html' => So_GetPage('floopz-music/content')
);

header("Content-type: application/json");
echo json_encode($data);
exit();
?>