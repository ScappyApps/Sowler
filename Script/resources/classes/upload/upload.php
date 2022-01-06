<?php


use Aws\S3\S3Client;

require_once(__DIR__ . '/Check.php');
require_once(__DIR__ . '/SightengineClient.php');
use \Sightengine\SightengineClient;

class Upload {

    function So_UploadPicture($file, $name, $type, $type_file, $user_id = 0, $placement = '') {
        global $so, $Connect;
        if ($so['loggedin'] == false) {
            return false;
        }
        if (empty($file) || empty($name) || empty($type) || empty($user_id)) {
            return false;
        }
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if (!file_exists('upload/' . $user_id)) {
            mkdir('upload/' . $user_id, 0777, true);
        }
        if (!file_exists('upload/' . $user_id . '/photos')) {
            mkdir('upload/' . $user_id . '/photos/', 0777, true);
        }
        if (!file_exists('upload/' . $user_id . '/photos/' . date('Y'))) {
            mkdir('upload/' . $user_id . '/photos/' . date('Y'), 0777, true);
        }
        if (!file_exists('upload/' . $user_id . '/photos/' . date('Y') . '/' . date('m'))) {
            mkdir('upload/' . $user_id . '/photos/' . date('Y') . '/' . date('m'), 0777, true);
        }
        if (!file_exists('upload/' . $user_id . '/videos')) {
            mkdir('upload/' . $user_id . '/videos/', 0777, true);
        }
        if (!file_exists('upload/' . $user_id . '/videos/' . date('Y'))) {
            mkdir('upload/' . $user_id . '/videos/' . date('Y'), 0777, true);
        }
        if (!file_exists('upload/' . $user_id . '/videos/' . date('Y') . '/' . date('m'))) {
            mkdir('upload/' . $user_id . '/videos/' . date('Y') . '/' . date('m'), 0777, true);
        }
        $allowed = 'jpg,png,jpeg,gif,mp4';
        $new_string = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $extension_allowed = explode(',', $allowed);
        $file_extension = pathinfo($new_string, PATHINFO_EXTENSION);
        if (!in_array($file_extension, $extension_allowed)) {
            return false;
        }
        $ar = array(
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/jpg',
            'video/mp4'
        );
        if (!in_array($type_file, $ar)) {
            return false;
        }
		
        $folder = 'photos';

        if ($file_extension == 'mp4') {
            $folder = 'videos';
        }
		
		if ($folder == 'photos') {
			$client = new SightengineClient('124118769', 'se32daDs6hutNutGhqJG');
			$output = $client->check(['nudity'])->set_file($file);
			
			if ($output['nudity']['safe'] < 0.6) {
				return false;
			}
		}
		
        $dir = 'upload/' . $user_id . '/' . $folder . '/' . date('Y') . '/' . date('m');
        $image_data['user_id'] = So_Secure($user_id);
        if ($type == 'cover') {
            $query_one_delete_cover = mysqli_query($Connect, " SELECT `cover` FROM " . T_USERS . " WHERE `user_id` = " . $image_data['user_id'] . " AND `active` = '1' ");
            $fetched_data = mysqli_fetch_assoc($query_one_delete_cover);
            $filename = $dir . '/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '_cover.' . $ext;
            $image_data['cover'] = $filename;
			$classUser = new User();
            $user_data = $classUser->So_UserData($image_data['user_id']);
            if (move_uploaded_file($file, $filename)) {
                $update_data = false;
                $update_data = mysqli_query($Connect, "UPDATE " . T_USERS . " SET `cover` = '{$image_data['cover']}' WHERE `user_id` = '{$image_data['user_id']}'");
                if ($update_data) {
                    $last_file = $filename;
                    $explode2 = @end(explode('.', $filename));
                    $explode3 = @explode('.', $filename);
                    $last_file = $explode3[0] . '_full.' . $explode2;
                    @So_CompactImage($filename, $last_file, 80);
                }
                if ($update_data == true) {
                    if (So_CropImage(1500, 500, $filename, $filename, 80)) {

                        $data_r = array(
                            'user_id' => $so['user']['user_id'],
                            'postText' => '',
                            'postImage' => $filename,
                            'postType' => 'updated_profile_cover',
                            'time' => time(),
                            'day' => date('d'),
                            'month' => date('m'),
                            'year' => date('Y'),
                            'hour' => date('h:m')
                        );

                        $classPost = new Post();

                        $insert_post = $classPost->So_RegisterPost($data_r);

                        if ($so['config']['amazon_s3'] == 1) {
                            $upload = $this->So_UploadAmazon($last_file);
                            mysqli_query($Connect, "UPDATE " . T_USERS . " SET `amazon_cover` = '1' WHERE `user_id` = '{$user_data['user_id']}' AND `amazon_cover` = '0'");
                        } else {
                            mysqli_query($Connect, "UPDATE " . T_USERS . " SET `amazon_cover` = '0' WHERE `user_id` = '{$user_data['user_id']}' AND `amazon_cover` = '1'");
                        }
                    }
                    return true;
                }
                return true;
            } else {
                return false;
            }
        } else if ($type == 'avatar') {
            $filename = $dir . '/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '_avatar.' . $ext;
            $image_data['avatar'] = $filename;
            $classUser = new User();
            $user_data = $classUser->So_UserData($image_data['user_id']);
            $image_data_d = array();
            @$image_data_d['avatar'] = $user_data['avatar'];
            if (move_uploaded_file($file, $filename)) {
                $update_data = mysqli_query($Connect, "UPDATE " . T_USERS . " SET `avatar` = '{$image_data['avatar']}' WHERE `user_id` = '{$user_data['user_id']}'");
                if ($update_data) {
                    $explode2 = @end(explode('.', $filename));
                    $explode3 = @explode('.', $filename);
                    $last_file = $explode3[0] . '_full.' . $explode2;
                    $compress = So_CompactImage($filename, $last_file, 50);
                    if ($compress) {
                        if (So_CropImage(600, 600, $filename, $filename, 100)) {

                            $data_r = array(
                                'user_id' => $so['user']['user_id'],
                                'postText' => '',
                                'postImage' => $filename,
                                'postType' => 'updated_profile_picture',
                                'time' => time(),
                                'day' => date('d'),
                                'month' => date('m'),
                                'year' => date('Y'),
                                'hour' => date('h:m')
                            );

                            $classPost = new Post();

                            $insert_post = $classPost->So_RegisterPost($data_r);


                            if ($so['config']['amazon_s3'] == 1) {
                                $upload = $this->So_UploadAmazon($last_file);
                                mysqli_query($Connect, "UPDATE " . T_USERS . " SET `amazon_avatar` = '1' WHERE `user_id` = '{$user_data['user_id']}' AND `amazon_avatar` = '0'");
                            } else {
                                mysqli_query($Connect, "UPDATE " . T_USERS . " SET `amazon_avatar` = '0' WHERE `user_id` = '{$user_data['user_id']}' AND `amazon_avatar` = '1'");
                            }
                        }
                    }
                    return true;
                }
            } else {
                return false;
            }
        } else if ($type == 'post') {
            $filename = $dir . '/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '_post.' . $ext;
            if (move_uploaded_file($file, $filename)) {
                if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'gif' || $file_extension == 'jpeg') {
                    @So_CompactImage($filename, $filename, 50);
                    if ($so['config']['amazon_s3'] == 1) {
                        $upload = $this->So_UploadAmazon($filename);
                    }
                }

                $data_array = array('extension' => $file_extension, 'filename' => $filename);

                return $data_array;
            } else {
                return false;
            }
        } else if ($type == 'comment') {
            $filename = $dir . '/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '_comment.' . $ext;
            if (move_uploaded_file($file, $filename)) {
                if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'gif' || $file_extension == 'jpeg') {
                    @So_CompactImage($filename, $filename, 50);
                    if ($so['config']['amazon_s3'] == 1) {
                        $upload = $this->So_UploadAmazon($filename);
                    }
                }

                $data_array = array('extension' => $file_extension, 'filename' => $filename);

                return $data_array;
            } else {
                return false;
            }
        } else if ($type == 'message') {
            $filename = $dir . '/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '_message.' . $ext;
            if (move_uploaded_file($file, $filename)) {
                if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'gif' || $file_extension == 'jpeg') {
                    @So_CompactImage($filename, $filename, 80);
                    if ($so['config']['amazon_s3'] == 1) {
                        $upload = $this->So_UploadAmazon($filename);
                    }
                }

                $data_array = array('extension' => $file_extension, 'filename' => $filename);

                return $data_array;
            } else {
                return false;
            }
        } else if ($type == 'sticker' && $file_extension == 'png') {
            $filename = 'upload/stickers/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '_sticker.' . $ext;
            if (move_uploaded_file($file, $filename)) {

                $data_array = array('extension' => $file_extension, 'filename' => $filename);

                return $data_array;
            } else {
                return false;
            }
        } else if ($type == 'sticker-folder' && $file_extension == 'png') {
            $filename = 'upload/stickers/' . str_replace(" ", "", $placement) . '/' . mt_rand() . '_' . date('d') . '_' . md5(time()) . '.' . $ext;
            if (move_uploaded_file($file, $filename)) {

                $data_array = array('extension' => $file_extension, 'filename' => $filename);

                return $data_array;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function So_UploadAmazon($file) {
        global $so;

        if ($so['config']['amazon_s3'] == 0) {
            return false;
        }

        if (empty($so['config']['amazon_s3_key']) || empty($so['config']['amazon_s3_s_key']) || empty($so['config']['bucket_amazon_name'])) {
            return false;
        }

        $amazon = new S3Client([
            'version' => 'latest',
            'region' => $so['config']['region'],
            'credentials' => [
                'key' => $so['config']['amazon_s3_key'],
                'secret' => $so['config']['amazon_s3_s_key'],
            ]
        ]);
        $amazon->putObject([
            'Bucket' => $so['config']['bucket_amazon_name'],
            'Key' => $file,
            'Body' => fopen($file, 'r+'),
            'ACL' => 'public-read',
            'CacheControl' => 'max-age=3153600',
        ]);

        if ($amazon->doesObjectExist($so['config']['bucket_name'], $file)) {
            @unlink($file);
            return true;
        }
    }

    function So_DeleteAmazon($file) {
        global $so;

        if ($so['config']['amazon_s3'] == 0) {
            return false;
        }

        if (empty($so['config']['amazon_s3_key']) || empty($so['config']['amazon_s3_s_key']) || empty($so['config']['bucket_amazon_name'])) {
            return false;
        }

        $amazon = new S3Client([
            'version' => 'latest',
            'region' => $so['config']['region'],
            'credentials' => [
                'key' => $so['config']['amazon_s3_key'],
                'secret' => $so['config']['amazon_s3_s_key'],
            ]
        ]);

        $amazon->deleteObject([
            'Bucket' => $so['config']['bucket_amazon_name'],
            'Key' => $file,
        ]);

        if (!$amazon->doesObjectExist($so['config']['bucket_amazon_name'], $file)) {
            return true;
        }
    }

}
