<?php

if ($so['loggedin'] === false) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}
$so['description'] = $so['lang']['site_description'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'settings';
$so['title'] = $so['lang']['settins_and_privacy'] . ' - ' . $so['config']['site_name'];
$so['content'] = So_GetPage('settings/content');
