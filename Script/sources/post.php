<?php
if (!isset($_GET['post_id'])) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}

$classPost = new Post();
$so['story'] = $classPost->So_PostData(So_Secure($_GET['post_id']));

$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'post';
if (!empty($so['story']['postImage'])) {
	$so['image'] = $so['story']['postImage'];
}
if (!empty($so['story']['postGif'])) {
	$so['image'] = $so['story']['postGif'];
}
if (!empty($so['story']['postText'])) {
	$so['description'] = $so['story']['postOriginal'];
	$so['title'] = $so['story']['postOriginal'];
} else {
	$so['description'] = $so['lang']['site_description'];
	$so['title'] = $so['config']['site_name'];
}
if (count($classPost->So_PostData(So_Secure($_GET['post_id']))) < 1) {
    $so['content'] = So_GetPage('404/content');
} else {
    $so['content'] = So_GetPage('story/content-notification');
}
