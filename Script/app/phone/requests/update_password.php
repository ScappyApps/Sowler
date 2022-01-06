<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {

    $errors = '';

    if (sha1(md5($_POST['actual'])) <> $so['user']['password']) {
        $errors = $so['lang']['password_invalid_actual'];
    }
    
    if (strlen($_POST['new']) < 8 || strlen($_POST['new']) > 32) {
        $errors = $so['lang']['password_invalid'];
    }
    
    if (empty($errors)) {
        $reset = $classUser->So_UpdateUser($so['user']['user_id'], array('password' => sha1(md5($_POST['new']))));

        if ($reset) {
            $data = array(
                'status' => 200,
                'message' => $so['lang']['setting_updated']
            );
        } else {
            $data = array(
                'status' => 400,
                'errors' => $errors
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