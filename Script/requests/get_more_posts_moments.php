<?php


$classPost = new Post();
if (isset($_GET['post_id']) && !empty($_GET['post_id']) && is_numeric($_GET['post_id'])) {
	$category = 0;
	if (isset($_GET['category']) && !empty($_GET['category']) && is_numeric($_GET['category'])) {
		$category = So_Secure($_GET['category']);
	}
	
	$post_id = So_Secure($_GET['post_id']);
	$posts = $classPost->So_GetAfterThreeMoments($category, $post_id);
	if (count($posts) > 0) {
		$html = '';
		
		foreach ($posts as $so['moment']) {
			$html .= So_GetPage('moments/story-moment');
		}
		
		$data = array(
			'status' => 200,
			'html' => $html
		);
		
	} else {
		$data = array('status' => 400);
	}
	
} else {
	$data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data);
exit();
?>