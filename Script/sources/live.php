<?php
if ($so['loggedin'] === false) {
	header("Location: " . So_SeoLink('index.php?link1=welcome'));
	exit();
}

if ($so['config']['live_stream'] <> 1) {
	header("Location: " . So_SeoLink('index.php?link1=home'));
	exit();
} else if ($so['config']['live_stream'] == 1) {
	if ($so['config']['live_stream_all_users'] == 0) {
		if ($so['user']['verified'] == 0) {
			header("Location: " . So_SeoLink('index.php?link1=home'));
			exit();
		}
	}
}

$so['description'] = $so['lang']['site_description'];
$so['keywords']    = $so['lang']['site_keywords'];
$so['page']        = 'live';
$so['title']       = $so['config']['site_name'] . ' - ' . $so['lang']['live_stream'];
$so['content']     = So_GetPage('live/content');