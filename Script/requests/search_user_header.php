<?php



$search = So_Search(So_Secure($_GET['value']));
foreach ($search as $so['result']) {
    $i = 0;
    $search[$i]['lastseen'] = So_Time($so['result']['lastseen']);
    $i++;
}

$result['result'] = $search;

if (count($search) > 0) {
    
    $data = array(
        'status' => 200,
        $result,
        'total' => count($search)
    );
} else {
    $data = array(
        'status' => 400
    );
}

header("Content-type: application/json");
echo json_encode($data, JSON_PRETTY_PRINT);
exit();


