<?php



$user_id = So_Secure($_GET['user_id']);
$select = "SELECT COUNT(*) AS `total` FROM " . T_NOTIFICATIONS . " WHERE `to_id` = '{$user_id}' AND `seen` = '0' LIMIT 99";
$query  = mysqli_query($Connect, $select);
$assoc  = mysqli_fetch_assoc($query);

$result[] = array(
	'total' => $assoc['total']
);

$data = array(
	'data' => $result
);
header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();
?>