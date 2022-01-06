<?php


$classPost = new Post();
$posts = $classPost->So_GetPosts();
if (count($posts) > 0) {
	
	$html = '';
	
	foreach ($posts as $so['story']) {
		$html .= So_GetPage('story/content');
	}
	$data = array('status' => 200, 'html' => $html);
} else {
	$data = array('status' => 400);
}

header("Content-type: application/json");
echo json_encode($data);
exit();
?>