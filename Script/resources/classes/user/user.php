<?php


class User {

    function So_UserData($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $data = array();
        $user_id = So_Secure($user_id);

        $select = "SELECT * FROM " . T_USERS . " WHERE `user_id` = '{$user_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        $assoc['name'] = $assoc['first_name'] . ' ' . $assoc['last_name'];
        if ($assoc['amazon_avatar'] == 0) {
            $assoc['avatar'] = $so['config']['site_url'] . '/' . $assoc['avatar'];
        } else {
            $assoc['avatar'] = $so['config']['amazon_url'] . '/' . $assoc['avatar'];
        }
        if ($assoc['amazon_cover'] == 0) {
            $assoc['cover'] = $so['config']['site_url'] . '/' . $assoc['cover'];
        } else {
            $assoc['cover'] = $so['config']['amazon_url'] . '/' . $assoc['cover'];
        }

        $total = 0;

        if ($assoc['avatar'] == $so['config']['site_url'] . '/upload/images/d-avatar.png') {
            $total = $total + 1;
        }
        if ($assoc['cover'] == $so['config']['site_url'] . '/') {
            $total = $total + 1;
			$assoc['cover'] = $so['config']['site_url'] . '/upload/images/cover.jpg';
        }
        if (empty($assoc['about'])) {
            $total = $total + 1;
        }
        if (empty($assoc['day']) && empty($assoc['month']) && empty($assoc['year'])) {
            $total = $total + 1;
        }
        if (empty($assoc['location'])) {
            $total = $total + 1;
        }

        $total = $total * 20; //4 completado = 80
        $porcent = 100 - $total; // 80
        $assoc['porcent'] = $porcent;

        $assoc['url'] = So_SeoLink('index.php?link1=timeline&username=' . $assoc['username']);
        $assoc['about'] = htmlspecialchars_decode($assoc['about']);
        $data = $assoc;

        return $data;
    }

    function So_CheckExistUserEmail($email = '') {
        global $so, $Connect;

        if (empty($email)) {
            return false;
        }

        $email = So_Secure($email);

        $select = mysqli_query($Connect, "SELECT `user_id` FROM " . T_USERS . " WHERE `email` = '{$email}'");
        if (mysqli_num_rows($select) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_CheckExistUsername($username = '') {
        global $so, $Connect;

        if (empty($username)) {
            return false;
        }

        $username = So_Secure($username);

        $select = mysqli_query($Connect, "SELECT `user_id` FROM " . T_USERS . " WHERE `username` = '{$username}'");
        if (mysqli_num_rows($select) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_CheckExistPhoneNumber($phone_number = '') {
        global $so, $Connect;

        if (empty($phone_number)) {
            return false;
        }

        $phone_number = So_Secure($phone_number);

        $select = mysqli_query($Connect, "SELECT `user_id` FROM " . T_USERS . " WHERE `phone_number` = '{$phone_number}'");
        if (mysqli_num_rows($select) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_CheckExistUserToken($token = '') {
        global $so, $Connect;

        if (empty($token)) {
            return false;
        }

        $token = So_Secure($token);

        $select = mysqli_query($Connect, "SELECT `user_id` FROM " . T_USERS . " WHERE `token_active` = '{$token}'");
        if (mysqli_num_rows($select) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_RegisterUser($registration_data) {
        global $so, $Connect;
        if (empty($registration_data) || $so['loggedin'] == true) {
            return false;
        }

        $ip = '0.0.0.0';
        $get_ip = So_GetIpAddress();
        if (!empty($get_ip)) {
            $ip = $get_ip;
        }
        $registration_data['lastseen'] = time();
        $registration_data['password'] = sha1($registration_data['password']);
        $registration_data['ip_address'] = So_Secure($ip);
        $registration_data['language'] = $_SESSION['lang'];
        $registration_data['token_active'] = sha1(md5(time()));
        $registration_data['active'] = 1;

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_USERS . " ({$fields}) VALUES ({$data})");
        $user_id = mysqli_insert_id($Connect);
        if ($query) {
            $select = mysqli_query($Connect, "SELECT `user_id` FROM " . T_USERS . " WHERE `admin` = '1'");
            if (mysqli_num_rows($select) < 1) {
                mysqli_query($Connect, "UPDATE " . T_USERS . " SET `admin` = '1' WHERE `user_id` = '{$user_id}'");
            }
            while ($assoc = mysqli_fetch_assoc($select)) {
                $this->So_RegisterFollowAdminRegister($user_id, $assoc['user_id']);
            }
            return $user_id;
        } else {
            return false;
        }
    }

    function So_LoginUser($email = '', $password = '') {
        global $so, $Connect;
        if (empty($email) || empty($password) || $so['loggedin'] == true) {
            return false;
        }

        $email = So_Secure($email);
        $password = So_Secure($password);
        $password = sha1(md5($password));

        $select = "SELECT `user_id` FROM " . T_USERS . " WHERE `username` = '{$email}' AND `password` = '{$password}'";
        $select .= " OR `email` = '{$email}' AND `password` = '{$password}'";
        $select .= " OR `phone_number` = '{$email}' AND `password` = '{$password}'";
        $query = mysqli_query($Connect, $select);

        if (mysqli_num_rows($query) > 0) {
            $assoc = mysqli_fetch_assoc($query);
            mysqli_query($Connect, "UPDATE " . T_USERS . " SET `ip_address` = '" . So_GetIpAddress() . "', `lastseen` = '" . time() . "' WHERE `user_id` = '{$assoc['user_id']}'");

            return $assoc['user_id'];
        } else {
            return false;
        }
    }

    function So_PasswordReset($email = '') {
        global $so, $Connect;

        $email = So_Secure($email);

        $select = "SELECT `first_name`, `last_name` FROM " . T_USERS . " WHERE `email` = '{$email}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        $token = sha1(md5(time()));

        $data_r = array(
            'body' => $so['lang']['for_password_reset'] . ' <a href="' . $so['config']['site_url'] . '/password-recover?token=' . $token . '">' . $so['config']['site_url'] . '/password-recover?token=' . $token . '</a>',
            'email' => $email,
            'name' => $assoc['first_name'] . ' ' . $assoc['last_name'],
            'subject' => $so['lang']['password_recover']
        );

        if (So_SendEmail($data_r)) {
            mysqli_query($Connect, "UPDATE " . T_USERS . " SET `token_active` = '{$token}' WHERE `email` = '{$email}'");
            return true;
        } else {
            return false;
        }
    }

    function So_SetPasswordToken($password = '', $token = '') {
        global $so, $Connect;

        if (empty($password) || empty($token)) {
            return false;
        }

        $password = sha1(md5(So_Secure($password)));
        $token = So_Secure($token);

        $query = mysqli_query($Connect, "UPDATE " . T_USERS . " SET `password` = '{$password}', `token_active` = '' WHERE `token_active` = '{$token}'");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function So_SetPassword($password = '', $user_id = 0) {
        global $so, $Connect;

        if (empty($password) || empty($user_id)) {
            return false;
        }

        $password = sha1(md5(So_Secure($password)));
        $user_id = So_Secure($user_id);

        $query = mysqli_query($Connect, "UPDATE " . T_USERS . " SET `password` = '{$password}' WHERE `user_id` = '{$user_id}'");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function So_UserIdFromUsername($username = '') {
        global $so, $Connect;

        if (empty($username)) {
            return false;
        }

        $username = So_Secure($username);

        $select = "SELECT `user_id` FROM " . T_USERS . " WHERE `username` = '{$username}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        if (mysqli_num_rows($query) > 0) {
            return $assoc['user_id'];
        } else {
            return false;
        }
    }

    function So_CheckFollowing($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        $select = "SELECT * FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$so['user']['user_id']}' AND `to_id` = '{$user_id}' AND `active` = '1'";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_RegisterFollowAdminRegister($user_id = 0, $admin = 0) {
        global $so, $Connect;

        if ($so['loggedin'] == true || empty($user_id) || empty($admin)) {
            return false;
        }

        $user_id = So_Secure($user_id);
        $admin = So_Secure($admin);

        if ($admin == $user_id || count($this->So_UserData($user_id)) < 1 || count($this->So_UserData($admin)) < 1) {
            return false;
        }

        if ($this->So_CheckFollowing($admin) === true) {
            $delete = mysqli_query($Connect, "DELETE FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$user_id}' AND  `to_id` = '{$admin}'");
            if ($delete) {
                return true;
            } else {
                return false;
            }
        }

        $registration_data = array(
            'from_id' => $user_id,
            'to_id' => $admin,
            'active' => '1',
            'privacy' => $this->So_UserData($admin)['privacy']
        );

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_FOLLOWERS . " ({$fields}) VALUES ({$data})");
        if ($query) {

            $notification = array(
                'user_id' => $user_id,
                'to_id' => $admin,
                'type' => 'followed_you',
                'time' => time(),
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y')
            );
            $fields_notification = '`' . implode('`,`', array_keys($notification)) . '`';
            $data_notification = '\'' . implode('\', \'', $notification) . '\'';
            mysqli_query($Connect, "INSERT INTO " . T_NOTIFICATIONS . " ({$fields_notification}) VALUES ({$data_notification})");

            return true;
        } else {
            return false;
        }
    }

    function So_RegisterFollow($user_id = 0) {
        global $so, $Connect;

        if ($so['loggedin'] == false || empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        if ($user_id == $so['user']['user_id'] || count($this->So_UserData($user_id)) < 1) {
            return false;
        }

        if ($this->So_CheckFollowing($user_id) === true) {
            $delete = mysqli_query($Connect, "DELETE FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$so['user']['user_id']}' AND  `to_id` = '{$user_id}'");
            if ($delete) {
                return true;
            } else {
                return false;
            }
        }

        $registration_data = array(
            'from_id' => $so['user']['user_id'],
            'to_id' => $user_id,
            'active' => '1',
            'privacy' => $this->So_UserData($user_id)['privacy']
        );

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_FOLLOWERS . " ({$fields}) VALUES ({$data})");
        if ($query) {

            $notification = array(
                'user_id' => $so['user']['user_id'],
                'to_id' => $user_id,
                'type' => 'followed_you',
                'time' => time(),
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y')
            );
            So_RegisterNotification($notification);

            return true;
        } else {
            return false;
        }
    }

    function So_CountFollowersUser($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        $select = "SELECT COUNT(`id`) AS `total` FROM " . T_FOLLOWERS . " WHERE `to_id` = '{$user_id}' AND `active` = '1'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        return $assoc['total'];
    }

    function So_CountFollowingUser($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        $select = "SELECT COUNT(`id`) AS `total` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$user_id}' AND `active` = '1'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        return $assoc['total'];
    }

    function So_UpdateUserRest($user_id, $update_data) {
        global $so, $Connect;

        if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
        }
        if (empty($update_data)) {
            return false;
        }
        $user_id = So_Secure($user_id);

        $update = array();
        foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . So_Secure($data, 0) . '\'';
        }
        $impload = implode(', ', $update);
        $query_one = " UPDATE " . T_USERS . " SET {$impload} WHERE `user_id` = '{$user_id}' ";
        $query = mysqli_query($Connect, $query_one);
        if ($query) {

            if (isset($update_data['privacy']) && !empty($update_data['privacy'])) {
                mysqli_query($Connect, "UPDATE " . T_FOLLOWERS . " SET `privacy` = '{$update_data['privacy']}' WHERE `to_id` = '{$user_id}' AND `privacy` <> '{$update_data['privacy']}'");
            }

            return true;
        } else {
            return false;
        }
    }

    function So_UpdateUser($user_id, $update_data) {
        global $so, $Connect;
        if ($so['loggedin'] == false) {
            return false;
        }
        if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
        }
        if (empty($update_data)) {
            return false;
        }
        $user_id = So_Secure($user_id);

        if ($so['user']['user_id'] <> $user_id) {
            if ($so['user']['admin'] < 1) {
                return false;
            }
        }

        $update = array();
        foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . So_Secure($data, 0) . '\'';
        }
        $impload = implode(', ', $update);
        $query_one = " UPDATE " . T_USERS . " SET {$impload} WHERE `user_id` = '{$user_id}' ";
        $query = mysqli_query($Connect, $query_one);
        if ($query) {

            if (isset($update_data['privacy']) && !empty($update_data['privacy'])) {
                mysqli_query($Connect, "UPDATE " . T_FOLLOWERS . " SET `privacy` = '{$update_data['privacy']}' WHERE `to_id` = '{$user_id}' AND `privacy` <> '{$update_data['privacy']}'");
            }

            return true;
        } else {
            return false;
        }
    }

    function So_DeleteUser($user_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        if ($user_id <> $so['user']['user_id']) {
            if ($so['user']['admin'] < 1) {
                return false;
            }
        }

        if ($this->So_UserData($user_id)['admin'] > 0) {
            return false;
        }

        if ($so['config']['amazon_s3'] == 1) {
            $classUpload = new Upload();
            $delete_amazon = $classUpload->So_DeleteAmazon($this->So_UserData($user_id)['avatar']);
            $delete_amazon = $classUpload->So_DeleteAmazon($this->So_UserData($user_id)['cover']);
        }

        $delete = mysqli_query($Connect, "DELETE FROM " . T_USERS . " WHERE `user_id` = '{$user_id}'");

        $select_posts = mysqli_query($Connect, "SELECT `post_id` FROM " . T_POSTS . " WHERE `user_id` = '{$user_id}'");
        while ($assoc_posts = mysqli_fetch_assoc($select_posts)) {
            $delete .= mysqli_query($Connect, "DELETE FROM " . T_POSTS . " WHERE `share_id` = '{$assoc_posts['post_id']}'");
        }

        $delete .= mysqli_query($Connect, "DELETE FROM " . T_POSTS . " WHERE `user_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_COMMENTS . " WHERE `user_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_NOTIFICATIONS . " WHERE `user_id` = '{$user_id}' || `to_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_POSTS . " WHERE `user_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_COMMENTS_LIKE . " WHERE `user_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$user_id}' || `to_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_MESSAGES . " WHERE `from_id` = '{$user_id}' || `to_id` = '{$user_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_OPEN_CHATS . " WHERE `from_id` = '{$user_id}' || `to_id` = '{$user_id}'");
        if ($delete) {
            So_DeleteFolderUser('upload/' . $user_id);
            return true;
        } else {
            return false;
        }
    }

    function So_GetFollowing($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        $data = array();

        $select = "SELECT `to_id` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$user_id}' AND `active` = '1' ORDER BY `id` DESC LIMIT 100";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_UserData($assoc['to_id']);
        }

        return $data;
    }

    function So_GetFollowers($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        $data = array();

        $select = "SELECT `from_id` FROM " . T_FOLLOWERS . " WHERE `to_id` = '{$user_id}' AND `active` = '1' ORDER BY `id` DESC LIMIT 100";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_UserData($assoc['from_id']);
        }

        return $data;
    }

}
