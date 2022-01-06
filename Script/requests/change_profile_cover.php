<?php



$classUser = new User();
$classUpload = new Upload();

if (isset($_FILES['cover']['name'])) {
    if ($classUpload->So_UploadPicture($_FILES["cover"]["tmp_name"], $_FILES['cover']['name'], 'cover', $_FILES['cover']['type'], $so['user']['user_id']) === true) {
        $Userdata = $classUser->So_UserData($so['user']['user_id']);
        $data = array(
            'status' => 200,
            'image' => $Userdata['cover']
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