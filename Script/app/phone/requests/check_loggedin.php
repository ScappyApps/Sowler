<?php


$classUser = new User();
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    if (So_ExistUser(So_Secure($_GET['user_id'])) === true) {
		$result[] = array(
			'status' => 200
		);
	} else {
		$result[] = array(
			'status' => 400
		);
	}
} else {
    $result[] = array(
        'status' => 400
    );
}
$data = array(
	'data' => $result
);

header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
?>