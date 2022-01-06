<?php


$classUser = new User();
if (isset($_POST) && !empty($_POST)) {
    $errors = '';
    
    $day        = So_Secure($_POST['day']);
    $month      = So_Secure($_POST['month']);
    $year       = So_Secure($_POST['year']);
    $privacy    = So_Secure($_POST['privacy']);
    
    if ($day < 1 || $day > 31) {
        $errors = $errors + 1;
    }
    
    if ($month < 1 || $month > 12) {
        $errors = $errors + 1;
    }
    
    if ($month == 2 && $day > 29) {
        $errors = $errors + 1;
    }
    
    if ($privacy < 1 || $privacy > 4) {
        $errors = $errors + 1;
    }
    
    if (empty($errors)) {

        $array = array(
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'privacy' => $privacy
        );

        if ($classUser->So_UpdateUser($so['user']['user_id'], $array)) {
            $data = array(
                'status' => 200
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
} else {
    $data = array(
        'status' => 400,
        'errors' => $so['lang']['check_details']
    );
}

header("Content-type: application/json");
echo json_encode($data);
exit();
?>