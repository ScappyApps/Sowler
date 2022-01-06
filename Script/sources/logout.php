<?php
$lang = $_SESSION['lang'];
session_destroy();
if (isset($_COOKIE['user_id'])) {
    unset($_COOKIE['user_id']);
    setcookie('user_id', null, -1);
}
$_SESSION['lang'] = $lang;
header("Location: " . $so['config']['site_url']);
exit();