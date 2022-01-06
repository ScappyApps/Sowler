<?php

if ($so['loggedin'] === false) {
    header("Location: " . So_SeoLink('index.php?link1=welcome'));
    exit();
}
$so['description'] = $so['lang']['site_description'];
$so['keywords'] = $so['lang']['site_keywords'];
$so['page'] = 'home';
$so['title'] = $so['config']['site_name'];
$so['content'] = So_GetPage('home/content');
