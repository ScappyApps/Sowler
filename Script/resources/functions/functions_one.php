<?php

function So_GetHashtag($tag = '', $type = true) {
    global $Connect;
    $create = false;
    if (empty($tag)) {
        return false;
    }
    $tag = So_Secure($tag);
    $md5_tag = md5($tag);
    if (is_numeric($tag)) {
        $query = " SELECT * FROM " . T_HASHTAGS . " WHERE `id` = {$tag}";
    } else {
        $query = " SELECT * FROM " . T_HASHTAGS . " WHERE `hash` = '{$md5_tag}' ";
        $create = true;
    }
    $sql_query = mysqli_query($Connect, $query);
    $sql_numrows = mysqli_num_rows($sql_query);
    $week = date('Y-m-d', strtotime(date('Y-m-d') . " +1week"));
    if ($sql_numrows == 1) {
        $sql_fetch = mysqli_fetch_assoc($sql_query);
        return $sql_fetch;
    } elseif ($sql_numrows == 0 && $type == true) {
        if ($create == true) {
            $hash = md5($tag);
            $query_two = " INSERT INTO " . T_HASHTAGS . " (`hash`, `tag`, `last_trend_time`,`expire`) VALUES ('{$hash}', '{$tag}', " . time() . ", '$week')";
            $sql_query_two = mysqli_query($Connect, $query_two);
            if ($sql_query_two) {
                $sql_id = mysqli_insert_id($Connect);
                $data = array(
                    'id' => $sql_id,
                    'hash' => $hash,
                    'tag' => $tag,
                    'last_trend_time' => time(),
                    'trend_use_num' => 0
                );
                return $data;
            }
        }
    }
}

function So_EditTags($text, $link = true, $hashtag = true, $mention = true) {
    $classUser = new User();
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode = urldecode($match);
                $match_url = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', $match_decode, $text);
            }
        }
    }
    if ($hashtag == true) {
        $hashtag_regex = '/(#\[([0-9]+)\])/i';
        preg_match_all($hashtag_regex, $text, $matches);
        $match_i = 0;
        foreach ($matches[1] as $match) {
            $hashtag = $matches[1][$match_i];
            $hashkey = $matches[2][$match_i];
            $hashdata = So_GetHashtag($hashkey);
            if (is_array($hashdata)) {
                $hashlink = '#' . $hashdata['tag'];
                $text = str_replace($hashtag, $hashlink, $text);
            }
            $match_i++;
        }
    }
    if ($mention == true) {
        $mention_regex = '/@\[([0-9]+)\]/i';
        if (preg_match_all($mention_regex, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match = So_Secure($match);
                $match_user = $classUser->So_UserData($match);
                $match_search = '@[' . $match . ']';
                $match_replace = '@' . $match_user['username'];
                if (isset($match_user['user_id'])) {
                    $text = str_replace($match_search, $match_replace, $text);
                }
            }
        }
    }
    return $text;
}

function So_Tags($text, $link = true, $hashtag = true, $mention = true) {
    $classUser = new User();
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode = urldecode($match);
                $match_decode_url = $match_decode;
                $count_url = mb_strlen($match_decode);
                if ($count_url > 50) {
                    $match_decode_url = mb_substr($match_decode_url, 0, 30) . '...' . mb_substr($match_decode_url, 30, 20);
                }
                $match_url = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
            }
        }
    }
    if ($hashtag == true) {
        $hashtag_regex = '/(#\[([0-9]+)\])/i';
        preg_match_all($hashtag_regex, $text, $matches);
        $match_i = 0;
        foreach ($matches[1] as $match) {
            $hashtag = $matches[1][$match_i];
            $hashkey = $matches[2][$match_i];
            $hashdata = So_GetHashtag($hashkey);
            if (is_array($hashdata)) {
                $hashlink = '<a href="' . So_SeoLink('index.php?link1=hashtag&hash=' . $hashdata['tag']) . '" data-ajax="?link1=hashtag&hash=' . $hashdata['tag'] . '" class="hash">#' . $hashdata['tag'] . '</a>';
                $text = str_replace($hashtag, $hashlink, $text);
            }
            $match_i++;
        }
    }
    if ($mention == true) {
        $mention_regex = '/@\[([0-9]+)\]/i';
        if (preg_match_all($mention_regex, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match = So_Secure($match);
                $match_user = $classUser->So_UserData($match);
                $match_search = '@[' . $match . ']';
                $match_replace = '<span class="user-popover" data-id="' . $match_user['user_id'] . '"><a href="' . So_SeoLink('index.php?link1=timeline&username=' . $match_user['username']) . '" class="hash" data-ajax="?link1=timeline&username=' . $match_user['username'] . '">' . $match_user['name'] . '</a></span>';
                if (isset($match_user['user_id'])) {
                    $text = str_replace($match_search, $match_replace, $text);
                }
            }
        }
    }
    return $text;
}

function So_GetTrends($type = 'latest', $limit = 9) {
    global $Connect;
    $data = array();

    if (empty($type)) {
        return false;
    }
    if (empty($limit) or ! is_numeric($limit) or $limit < 1) {
        $limit = 9;
    }
    if ($type == "latest") {
        $query = "SELECT * FROM " . T_HASHTAGS . " WHERE `expire` >= CURRENT_DATE()  ORDER BY `last_trend_time` DESC LIMIT {$limit}";
    } elseif ($type == "top") {
        $query = "SELECT * FROM " . T_HASHTAGS . " WHERE `expire` >= CURRENT_DATE()  ORDER BY `trend_use_num` DESC LIMIT {$limit}";
    }
    $sql_query = mysqli_query($Connect, $query);
    $sql_numrows = mysqli_num_rows($sql_query);
    if ($sql_numrows > 0) {
        $classPost = new Post();
        while ($sql_fetch = mysqli_fetch_assoc($sql_query)) {
            $sql_fetch['url'] = So_SeoLink('index.php?link1=hashtag&hash=' . $sql_fetch['tag']);
            $sql_fetch['total'] = count($classPost->So_HashtagPosts($sql_fetch['tag']));
            if ($sql_fetch['total'] > 1000) {
                $sql_fetch['total'] = number_format($sql_fetch['total']);
            }
            $data[] = $sql_fetch;
        }
    }
    return $data;
}

function So_RegisterNotification($registration_data) {
    global $so, $Connect;
    if ($so['loggedin'] == false) {
        return false;
    }

    $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
    $data = '\'' . implode('\', \'', $registration_data) . '\'';
    $query = mysqli_query($Connect, "INSERT INTO " . T_NOTIFICATIONS . " ({$fields}) VALUES ({$data})");

    if ($query) {
        return true;
    } else {
        return false;
    }
}

function So_GetHashtagPosts($s_query, $after_post_id = 0, $limit = 5, $before_post_id = 0) {
    global $Connect;
    $data = array();
    $search_query = str_replace('#', '', So_Secure($s_query));
    $hashdata = So_GetHashtag($search_query, false);
    if (is_array($hashdata) && count($hashdata) > 0) {
        $search_string = "#[" . $hashdata['id'] . "]";
        $query_one = "SELECT post_id FROM " . T_POSTS . " WHERE `postText` LIKE '%{$search_string}%'";
        if (isset($after_post_id) && !empty($after_post_id) && is_numeric($after_post_id)) {
            $after_post_id = So_Secure($after_post_id);
            $query_one .= " AND post_id < {$after_post_id}";
        }
        if (isset($before_post_id) && !empty($before_post_id) && is_numeric($before_post_id)) {
            $before_post_id = So_Secure($before_post_id);
            $query_one .= " AND post_id > {$before_post_id}";
        }
        $query_one .= " ORDER BY `post_id` DESC LIMIT {$limit}";
        $sql_query_one = mysqli_query($Connect, $query_one);
        $classPost = new Post();
        while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
            $posts = $classPost->So_PostData($sql_fetch_one['post_id']);
            if (is_array($posts)) {
                $data[] = $posts;
            }
        }
    }
    return $data;
}

function So_GetSuggestions($limit = 5, $verified = 0) {
    global $so, $Connect;

    $data = array();

    $classUser = new User();

    $select = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` <> '{$so['user']['user_id']}'";
    $select .= " AND `user_id` NOT IN(SELECT `to_id` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$so['user']['user_id']}')";
    if (!empty($verified)) {
        $select .= " AND `verified` = '1'";
    }
    $select .= " ORDER BY RAND() LIMIT {$limit}";
    $query = mysqli_query($Connect, $select);
    while ($assoc = mysqli_fetch_assoc($query)) {
        $data[] = $classUser->So_UserData($assoc['user_id']);
    }

    return $data;
}

function So_Search($query = '', $before_id = 0, $limit = 36) {
    global $so, $Connect;
    if ($so['loggedin'] == false) {
        return false;
    }

    $query = So_Secure($query);

    $data = array();

    $classUser = new User();

    $select = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` <> '{$so['user']['user_id']}'";
    $select .= " AND (`username` LIKE '%" . $query . "%' OR `first_name` LIKE '%" . $query . "%' OR `last_name` LIKE '%" . $query . "%' OR CONCAT (`first_name`, ' ', `last_name`) LIKE '%" . $query . "%')";

    if (!empty($before_id)) {
        $before_id = So_Secure($before_id);
        $select .= " AND `user_id` < '{$before_id}'";
    }

    $select .= " ORDER BY `user_id` DESC LIMIT {$limit}";
    $query = mysqli_query($Connect, $select);
    while ($assoc = mysqli_fetch_assoc($query)) {
        $data[] = $classUser->So_UserData($assoc['user_id']);
    }

    return $data;
}

function So_Notifications() {
    global $so, $Connect;

    $data = array();

    $classUser = new User();
    $classPost = new Post();
    $classComment = new Comment();

    $select = "SELECT * FROM " . T_NOTIFICATIONS . " WHERE `to_id` = '{$so['user']['user_id']}' ORDER BY `id` DESC LIMIT 100";
    $query = mysqli_query($Connect, $select);
    while ($assoc = mysqli_fetch_assoc($query)) {
        $assoc['from'] = $classUser->So_UserData($assoc['user_id']);
        $assoc['to'] = $classUser->So_UserData($assoc['to_id']);
        $assoc['post'] = $classPost->So_PostData($assoc['post_id']);
        if ($assoc['type'] == 'shared_your_post') {
            $select_one = mysqli_query($Connect, "SELECT `share_id` FROM " . T_POSTS . " WHERE `post_id` = '{$assoc['post_id']}'");
            $assoc_one = mysqli_fetch_assoc($select_one);
            $assoc['post'] = $classPost->So_PostData($assoc_one['share_id']);
        }
        $assoc['comment'] = $classComment->So_CommentData($assoc['comment_id']);
        $data[] = $assoc;
    }

    return $data;
}

function So_NotificationAppGet($user_id = 0) {
    global $so, $Connect;
	
	if (empty($user_id)) {
		return false;
	}

	$user_id = So_Secure($user_id);
	
    $data = array();

    $classUser = new User();
    $classPost = new Post();
    $classComment = new Comment();

    $select = "SELECT * FROM " . T_NOTIFICATIONS . " WHERE `to_id` = '{$user_id}' ORDER BY `id` DESC LIMIT 100";
    $query = mysqli_query($Connect, $select);
    while ($assoc = mysqli_fetch_assoc($query)) {
        $assoc['avatar'] = $classUser->So_UserData($assoc['user_id'])['avatar'];
        $assoc['name'] = $classUser->So_UserData($assoc['user_id'])['name'];
        $assoc['comment'] = false;
		$assoc['time'] = So_Time($assoc['time']);
		
		$assoc['original_type'] = $assoc['type'];
		
		$assoc['type'] = $so['lang'][$assoc['type']];
        $data[] = $assoc;
    }

    return $data;
}

function So_NotificationsRest($notifi_id = 0, $user_id = 0) {
    global $so, $Connect;
	
	if (empty($notifi_id) || empty($user_id)) {
		return false;
	}
	
	$user_id = So_Secure($user_id);
	$notifi_id = So_Secure($notifi_id);

    $data = array();

    $classUser = new User();
    $classPost = new Post();
    $classComment = new Comment();

    $select = "SELECT * FROM " . T_NOTIFICATIONS . " WHERE `id` = '{$notifi_id}' AND `to_id` = '{$user_id}' ORDER BY `id` DESC LIMIT 100";
    $query = mysqli_query($Connect, $select);
    $assoc = mysqli_fetch_assoc($query);
	
	$assoc['from'] = $classUser->So_UserData($assoc['user_id']);
	$assoc['to'] = $classUser->So_UserData($assoc['to_id']);
	$assoc['post'] = $classPost->So_PostData($assoc['post_id']);
	
	if ($assoc['type'] == 'shared_your_post') {
		$select_one = mysqli_query($Connect, "SELECT `share_id` FROM " . T_POSTS . " WHERE `post_id` = '{$assoc['post_id']}'");
		$assoc_one = mysqli_fetch_assoc($select_one);
		$assoc['post'] = $classPost->So_PostData($assoc_one['share_id']);
	}
	
	$assoc['comment'] = $classComment->So_CommentData($assoc['comment_id']);
	$data = $assoc;
	
    return $data;
}

function So_CountNotifications() {
    global $so, $Connect;

    $select = "SELECT * FROM " . T_NOTIFICATIONS . " WHERE `to_id` = '{$so['user']['user_id']}' AND `seen` = '0' LIMIT 100";
    $query = mysqli_query($Connect, $select);
    return mysqli_num_rows($query);
}
function So_CountMessages() {
    global $so, $Connect;

    $select = "SELECT * FROM " . T_MESSAGES . " WHERE `to_id` = '{$so['user']['user_id']}' AND `seen` = '0'";
    $query = mysqli_query($Connect, $select);
    return mysqli_num_rows($query);
}

function So_DeleteFolderUser($dir) {
    $files = @array_diff(@scandir($dir), array('.', '..'));
    if (is_array($files)) {
        foreach ($files as $file) {
            (@is_dir("$dir/$file")) ? So_DeleteFolderUser("$dir/$file") : @unlink("$dir/$file");
        }
    }
    return @rmdir($dir);
}
