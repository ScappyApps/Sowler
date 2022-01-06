<?php


$notifications = So_Notifications();
if (count($notifications) > 0) {
    $html = '';
    
    foreach ($notifications as $so['notification']) {
        $html .= So_GetPage('header/event-notification');
    }
    
    $data = array(
        'status' => 200,
        'html' => $html
    );
    
} else {
    $data = array(
        'status' => 200,
        'html' => '<p>' . $so['lang']['none_notifications'] . '</p>'
    );
}

header("Content-type: application/json");
echo json_encode($data);
exit();
