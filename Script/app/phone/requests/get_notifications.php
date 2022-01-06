<?php


$notifications = So_NotificationAppGet($_GET['user_id']);

$data = array(
	'data' => $notifications
);

$query = mysqli_query($Connect, "UPDATE " . T_NOTIFICATIONS . " SET `seen` = '1' WHERE `to_id` = '" . So_Secure($_GET['user_id']) . "' AND `seen` = '0'");

header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
?>
