<?php
$classUser = new User();
if (isset($_GET['session']) && !empty($_GET['session']) && $_GET['session'] <> $_SESSION['user_id']) {
	$session = So_Secure($_GET['session']);
	if (count($classUser->So_UserData($session)) > 0) {
		$_SESSION['user_id'] = $session;
        setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
	}
}
$so['profile'] = $classUser->So_UserData(So_Secure($_GET['user_id']));

$so['description'] = $so['profile']['about'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'timeline-app';
$so['title'] = $so['profile']['name'];
$so['content'] = So_GetPage('app/timeline/content');
$so['image'] = $so['profile']['avatar'];
