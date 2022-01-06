<?php



$classUser = new User();
$classSticker = new Sticker();
if (isset($_POST['package']) && $_POST['to_id'] <> $so['user']['user_id']) {
    
    $so['chat'] = $classUser->So_UserData($_POST['to_id']);
    $so['package'] = $classSticker->So_StickerStoreData($_POST['package']);
    
    $data = array(
        'status' => 200,
        'html' => So_GetPage('chat/extra/list-package')
    );
} else {
    $data = array('status' => 400, 'error' => true);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

