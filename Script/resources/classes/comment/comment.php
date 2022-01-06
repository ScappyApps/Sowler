<?php



class Comment {

    function So_RegisterComment($registration_data, $emoticon = '') {
        global $so, $Connect;
        if (empty($registration_data) || $so['loggedin'] == false) {
            return false;
        }

        $classUser = new User();

        if (empty($registration_data['postSticker']) && empty($registration_data['postText']) && empty($registration_data['postFile']) && empty($registration_data['postImage']) && empty($registration_data['postVideo']) && empty($registration_data['postGif']) && empty($registration_data['share_id'])) {
            return false;
        }

        $pattern = '#^(?:https?://)?';    # Optional URL scheme. Either http or https.
        $pattern .= '(?:www\.)?';         #  Optional www subdomain.
        $pattern .= '(?:';                #  Group host alternatives:
        $pattern .= 'youtu\.be/';       #    Either youtu.be,
        $pattern .= '|youtube\.com';    #    or youtube.com
        $pattern .= '(?:';              #    Group path alternatives:
        $pattern .= '/embed/';        #      Either /embed/,
        $pattern .= '|/v/';           #      or /v/,
        $pattern .= '|/watch\?v=';    #      or /watch?v=,    
        $pattern .= '|/watch\?.+&v='; #      or /watch?other_param&v=
        $pattern .= ')';                #    End path alternatives.
        $pattern .= ')';                  #  End host alternatives.
        $pattern .= '([\w-]{11})';        # 11 characters (Length of Youtube video ids).
        $pattern .= '(?:.+)?$#x';         # Optional other ending URL parameters.

        if (preg_match($pattern, $registration_data['postText'], $match)) {
            $registration_data['postYoutube'] = So_Secure($match[1]);
            $registration_data['postText'] = preg_replace('/((?:https?:\/\/)?www\.youtube\.com\/watch\?v=\w+\w-)/', "", $registration_data['postText']);
        }
        if (preg_match('#giphy.com\/(?:gifs|media)\/(.*)#i', $registration_data['postText'], $match)) {
            $registration_data['postGif'] = So_Secure($match[1]);
            $registration_data['postGif'] = explode("/", $registration_data['postGif']);
            $registration_data['postGif'] = $registration_data['postGif'][0];
        }
        if (preg_match('#giphy.com\/(?:gifs|media)\/(.*)#i', $registration_data['postGif'], $match)) {
            $registration_data['postGif'] = So_Secure($match[1]);
            $registration_data['postGif'] = explode("/", $registration_data['postGif']);
            $registration_data['postGif'] = $registration_data['postGif'][0];
        }

        $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
        $i = 0;
        preg_match_all($link_regex, $registration_data['postText'], $matches);
        foreach ($matches[0] as $match) {
            $match_url = strip_tags($match);
            $syntax = '[a]' . urlencode($match_url) . '[/a]';
            $registration_data['postText'] = str_replace($match, $syntax, $registration_data['postText']);
        }
        $mention_regex = '/@([A-Za-z0-9_]+)/i';
        preg_match_all($mention_regex, $registration_data['postText'], $matches);
        foreach ($matches[1] as $match) {
            $match = So_Secure($match);
            $match_user = $classUser->So_UserData($classUser->So_UserIdFromUsername($match));
            $match_search = '@' . $match;
            $match_replace = '@[' . $match_user['user_id'] . ']';
            if (isset($match_user['user_id'])) {
                $registration_data['postText'] = str_replace($match_search, $match_replace, $registration_data['postText']);
                $mentions[] = $match_user['user_id'];
            }
        }
        $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
        preg_match_all($hashtag_regex, $registration_data['postText'], $matches);
        foreach ($matches[1] as $match) {
            if (!is_numeric($match)) {
                $hashdata = So_GetHashtag($match);
                if (is_array($hashdata)) {
                    $match_search = '#' . $match;
                    $match_replace = '#[' . $hashdata['id'] . ']';
                    if (mb_detect_encoding($match_search, 'ASCII', true)) {
                        $registration_data['postText'] = preg_replace("/$match_search\b/i", $match_replace, $registration_data['postText']);
                    } else {
                        $registration_data['postText'] = str_replace($match_search, $match_replace, $registration_data['postText']);
                    }
                    $hashtag_query = "UPDATE " . T_HASHTAGS . " SET 
                    `last_trend_time` = " . time() . ", 
                    `trend_use_num`   = " . ($hashdata['trend_use_num'] + 1) . ",
                    `expire`          = '" . date('Y-m-d', strtotime(date('Y-m-d') . " +1week")) . "'       
                    WHERE `id` = " . $hashdata['id'];
                    $hashtag_sql_query = mysqli_query($Connect, $hashtag_query);
                }
            }
        }

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_COMMENTS . " ({$fields}) VALUES ({$data})");
        $comment_id = mysqli_insert_id($Connect);

        if ($query) {

            $classPost = new Post();
            $postData = $classPost->So_PostData($registration_data['post_id']);

            if (isset($mentions) && is_array($mentions)) {
                foreach ($mentions as $mention) {
                    if ($mention <> $so['user']['user_id']) {
                        $notification = array(
                            'comment_id' => $comment_id,
                            'post_id' => $registration_data['post_id'],
                            'user_id' => $so['user']['user_id'],
                            'to_id' => $postData['user_id'],
                            'type' => 'mentioned_you_on_a_comment',
                            'time' => time(),
                            'day' => date('d'),
                            'month' => date('m'),
                            'year' => date('Y')
                        );
                        So_RegisterNotification($notification);
                    }
                }
            }

            $classEmoticon = new Emoticon();
            if (!empty($emoticon)) {
                $list_emo = explode(',', $emoticon);
                foreach ($list_emo as $so['emoticon'] => $value) {
                    $classEmoticon->So_RegisterEmoticon($value);
                }
            }

            if ($registration_data['user_id'] <> $postData['user_id']) {
                $notification = array(
                    'comment_id' => $comment_id,
                    'post_id' => $registration_data['post_id'],
                    'user_id' => $so['user']['user_id'],
                    'to_id' => $postData['user_id'],
                    'type' => 'commented_on_your_post',
                    'time' => time(),
                    'day' => date('d'),
                    'month' => date('m'),
                    'year' => date('Y')
                );
                So_RegisterNotification($notification);
            }

            return $comment_id;
        } else {
            return false;
        }
    }

    function So_CommentData($comment_id = 0) {
        global $so, $Connect;

        if (empty($comment_id)) {
            return false;
        }

        $data = array();
        $comment_id = So_Secure($comment_id);

        $select = "SELECT * FROM " . T_COMMENTS . " WHERE `comment_id` = '{$comment_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        $classUser = new User();
        $classEmoticon = new Emoticon();

        $assoc['user'] = $classUser->So_UserData($assoc['user_id']);

        if (!empty($assoc['postFile'])) {
            $assoc['postFile'] = $so['config']['site_url'] . '/' . $assoc['postFile'];
        }
        if (!empty($assoc['postImage'])) {
            $assoc['postImage'] = $so['config']['site_url'] . '/' . $assoc['postImage'];
            $assoc['image_w'] = getimagesize($assoc['postImage'])[0];
            $assoc['image_h'] = getimagesize($assoc['postImage'])[1];
        }
        if (!empty($assoc['postVideo'])) {
            $assoc['postVideo'] = $so['config']['site_url'] . '/' . $assoc['postVideo'];
        }
        if (!empty($assoc['postGif'])) {
            $assoc['postGif'] = $assoc['postGif'];
        }

        if (!empty($assoc['postText'])) {
            $assoc['postText'] = So_Tags($assoc['postText']);
            $assoc['postText'] = $classEmoticon->So_Emoticon($assoc['postText']);
        }
		
		if (!empty($assoc['postSticker'])) {
			$assoc['postSticker'] =  $so['config']['site_url'] . '/' . $assoc['postSticker'];
		}
		
		if (!empty($assoc['time'])) {
			$assoc['post_time'] = So_Time($assoc['time']);
		}

        $data = $assoc;

        return $data;
    }

    function So_GetComments($post_id = 0) {
        global $so, $Connect;

        $data = array();
		
		$post_id = So_Secure($post_id);

        $select = "SELECT `comment_id` FROM " . T_COMMENTS . " WHERE `post_id` = '{$post_id}'";
        $select .= " ORDER BY `comment_id` DESC LIMIT 10";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_CommentData($assoc['comment_id']);
        }

        return $data;
    }

    function So_DeleteComment($comment_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($comment_id)) {
            return false;
        }

        $comment_id = So_Secure($comment_id);
        $selectComment = mysqli_query($Connect, "SELECT * FROM " . T_COMMENTS . " WHERE `comment_id` = '{$comment_id}'");
        $postData = mysqli_fetch_assoc($selectComment);

        if (count($postData) < 1) {
            return false;
        }

        $classPost = new Post();

        if ($postData['user_id'] <> $so['user']['user_id']) {
            if ($classPost->So_PostData($postData['post_id'])['user_id'] <> $so['user']['user_id']) {
                if ($so['user']['admin'] < 1) {
                    return false;
                }
            }
        }

        if (!empty($postData['postFile'])) {
            @unlink($postData['postFile']);
        }
        if (!empty($postData['postImage'])) {
            @unlink($postData['postImage']);
        }
        if (!empty($postData['postVideo'])) {
            @unlink($postData['postVideo']);
        }

        $delete = mysqli_query($Connect, "DELETE FROM " . T_COMMENTS . " WHERE `comment_id` = '{$comment_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_COMMENTS_LIKE . " WHERE `comment_id` = '{$comment_id}'");
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    function So_CommentLiked($comment_id = 0, $user_id = 0) {
        global $so, $Connect;
        if (empty($comment_id) || empty($user_id)) {
            return false;
        }

        $comment_id = So_Secure($comment_id);
        $user_id = So_Secure($user_id);

        $select = "SELECT * FROM " . T_COMMENTS_LIKE . " WHERE `user_id` = '{$user_id}' AND `comment_id` = '{$comment_id}'";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_CountCommentLike($comment_id = 0) {
        global $so, $Connect;
        if (empty($comment_id)) {
            return false;
        }

        $comment_id = So_Secure($comment_id);

        $select = "SELECT * FROM " . T_COMMENTS_LIKE . " WHERE `comment_id` = '{$comment_id}'";
        $query = mysqli_query($Connect, $select);
        return mysqli_num_rows($query);
    }

    function So_RegisterCommentLike($comment_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($comment_id)) {
            return false;
        }

        $comment_id = So_Secure($comment_id);
        $user_id = So_Secure($so['user']['user_id']);

        if ($this->So_CommentLiked($comment_id, $user_id) === true) {
            $delete = mysqli_query($Connect, "DELETE FROM " . T_COMMENTS_LIKE . " WHERE `user_id` = '{$user_id}' AND `comment_id` = '{$comment_id}'");
            if ($delete) {
                return true;
            } else {
                return false;
            }
        } else {

            if (count($this->So_CommentData($comment_id)) < 1) {
                return false;
            }

            $registration_data = array(
                'post_id' => $this->So_CommentData($comment_id)['post_id'],
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'time' => time()
            );

            $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
            $data = '\'' . implode('\', \'', $registration_data) . '\'';
            $query = mysqli_query($Connect, "INSERT INTO " . T_COMMENTS_LIKE . " ({$fields}) VALUES ({$data})");
            if ($query) {
                if ($user_id <> $this->So_CommentData($comment_id)['user_id']) {
                    $notification = array(
                        'comment_id' => $comment_id,
                        'post_id' => $this->So_CommentData($comment_id)['post_id'],
                        'user_id' => $so['user']['user_id'],
                        'to_id' => $this->So_CommentData($comment_id)['user_id'],
                        'type' => 'liked_your_comment',
                        'time' => time(),
                        'day' => date('d'),
                        'month' => date('m'),
                        'year' => date('Y')
                    );
                    So_RegisterNotification($notification);
                }
                return true;
            } else {
                return false;
            }
        }
    }

}
