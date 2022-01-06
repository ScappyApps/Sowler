<?php



if (!empty($_GET['username']) && !empty($_GET['email']) && !empty($_GET['first_name']) && !empty($_GET['last_name'])) {
	
    $classUser = new User();
	$userData = $classUser->So_UserData(So_Secure($_GET['user_id']));
	
	if ($_GET['email'] != $userData['email']) {
		if ($classUser->So_CheckExistUserEmail($_GET['email'])) {
			$error[] = array(
				'status' => 400,
				'errors' => 2
			);
		}
	}
	
	if ($_GET['username'] != $userData['username']) {
		if ($classUser->So_CheckExistUsername($_GET['username'])) {
			if (!isset($error)) {
				$error[] = array(
					'status' => 400,
					'errors' => 3
				);
			}
		}
	}
	if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
		if (!isset($error)) {
			$error[] = array(
				'status' => 400,
				'errors' => 4
			);
		}
	}
	if (strlen($_GET['username']) < 5 || strlen($_GET['username']) > 32) {
		if (!isset($error)) {
			$error[] = array(
				'status' => 400,
				'errors' => 5
			);
		}
	}
	if (!preg_match('/^[\w]+$/', $_GET['username'])) {
		if (!isset($error)) {
			$error[] = array(
				'status' => 400,
				'errors' => 6
			);
		}
	}
	/**if (!empty($_GET['year']) || !empty($_GET['day']) || !empty($_GET['month'])) {
		$day = So_Secure($_GET['day']);
		$month = So_Secure($_GET['month']);
		$year = So_Secure($_GET['year']);
		$privacy = So_Secure($_GET['privacy']);

		if ($day < 1 || $day > 31) {
			if (!isset($error)) {
				$error[] = array(
					'status' => 400,
					'errors' => 7
				);
			}
		}

		if ($month < 1 || $month > 12) {
			if (!isset($error)) {
				$error[] = array(
					'status' => 400,
					'errors' => 7
				);
			}
		}

		if ($month == 2 && $day > 29) {
			if (!isset($error)) {
				$error[] = array(
					'status' => 400,
					'errors' => 7
				);
			}
		}

		if ($privacy < 1 || $privacy > 4) {
			if (isset($error)) {
				$error[] = array(
					'status' => 400,
					'errors' => 7
				);
			}
		}
	}
	
	$gender = 'male';
	$gender_array = array(
		'male',
		'female'
	);
	if (!empty($_GET['gender'])) {
		if (in_array($_GET['gender'], $gender_array)) {
			$gender = $_GET['gender'];
		}
	}**/
	
	if (empty($error)) {
		$Update_data = array(
			'username' => $_GET['username'],
			'email' => $_GET['email'],
			'phone_number' => $_GET['phone_number'],
			'first_name' => $_GET['first_name'],
			'last_name' => $_GET['last_name'],
			'about' => $_GET['about'],
			'location' => $_GET['location']
		);
		if ($classUser->So_UpdateUserRest($userData['user_id'], $Update_data)) {
			$result[] = array(
						'status' => 200
					);
			$data = array(
				'data' => $result
			);
		}
	} else {
		$errors = array(
			'data' => $error
		);
	}
} else {
	
	$error[] = array(
			'status' => 400,
			'errors' => 1
		);
	
	$errors = array(
		'data' => $error
	);
	
}
header("Content-type: application/json");
if (isset($errors)) {
    echo json_encode($errors, JSON_PRETTY_PRINT);
} else {
    echo json_encode($data, JSON_PRETTY_PRINT);
}
exit();
