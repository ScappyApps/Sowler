<?php


require_once('./resources/init.php');

if ($so['config']['version'] == '1.6.2') {
    $update  = mysqli_query($Connect, "UPDATE " . T_CONFIG . " SET `value` = '1.7' WHERE `name` = 'version'");
    $update .= mysqli_query($Connect, "INSERT INTO " . T_CONFIG . " (`name`, `value`) VALUES ('websocket', '0')");
    $update .= mysqli_query($Connect, "INSERT INTO " . T_CONFIG . " (`name`, `value`) VALUES ('site_port', '3000')");
    if ($update) {
        echo 'Done, delete file update.php';
    } else {
        echo 'error, contact the developer';
    }
} else {
    echo 'error, you need to have version 1.6.2 to do update, your version is: ' . $so['config']['version'];
}
?>