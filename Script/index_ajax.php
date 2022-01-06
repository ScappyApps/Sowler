<?php

require 'resources/init.php';

$page = '';
if (!isset($_GET['link1'])) {
    $page = 'welcome';
} else if (isset($_GET['link1'])) {
    $page = $_GET['link1'];
}
switch ($page) {
    case 'welcome':
        include('sources/welcome.php');
        break;
    case '404':
        include('sources/404.php');
        break;
    case 'admincp':
        include('sources/admincp.php');
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
    case 'hashtag':
        include('sources/hashtag.php');
        break;
    case 'suggestions-follow':
        include('sources/suggestions_follow.php');
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
	case 'timeline-app':
        include('sources/timeline_app.php');
        break;
	case 'live':
        include('sources/live.php');
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
if (empty($so['title'])) {
    $data['title'] = $so['config']['siteTitle'];
}
$data['url'] = '';
$actual_link = "http://$_SERVER[HTTP_HOST]";
$data['title'] = stripslashes(So_Secure($so['title']));
$data['page'] = $so['page'];

$url = '';
if (!empty($_POST['url'])) {
    $url = $_POST['url'];
}

$data['url'] = So_SeoLink('index.php' . $url);
if ($data['page'] == 'home') {
    $data['url'] = $so['config']['site_url'];
}
echo $so['content'];
?>
<input type="hidden" id="json-data" value='<?php echo htmlspecialchars(json_encode($data)); ?>' />