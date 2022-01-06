<?php
if ($so['loggedin'] == false || !isset($_GET['q']) || empty($_GET['q'])) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}

$so['description'] = $so['lang']['site_description'];
$so['keywords']    = $so['lang']['site_keywords'];
$so['page'] = 'search';
$so['title'] = $_GET['q'] . ' - ' . $so['config']['site_name'];
$so['content'] = So_GetPage('search/content');
