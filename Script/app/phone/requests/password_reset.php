<?php


$classUser = new User();
if (isset($_GET) && !empty($_GET)) {

    $errors = '';

    if ($classUser->So_CheckExistUserEmail($_GET['email']) === false) {
        $errors = 1;
    }
    if (empty($errors)) {
        $reset = $classUser->So_PasswordReset($_GET['email']);

        if ($reset) {
            $result[] = array(
                'status' => 200,
                'html' => 2
            );
        } else {
            $result[] = array(
                'status' => 400,
                'errors' => 3
            );
        }
    } else {
        $result[] = array(
            'status' => 400,
            'errors' => $errors
        );
    }
} else {
    $result[] = array(
        'status' => 400,
        'errors' => 4
    );
}
$data = array(
	'data' => $result
);

header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
?>