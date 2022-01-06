<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {

    $errors = '';

    if ($so['user']['admin'] < 1) {
        if (sha1(md5($_POST['actual'])) <> $so['user']['password']) {
            $errors = $so['lang']['password_invalid_actual'];
        }
    }
    
    if (empty($errors)) {
        
        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            $user_id = So_Secure($_POST['user_id']);
        } else {
            $user_id = So_Secure($so['user']['user_id']);
        }
        
        $reset = $classUser->So_DeleteUser($user_id);

        if ($reset) {
            $data = array(
                'status' => 200,
                'message' => $so['lang']['account_deleted']
            );
        } else {
            $data = array(
                'status' => 400,
                'errors' => $so['lang']['error_to_delete_account']
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