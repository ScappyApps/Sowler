<?php


if (isset($_POST['noti_id']) && is_numeric($_POST['noti_id']) && !empty($_POST['noti_id'])) {
    $query = mysqli_query($Connect, "UPDATE " . T_NOTIFICATIONS . " SET `seen` = '1' WHERE `id` = '" . So_Secure($_POST['noti_id']) . "' AND `to_id` = '{$so['user']['user_id']}' AND `seen` = '0'");

    if ($query) {
        $data = array('status' => 200);
    } else {
        $data = array('status' => 400);
    }
} else {
    $data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data);
exit();