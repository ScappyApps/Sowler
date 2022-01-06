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
$classComment = new Comment();
$so['comment'] = $commentData = $classComment->So_CommentData(So_Secure($_GET['comment_id']));

$classPost = new Post();
$so['story'] = $postData = $classPost->So_PostData(So_Secure($so['comment']['post_id']));

if (count($commentData) < 1) {
    header("Location: " . So_SeoLink('index.php?link1=404'));
    exit();
}

$so['description'] = $so['lang']['site_description'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'comment-app';
$so['title'] = $so['config']['site_name'];
$so['content'] = So_GetPage('app/comment/content-story');
