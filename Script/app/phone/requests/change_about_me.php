<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {
    if ($classUser->So_UpdateUser($so['user']['user_id'], array('about' => $_POST['about']))) {
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