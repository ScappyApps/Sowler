<?php


if (isset($_GET['user']) && !empty($_GET['user'])) {
    $user = So_Secure($_GET['user']);
    $users = So_Search($user, 0, 100);
    if (count($users) > 0) {
        $html = '';
        foreach ($users as $so['result']) {
            $html .= So_GetPage('admincp/users/list');
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

