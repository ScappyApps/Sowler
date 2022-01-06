<?php



if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $classUser = new User();
	$classPost = new Post();
	$user_id = So_Secure($_GET['user_id']);
	
	$userData = $classUser->So_UserData($user_id);
	$userData['about'] = str_replace("&#039;", "", $userData['about']);
	$userData['posts'] = $classPost->So_CountPostsUser($userData['user_id']);
	$userData['following'] = $classUser->So_CountFollowingUser($userData['user_id']);
	$userData['followers'] = $classUser->So_CountFollowersUser($userData['user_id']);
	
	$result[] = $userData;
	
	$data = array(
		'data' => $result
	);
	
} else {
    $data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
