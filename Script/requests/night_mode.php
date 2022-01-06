<?php


if (isset($_SESSION['night_mode']) && $_SESSION['night_mode'] == 1) {
    $_SESSION['night_mode'] = 0;
} else {
    $_SESSION['night_mode'] = 1;
}
header("Content-type: application/json");
echo json_encode(array('status' => 200));
exit();
