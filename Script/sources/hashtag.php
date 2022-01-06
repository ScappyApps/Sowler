<?php
if ($so['loggedin'] == false || !isset($_GET['hash']) || empty($_GET['hash'])) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}

$so['description'] = $so['lang']['site_description'];
$so['keywords']    = $so['lang']['site_keywords'];
$so['page'] = 'hashtag';
$so['title'] = '#' . $_GET['hash'] . ' hashtag ' . $so['config']['site_name'];
$so['content'] = So_GetPage('hashtags/content');
