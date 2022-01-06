
total_notifications = 0;
total_messages = 0;
jQuery(document).ready(function ($) {
    var uls = $('#twidley-dropdown #twidley-dropdown-menu');
    uls.hide();

    $('#twidley-dropdown > #open-twidley-dropdown').click(function (e) {
        e.stopPropagation();
        uls.hide();
        if ($(this).attr('data-status') == 'false') {
            $(this).find('#twidley-dropdown-menu').show();
            $(this).attr('data-status', 'true');
        } else {
            $(this).find('#twidley-dropdown-menu').hide();
            $(this).attr('data-status', 'false');
        }
    });
    $('#twidley-dropdown #twidley-dropdown-menu').click(function (e) {
        e.stopPropagation();
    });
    $('body').click(function () {
        uls.hide();
    });
});
$(function () {
    $(document).on('mouseover', '.button-hover-get-reactions', function () {
        $('.list-reactions').attr('style', 'position: static;width: 296px;height: 80px;margin-top: -60px;margin-left: -65px;');

        var post_id = $(this).attr('data-story');
        if ($('#list-reactions-' + post_id + ' .asljdklajas').length > 0) {
            setTimeout(function () {
                $('.list-reactions').attr('style', '');
            }, 2000);
            return false;
        }

        $.get(So_Requests() + '?type=get-reactions', {
            post_id: post_id
        }, function (data) {
            if (data.status == 200) {
                if ($('#list-reactions-' + post_id + ' .asljdklajas').length > 0) {
                    return false;
                } else {
                    $('#list-reactions-' + post_id).html(data.html);
                }
            } else {
                return false;
            }
        });
    });
});
function So_DeletePost(post_id) {
    $.get(So_Requests() + '?type=delete-post', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('#stream-item-tweet-' + post_id).remove();
            So_CloseFormShare();
        } else {
            return false;
        }
    });
}
function So_DeletePostRest(post_id) {
    $.get(So_Requests() + '?type=delete-post-app', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('#stream-item-tweet-' + post_id).remove();
            So_AlertMessage(data.message);
            So_CloseFormShare();
        } else {
            return false;
        }
    });
}
$(document).on('click', '.click-toplay-pause-video', function () {
    if ($(this).attr('data-status') == 'true') {
        $(this).attr('data-status', 'false');
        $('#video-gif-' + $(this).attr('data-post-id')).trigger('pause');
    } else {
        $(this).attr('data-status', 'true');
        $('#video-gif-' + $(this).attr('data-post-id')).trigger('play');
    }
});
function So_OpenPublisherBoxLightbox() {
    $('.timeline-tweet-box').show();
    if ($('.RichEditor-scrollContainer.u-borderRadiusInherit, textarea#tweet-box-home-timeline').length < 1) {
        return false;
    }
    $('.lightbox, .TweetBoxToolbar-tweetButton.tweet-button').show();
    $('.timeline-tweet-box').css({
        'position': 'absolute',
        'z-index': '4002',
        'border-radius': '10px',
        'border': 'none'
    });
    $('.RichEditor-scrollContainer.u-borderRadiusInherit, textarea#tweet-box-home-timeline').css('height', '80px');
    $('.top-timeline-tweetbox .tweet-user').css({'border-bottom-left-radius': '7px', 'border-bottom-right-radius': '7px'});
    $('.timeline-tweet-box .modal-header').show();
}
function So_HideLightbox() {
    $('.lightbox, .TweetBoxToolbar-tweetButton.tweet-button').hide();
    $('.timeline-tweet-box').css({
        'position': '',
        'z-index': '',
        'border-radius': '',
        'border': ''
    });
    if ($(window).width() < 640) {
        $('.timeline-tweet-box').hide();
    }
    $('.RichEditor-scrollContainer.u-borderRadiusInherit, textarea#tweet-box-home-timeline').css('height', '');
    $('.top-timeline-tweetbox .tweet-user').css({'border-bottom-left-radius': '', 'border-bottom-right-radius': ''});
    $('.timeline-tweet-box .modal-header').hide();
    $('body').css('overflow-y', 'auto');
    $('.DMDialog.modal-container').html('');
    $('.DMDialog.modal-container').addClass('hidden');
}
$(document).on('click', '.lightbox', function () {
    So_HideLightbox();
});
function So_InsertLike(post_id) {
    $.get(So_Requests() + '?type=insert-like-post', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('#button-like-' + post_id).html(data.html);
            $('#count-like-' + post_id).html(data.count);
        } else {
            return false;
        }
    });
}
function So_InsertLikeComment(comment_id) {
    $.get(So_Requests() + '?type=insert-like-comment', {
        comment_id: comment_id
    }, function (data) {
        if (data.status == 200) {
            $('#button-comment-' + comment_id).html(data.html);
        } else {
            return false;
        }
    });
}
function So_DetectLanguage(text, post_id, key, lang) {
    $.get('https://translate.yandex.net/api/v1.5/tr.json/detect?key=' + key, {
        text: text
    }, function (data) {
        if (data.code.length > 0) {
            if (data.code == 404) {
                return false;
            }
        }
        if (data.lang != lang) {
            $('#translate-button-' + post_id).show();
        } else {
            return false;
        }
    });
}
function So_TranslateText(text, post_id, key, lang) {
    $.get('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + key, {
        text: text,
        lang: lang,
        format: 'html'
    }, function (data) {
        $('#translate-p-' + post_id).html(data.text);
        $('#translate-button-' + post_id).hide();
    });
}
function So_DetectLanguageComment(text, comment_id, key, lang) {
    $.get('https://translate.yandex.net/api/v1.5/tr.json/detect?key=' + key, {
        text: text
    }, function (data) {
        if (data.lang != lang) {
            $('#translate-button-comment-' + comment_id).show();
        } else {
            return false;
        }
    });
}
function So_TranslateTextComment(text, comment_id, key, lang) {
    $.get('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + key, {
        text: text,
        lang: lang,
        format: 'html'
    }, function (data) {
        $('#translate-p-comment-' + comment_id).html(data.text);
        $('#translate-button-comment-' + comment_id).hide();
    });
}
function So_SharePost(post_id) {
    $.get(So_Requests() + '?type=get-form-share', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('.content-share').html(data.html);
            $('body').css('overflow-y', 'hidden');
        } else {
            return false;
        }
    });
}
function So_SharePostApp(post_id) {
    $.get(So_Requests() + '?type=get-form-share-app', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('.content-share').html(data.html);
            $('body').css('overflow-y', 'hidden');
        } else {
            return false;
        }
    });
}
function So_CloseFormShare() {
    $('.content-share').html('');
    $('body').css('overflow-y', '');
}
function So_PutGifPublisherBox(image) {
    $('input#post-gif').val(image);
    $('.ComposerThumbnail-image').attr('src', image);
    $('.ajfajdawda.dropdown-menu').toggle();
    $('.news-feed-form .thumbnail-wrapper').show();
    So_BackgroundHidden();
    $('.color-box').hide();
}
$(document).on('click', '#put-gif-publisher-box-comment', function () {
	var image_ = $(this).attr('data-image');
	var post_id = $(this).attr('post-id');
	$('#publisher-box-comment-' + post_id).find('input#post-gif-comment').val(image_);
    $('#publisher-box-comment-' + post_id).find('.ComposerThumbnail-image').attr('src', image_);
    $('#publisher-box-comment-' + post_id).find('.ComposerThumbnails').show();
    $('#publisher-box-comment-' + post_id + ' .thumbnail-wrapper').show();
	So_OpenCommentsGif(post_id);
});
function So_PutGifPublisherBoxChat(image, user_id) {
    $('input#post-gif-chat-' + user_id).val(image);
    $('.ComposerThumbnail-image-chat-' + user_id).attr('src', image);
    $('.ajfajdawda.dropdown-menu').toggle();
    $('.ComposerThumbnail--gif-chat-' + user_id).show();
}
function So_GetLightboxComment(post_id) {

    $.get(So_Requests() + '?type=get-lightbox-comment', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('.content-share').html(data.html);
            $('body').css('overflow-y', 'hidden');
        } else {
            return false;
        }
    });
}
function So_GetLightboxCommentApp(post_id) {

    $.get(So_Requests() + '?type=get-lightbox-comment-app', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('.content-share').html(data.html);
            $('body').css('overflow-y', 'hidden');
        } else {
            return false;
        }
    });
}
function So_DeleteComment(comment_id) {
    $.get(So_Requests() + '?type=delete-comment', {
        comment_id: comment_id
    }, function (data) {
        if (data.status == 200) {
            $('#stream-item-tweet-comment-' + comment_id).remove();
        } else {
            return false;
        }
    });
}
function So_LoadMorePosts() {
    var post_id = $('.twidley-post:last').attr('data-id');

    var user_id = 0;
    if (typeof ($('.twidley-post').attr('data-user-id')) == "string") {
        user_id = $('.twidley-post').attr('data-user-id');
    }

    $.get(So_Requests() + '?type=get-more-posts', {
        post_id: post_id,
        user_id: user_id
    }, function (data) {
        scrolled = 0;
        if (data.status == 200) {
            $('#qadqwdqw').append(data.html);
        } else {
            return false;
        }
    });
}
$(function () {
    scrolled = 0;
    $(window).scroll(function () {
        var nearToBottom = 100;
        if ($('.twidley-post').length > 0 && $('.page-hashtag').length < 1 && $('.page-notification').length < 1) {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - nearToBottom) {
                if (scrolled == 0) {
                    scrolled = 1;
                    So_LoadMorePosts();
                }
            }
        }
    });
});
function So_RegisterFollow(user_id) {
    $.get(So_Requests() + '?type=register-follow-suggestion', {
        user: user_id
    }, function (data) {
        if (data.status == 200) {
            $('#button-suggestion-follow-' + user_id).html(data.html);
        } else {
            return false;
        }
    });
}
function So_RegisterFollowSuggestion(user_id) {
    $.get(So_Requests() + '?type=register-follow-suggestion-follow', {
        user: user_id
    }, function (data) {
        if (data.status == 200) {
            $('#register-follow-button-' + user_id).html(data.html);
        } else {
            return false;
        }
    });
}
function So_RegisterFollowHover(user_id) {
    $.get(So_Requests() + '?type=register-follow-hover', {
        user: user_id
    }, function (data) {
        if (data.status == 200) {
            $('#user-actions-follow-button-' + user_id).html(data.html);
        } else {
            return false;
        }
    });
}
function So_RegisterFollowResult(user_id) {
    $.get(So_Requests() + '?type=register-follow-result', {
        user: user_id
    }, function (data) {
        if (data.status == 200) {
            $('#result-button-follow-' + user_id).html(data.html);
        } else {
            return false;
        }
    });
}
$(document).on('mouseover', 'a.account-group.js-account-group.js-action-profile.js-user-profile-link.js-nav', function () {
    var user_id = $(this).attr('data-user-id');
    var topPosition = $(this).offset().top + 25;
    var leftPosition = $(this).offset().left;
    $.post(So_Requests() + '?type=get-hover-profile-container', {
        user_id: user_id
    }, function (data) {
        if (data.status == 200) {
            $('#profile-hover-container').html(data.html);
            $('#profile-hover-container').attr('style', "top: " + topPosition + "px;left: " + leftPosition + 'px;opacity: 1;display: block;');
        } else {
            return false;
        }
    });
});

So_CheckHoverProfileHover();
function So_CheckHoverProfileHover() {
    if ($('a.account-group.js-account-group.js-action-profile.js-user-profile-link.js-nav:hover').length < 1 && $('.profile-card.ProfileCard:hover').length < 1) {
        $('#profile-hover-container').html('');
        $('#profile-hover-container').removeAttr('style');
    }
    setTimeout(function () {
        So_CheckHoverProfileHover();
    }, 300);
}
$(document).on('click', '#global-actions>li', function () {
    $('ul#global-actions').find('li').removeClass('active');
    $(this).addClass('active');
});
function So_GetLightboxCommentNotification(post_id, comment_id) {

    $.get(So_Requests() + '?type=get-lightbox-comment-story', {
        post_id: post_id,
        comment_id: comment_id
    }, function (data) {
        if (data.status == 200) {
            $('.content-share').html(data.html);
            $('body').css('overflow-y', 'hidden');
        } else {
            return false;
        }
    });
}
function So_UpdateNotifications() {
    $.get(So_Requests() + '?type=update-notifications', {

    }, function (data) {
        if (data.status == 200) {
            total_notifications = 0;
            $('.people.notifications>a>span.count-inner').html('');
            $('.people.notifications>a>span.count').hide();
        } else {
            return false;
        }
    });
}
function So_NightMode() {
    $.get(So_Requests() + '?type=night-mode', {

    }, function (data) {
        window.location.href = window.location;
    });
}
function So_RegisterFollowTimeline(user_id) {
    $.get(So_Requests() + '?type=register-follow-timeline', {
        user: user_id
    }, function (data) {
        if (data.status == 200) {
            $('.control-block-button').html(data.html);
        } else {
            return false;
        }
    });
}
function So_OpenMessages() {
    $.get(So_Requests() + '?type=get-messages', {

    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container').html(data.html);
            $('body').css('overflow-y', 'hidden');
            $('.DMDialog.modal-container').removeClass('hidden');
        } else {
            return false;
        }
    });
}
function So_GetSearch() {
    $.get(So_Requests() + '?type=get-search-user-message', {

    }, function (data) {
        if (data.status == 200) {
            $('#fjiojdawqd').html(data.html);
        } else {
            return false;
        }
    });
}
function So_SearchUserMessages(value) {
    $.get(So_Requests() + '?type=search-user-message', {
        value: value
    }, function (data) {
        if (data.status == 200) {
            $('#DMComposeTypeaheadSuggestions').html(data.html);
        } else {
            $('#DMComposeTypeaheadSuggestions').html('');
        }
    });
}
$(document).on('click', '.DMInbox-conversationItem', function () {
    $.post(So_Requests() + '?type=get-chat', {
        user_id: $(this).attr('data-token-id')
    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container').html(data.html);
            $('body').css('overflow-y', 'hidden');
            $('.DMDialog.modal-container').removeClass('hidden');
        } else {
            return false;
        }
    });
});
var So_LoadImg = function (selector, callback) {
    $(selector).each(function () {
        if (this.complete || $(this).height() > 0) {
            callback.apply(this);
        } else {
            $(this).on('load', function () {
                callback.apply(this);
            });
        }
    });
};
function So_UpdateSeenMessages(user_id) {
    $.post(So_Requests() + '?type=update-seen-messages', {
        user_id: user_id
    }, function (data) {
        return true;
    });
}
function So_GetMessagesNews() {
    if ($('#list-messages').length < 1) {
        return false;
    }

    var user_id = $('.messenger-chat-opened').attr('data-messenger');
    var message_id = 0;
    var div_scroll = $('#list-messages')[0];
    if (typeof ($('#message-content-with-id').attr('data-message'))) {
        message_id = $('#message-content-with-id:last-child').attr('data-message');
    }

    $.post(So_Requests() + '?type=get-messages-news', {
        message_id: message_id,
        user_id: user_id
    }, function (data) {

        if (data.status == 200) {
            $('#list-messages').append(data.html);
            div_scroll.scrollTop = div_scroll.scrollHeight;
        }
        setTimeout(function () {
            So_GetMessagesNews();
        }, 4000);
    });
}
function So_CheckNotifications() {
    $.get(So_Requests() + '?type=get-notifications-count', {

    }, function (data) {
        var JSONdata = JSON.parse(data);
        if (JSONdata[0].status == 200) {
            if (JSONdata[0].count_notifications == 0) {
                $('ul.nav.js-global-actions .people .count').hide();
                $('.count-notifications').html('0');
            } else {
                $('ul.nav.js-global-actions .people .count').show();
                $('.count-notifications').html(JSONdata[0].count_notifications);
            }
            if (JSONdata[0].count_messages == 0) {
                $('.global-dm-nav .dm-new').hide();
                $('.count-messages').html('0');
            } else {
                $('.global-dm-nav .dm-new').show();
                $('.count-messages').html(JSONdata[0].count_messages);
            }
            if (JSONdata[0].users_online != 0) {
                $('.chat-container > #chat-list-messages').html(JSONdata[0].users_online);
            } else {
                $('.chat-container > #chat-list-messages').html('');
            }
        }
        setTimeout(function () {
            So_CheckNotifications();
        }, 4000);
    });
}
function So_OpenHeader() {
    $('.header-content').slideToggle();
}
function So_SearchFriend(value) {
    if (value == '') {
        $('#normal-list-chat').show();
        $('#search-list-chat').hide();
        $('#search-list-chat boxresult').html('');
        return false;
    }
    $.get(So_Requests() + '?type=get-messages-result-chat', {
        value: value
    }, function (data) {
        if (data.status == 200) {
            $('#normal-list-chat').hide();
            $('#search-list-chat').show();
            $('#search-list-chat boxresult').html(data.html);
        } else {
            $('#normal-list-chat').show();
            $('#search-list-chat').hide();
            $('#search-list-chat boxresult').html('');
        }
    });
}
function So_OpenFloopz() {
    if ($('.floopz-music').find('.container-played-music').length < 1) {

        $.get(So_Requests() + '?type=get-floopz-lightbox', {

        }, function (data) {
            if (data.status == 200) {
                $('.floopz-music').html(data.html);
                $('.lightbox').show();
            } else {
                return false;
            }
        });

    } else {
        So_MaximizeFloopz();
    }
}
function So_MaximizeFloopz() {
    $('.lightbox').show();
    $('.floopz-music .container-played-music iframe').removeAttr('style');
    $('.h1-floopz').show();
}
function So_MinimizeFloopz() {
    $('.lightbox').hide();

    if ($(window).width() < 770) {
        $('.floopz-music .container-played-music iframe').attr('style', 'height: 70px;/* box-shadow: rgb(232, 236, 241) 0px -2px 2px; */width: 100%;');
    } else {
        $('.floopz-music .container-played-music iframe').attr('style', 'height: 70px;/* box-shadow: rgb(232, 236, 241) 0px -2px 2px; */width: 100%;padding: 0px 71px;');
    }
    $('.h1-floopz').hide();
}
var event_reaction = 0;
function So_RegisterReaction(post_id, reaction) {
    if (event_reaction == 1) {
        return false;
    }

    event_reaction = 1;

    $.post(So_Requests() + '?type=register-reaction', {
        post_id: post_id,
        reaction: reaction
    }, function (data) {
        event_reaction = 0;
        if (data.status == 200) {
            $('#button-like-' + post_id).find('.reactions .reaction').html(data.html);
			$('#total-reactions-' + post_id).html(data.count);
        } else {
            return false;
        }
    });
}
function So_ShareTo(url) {
    url = url.replace("%2F", "/");
    url = url.replace("%3A", ":");
    window.open(url);
}
function So_ChangeSeePosts(value, image) {
    $.post(So_Requests() + '?type=change-all-posts', {
        value: value
    }, function (data) {
        $('#qadqwdqw').html('<br><img id="load-posts-image" src="' + image + '" width="100%" />');
        if (data.status == 200) {
            setTimeout(function () {
                So_LoadPosts();
            }, 1000);
        } else {
            return false;
        }
    });
}
function So_SubmitForm(form_name) {
    $('#' + form_name).submit();
}
function So_CloseChatUser(user_id) {
    $('.opened-chats').find('#chat-' + user_id).remove();
    /**$.post(So_Requests() + '?type=close-chat', {
     user_id: user_id 
     }, function (data) {
     if (data.status == 200) { 
     $('.opened-chats').find('#chat-' + user_id).remove();
     } else {
     return false;
     }
     });**/
}
$(document).on('click', '.chat-header', function () {
    var user_id = $(this).attr('data-chat');
    $('#chat-' + user_id).find('.box-general').toggle(200);

});
function So_OpenListThings(user_id) {
    $('.opened-chats').find('.chat-list-box').hide();
    $('.opened-chats .chat-box-normal .chat-footer .chat-extra-box').hide();
    if ($('.opened-chats').find('#chat-list-box-' + user_id).attr('data-toggle') == 'false') {
        $('.opened-chats').find('#chat-list-box-' + user_id).attr('data-toggle', 'true');
        $('.opened-chats').find('#chat-list-box-' + user_id).show();
    } else {
        $('.opened-chats').find('#chat-list-box-' + user_id).attr('data-toggle', 'false');
        $('.opened-chats').find('#chat-list-box-' + user_id).hide();
    }
}
function So_GetMessagesChat(user_id, seen, message_seen) {
    $.post(So_Requests() + '?type=get-messages-chat', {
        user_id: user_id
    }, function (data) {
        if (data.status == 200) {
            var div_scroll = $('.opened-chats').find('#chat-' + user_id).find('.chat-body')[0];
            $('.opened-chats').find('#chat-' + user_id).find('.chat-body').html(data.messages);
            setTimeout(function () {
                if (seen == 1) {
                    $('#chat-' + user_id + ' .chat-body').append('<div class="visto-chat-' + user_id + '" style="background: #ffffff;padding: 0px 10px 5px; text-align: rigth;">' + message_seen + '</div>');
                    $('.visto-chat-' + user_id).show();
                }
                div_scroll.scrollTop = div_scroll.scrollHeight;
            }, 500);
        } else {
            return false;
        }
    });
}
$(document).on('click', '#submit-form-sticker', function () {
    var image = $(this).attr('data-sticker');
    var user_id = $(this).attr('data-user');

    $('.opened-chats').find('#chat-' + user_id).find('#post-sticker-chat').val(image);
    $('.chat-form-' + user_id).submit();
    So_OpenListThings(user_id);
});
function So_GetStickersStore(user_id) {
    $.post(So_Requests() + '?type=get-stickers-store', {
        user_id: user_id
    }, function (data) {
        if (data.status == 200) {
            So_OpenListThings(user_id);
            $('.DMDialog.modal-container.hidden').removeClass('hidden');
            $('.DMDialog.modal-container').html(data.html);
        } else {
            return false;
        }
    });
}
function So_GetStickersStorePost() {
    $.post(So_Requests() + '?type=get-stickers-store-post', {
    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container.hidden').removeClass('hidden');
            $('.DMDialog.modal-container').html(data.html);
        } else {
            return false;
        }
    });
}
function So_CloseStickersStore() {
    $('.DMDialog.modal-container').addClass('hidden');
    $('.DMDialog.modal-container.hidden').html('');
}
function So_GetStickerList(package, to_id) {
    $.post(So_Requests() + '?type=get-package-list', {
        package: package,
        to_id: to_id
    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container').html(data.html);
        } else {
            return false;
        }
    });
}
function So_GetStickerListPost(package) {
    $.post(So_Requests() + '?type=get-package-list-post', {
        package: package
    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container').html(data.html);
        } else {
            return false;
        }
    });
}
function So_AddToMyStickers(package, to_id) {
    $.post(So_Requests() + '?type=add-package-list', {
        sticker_id: package
    }, function (data) {
        if (data.status == 200) {
            $('#button-package-' + package).remove();
            So_CloseStickersStore();
            So_GetChatStickers(to_id);
        } else {
            return false;
        }
    });
}
function So_AddToMyStickersPost(package) {
    $.post(So_Requests() + '?type=add-package-list', {
        sticker_id: package
    }, function (data) {
        if (data.status == 200) {
            $('#button-package-' + package).remove();
            So_CloseStickersStore();
            $('#stickers-post').find('a#navbarDropdownMenuLink').click();
        } else {
            return false;
        }
    });
}
function verificachars() {
    var objeto = $('#tweet-box-home-timeline').val();

    if (objeto.length > 130) {
        So_BackgroundHidden();
        $('.color-box').hide();
    }
}
$(document).on('click', '#submit-form-sticker-post', function () {
    var image = site_url + '/' + $(this).attr('data-sticker');
    $('input#post-sticker').val(image);
    $('.ComposerThumbnail-image').attr('src', image).css({'width': '64px', 'height': '64px'});
    $('.ComposerThumbnail-imageContainer, .ComposerThumbnail.ComposerThumbnail--gif').css({'width': '64px', 'height': '64px'});
    $('.news-feed-form .thumbnail-wrapper').show();
	
    So_BackgroundHidden();
    $('.color-box, .ComposerThumbnail-overlay.ComposerThumbnail-gifBadge').hide();
    $('#stickers-post').find('.dropdown-menu').hide();
});
function So_GetStickersStoreComment(post_id) {
    $.post(So_Requests() + '?type=get-stickers-store-comment', {
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container.hidden').removeClass('hidden');
            $('.DMDialog.modal-container').html(data.html);
        } else {
            return false;
        }
    });
}
function So_GetStickerListComment(package, post_id) {
    $.post(So_Requests() + '?type=get-package-list-comment', {
        package: package,
        post_id: post_id
    }, function (data) {
        if (data.status == 200) {
            $('.DMDialog.modal-container').html(data.html);
        } else {
            return false;
        }
    });
}
function So_AddToMyStickersComment(package, post_id) {
    $.post(So_Requests() + '?type=add-package-list', {
        sticker_id: package
    }, function (data) {
        if (data.status == 200) {
            So_CloseStickersStore();
            $('#publisher-box-comment-' + post_id + ' #stickers-post').find('.dropdown-menu').hide();
            So_GetCommentStickers(post_id);
        } else {
            return false;
        }
    });
}
$(document).on('click', '#submit-form-sticker-comment', function () {
    var image = $(this).attr('data-sticker');
    var post_id = $(this).attr('data-post');
	$('.show-publisher-box-comment-' + post_id + ' input#post-sticker-comment').val(image);
    $('.show-publisher-box-comment-' + post_id + ' .ComposerThumbnail-image').attr('src', image).css({'width': '64px', 'height': '64px'});
    $('.show-publisher-box-comment-' + post_id + ' .ComposerThumbnail-imageContainer, .ComposerThumbnail.ComposerThumbnail--gif').css({'width': '64px', 'height': '64px'});
    $('.show-publisher-box-comment-' + post_id + ' .thumbnail-wrapper').show();
    So_BackgroundHidden();
    $('#stickers-post').find('.dropdown-menu').hide();
    $('.ComposerThumbnail-overlay.ComposerThumbnail-gifBadge').hide();
	
});

/* -----------------------------
 * Top Search bar function
 * Script file: selectize.min.js
 * Documentation about used plugin:
 * https://github.com/selectize/selectize.js
 * ---------------------------*/

function So_SearchUsers(value) {
    return false;

    /**$.get(So_Requests() + '?type=search-user-header', {
        value: value
    }, function (data) {
        var array_options;
        for (var i = 0; i < data.total; i++) {
            array_options = array_options + {image: data[0].result[i].avatar, name: data[0].result[i].name, lastseen: data[0].result[i].lastseen, icon: 'olymp-happy-face-icon', url: data[0].result[i].url, username: data[0].result[i].username};
        }
        if (data.status == 200) {
            if (topUserSearch.length) {
                topUserSearch.selectize({
                    persist: true,
                    maxItems: 2,
                    valueField: 'name',
                    labelField: 'name',
                    searchField: ['name'],
                    options: [
                        forEach(function () {
                            for (var i = 0; i < data.total; i++) {
                                {image: data[0].result[i].avatar, name: data[0].result[i].name, lastseen: data[0].result[i].lastseen, icon: 'olymp-happy-face-icon', url: data[0].result[i].url, username: data[0].result[i].username}
                            }
                        })
                    ],
                    render: {
                        option: function (item, escape) {
                            return '<div class="inline-items">' +
                                    (item.image ? '<div class="author-thumb"><img width="35px" src="' + escape(item.image) + '" alt="avatar"></div>' : '') +
                                    '<div class="notification-event">' +
                                    (item.name ? '<span class="h6 notification-friend"><a href="' + escape(item.url) + '" data-ajax="' + escape(item.username) + '">' + escape(item.name) + '</a></span>' : '') +
                                    (item.lastseen ? '<span class="chat-message-item">' + escape(item.lastseen) + '</span>' : '') +
                                    '</div>' +
                                    (item.icon ? '<span class="notification-icon"><svg class="' + escape(item.icon) + '"><use xlink:href="icons/icons.svg#' + escape(item.icon) + '"></use></svg></span>' : '') +
                                    '</div>';
                        },
                        item: function (item, escape) {
                            var label = item.name;
                            return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    '</div>';
                        }
                    }
                });
            }
        }
    });**/
}
$(document).on('mouseenter', '.hover-notifications', function () {
    $.get(So_Requests() + '?type=get-notifications', {

    }, function (data) {
        if (data.status == 200) {
            $('ul.notification-list.content-notifications').html(data.html);
        } else {
            return false;
        }
    });
});
$(document).on('mouseenter', '.hover-messages', function () {
    $.get(So_Requests() + '?type=get-messages-list', {

    }, function (data) {
        if (data.status == 200) {
            $('ul.notification-list.chat-message.header-type').html(data.html);
        } else {
            return false;
        }
    });
});
function So_UpdateSeenNotification(noti_id) {
    if (noti_id == '') {
        return false;
    }

    $.post(So_Requests() + '?type=update-seen-notification', {
        noti_id: noti_id
    }, function (data) {
        return true;
    });
}
function So_ShowPublisherBoxComment(post_id) {
	$('#stream-item-tweet-' + post_id).find('#show-publisher-comment').toggle();
}
$(document).on('click', '#askfjaskj-close', function () {
	$('input#post-gif').val('');
	$('input#post-gif-comment').val('');
	$('input#post-sticker-comment').val('');
	$('.ComposerThumbnail-image').attr('src', '');
	$('.news-feed-form .thumbnail-wrapper').hide();
});
function So_SubmitChat(chat_id, event) {
	if(event.keyCode == 13 && event.shiftKey == 0) {
	$('form.chat-form-' + chat_id).submit();
	} else {
		return false;
	}
}
function So_SubmitMessenger(event) {
	if(event.keyCode == 13 && event.shiftKey == 0) {
	$('form#publisher-box').submit();
	} else {
		return false;
	}
}