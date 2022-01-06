<?php
if (isset($_GET['session']) && !empty($_GET['session']) && $_GET['session'] <> $_SESSION['user_id']) {
	$session = So_Secure($_GET['session']);
	$classUser = new User();
	if (count($classUser->So_UserData($session)) > 0) {
		$_SESSION['user_id'] = $session;
        setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
	}
}

$so['description'] = $so['lang']['site_description'];
$so['keywords']    = $so['lang']['site_keywords'];
$so['page'] = 'search-app';
$so['title'] = $so['config']['site_name'];
$so['content'] = So_GetPage('app/search/content');
