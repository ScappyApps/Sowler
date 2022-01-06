<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {

    $errors = '';

    if ($classUser->So_CheckExistUsername($_POST['email']) === false && $classUser->So_CheckExistUserEmail($_POST['email']) === false && $classUser->So_CheckExistPhoneNumber($_POST['email']) === false) {
        $errors = $so['lang']['user_not_found'];
    }
    if (empty($errors)) {
        $login = $classUser->So_LoginUser($_POST['email'], $_POST['password']);

        if ($login) {
            $_SESSION['user_id'] = $login;
            setcookie("user_id", $login, time() + (10 * 365 * 24 * 60 * 60));

            $data = array(
                'status' => 200
            );
        } else {
            $data = array(
                'status' => 400,
                'errors' => $so['lang']['error_on_signin']
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