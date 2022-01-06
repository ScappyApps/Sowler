<?php


$classUser = new User();
if (isset($_GET['user'])) {
    $user_id = So_Secure($_GET['user']);
    
    if ($classUser->So_RegisterFollow($user_id) === true) {
        $so['suggestion'] = $classUser->So_UserData($user_id);
        $html = So_GetPage('buttons/sidebar-button-follow');
        
        $data = array(
            'status' => 200,
            'html' => $html
        );
    } else {
        $data = array(
            'status' => 400
        );
    }
    
} else {
    $data = array(
        'status' => 400
    );
}
header("Content-type: application/json");
echo json_encode($data);
exit();

