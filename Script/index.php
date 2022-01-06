<?php


require_once('resources/init.php');
require 'smarty/libs/Smarty.class.php';

$smarty = new Smarty();

$smarty->setTemplateDir('view/');
$smarty->setCompileDir('view/compile/');
$smarty->setCacheDir('view/cache/');

$page = '';
if ($so['loggedin'] == true && !isset($_GET['link1'])) {
    $page = 'home';
} elseif (isset($_GET['link1'])) {
    $page = $_GET['link1'];
}
if ((!isset($_GET['link1']) && $so['loggedin'] == false) || (isset($_GET['link1']) && $so['loggedin'] == false && $page == 'home')) {
    $page = 'welcome';
}
if ($so['config']['maintenance_mode'] == 1) {
    if ($so['loggedin'] == false) {
        if ($page == 'admincp') {
            $page = 'welcome';
        } else {
            $page = 'maintenance';
        }
    } else {
        if ($so['user']['admin'] == 0) {
            $page = 'maintenance';
        }
    }
}

switch ($page) {
    case 'welcome':
        include('sources/welcome.php');
        break;
    case 'signup':
        include('sources/signup.php');
        break;
    case 'tos':
        include('sources/tos.php');
        break;
    case 'about':
        include('sources/about.php');
        break;
    case 'privacy':
        include('sources/privacy.php');
        break;
    case 'home':
        include('sources/home.php');
        break;
    case 'settings':
        include('sources/settings.php');
        break;
    case 'moments':
        include('sources/moments.php');
        break;
    case 'notifications':
        include('sources/notifications.php');
        break;
    case 'timeline':
        include('sources/timeline.php');
        break;
    case 'logout':
        include('sources/logout.php');
        break;
    case 'forgot-password':
        include('sources/forgot_password.php');
        break;
    case 'password-recover':
        include('sources/password_recover.php');
        break;
    case 'hashtag':
        include('sources/hashtag.php');
        break;
    case 'suggestions-follow':
        include('sources/suggestions_follow.php');
        break;
    case 'search':
        include('sources/search.php');
        break;
    case 'post':
        include('sources/post.php');
        break;
    case 'comment':
        include('sources/comment.php');
        break;
    case 'admincp':
        include('sources/admincp.php');
        break;
	case 'load-posts':
		include('sources/load_posts.php');
		break;
	case 'notifications-app':
		include('sources/notifications_app.php');
		break;
	case 'post-app':
        include('sources/post_app.php');
        break;
	case 'comment-app':
        include('sources/comment_app.php');
        break;
	case 'search-app':
        include('sources/search_app.php');
        break;
	case 'timeline-app':
        include('sources/timeline_app.php');
        break;
	case 'live':
        include('sources/live.php');
        break;
	case 'adsense':
        include('sources/adsense.php');
        break;
	case 'messenger':
        include('sources/messenger.php');
        break;
    default:
        include('sources/404.php');
        break;
}
if (empty($so['content'])) {
    include('sources/404.php');
}

$html = '';

if ($_GET['link1'] == 'admincp') {
    $html = So_Getpage('admincp');
} else if ($_GET['link1'] == 'adsense') {
    $html = So_Getpage('adsense/container');
} else {
    $html = So_Getpage('container');
}

$smarty->assign('container', $html);

$smarty->display('themes/' . $so['config']['theme'] . '/layout/index.html');

mysqli_close($Connect);
unset($so);
?>