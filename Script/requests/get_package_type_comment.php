<?php



$classSticker = new Sticker();
$classPost = new Post();

if (isset($_POST['sticker_id'])) {
    $so['package'] = $classSticker->So_StickerStoreData($_POST['sticker_id']);
    
    $so['story'] = $classPost->So_PostData($_POST['post_id']);
    
    $data = array(
        'status' => 200,
        'html' => So_GetPage('comment/list-of-pack')
    );
} else {
    $data = array('status' => 400, 'error' => true);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

