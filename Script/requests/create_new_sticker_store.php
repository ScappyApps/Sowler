<?php



$classSticker = new Sticker();
$classUpload = new Upload();

if (isset($_FILES['image']['name']) && !empty($_POST['sticker_id'])) {

    $upload = $classUpload->So_UploadPicture($_FILES["image"]["tmp_name"], $_FILES['image']['name'], 'sticker-folder', $_FILES['image']['type'], $so['user']['user_id'], $classSticker->So_StickerStoreData($_POST['sticker_id'])['name']);

    if ($upload) {
        $data_r = array(
            'sticker_id' => So_Secure($_POST['sticker_id']),
            'image' => $upload['filename']
        );

        if ($classSticker->So_RegisterSticker($data_r)) {
            
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
