<?php



$classSticker = new Sticker();
$classUpload = new Upload();

if (isset($_FILES['image']['name']) && !empty($_POST['name'])) {

    $upload = $classUpload->So_UploadPicture($_FILES["image"]["tmp_name"], $_FILES['image']['name'], 'sticker', $_FILES['image']['type'], $so['user']['user_id']);

    if ($upload) {
        $data_r = array(
            'name' => So_Secure($_POST['name']),
            'image' => $upload['filename']
        );

        if ($classSticker->So_RegisterPackageSticker($data_r)) {
            
            if (!file_exists('upload/stickers/' . str_replace(" ", "", $data_r['name']))) {
                mkdir('upload/stickers/' . str_replace(" ", "", $data_r['name']), 0777, true);
            }
            
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
            'status' => 400
        );
    }
} else {
    $data = array(
        'status' => 400
    );
}
header("Content-type: application/json");
echo json_encode($data);
exit();
