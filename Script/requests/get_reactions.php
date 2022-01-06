<?php


$classPost = new Post();
if (isset($_GET['post_id'])) {
	$so['story'] = $classPost->So_PostData(So_Secure($_GET['post_id']));
	$data = array('status' => 200, 'html' => So_GetPage('buttons/reactions/reactions-story'));
} else {
	$data = array('status' => 400);
}

header("Content-type: application/json");
echo json_encode($data);
exit();
?>