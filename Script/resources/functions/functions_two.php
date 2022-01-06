<?php

require_once('boot.php');

function So_TotalUsers() {
    global $so, $Connect;
    if ($so['loggedin'] === false || $so['user']['admin'] < 1) {
        return false;
    }

    $select = "SELECT `user_id` FROM " . T_USERS;
    $query = mysqli_query($Connect, $select);

    return mysqli_num_rows($query);
}

function So_TotalPosts() {
    global $so, $Connect;
    if ($so['loggedin'] === false || $so['user']['admin'] < 1) {
        return false;
    }

    $select = "SELECT `post_id` FROM " . T_POSTS;
    $query = mysqli_query($Connect, $select);

    return mysqli_num_rows($query);
}

function So_GetUsers() {
    global $so, $Connect;
    if ($so['loggedin'] === false || $so['user']['admin'] < 1) {
        return false;
    }

    $data = array();

    $select = "SELECT `user_id` FROM " . T_USERS . " ORDER BY `user_id` LIMIT 100";
    $query = mysqli_query($Connect, $select);
    $classUser = new User();
    while ($assoc = mysqli_fetch_assoc($query)) {
        $data[] = $classUser->So_UserData($assoc['user_id']);
    }

    return $data;
}

function So_GetConfigAdmin() {
    global $so, $Connect;
    if ($so['loggedin'] === false || $so['user']['admin'] < 1) {
        return false;
    }

    $data = array();

    $select = "SELECT * FROM " . T_CONFIG;
    $query = mysqli_query($Connect, $select);
    while ($assoc = mysqli_fetch_assoc($query)) {
        $data[] = $assoc;
    }

    return $data;
}

function So_SaveConfig($name, $value) {
    global $so, $Connect;
    if ($so['loggedin'] === false || $so['user']['admin'] < 1) {
        return false;
    }

    $name = So_Secure($name);
    $value = So_Secure($value);

    if ($name == 'message_alert') {
        $value = html_entity_decode($value);
    }

    $update = mysqli_query($Connect, "UPDATE " . T_CONFIG . " SET `value` = '{$value}' WHERE `name` = '{$name}'");
    if ($update) {
        return true;
    } else {
        return false;
    }
}

function So_DecodeBr2nl($st) {
    $breaks = array(
        "\r\n",
        "\r",
        "\n"
    );
    $st = str_replace($breaks, "", $st);
    $st_no_lb = preg_replace("/\r|\n/", "", $st);
    return preg_replace('/<br(\s+)?\/?>/i', "\r", $st_no_lb);
}

function So_GeoData() {
    $user_ip = getenv('REMOTE_ADDR');
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));

    $data = array(
        'country' => $geo["geoplugin_countryName"],
        'timezone' => $geo["geoplugin_timezone"]
    );

    return $data;
}

function So_CheckReaction($post_id = 0) {
    global $so, $Connect;

    if (empty($post_id) || !is_numeric($post_id)) {
        return false;
    }

    $post_id = So_Secure($post_id);
    $from_id = So_Secure($so['user']['user_id']);

    $select = "SELECT `id` FROM " . T_REACTIONS . " WHERE `from_id` = '{$from_id}' AND `post_id` = '{$post_id}'";
    $query = mysqli_query($Connect, $select);

    if (mysqli_num_rows($query) > 0) {
        return true;
    } else {
        return false;
    }
}

function So_ButtonReaction($post_id = 0) {
    global $so, $Connect;

    if (empty($post_id) || !is_numeric($post_id)) {
        return false;
    }

    $post_id = So_Secure($post_id);
    $from_id = So_Secure($so['user']['user_id']);

    if (So_CheckReaction($post_id) === false) {
        return 'reaction-default';
    }

    $select = "SELECT `type` FROM " . T_REACTIONS . " WHERE `from_id` = '{$from_id}' AND `post_id` = '{$post_id}'";
    $query = mysqli_query($Connect, $select);
    $assoc = mysqli_fetch_assoc($query);

    return $assoc['type'];
}

function So_DeleteReaction($post_id = 0) {
    global $so, $Connect;

    if ($so['loggedin'] == false || empty($post_id) || !is_numeric($post_id)) {
        return false;
    }

    $post_id = So_Secure($post_id);
    $from_id = So_Secure($so['user']['user_id']);

    $delete = "DELETE FROM " . T_REACTIONS . " WHERE `from_id` = '{$from_id}' AND `post_id` = '{$post_id}'";
    $query = mysqli_query($Connect, $delete);

    if ($query) {
        return true;
    } else {
        return false;
    }
}

function So_UpdateReaction($registration_data) {
    global $so, $Connect;

    if ($so['loggedin'] == false) {
        return false;
    }

    $update = "UPDATE " . T_REACTIONS . " SET `type` = '{$registration_data['type']}' WHERE `from_id` = '{$registration_data['from_id']}'";
    $update .= " AND `post_id` = '{$registration_data['post_id']}'";
    $query = mysqli_query($Connect, $update);

    if ($query) {
        return true;
    } else {
        return false;
    }
}

function So_RegisterReaction($registration_data) {
    global $so, $Connect;

    if ($so['loggedin'] == false) {
        return false;
    }

    $reactions = array(
        'reaction_like' => 'reaction_like',
        'reaction_love' => 'reaction_love',
        'reaction_haha' => 'reaction_haha',
        'reaction_wow' => 'reaction_wow',
        'reaction_cry' => 'reaction_cry',
        'reaction_grr' => 'reaction_grr'
    );

    if (!in_array($registration_data['type'], $reactions)) {
        return false;
    }

    if (So_CheckReaction($registration_data['post_id']) === true) {
        $select = mysqli_query($Connect, "SELECT `type` FROM " . T_REACTIONS . " WHERE `from_id` = '{$registration_data['from_id']}' AND `post_id` = '{$registration_data['post_id']}'");
        $assoc = mysqli_fetch_assoc($select);

        if ($assoc['type'] == $registration_data['type']) {
            if (So_DeleteReaction($registration_data['post_id']) === true) {
                return true;
            } else {
                return false;
            }
        } else {
            if (So_UpdateReaction($registration_data) === true) {
                return true;
            } else {
                return false;
            }
        }
    }

    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data = '\'' . implode('\', \'', $registration_data) . '\'';
    $query = mysqli_query($Connect, "INSERT INTO " . T_REACTIONS . " ({$fields}) VALUES ({$data})");
    if ($query) {
        if ($registration_data['to_id'] <> $so['user']['user_id']) {
            $notification = array(
                'post_id' => $registration_data['post_id'],
                'user_id' => $so['user']['user_id'],
                'to_id' => $registration_data['to_id'],
                'type' => 'reacted_on_your_post',
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

function So_CheckTypeReaction($post_id = 0, $type = 'reaction_like') {
    global $so, $Connect;

    if (empty($post_id) || !is_numeric($post_id)) {
        return false;
    }

    $post_id = So_Secure($post_id);
    $type = So_Secure($type);

    $select = "SELECT `id` FROM " . T_REACTIONS . " WHERE `post_id` = '{$post_id}' AND `type` = '{$type}' ORDER BY `id` DESC LIMIT 1";
    $query = mysqli_query($Connect, $select);
    if (mysqli_num_rows($query) > 0) {
        return true;
    } else {
        return false;
    }
}

function So_TotalReactions($post_id = 0) {
    global $so, $Connect;

    if (empty($post_id) || !is_numeric($post_id)) {
        return false;
    }

    $post_id = So_Secure($post_id);

    $select = "SELECT `id` FROM " . T_REACTIONS . " WHERE `post_id` = '{$post_id}' ORDER BY `id` DESC";
    $query = mysqli_query($Connect, $select);

    $total = mysqli_num_rows($query);

    if ($total > 0 && $total < 10) {
        $total = 0 . $total;
    }

    return $total;
}

function So_Birthday($limit = 1) {
	global $so, $Connect;
	
	if ($so['loggedin'] == false) {
		return false;
	}
	
	$data = array();
	
	$select  = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` <> '{$so['user']['user_id']}'";
	$select .= " AND `user_id` IN (SELECT `to_id` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$so['user']['user_id']}' AND `privacy` <> '4')";
	$select .= " AND `day` = '" . date("d") . "' AND `month` = '" . date("m") . "' ORDER BY RAND() LIMIT {$limit}";
	$query   = mysqli_query($Connect, $select);
	if (mysqli_num_rows($query) > 0) {
		$assoc = mysqli_fetch_assoc($query);
		
		$classUser = new User();
		$data = $classUser->So_UserData($assoc['user_id']);
		
		return $data;
		
	} else {
		return false;
	}
}

function So_LastChatId() {
	global $so, $Connect;
	
	if ($so['loggedin'] == false) {
		return false;
	}
	
	$select = "SELECT `from_id` FROM " . T_OPEN_CHATS . " WHERE `to_id` = '{$so['user']['user_id']}' ORDER BY `time` DESC LIMIT 1";
	$query  = mysqli_query($Connect, $select);
	$assoc  = mysqli_fetch_assoc($query);
	
	return $assoc['from_id'];
}

function So_LastPhotos($user_id = 0) {
	global $so, $Connect;
	
	if (empty($user_id)) {
		return false;
	}
	
	$user_id = So_Secure($user_id);
	
	$data = array();
	
	$select = "SELECT `post_id` FROM " . T_POSTS . " WHERE `user_id` = '{$user_id}' AND `postImage` <> '' ORDER BY `post_id` DESC LIMIT 9";
	$query  = mysqli_query($Connect, $select);
	$classPost = new Post();
	while ($assoc = mysqli_fetch_assoc($query)) {
		$data[] = $classPost->So_PostData($assoc['post_id']);
	}
	
	return $data;
}

?>