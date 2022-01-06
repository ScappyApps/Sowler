<?php


error_reporting(0);
@ini_set('max_execution_time', 0);
require_once('resources/config.php');

$so = array();
$Connect = mysqli_connect($host, $user, $pass, $name, 3306);

$so['purshase_code'] = $purshase_code;

$config = So_GetConfig();
$config['theme_url'] = $site_url . '/themes/' . $config['theme'];
$config['site_url'] = $site_url;
$config['site_icon'] = $site_url . '/upload/images/icon.png';
$config['site_logo'] = $site_url . '/upload/images/logo.png';
$amazon_url = "https://test.s3.amazonaws.com";
if (!empty($config['bucket_amazon_name'])) {
    $amazon_url = "https://{bucket}.s3.amazonaws.com";
    $amazon_url = str_replace('{bucket}', $config['bucket_amazon_name'], $amazon_url);
}
$config['amazon_url'] = $amazon_url;
$so['config'] = $config;
$so['loggedin'] = false;

function So_Language() {
    $language = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
    if ($language == 'pt') {
        $language = 'pt-br';
    }
    return $language;
}

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    if (So_ExistUser($_SESSION['user_id']) === true) {
        $so['loggedin'] = true;
        $classUser = new User();
        $so['user'] = $classUser->So_UserData(So_Secure($_SESSION['user_id']));
        $language = $so['user']['language'];
        $_SESSION['lang'] = $language;
    }
} else if (isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
    if (So_ExistUser($_COOKIE['user_id']) === true) {
        $so['loggedin'] = true;
        $classUser = new User();
        $so['user'] = $classUser->So_UserData(So_Secure($_COOKIE['user_id']));
        $language = $so['user']['language'];
        $_SESSION['lang'] = $language;
    }
} else if (!isset($_SESSION['lang'])) {
    if (!file_exists('resources/languages/' . So_Language() . '.php')) {
        $language = $so['config']['language'];
    } else {
        $language = So_Language();
    }
    $_SESSION['lang'] = $language;
}
if (isset($_GET['lang']) AND ! empty($_GET['lang'])) {
    $lang_name = So_Secure(strtolower($_GET['lang']));
    if (file_exists('resources/languages/' . $lang_name . '.php')) {
        $_SESSION['lang'] = $lang_name;
        if ($so['loggedin'] == true) {
			if ($so['user']['user_id'] <> 112) {
				mysqli_query($Connect, "UPDATE " . T_USERS . " SET `language` = '" . $lang_name . "' WHERE `user_id` = " . So_Secure($so['user']['user_id']));
			}
		}
    }
}
if (!file_exists('resources/languages/' . $_SESSION['lang'] . '.php')) {
    $so['language'] = $so['config']['language'];
} else {
	if ($so['loggedin'] == true) {
		$so['language'] = $so['user']['language'];
	} else {
		$so['language'] = $_SESSION['lang'];
	}
}

require_once('resources/languages/' . $so['language'] . '.php');
require_once('resources/functions/phpmailer.php');
require_once('resources/functions/external/amazon/aws-autoloader.php');
