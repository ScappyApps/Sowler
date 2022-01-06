<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {

    $errors = '';

    if ($classUser->So_CheckExistUserToken($_POST['token']) === false) {
        $errors = $so['lang']['invalid_token'];
    }
    if (empty($errors)) {
        $reset = $classUser->So_SetPasswordToken($_POST['password'], $_POST['token']);

        if ($reset) {
            $data = array(
                'status' => 200,
                'html' => $so['lang']['password_is_changed']
            );
        } else {
            $data = array(
                'status' => 400,
                'errors' => $so['lang']['error_on_changed_password']
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