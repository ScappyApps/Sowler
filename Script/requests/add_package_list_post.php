<?php



$classSticker = new Sticker();

if (!empty($_POST['sticker_id'])) {
    $data_r = array(
        'sticker_id' => So_Secure($_POST['sticker_id']),
        'user_id' => $so['user']['user_id']
    );

    if ($classSticker->So_RegisterUserSticker($data_r)) {
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
header("Content-type: application/json");
echo json_encode($data);
exit();
