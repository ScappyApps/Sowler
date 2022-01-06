<?php



if (isset($_POST['post_id'])) {
    $classComment = new Live();
    $after_id = 0;
    if (isset($_POST['after_id']) && !empty($_POST['after_id']) && is_numeric($_POST['after_id'])) {
        $after_id = So_Secure($_POST['after_id']);
    }
    $comments = $classComment->So_GetComments(So_Secure($_POST['post_id']), $after_id);
    if (count($comments) > 0) {
        $html = '';
        
        foreach ($comments as $so['comment']) {
            $html .= So_GetPage('live/comment/content');
        }
        $data = array('status' => 200, 'html' => $html);
        
    } else {
        $data = array('status' => 400);
    }
} else {
    $data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data);
exit();

