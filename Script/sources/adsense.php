<?php
if ($so['loggedin'] === false) {
	header("Location: " . So_SeoLink('index.php?link1=welcome'));
	exit();
}

if ($so['config']['adsense'] <> 1) {
	header("Location: " . So_SeoLink('index.php?link1=home'));
	exit();
}

$so['description'] = $so['lang']['site_description'];
$so['keywords']    = $so['lang']['site_keywords'];
$so['page']        = 'adsense';
$so['title']       = $so['config']['site_name'] . ' - ' . $so['lang']['my_adsense'];
$so['content']     = So_GetPage('adsense/content');