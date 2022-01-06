<?php


$classPost = new Post();
if (!empty($_POST['post_id']) && !empty($_POST['reaction'])) {
	$data_r = array(
		'from_id' => So_Secure($so['user']['user_id']),
		'to_id' => $classPost->So_PostData($_POST['post_id'])['user_id'],
		'post_id' => So_Secure($_POST['post_id']),
		'type' => So_Secure($_POST['reaction'])
	);
	
	$register = So_RegisterReaction($data_r);
	if ($register) {
		
		$so['story']	= $classPost->So_PostData($_POST['post_id']);
		$button			= So_ButtonReaction($so['story']['post_id']);
		$data = array(
			'status' => 200,
			'html' => So_GetPage('buttons/reactions/' . $button),
			'count' => So_GetPage('buttons/reactions/total')
		);
		
	} else {
		$data = array('status' => 400);
	}
	
} else {
	$data = array('status' => 400);
}

header("Content-type: application/json");
echo json_encode($data);
exit();
?>