<?php




require_once('./resources/init.php');

$type = '';
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = $_GET['type'];
}

switch ($type) {
    case 'signup':
        include('requests/signup.php');
        break;
    case 'signin':
        include('requests/signin.php');
        break;
    case 'password-reset':
        include('requests/password_reset.php');
        break;
    case 'password-recover':
        include('requests/password_recover.php');
        break;
    case 'change-profile-avatar':
        include('requests/change_profile_avatar.php');
        break;
    case 'change-profile-cover':
        include('requests/change_profile_cover.php');
        break;
    case 'change-about-me':
        include('requests/change_about_me.php');
        break;
    case 'change-my-location':
        include('requests/change_my_location.php');
        break;
    case 'change-my-birthday':
        include('requests/change_my_birthday.php');
        break;
    case 'insert-new-post':
        include('requests/insert_new_post.php');
        break;
    case 'delete-post':
        include('requests/delete_post.php');
        break;
    case 'insert-like-post':
        include('requests/insert_like_post.php');
        break;
    case 'get-form-share':
        include('requests/get_form_share.php');
        break;
    case 'share-post':
        include('requests/share_post.php');
        break;
    case 'get-lightbox-comment':
        include('requests/get_lightbox_comment.php');
        break;
    case 'insert-new-comment':
        include('requests/insert_new_comment.php');
        break;
    case 'insert-like-comment':
        include('requests/insert_like_comment.php');
        break;
    case 'delete-comment':
        include('requests/delete_comment.php');
        break;
    case 'get-more-posts':
        include('requests/get_more_posts.php');
        break;
    case 'register-follow-suggestion':
        include('requests/register_follow_suggestion.php');
        break;
    case 'get-hover-profile-container':
        include('requests/get_hover_profile_container.php');
        break;
    case 'register-follow-suggestion-follow':
        include('requests/register_follow_suggestion_follow.php');
        break;
    case 'register-follow-hover':
        include('requests/register_follow_hover.php');
        break;
    case 'register-follow-result':
        include('requests/register_follow_result.php');
        break;
    case 'get-lightbox-comment-story':
        include('requests/get_lightbox_comment_story.php');
        break;
    case 'update-notifications':
        include('requests/update_notifications.php');
        break;
    case 'update-general-settings':
        include('requests/update_general_settings.php');
        break;
    case 'update-password':
        include('requests/update_password.php');
        break;
    case 'delete-account':
        include('requests/delete_account.php');
        break;
    case 'night-mode':
        include('requests/night_mode.php');
        break;
    case 'register-follow-timeline':
        include('requests/register_follow_timeline.php');
        break;
    case 'get-messages':
        include('requests/get_messages.php');
        break;
    case 'get-search-user-message':
        include('requests/get_search_user_message.php');
        break;
    case 'search-user-message':
        include('requests/search_user_message.php');
        break;
    case 'get-chat':
        include('requests/get_chat.php');
        break;
    case 'insert-new-message':
        include('requests/insert_new_message.php');
        break;
    case 'update-seen-messages':
        include('requests/update_seen_messages.php');
        break;
    case 'get-messages-news':
        include('requests/get_messages_news.php');
        break;
    case 'get-notifications-count':
        include('requests/get_notifications_count.php');
        break;
    case 'search-admin-user':
        include('requests/search_admin_user.php');
        break;
    case 'update-site-settings':
        include('requests/update_site_settings.php');
        break;
    case 'get-messages-result-chat':
        include('requests/get_messages_result_chat.php');
        break;
    case 'update-general-settings-user':
        include('requests/update_general_settings_user.php');
        break;
    case 'load-posts':
        include('requests/load_posts.php');
        break;
    case 'get-lightbox-comment-app':
        include('app/phone/requests/get_lightbox_comment.php');
        break;
    case 'get-form-share-app':
        include('app/phone/requests/get_form_share.php');
        break;
    case 'share-post-app':
        include('app/phone/requests/share_post.php');
        break;
    case 'delete-post-app':
        include('app/phone/requests/delete_post.php');
        break;
    case 'start-live-stream':
        include('requests/start_live_stream.php');
        break;
    case 'insert-new-comment-live':
        include('requests/insert_new_comment_live.php');
        break;
    case 'get-comments-live':
        include('requests/get_comments_live.php');
        break;
    case 'get-details-url':
        include('requests/get_details_url.php');
        break;
    case 'get-floopz-lightbox':
        include('requests/get_floopz_lightbox.php');
        break;
    case 'get-reactions':
        include('requests/get_reactions.php');
        break;
    case 'register-reaction':
        include('requests/register_reaction.php');
        break;
    case 'change-all-posts':
        include('requests/change_all_posts.php');
        break;
    case 'insert-new-post-moment':
        include('requests/insert_new_post_moment.php');
        break;
    case 'get-more-posts-moments':
        include('requests/get_more_posts_moments.php');
        break;
    case 'get-normal-chat':
        include('requests/get_normal_chat.php');
        break;
    case 'close-chat':
        include('requests/close_chat.php');
        break;
    case 'get-chat-emoticons':
        include('requests/get_chat_emoticons.php');
        break;
    case 'get-chat-upload':
        include('requests/get_chat_upload.php');
        break;
    case 'get-messages-chat':
        include('requests/get_messages_chat.php');
        break;
    case 'insert-new-message-chat':
        include('requests/insert_new_message_chat.php');
        break;
    case 'get-messages-news-chat':
        include('requests/get_messages_news_chat.php');
        break;
    case 'get-chat-gifs':
        include('requests/get_chat_gifs.php');
        break;
    case 'get-chat-stickers':
        include('requests/get_chat_stickers.php');
        break;
    case 'create-package':
        include('requests/create_package.php');
        break;
    case 'create-new-sticker-store':
        include('requests/create_new_sticker_store.php');
        break;
    case 'get-stickers-store':
        include('requests/get_stickers_store.php');
        break;
    case 'get-package-list':
        include('requests/get_package_list.php');
        break;
    case 'add-package-list':
        include('requests/add_package_list.php');
        break;
    case 'get-packge-type':
        include('requests/get_package_type.php');
        break;
    case 'get-post-stickers':
        include('requests/get_post_stickers.php');
        break;
    case 'get-packge-type-post':
        include('requests/get_package_type_post.php');
        break;
    case 'get-stickers-store-post':
        include('requests/get_stickers_store_post.php');
        break;
    case 'get-package-list-post':
        include('requests/get_package_list_post.php');
        break;
    case 'get-comment-stickers':
        include('requests/get_comment_stickers.php');
        break;
    case 'get-packge-type-comment':
        include('requests/get_package_type_comment.php');
        break;
    case 'get-stickers-store-comment':
        include('requests/get_stickers_store_comment.php');
        break;
    case 'get-package-list-comment':
        include('requests/get_package_list_comment.php');
        break;
    case 'load-messages-messenger':
        include('requests/load_messages_messenger.php');
        break;
    case 'get-notifications':
        include('requests/get_notifications.php');
        break;
    case 'get-messages-list':
        include('requests/get_messages_list.php');
        break;
    case 'update-seen-notification':
        include('requests/update_seen_notification.php');
        break;
    case 'search-user-header':
        include('requests/search_user_header.php');
        break;
	case 'get-emoticons-comment':
		include('requests/get_emoticons_comment.php');
		break;
}
mysqli_close($Connect);
unset($so);
?>