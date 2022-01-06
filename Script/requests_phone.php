<?php




require_once('./resources/init.php');

$type = '';
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = $_GET['type'];
}

switch ($type) {
    case 'signup':
        include('app/phone/requests/signup.php');
        break;
    case 'signin':
        include('app/phone/requests/signin.php');
        break;
    case 'password-reset':
        include('app/phone/requests/password_reset.php');
        break;
    case 'password-recover':
        include('app/phone/requests/password_recover.php');
        break;
    case 'change-profile-avatar':
        include('app/phone/requests/change_profile_avatar.php');
        break;
    case 'change-profile-cover':
        include('app/phone/requests/change_profile_cover.php');
        break;
    case 'change-about-me':
        include('app/phone/requests/change_about_me.php');
        break;
   case 'change-my-location':
        include('app/phone/requests/change_my_location.php');
        break;
    case 'change-my-birthday':
        include('app/phone/requests/change_my_birthday.php');
        break;
    case 'insert-new-post':
        include('app/phone/requests/insert_new_post.php');
        break;
    case 'delete-post':
        include('app/phone/requests/delete_post.php');
        break;
    case 'insert-like-post':
        include('app/phone/requests/insert_like_post.php');
        break;
    case 'get-form-share':
        include('app/phone/requests/get_form_share.php');
        break;
    case 'share-post':
        include('app/phone/requests/share_post.php');
        break;
    case 'get-lightbox-comment':
        include('app/phone/requests/get_lightbox_comment.php');
        break;
    case 'insert-new-comment':
        include('app/phone/requests/insert_new_comment.php');
        break;
    case 'insert-like-comment':
        include('app/phone/requests/insert_like_comment.php');
        break;
    case 'delete-comment':
        include('app/phone/requests/delete_comment.php');
        break;
    case 'get-more-posts':
        include('app/phone/requests/get_more_posts.php');
        break;
    case 'register-follow-suggestion':
        include('app/phone/requests/register_follow_suggestion.php');
        break;
    case 'get-hover-profile-container':
        include('app/phone/requests/get_hover_profile_container.php');
        break;
    case 'register-follow-suggestion-follow':
        include('app/phone/requests/register_follow_suggestion_follow.php');
        break;
    case 'register-follow-hover':
        include('app/phone/requests/register_follow_hover.php');
        break;
    case 'register-follow-result':
        include('app/phone/requests/register_follow_result.php');
        break;
    case 'get-lightbox-comment-story':
        include('app/phone/requests/get_lightbox_comment_story.php');
        break;
    case 'update-notifications':
        include('app/phone/requests/update_notifications.php');
        break;
    case 'update-general-settings':
        include('app/phone/requests/update_general_settings.php');
        break;
    case 'update-password':
        include('app/phone/requests/update_password.php');
        break;
    case 'delete-account':
        include('app/phone/requests/delete_account.php');
        break;
    case 'night-mode':
        include('app/phone/requests/night_mode.php');
        break;
    case 'register-follow-timeline':
        include('app/phone/requests/register_follow_timeline.php');
        break;
    case 'get-messages':
        include('app/phone/requests/get_messages.php');
        break;
    case 'get-search-user-message':
        include('app/phone/requests/get_search_user_message.php');
        break;
    case 'search-user-message':
        include('app/phone/requests/search_user_message.php');
        break;
    case 'get-chat':
        include('app/phone/requests/get_chat.php');
        break;
    case 'insert-new-message':
        include('app/phone/requests/insert_new_message.php');
        break;
    case 'update-seen-messages':
        include('app/phone/requests/update_seen_messages.php');
        break;
    case 'get-messages-news':
        include('app/phone/requests/get_messages_news.php');
        break;
    case 'get-notifications-count':
        include('app/phone/requests/get_notifications_count.php');
        break;
    case 'search-admin-user':
        include('app/phone/requests/search_admin_user.php');
        break;
    case 'update-site-settings':
        include('app/phone/requests/update_site_settings.php');
        break;
    case 'get-messages-result-chat':
        include('app/phone/requests/get_messages_result_chat.php');
        break;
    case 'update-general-settings-user':
        include('app/phone/requests/update_general_settings_user.php');
        break;
	case 'load-posts':
		include('app/phone/requests/load_posts.php');
		break;
	case 'check-loggedin':
		include('app/phone/requests/check_loggedin.php');
		break;
	case 'check-app':
		include('app/phone/requests/check_app.php');
		break;
	case 'user-data':
		include('app/phone/requests/user_data.php');
		break;
	case 'get-notifications':
		include('app/phone/requests/get_notifications.php');
		break;
}
mysqli_close($Connect);
unset($so);
?>