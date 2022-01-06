<?php


if (isset($_GET['value']) && !empty($_GET['value'])) {
    $value = So_Secure($_GET['value']);
    $users = So_Search($value);
    if (count($users) > 0) {
        $html = '';
        foreach ($users as $so['list']) {
            $html .= So_GetPage('messages/user-list');
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

