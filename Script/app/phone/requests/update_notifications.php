<?php



$query = mysqli_query($Connect, "UPDATE " . T_NOTIFICATIONS . " SET `seen` = '1' WHERE `to_id` = '{$so['user']['user_id']}' AND `seen` = '0'");

if ($query) {
    $data = array('status' => 200);
} else {
    $data = array('status' => 400);
}

header("Content-type: application/json");
echo json_encode($data);
exit();