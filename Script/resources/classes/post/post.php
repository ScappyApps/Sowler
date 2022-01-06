<?php


class Post {

    function So_CountPostsUser($user_id = 0) {
        global $so, $Connect;

        if (empty($user_id)) {
            return false;
        }

        $user_id = So_Secure($user_id);

        $select = "SELECT COUNT(`post_id`) AS `total` FROM " . T_POSTS . " WHERE `user_id` = '{$user_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        return $assoc['total'];
    }

    function So_RegisterPost($registration_data, $emoticon = '') {
        global $so, $Connect;
        if (empty($registration_data) || $so['loggedin'] == false) {
            return false;
        }

        $classUser = new User();

        if (empty($registration_data['postText']) && empty($registration_data['postFile']) && empty($registration_data['postImage']) && empty($registration_data['postVideo']) && empty($registration_data['postGif']) && empty($registration_data['share_id']) && (!isset($registration_data['postType']) || empty($registration_data['postType'])) && empty($registration_data['html_url']) && $registration_data['postType'] <> 'started_live_stream') {
            return false;
        }


        $pattern = '#^(?:https?://)?';    # Optional URL scheme. Either http or https.
        $pattern .= '(?:www\.)?';         #  Optional www subdomain.
        $pattern .= '(?:';                #  Group host alternatives:
        $pattern .= 'youtu\.be/';       #    Either youtu.be,
        $pattern .= '|youtube\.com';    #    or youtube.com
        $pattern .= '(?:';              #    Group path alternatives:
        $pattern .= '/embed/';        #      Either /embed/,
        $pattern .= '|/v/';           #      or /v/,
        $pattern .= '|/watch\?v=';    #      or /watch?v=,    
        $pattern .= '|/watch\?.+&v='; #      or /watch?other_param&v=
        $pattern .= ')';                #    End path alternatives.
        $pattern .= ')';                  #  End host alternatives.
        $pattern .= '([\w-]{11})';        # 11 characters (Length of Youtube video ids).
        $pattern .= '(?:.+)?$#x';         # Optional other ending URL parameters.

        if (preg_match($pattern, $registration_data['postText'], $match)) {
            if (!isset($registration_data['share_id'])) {
                $registration_data['postYoutube'] = So_Secure($match[1]);
            }
            $registration_data['postText'] = preg_replace('/((?:https?:\/\/)?www\.youtube\.com\/watch\?v=\w+\-\w+)/', "", $registration_data['postText']);
        }
        if (preg_match('#giphy.com\/(?:gifs|media)\/(.*)#i', $registration_data['postText'], $match)) {
            $registration_data['postGif'] = So_Secure($match[1]);
            $registration_data['postGif'] = explode("/", $registration_data['postGif']);
            $registration_data['postGif'] = $registration_data['postGif'][0];
        }
		if (isset($registration_data['postGif'])) {
			if (preg_match('#giphy.com\/(?:gifs|media)\/(.*)#i', $registration_data['postGif'], $match)) {
				$registration_data['postGif'] = So_Secure($match[1]);
				$registration_data['postGif'] = explode("/", $registration_data['postGif']);
				$registration_data['postGif'] = $registration_data['postGif'][0];
			}
		}

        $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
        $i = 0;
        preg_match_all($link_regex, $registration_data['postText'], $matches);
        foreach ($matches[0] as $match) {
            $match_url = strip_tags($match);
            $syntax = '[a]' . urlencode($match_url) . '[/a]';
            $registration_data['postText'] = str_replace($match, $syntax, $registration_data['postText']);
        }
        $mention_regex = '/@([A-Za-z0-9_]+)/i';
        preg_match_all($mention_regex, $registration_data['postText'], $matches);
        foreach ($matches[1] as $match) {
            $match = So_Secure($match);
            $match_user = $classUser->So_UserData($classUser->So_UserIdFromUsername($match));
            $match_search = '@' . $match;
            $match_replace = '@[' . $match_user['user_id'] . ']';
            if (isset($match_user['user_id'])) {
                $registration_data['postText'] = str_replace($match_search, $match_replace, $registration_data['postText']);
                $mentions[] = $match_user['user_id'];
            }
        }
        $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
        preg_match_all($hashtag_regex, $registration_data['postText'], $matches);
        foreach ($matches[1] as $match) {
            if (!is_numeric($match)) {
                $hashdata = So_GetHashtag($match);
                if (is_array($hashdata)) {
                    $match_search = '#' . $match;
                    $match_replace = '#[' . $hashdata['id'] . ']';
                    if (mb_detect_encoding($match_search, 'ASCII', true)) {
                        $registration_data['postText'] = preg_replace("/$match_search\b/i", $match_replace, $registration_data['postText']);
                    } else {
                        $registration_data['postText'] = str_replace($match_search, $match_replace, $registration_data['postText']);
                    }
                    $hashtag_query = "UPDATE " . T_HASHTAGS . " SET 
                    `last_trend_time` = " . time() . ", 
                    `trend_use_num`   = " . ($hashdata['trend_use_num'] + 1) . ",
                    `expire`          = '" . date('Y-m-d', strtotime(date('Y-m-d') . " +1week")) . "'       
                    WHERE `id` = " . $hashdata['id'];
                    $hashtag_sql_query = mysqli_query($Connect, $hashtag_query);
                }
            }
        }

		if (!empty($registration_data['postFile']) || !empty($registration_data['postImage']) || !empty($registration_data['postVideo'])) {
			if ($so['config']['amazon_s3'] == 1) {
				$registration_data['amazon'] = 1;
			}
		}
		
        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_POSTS . " ({$fields}) VALUES ({$data})");
        $post_id = mysqli_insert_id($Connect);

        if ($query) {

            if (isset($mentions) && is_array($mentions)) {
                foreach ($mentions as $mention) {
                    if ($mention <> $so['user']['user_id']) {
                        $notification = array(
                            'post_id' => $post_id,
                            'user_id' => $so['user']['user_id'],
                            'to_id' => $mention,
                            'type' => 'mentioned_you_on_a_post',
                            'time' => time(),
                            'day' => date('d'),
                            'month' => date('m'),
                            'year' => date('Y')
                        );
                        So_RegisterNotification($notification);
                    }
                }
            }

            $classEmoticon = new Emoticon();
            if (!empty($emoticon)) {
                $list_emo = explode(',', $emoticon);
                foreach ($list_emo as $so['emoticon'] => $value) {
                    $classEmoticon->So_RegisterEmoticon($value);
                }
            }
            if (isset($registration_data['share_id']) && is_numeric($registration_data['share_id'])) {
                if ($this->So_PostData($registration_data['share_id'])['user_id'] <> $so['user']['user_id']) {
                    $notification = array(
                        'post_id' => $post_id,
                        'user_id' => $so['user']['user_id'],
                        'to_id' => $this->So_PostData($registration_data['share_id'])['user_id'],
                        'type' => 'shared_your_post',
                        'time' => time(),
                        'day' => date('d'),
                        'month' => date('m'),
                        'year' => date('Y')
                    );
                    So_RegisterNotification($notification);
                }
            }

            return $post_id;
        } else {
            return false;
        }
    }

    function So_PostData($post_id = 0) {
        global $so, $Connect;

        if (empty($post_id)) {
            return false;
        }

        $data = array();
        $post_id = So_Secure($post_id);

        $select = "SELECT * FROM " . T_POSTS . " WHERE `post_id` = '{$post_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        $classUser = new User();
        $classEmoticon = new Emoticon();
        if (!empty($assoc['post_id'])) {
            $assoc['user'] = $classUser->So_UserData($assoc['user_id']);
        }
        if (!empty($assoc['postFile'])) {
			if ($assoc['amazon'] == 0) {
				$assoc['postFile'] = $so['config']['site_url'] . '/' . $assoc['postFile'];
			} else {
				$assoc['postFile'] = $so['config']['amazon_url'] . '/' . $assoc['postFile'];
			}
        }
        if (!empty($assoc['postImage'])) {
			if ($assoc['amazon'] == 0) {
				$assoc['postImage'] = $so['config']['site_url'] . '/' . $assoc['postImage'];
			} else {
				$assoc['postImage'] = $so['config']['amazon_url'] . '/' . $assoc['postImage'];
			}
            $assoc['image_w'] = getimagesize($assoc['postImage'])[0];
            $assoc['image_h'] = getimagesize($assoc['postImage'])[1];
        }
        if (!empty($assoc['postVideo'])) {
			if ($assoc['amazon'] == 0) {
				$assoc['postVideo'] = $so['config']['site_url'] . '/' . $assoc['postVideo'];
			} else {
				$assoc['postVideo'] = $so['config']['amazon_url'] . '/' . $assoc['postVideo'];
			}
        }
        if (!empty($assoc['postGif'])) {
            $assoc['postGif'] = $assoc['postGif'];
        }
		
		if (!empty($assoc['postSticker'])) {
			$assoc['postSticker'] =  $so['config']['site_url'] . '/' . $assoc['postSticker'];
		}
		
        if (!empty($assoc['postText'])) {
			$assoc['postOriginal'] = $assoc['postText'];
            $assoc['postText'] = So_Tags($assoc['postText']);
            $assoc['postText'] = $classEmoticon->So_Emoticon($assoc['postText']);
        }
        if (!empty($assoc['time'])) {
            $assoc['post_time'] = So_Time($assoc['time']);
        }

        $data = $assoc;

        return $data;
    }

    function So_GetPosts($before_post_id = '', $user_id = '') {
        global $so, $Connect;

        $data = array();

        if (!empty($after_post_id)) {
            $before_post_id = So_Secure($before_post_id);
        }
        if (!empty($user_id)) {
            $user_id = So_Secure($user_id);
        }

        $select = "SELECT `post_id` FROM " . T_POSTS;

        if (!empty($before_post_id) && empty($user_id)) {
            $select .= " WHERE `user_id` = '{$so['user']['user_id']}' AND `post_id` < '{$before_post_id}' ";
			if ($so['user']['all_posts'] == 0) {
				$select .= " || `post_id` < '{$before_post_id}' AND `user_id` IN (SELECT `to_id` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$so['user']['user_id']}' AND `active` = '1' AND `privacy` <> '4')";
			} else {
				$select .= " || `post_id` < '{$before_post_id}' AND `user_id` IN (SELECT `user_id` FROM " . T_USERS . " WHERE `privacy` <> '4')";
			}
        }
        if (!empty($before_post_id) && !empty($user_id)) {
            $select .= " WHERE `post_id` < '{$before_post_id}' AND `user_id` = '{$user_id}'";
            if ($so['user']['user_id'] <> $user_id) {
				if ($so['user']['all_posts'] == 0) {
					$select .= " AND `user_id` NOT IN (SELECT `user_id` FROM " . T_FOLLOWERS . " WHERE `user_id` = '{$user_id}' AND `privacy` <> '4')";
				} else {
					$select .= " AND `user_id` IN (SELECT `user_id` FROM " . T_USERS . " WHERE `privacy` <> '4')";
				}
            }
        }

        if (empty($before_post_id) && empty($user_id)) {
			if ($so['user']['all_posts'] == 0) {
				$select .= " WHERE `user_id` = '{$so['user']['user_id']}' ";
				$select .= " || `user_id` IN (SELECT `to_id` FROM " . T_FOLLOWERS . " WHERE `from_id` = '{$so['user']['user_id']}' AND `active` = '1' AND `privacy` <> '4')";
			} else {
				$select .= " WHERE `user_id` IN (SELECT `user_id` FROM " . T_USERS . " WHERE `privacy` <> '4')";
			}
        }

        if (empty($before_post_id) && !empty($user_id)) {
            $select .= " WHERE `user_id` = '{$user_id}'";
        }

        $select .= " ORDER BY `post_id` DESC LIMIT 20";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_PostData($assoc['post_id']);
        }

        return $data;
    }

    function So_HashtagPosts($s_query, $after_post_id = 0, $limit = 0, $before_post_id = 0) {
        global $Connect;

        $data = array();

        $search_query = str_replace('#', '', So_Secure($s_query));
        $hashdata = So_GetHashtag($search_query, false);

        if (is_array($hashdata) && count($hashdata) > 0) {

            $search_string = "#[" . $hashdata['id'] . "]";
            $query_one = "SELECT `post_id` FROM " . T_POSTS . " WHERE `postText` LIKE '%{$search_string}%'";

            if (isset($after_post_id) && !empty($after_post_id) && is_numeric($after_post_id)) {
                $after_post_id = So_Secure($after_post_id);
                $query_one .= " AND post_id < {$after_post_id}";
            }

            if (isset($before_post_id) && !empty($before_post_id) && is_numeric($before_post_id)) {
                $before_post_id = So_Secure($before_post_id);
                $query_one .= " AND post_id > {$before_post_id}";
            }

            $query_one .= " ORDER BY `post_id` DESC";

            if (!empty($limit)) {
                $query_one .= " LIMIT {$limit}";
            }

            $sql_query_one = mysqli_query($Connect, $query_one);

            while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
                $posts = $this->So_PostData($sql_fetch_one['post_id']);
                if (is_array($posts)) {
                    $data[] = $posts;
                }
            }
        }
        return $data;
    }

    function So_DeletePost($post_id = 0, $rest = '') {
        global $so, $Connect;
		if (empty($rest) || empty($post_id)) {
			if ($so['loggedin'] == false || empty($post_id)) {
				return false;
			}
		}

        $post_id = So_Secure($post_id);
        $selectPost = mysqli_query($Connect, "SELECT * FROM " . T_POSTS . " WHERE `post_id` = '{$post_id}'");
        $postData = mysqli_fetch_assoc($selectPost);

        if (count($postData) < 1) {
            return false;
        }

		if (empty($rest)) {
			if ($postData['user_id'] <> $so['user']['user_id']) {
				if ($so['user']['admin'] < 1) {
					return false;
				}
			}
		}

        if (!empty($postData['postFile'])) {
            @unlink($postData['postFile']);
			if ($so['config']['amazon_s3'] == 1) {
				$classUpload = new Upload();
				$delete_amazon = $classUpload->So_DeleteAmazon($postData['postFile']);
			}
        }
        if (!empty($postData['postImage']) && $postData['postType'] <> ('updated_profile_picture' && 'updated_profile_cover')) {
            @unlink($postData['postImage']);
			if ($so['config']['amazon_s3'] == 1) {
				$classUpload = new Upload();
				$delete_amazon = $classUpload->So_DeleteAmazon($postData['postImage']);
			}
        }
        if (!empty($postData['postVideo'])) {
            @unlink($postData['postVideo']);
			if ($so['config']['amazon_s3'] == 1) {
				$classUpload = new Upload();
				$delete_amazon = $classUpload->So_DeleteAmazon($postData['postVideo']);
			}
        }

        $delete = mysqli_query($Connect, "DELETE FROM " . T_POSTS . " WHERE `post_id` = '{$post_id}' || `share_id` = '{$post_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_COMMENTS . " WHERE `post_id` = '{$post_id}'");
        $delete .= mysqli_query($Connect, "DELETE FROM " . T_COMMENTS_LIKE . " WHERE `post_id` = '{$post_id}'");
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    function So_PostLiked($post_id = 0, $user_id = 0) {
        global $so, $Connect;
        if (empty($post_id) || empty($user_id)) {
            return false;
        }

        $post_id = So_Secure($post_id);
        $user_id = So_Secure($user_id);

        $select = "SELECT * FROM " . T_LIKE . " WHERE `user_id` = '{$user_id}' AND `post_id` = '{$post_id}'";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_CountPostLike($post_id = 0) {
        global $so, $Connect;
        if (empty($post_id)) {
            return false;
        }

        $post_id = So_Secure($post_id);

        $select = "SELECT * FROM " . T_LIKE . " WHERE `post_id` = '{$post_id}'";
        $query = mysqli_query($Connect, $select);
        return mysqli_num_rows($query);
    }

    function So_CountPostShare($post_id = 0) {
        global $so, $Connect;
        if (empty($post_id)) {
            return false;
        }

        $post_id = So_Secure($post_id);

        $select = "SELECT * FROM " . T_POSTS . " WHERE `share_id` = '{$post_id}'";
        $query = mysqli_query($Connect, $select);
        return mysqli_num_rows($query);
    }

    function So_CountPostComment($post_id = 0) {
        global $so, $Connect;
        if (empty($post_id)) {
            return false;
        }

        $post_id = So_Secure($post_id);

        $select = "SELECT * FROM " . T_COMMENTS . " WHERE `post_id` = '{$post_id}'";
        $query = mysqli_query($Connect, $select);
        return mysqli_num_rows($query);
    }

    function So_RegisterPostLike($post_id = 0) {
        global $so, $Connect;
        if ($so['loggedin'] == false || empty($post_id)) {
            return false;
        }

        $post_id = So_Secure($post_id);
        $user_id = So_Secure($so['user']['user_id']);

        if ($this->So_PostLiked($post_id, $user_id) === true) {
            $delete = mysqli_query($Connect, "DELETE FROM " . T_LIKE . " WHERE `user_id` = '{$user_id}' AND `post_id` = '{$post_id}'");
            if ($delete) {
                return true;
            } else {
                return false;
            }
        } else {

            if (count($this->So_PostData($post_id)) < 1) {
                return false;
            }

            $registration_data = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'time' => time()
            );

            $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
            $data = '\'' . implode('\', \'', $registration_data) . '\'';
            $query = mysqli_query($Connect, "INSERT INTO " . T_LIKE . " ({$fields}) VALUES ({$data})");
            if ($query) {
                if ($this->So_PostData($post_id)['user_id'] <> $so['user']['user_id']) {

					$type = 'liked_your_post';
					if ($this->So_PostData($post_id)['postType'] == 'updated_profile_picture') {
						$type = 'liked_your_profile_picture';
					}
					if ($this->So_PostData($post_id)['postType'] == 'updated_profile_cover') {
						$type = 'liked_your_profile_cover';
					}
				
                    $notification = array(
                        'post_id' => $post_id,
                        'user_id' => $user_id,
                        'to_id' => $this->So_PostData($post_id)['user_id'],
                        'type' => $type,
                        'time' => time(),
                        'day' => date('d'),
                        'month' => date('m'),
                        'year' => date('Y')
                    );
                    So_RegisterNotification($notification);
                }

                return true;
            } else {
                return false;
            }
        }
    }
	
	function So_LastMoment($category = '') {
		global $so, $Connect;
		
		$data = array();
		
		$category = So_Secure($category);
		
		$select  = "SELECT `post_id` FROM " . T_POSTS . " WHERE `titleMoment` <> ''";
		if (!empty($category)) {
			$select .= " AND `categoryMoment` = '{$category}'";
		}
		$select .= " ORDER BY `post_id` DESC LIMIT 1";
		$query	= mysqli_query($Connect, $select);
		$assoc	= mysqli_fetch_assoc($query);
		
		$data = $this->So_PostData($assoc['post_id']);
		
		return $data;
	}
	
	function So_GetThreeMoments($category = '') {
		global $so, $Connect;
		
		$data = array();
		
		$category = So_Secure($category);
		
		$last_moment = $this->So_LastMoment($category)['post_id'];
		
		$select  = "SELECT `post_id` FROM " . T_POSTS . " WHERE `post_id` < '{$last_moment}' AND `titleMoment` <> ''";
		if (!empty($category)) {
			$select .= " AND `categoryMoment` = '{$category}'";
		}
		$select .= " ORDER BY `post_id` DESC LIMIT 3";
		$query	= mysqli_query($Connect, $select);
		while ($assoc = mysqli_fetch_assoc($query)) {
			$data[] = $this->So_PostData($assoc['post_id']);
		}
		return $data;
	}
	
	function So_GetThreeLastMoment($category = '') {
		global $so, $Connect;
		
		$data = array();
		
		$category = So_Secure($category);
		
		$var = $this->So_GetThreeMoments($category);
		$last_three = end($var);
		
		$select  = "SELECT `post_id` FROM " . T_POSTS . " WHERE `post_id` < '{$last_three['post_id']}' AND `titleMoment` <> ''";
		if (!empty($category)) {
			$select .= " AND `categoryMoment` = '{$category}'";
		}
		$select .= " ORDER BY `post_id` DESC LIMIT 1";
		$query	= mysqli_query($Connect, $select);
		$assoc	= mysqli_fetch_assoc($query);
		
		$data = $this->So_PostData($assoc['post_id']);
		
		return $data;
	}
	
	function So_GetAfterThreeMoments($category = 0, $before_post_id = 0) {
		global $so, $Connect;
		
		$data = array();
		
		$category = So_Secure($category);
		
		$last_moment = $this->So_GetThreeLastMoment($category)['post_id'];
		
		$select  = "SELECT `post_id` FROM " . T_POSTS;
		if (!empty($before_post_id)) {
			$before_post_id = So_Secure($before_post_id);
			$select .= " WHERE `post_id` < '{$before_post_id}'";
		} else {
			$select .= " WHERE `post_id` < '{$last_moment}'";
		}
		$select .= " AND `titleMoment` <> ''";
		if (!empty($category)) {
			$select .= " AND `categoryMoment` = '{$category}'";
		}
		$select .= "ORDER BY `post_id` DESC LIMIT 25";
		$query	= mysqli_query($Connect, $select);
		while ($assoc = mysqli_fetch_assoc($query)) {
			$data[] = $this->So_PostData($assoc['post_id']);
		}
		return $data;
	}
	
	

}
