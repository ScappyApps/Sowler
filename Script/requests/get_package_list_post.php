<?php



$classSticker = new Sticker();
if (isset($_POST['package'])) {
    
    $so['package'] = $classSticker->So_StickerStoreData($_POST['package']);
    
    $data = array(
        'status' => 200,
        'html' => So_GetPage('story/list-package')
    );
} else {
    $data = array('status' => 400, 'error' => true);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

