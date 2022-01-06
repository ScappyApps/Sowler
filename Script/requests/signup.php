<?php


$classUser = new User();
if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['password'])) {

    $errors = '';

    if ($classUser->So_CheckExistUserEmail($_POST['email']) === true) {
        $errors .= $so['lang']['email_exist'] . '<br/>';
    }
    if ($classUser->So_CheckExistUsername($_POST['username']) === true) {
        $errors .= $so['lang']['username_exist'] . '<br/>';
    }
    if (empty($errors)) {
        $data_r = array(
            'username' => So_Secure($_POST['username']),
            'email' => So_Secure($_POST['email']),
            'password' => md5(So_Secure($_POST['password'])),
            'first_name' => So_Secure($_POST['first_name']),
            'last_name' => So_Secure($_POST['last_name']),
            'registered_day' => date('d'),
            'registered_month' => date('m'),
            'registered_year' => date('Y'),
        );

        $register = $classUser->So_RegisterUser($data_r);

        if ($register) {
            $_SESSION['user_id'] = $register;
            setcookie("user_id", $register, time() + (10 * 365 * 24 * 60 * 60));

            $html = So_GetPage('welcome/change-profile-avatar');

            $data = array(
                'status' => 200,
                'html' => $html
            );
        } else {
            $data = array(
                'status' => 400,
                'errors' => $so['lang']['error_on_signup']
            );
        }
    } else {
        $data = array(
            'status' => 400,
            'errors' => $errors
        );
    }
} else {
    $data = array(
        'status' => 400,
        'errors' => $so['lang']['check_details']
    );
}

header("Content-type: application/json");
echo json_encode($data);
exit();
?>