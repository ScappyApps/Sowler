<?php



if (isset($_POST)) {
    $classUser = new User();
    $Userdata = $classUser->So_UserData(So_Secure($_POST['user_id']));
    $age_data = '0000-00-00';
    if (!empty($Userdata['user_id']) && $so['user']['admin'] > 0 && $_POST['admin'] >= 0 && $_POST['admin'] < 3) {
        if ($_POST['email'] != $Userdata['email']) {
            if ($classUser->So_CheckExistUserEmail($_POST['email'])) {
                $errors[] = $so['lang']['email_exist'];
            }
        }
        if ($_POST['username'] != $Userdata['username']) {
            if ($classUser->So_CheckExistUsername($_POST['username'])) {
                $errors[] = $so['lang']['username_exist'];
            }
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = $so['lang']['email_invalid_characters'];
        }
        if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 32) {
            $errors[] = $so['lang']['username_characters_length'];
        }
        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
            $errors[] = $so['lang']['username_invalid_characters'];
        }
        if (!empty($_POST['year']) || !empty($_POST['day']) || !empty($_POST['month'])) {
            $day = So_Secure($_POST['day']);
            $month = So_Secure($_POST['month']);
            $year = So_Secure($_POST['year']);
            $privacy = So_Secure($_POST['privacy']);

            if ($day < 1 || $day > 31) {
                $errors[] = $so['lang']['birthday_invalid'];
            }

            if ($month < 1 || $month > 12) {
                $errors[] = $so['lang']['birthday_invalid'];
            }

            if ($month == 2 && $day > 29) {
                $errors[] = $so['lang']['birthday_invalid'];
            }

            if ($privacy < 1 || $privacy > 4) {
                $errors[] = $so['lang']['birthday_invalid'];
            }
        }
        
        $gender = 'male';
        $gender_array = array(
            'male',
            'female'
        );
        if (!empty($_POST['gender'])) {
            if (in_array($_POST['gender'], $gender_array)) {
                $gender = $_POST['gender'];
            }
        }
        if (empty($errors)) {
            $Update_data = array(
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'about' => $_POST['about'],
                'language' => $_POST['language'],
                'gender' => $_POST['gender'],
                'day' => $_POST['day'],
                'month' => $_POST['month'],
                'year' => $_POST['year'],
                'location' => $_POST['location'],
				'auto_message' => $_POST['auto_message'],
                'privacy' => $_POST['privacy'],
                'admin' => $_POST['admin'],
                'verified' => $_POST['verified']
            );
            if ($classUser->So_UpdateUser(So_Secure($_POST['user_id']), $Update_data)) {
                $data = array(
                    'status' => 200,
                    'message' => $so['lang']['setting_updated']
                );
            }
        }
    }
}
header("Content-type: application/json");
if (isset($errors)) {
    echo json_encode(array(
        'errors' => $errors
    ));
} else {
    echo json_encode($data);
}
exit();
