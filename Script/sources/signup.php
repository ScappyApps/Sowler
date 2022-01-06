<?php
if ($so['loggedin'] === true) {
	header("Location: " . So_SeoLink('index.php?link1=home'));
	exit();
}
$so['description'] = $so['lang']['site_description'];
$so['keywords']    = $so['lang']['site_keywords'];
$so['page']        = 'signup';
$so['title']       = $so['config']['site_name'] . ' - ' . $so['lang']['sign_up'];
$so['content']     = So_GetPage('welcome/signup-content');