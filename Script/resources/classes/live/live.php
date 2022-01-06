<?php


class Live {
	
	function So_CreateLiveStream($registration_data = array()) {
		global $so, $Connect;
		
		if ($so['loggedin'] == false || $so['user']['verified'] < 1) {
			return false;
		}
		
		$user_id = So_Secure($so['user']['user_id']);
		
		$select = mysqli_query($Connect, "SELECT `post_id` FROM " . T_POSTS . " WHERE `user_id` = '{$user_id}' AND `postType` = 'started_live_stream'");
		if (mysqli_num_rows($select) > 0) {
			$live = mysqli_fetch_assoc($select);
			
			$this->So_DeleteLiveStream($user_id, $live['post_id']);
			
		}
		
		$classPost = new Post();
		$registerLive = $classPost->So_RegisterPost($registration_data);
		
		if ($registerLive) {
			$this->So_NotifiAllFollowers($user_id, $registerLive);
			
			return $registerLive;
		} else {
			return false;
		}
		
	}
	
	function So_DeleteLiveStream($user_id = 0, $post_id = 0) {
		global $so, $Connect;
		
		if ($so['loggedin'] == false || empty($user_id) || empty($post_id)) {
			return false;
		}
		
		$post_id = So_Secure($post_id);
		$user_id = So_Secure($user_id);
		
		$delete = mysqli_query($Connect, "DELETE FROM " . T_POSTS . " WHERE `post_id` = '{$post_id}' AND `user_id` = '{$user_id}' AND `postType` = 'started_live_stream'");
		
		if ($delete) {
			return true;
		} else {
			return false;
		}
	}
	
	function So_NotifiAllFollowers($user_id = 0, $post_id = 0) {
		global $so, $Connect;
		
		if ($so['loggedin'] == false || empty($user_id) || empty($post_id)) {
			return false;
		}
		
		$post_id = So_Secure($post_id);
		$user_id = So_Secure($user_id);
		
		$select = "SELECT `from_id` FROM " . T_FOLLOWERS . " WHERE `to_id` = '{$user_id}' ORDER BY `id` DESC";
		$query	= mysqli_query($Connect, $select);
		while ($assoc = mysqli_fetch_assoc($query)) {
			$notification = array(
				'post_id' => $post_id,
				'user_id' => $user_id,
				'to_id' => $assoc['from_id'],
				'type' => 'started_live_stream',
				'time' => time(),
				'day' => date('d'),
				'month' => date('m'),
				'year' => date('Y')
			);
			So_RegisterNotification($notification);
		}
		
		return true;
	}
	
	function So_GetComments($post_id = 0, $after_id = 0) {
        global $so, $Connect;

        if (empty($post_id)) {
            return false;
        }
        
        $data = array();
		$classComment = new Comment();
		
		$post_id	= So_Secure($post_id);
		$after_id	= So_Secure($after_id);

        $select = "SELECT `comment_id` FROM " . T_COMMENTS . " WHERE `post_id` = '{$post_id}'";
		
		if (isset($after_id) && is_numeric($after_id) && !empty($after_id)) {
			$select .= " AND `comment_id` > '{$after_id}'";
		}
		
        $select .= " ORDER BY `comment_id` DESC";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $classComment->So_CommentData($assoc['comment_id']);
        }

        return $data;
    }
}
?>