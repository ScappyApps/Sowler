<?php
if ($so['loggedin'] === false) {
    $so['description'] = $so['lang']['site_description'];
	$so['keywords']    = $so['lang']['site_keywords'];
	$so['page']        = '404';
	$so['title']       = $so['lang']['error_404_not_found'];
	$so['content']     = So_GetPage('404/content');
} else {
	if (isset($_GET['id']) && !empty($_GET['id'])) {
		$classPost = new Post();
		$_GET['post_id'] = $_GET['id'];
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
	} else {
		$so['description']	= $so['lang']['site_description'];
		$so['keywords']   	= $so['lang']['site_keywords'];
		$so['page']			= 'momments';
		$so['title']		= $so['lang']['moments'];
		$so['content']		= So_GetPage('moments/content');
	}
}
