<?php

@ini_set('session.cookie_httponly',1);
@ini_set('session.use_only_cookies',1);
@header("X-FRAME-OPTIONS: SAMEORIGIN");
if (!version_compare(PHP_VERSION, '5.6.0', '>=')) {
    exit("Required PHP_VERSION >= 5.6.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n");
}

$user_ip	= getenv('REMOTE_ADDR');
if ($user_ip == '::1') {
	date_default_timezone_set('UTC');
} else {
	$geo		= @unserialize(@file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
	@date_default_timezone_set($geo["geoplugin_timezone"]);
}
@session_start();
@ini_set('gd.jpeg_ignore_warning', 1);
require_once('functions/functions_general.php');
require_once('functions/tabels.php');
require_once('functions/functions_one.php');
require_once('classes/all_classes.php');
require_once('functions/functions_two.php');