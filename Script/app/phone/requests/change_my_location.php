<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {
    if ($classUser->So_UpdateUser($so['user']['user_id'], array('location' => So_Secure($_POST['location'])))) {
        $data = array(
            'status' => 200
        );
    } else {
        $data = array(
            'status' => 400
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