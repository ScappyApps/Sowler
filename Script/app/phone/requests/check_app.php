<?php

if (isset($_GET['code']) && !empty($_GET['code'])) {
    if (So_Secure($_GET['code']) == $so['config']['app_token']) {
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