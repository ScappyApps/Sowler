<?php

if ($so['loggedin'] === false) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}

$classUser = new User();
$user_id = $classUser->So_UserIdFromUsername(So_Secure($_GET['username']));
if (!isset($_GET['username']) || $user_id === false) {
    header("Location: " . So_SeoLink('index.php?link1=home'));
    exit();
}

$so['profile'] = $classUser->So_UserData($user_id);

$so['description'] = $so['profile']['about'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'timeline';
$so['title'] = $so['profile']['name'];
$so['content'] = So_GetPage('timeline/content');
$so['image'] = $so['profile']['avatar'];
