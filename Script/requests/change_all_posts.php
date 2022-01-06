<?php



if (isset($_POST) && $_POST['value'] < 2) {
    $classUser = new User();
    $Userdata = $classUser->So_UserData($so['user']['user_id']);
    if (!empty($Userdata['user_id'])) {
		$Update_data = array(
			'all_posts' => $_POST['value'] 
		);
		if ($classUser->So_UpdateUser($so['user']['user_id'], $Update_data)) {
			$data = array(
				'status' => 200,
				'message' => $so['lang']['setting_updated']
			);
		}
    }
}
header("Content-type: application/json");
if (isset($errors)) {
    echo json_encode(array(
        'errors' => $errors
    ));
} else {
    echo json_encode($data);
}
exit();
