<?php



if (isset($_POST)) {
    foreach ($_POST as $key => $value) {
        So_SaveConfig($key, $value);
    }
    $data = array('status' => 200);
} else {
    $data = array('status' => 400);
}
header("Content-type: application/json");
echo json_encode($data);
exit();
