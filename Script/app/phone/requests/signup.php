<?php


$classUser = new User();
if (!empty($_GET['username']) && !empty($_GET['email']) && !empty($_GET['first_name']) && !empty($_GET['last_name']) && !empty($_GET['password'])) {

    if ($classUser->So_CheckExistUserEmail($_GET['email']) === true) {
        $errors = 1;
    }
    if ($classUser->So_CheckExistUsername($_GET['username']) === true) {
        $errors = 2;
    }
    if (empty($errors)) {
        $data_r = array(
            'username' => So_Secure($_GET['username']),
            'email' => So_Secure($_GET['email']),
            'password' => md5(So_Secure($_GET['password'])),
            'first_name' => So_Secure($_GET['first_name']),
            'last_name' => So_Secure($_GET['last_name']),
            'registered_day' => date('d'),
            'registered_month' => date('m'),
            'registered_year' => date('Y'),
        );

        $register = $classUser->So_RegisterUser($data_r);

        if ($register) {

            $data[] = array(
                'status' => 200,
                'user_id' => $register
            );
        } else {
            $data[] = array(
                'status' => 400,
                'errors' => 3
            );
        }
    } else {
        $data[] = array(
            'status' => 400,
            'errors' => $errors
        );
    }
} else {
    $data[] = array(
        'status' => 400,
        'errors' => 4
    );
}

$data = array(
	'data' => $data
);

header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
?>