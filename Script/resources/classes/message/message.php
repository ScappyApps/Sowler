<?php



class Message {

    function So_GetFriends($limit = 50) {
        global $so, $Connect;

        $data = array();

        $user_id = So_Secure($so['user']['user_id']);
        $classUser = new User();

        $select = "SELECT `to_id` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$user_id}'";
        $select .= " AND `to_id` NOT IN (SELECT `user_id` FROM " . T_USERS . " WHERE `privacy` = '4')";
        $select .= " ORDER BY `id` DESC LIMIT {$limit}";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            while ($assoc = mysqli_fetch_assoc($query)) {
                $user = $classUser->So_UserData($assoc['to_id']);
                if (!empty($user)) {
                    $data[] = $user;
                }
            }
        }

        return $data;
    }
    
    function So_GetMessagesOpen($limit = 50) {
        global $so, $Connect;

        $data = array();

        $user_id = So_Secure($so['user']['user_id']);
        $classUser = new User();

        $select = "SELECT `to_id` FROM " . T_OPEN_CHATS . " WHERE (`from_id` = '{$user_id}'";
        $select .= " AND `to_id` NOT IN (SELECT `user_id` FROM " . T_USERS . " WHERE `privacy` = '4'))";
        $select .= " OR (`to_id` = '{$user_id}'";
        $select .= " AND `from_id` NOT IN (SELECT `user_id` FROM " . T_USERS . " WHERE `privacy` = '4'))";
        $select .= " ORDER BY `time` DESC LIMIT {$limit}";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            while ($assoc = mysqli_fetch_assoc($query)) {
                $user = $classUser->So_UserData($assoc['to_id']);
                if (!empty($user)) {
                    $data[] = $user;
                }
            }
        }

        return $data;
    }

    function So_MessageData($message_id = 0, $last_m = false) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($message_id) || !is_numeric($message_id)) {
            return false;
        }

        $message_id = So_Secure($message_id);

        $data = array();
        $classUser = new User();
        $classEmoticon = new Emoticon();

        $select = " SELECT * FROM " . T_MESSAGES . " WHERE `id` = '{$message_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);
        $assoc['from'] = $classUser->So_UserData($assoc['from_id']);
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
        if (!empty($assoc['postSticker'])) {
            $assoc['postSticker'] = $so['config']['site_url'] . '/' . $assoc['postSticker'];
        }

        if (!empty($assoc['postText'])) {
            $assoc['postText'] = So_Tags($assoc['postText']);
			if ($last_m == false) {
				$assoc['postText'] = $classEmoticon->So_Emoticon($assoc['postText']);
			}
        }

        $data = $assoc;

        return $data;
    }

    function So_GetMessages($data = array(), $limit = 50) {
        global $so, $Connect;
        if ($so['loggedin'] == false) {
            return false;
        }
        $message_data = array();
        $user_id = So_Secure($data['user_id']);
        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }
        $query_one = " SELECT * FROM " . T_MESSAGES;
        if (isset($data['new']) && $data['new'] == true) {
            $query_one .= " WHERE `seen` = 0 AND `from_id` = {$user_id} AND `to_id` = {$so['user']['user_id']} AND `deleted_two` = '0'";
        } else {
            $query_one .= " WHERE ((`from_id` = {$user_id} AND `to_id` = {$so['user']['user_id']} AND `deleted_two` = '0') OR (`from_id` = {$so['user']['user_id']} AND `to_id` = {$user_id} AND `deleted_one` = '0'))";
        }
        if (!empty($data['message_id'])) {
            $data['message_id'] = So_Secure($data['message_id']);
            $query_one .= " AND `id` = " . $data['message_id'];
        } else if (!empty($data['before_message_id']) && is_numeric($data['before_message_id']) && $data['before_message_id'] > 0) {
            $data['before_message_id'] = So_Secure($data['before_message_id']);
            $query_one .= " AND `id` < " . $data['before_message_id'] . " AND `id` <> " . $data['before_message_id'];
        } else if (!empty($data['after_message_id']) && is_numeric($data['after_message_id']) && $data['after_message_id'] > 0) {
            $data['after_message_id'] = So_Secure($data['after_message_id']);
            $query_one .= " AND `id` > " . $data['after_message_id'] . " AND `id` <> " . $data['after_message_id'];
        }
        $sql_query_one = mysqli_query($Connect, $query_one);
        $query_limit_from = mysqli_num_rows($sql_query_one) - 50;
        if ($query_limit_from < 1) {
            $query_limit_from = 0;
        }
        if (isset($limit)) {
            $query_one .= " ORDER BY `id` ASC LIMIT {$query_limit_from}, 50";
        }

        $query = mysqli_query($Connect, $query_one);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $assoc = $this->So_MessageData($assoc['id']);
            $message_data[] = $assoc;
            if ($assoc['from']['user_id'] == $user_id && $assoc['seen'] == 0) {
                mysqli_query($Connect, " UPDATE " . T_MESSAGES . " SET `seen` = " . time() . " WHERE `id` = " . $assoc['id']);
            }
        }
        return $message_data;
    }

    function So_RegisterMessage($registration_data, $emoticon = '') {
        global $so, $Connect;
        if (empty($registration_data) || $so['loggedin'] == false) {
            return false;
        }

        $classUser = new User();

        if (empty($registration_data['postSticker']) && empty($registration_data['postText']) && empty($registration_data['postFile']) && empty($registration_data['postImage']) && empty($registration_data['postVideo']) && empty($registration_data['postGif'])) {
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
            if (!isset($registration_data['share_id'])) {
                $registration_data['postYoutube'] = So_Secure($match[1]);
            }
            $registration_data['postText'] = preg_replace('/((?:https?:\/\/)?www\.youtube\.com\/watch\?v=\w+\-\w+)/', "", $registration_data['postText']);
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

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_MESSAGES . " ({$fields}) VALUES ({$data})");
        $message_id = mysqli_insert_id($Connect);

        if ($query) {
            $this->So_UpdateSeenMessages($registration_data['to_id']);
            $this->So_RegisterOpenChat($registration_data['to_id'], $registration_data['from_id']);
            $classEmoticon = new Emoticon();
            if (!empty($emoticon)) {
                $list_emo = explode(',', $emoticon);
                foreach ($list_emo as $so['emoticon'] => $value) {
                    $classEmoticon->So_RegisterEmoticon($value);
                }
            }
            return $message_id;
        } else {
            return false;
        }
    }

    function So_RegisterOpenChat($user_id = 0, $from_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false) {
            return false;
        }
        if (empty($user_id) || empty($from_id)) {
            return false;
        }
        $from_id = So_Secure($from_id);
        $user_id = So_Secure($user_id);
        $time = time();
        $query = mysqli_query($Connect, "SELECT COUNT(`id`) as count FROM " . T_OPEN_CHATS . " WHERE `to_id` = '$user_id' AND `from_id` = '{$from_id}'");
        $query_count = mysqli_fetch_assoc($query);
        if ($query_count['count'] > 0) {
            $query_two = mysqli_query($Connect, "UPDATE " . T_OPEN_CHATS . " SET `time` = '$time' WHERE `to_id` = '$user_id' AND `from_id` = '{$from_id}'");
            $query_two = mysqli_query($Connect, "UPDATE " . T_OPEN_CHATS . " SET `time` = '$time' WHERE `to_id` = '{$from_id}' AND `from_id` = '$user_id'");
            $query_three = mysqli_query($Connect, "SELECT COUNT(`id`) as count FROM " . T_OPEN_CHATS . " WHERE `from_id` = '$user_id' AND `to_id` = '{$from_id}'");
            $query_three_fetch = mysqli_fetch_assoc($query_three);
            if ($query_three_fetch['count'] == 0) {
                $query_four = mysqli_query($Connect, "INSERT INTO " . T_OPEN_CHATS . " (`from_id`, `to_id`, `time`) VALUES ('$user_id', '{$from_id}', '$time')");
            }
            if ($query_two) {
                return true;
            }
        } else {
            $query_two = mysqli_query($Connect, "INSERT INTO " . T_OPEN_CHATS . " (`from_id`, `to_id`, `time`) VALUES ('{$from_id}', '$user_id', '$time')");
            if ($query_two) {
                $query_three = mysqli_query($Connect, "SELECT COUNT(`id`) as count FROM " . T_OPEN_CHATS . " WHERE `to_id` = '{$from_id}' AND `from_id` = '$user_id'");
                $query_three_fetch = mysqli_fetch_assoc($query_three);
                if ($query_three_fetch['count'] == 0) {
                    $query_four = mysqli_query($Connect, "INSERT INTO " . T_OPEN_CHATS . " (`from_id`, `to_id`, `time`) VALUES ('$user_id', '{$from_id}', '$time')");
                }
                return true;
            }
        }
    }

    function So_UpdateSeenMessages($user_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false) {
            return false;
        }

        $user_id = So_Secure($user_id);
        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $update = mysqli_query($Connect, "UPDATE " . T_MESSAGES . " SET `seen` = '" . time() . "' WHERE `from_id` = '{$user_id}' AND `to_id` = '{$so['user']['user_id']}' AND `seen` = '0'");
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    function So_GetLastMessage($user_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false) {
            return false;
        }

        $user_id = So_Secure($user_id);
        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $data = array();

        $select = "SELECT `id` FROM " . T_MESSAGES . " WHERE `from_id` = '{$user_id}' AND `to_id` = '{$so['user']['user_id']}'";
        $select .= " OR `from_id` = '{$so['user']['user_id']}' AND `to_id` = '{$user_id}'";
        $select .= " ORDER BY `id` DESC LIMIT 1";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_MessageData($assoc['id'], true);
        }

        return $data;
    }

    function So_CheckMessagesNotSaw($user_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);
        $to_id = So_Secure($so['user']['user_id']);

        $select = "SELECT COUNT(`id`) AS `total` FROM " . T_MESSAGES . " WHERE `from_id` = '{$user_id}'";
        $select .= " AND `to_id` = '{$to_id}' AND `seen` = '0'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        return $assoc['total'];
    }

    function So_CheckLastSeenMessage($user_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);
        $to_id = So_Secure($so['user']['user_id']);

        $select = "SELECT * FROM " . T_MESSAGES . " WHERE (`from_id` = '{$user_id}' AND `to_id` = '{$to_id}' || `from_id` = '{$to_id}' AND `to_id` = '{$user_id}') ORDER BY `id` DESC LIMIT 1";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            $assoc = mysqli_fetch_assoc($query);
            if ($assoc['from_id'] == $to_id && $assoc['seen'] != 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
