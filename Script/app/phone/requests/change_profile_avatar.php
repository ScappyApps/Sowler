<?php



$classUser = new User();
$classUpload = new Upload();

if (isset($_FILES['avatar']['name'])) {
    if ($classUpload->So_UploadPicture($_FILES["avatar"]["tmp_name"], $_FILES['avatar']['name'], 'avatar', $_FILES['avatar']['type'], $so['user']['user_id']) === true) {
        $Userdata = $classUser->So_UserData($so['user']['user_id']);
        $data = array(
            'status' => 200,
            'image' => $Userdata['avatar']
        );
    } else {
        $data = array(
            'status' => 400,
            'errors' => ''
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