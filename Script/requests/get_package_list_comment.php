<?php


$classPost = new Post();
$classSticker = new Sticker();
if (isset($_POST['package'])) {
    
    $so['package'] = $classSticker->So_StickerStoreData($_POST['package']);
    $so['story']   = $classPost->So_PostData($_POST['post_id']);
    
    $data = array(
        'status' => 200,
        'html' => So_GetPage('comment/list-package')
    );
} else {
    $data = array('status' => 400, 'error' => true);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

