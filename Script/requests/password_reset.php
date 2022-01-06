<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {

    $errors = '';

    if ($classUser->So_CheckExistUserEmail($_POST['email']) === false) {
        $errors = $so['lang']['user_email_not_found'];
    }
    if (empty($errors)) {
        $reset = $classUser->So_PasswordReset($_POST['email']);

        if ($reset) {
            $data = array(
                'status' => 200,
                'html' => $so['lang']['email_sended_check']
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