<?php


$classUser = new User();
if (isset($_POST['user_id']) && count($classUser->So_UserData(So_Secure($_POST['user_id']))) > 0) {
    
    $so['user_hover'] = $classUser->So_UserData(So_Secure($_POST['user_id']));
    $html = So_GetPage('modals/hover-profile-container');
    
    $data = array(
        'status' => 200,
        'html' => $html
    );
    
} else {
    $data = array(
        'status' => 400
    );
}
header("Content-type: application/json");
echo json_encode($data);
exit();
