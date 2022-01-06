<?php

if ($so['loggedin'] === false || !isset($_GET['comment_id'])) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
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
$so['page'] = 'comment';
$so['title'] = $so['config']['site_name'];
$so['content'] = So_GetPage('comment/content-story');
