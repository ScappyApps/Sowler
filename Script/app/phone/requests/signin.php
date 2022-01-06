<?php


$classUser = new User();
if (isset($_GET) && !empty($_GET)) {

    $errors = '';

    if ($classUser->So_CheckExistUsername($_GET['email']) === false && $classUser->So_CheckExistUserEmail($_GET['email']) === false && $classUser->So_CheckExistPhoneNumber($_GET['email']) === false) {
        $errors = 1;
    }
    if (empty($errors)) {
        $login = $classUser->So_LoginUser($_GET['email'], $_GET['password']);

        if ($login) {
			$result[] = array(
				'status' => 200,
				'user_id' => $login
			);
			
			$data = array(
				'data' => $result
			);
			
        } else {
			$result[] = array(
				'status' => 400,
				'errors' => 2
			);
			
			$data = array(
				'data' => $result
			);
        }
    } else {
		$result[] = array(
			'status' => 400,
			'errors' => $errors
		);
		
		$data = array(
			'data' => $result
		);
    }
} else {
	$result[] = array(
		'status' => 400,
		'errors' => 3
	);
	
    $data = array(
		'data' => $result
    );
}

header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
?>