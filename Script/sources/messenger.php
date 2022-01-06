<?php
if ($so['loggedin'] === false || !isset($_GET['user_id'])) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}
$so['description'] = $so['lang']['site_description'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'messenger';
$so['title'] = $so['lang']['messages'] . ' - ' . $so['config']['site_name'];
$so['content'] = So_GetPage('messages/content');
