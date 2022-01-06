<?php
$classUser = new User();
if (isset($_GET['session']) && !empty($_GET['session']) && $_GET['session'] <> $_SESSION['user_id']) {
	$session = So_Secure($_GET['session']);
	if (count($classUser->So_UserData($session)) > 0) {
		$so['user'] = $classUser->So_UserData($session);
		$so['loggedin'] = true;
		$_SESSION['user_id'] = $session;
        setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
	}
}

$classPost = new Post();
$so['story'] = $classPost->So_PostData(So_Secure($_GET['post_id']));


$so['description'] = $so['lang']['site_description'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'post-app';
$so['title'] = $so['config']['site_name'];
if (count($classPost->So_PostData(So_Secure($_GET['post_id']))) < 1) {
    $so['content'] = So_GetPage('404/content');
} else {
    $so['content'] = So_GetPage('app/feed/content');
}
