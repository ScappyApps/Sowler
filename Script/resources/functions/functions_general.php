<?php


function So_CompactImage($source_url, $destination_url, $quality) {
    $imgsize = getimagesize($source_url);
    $finfof = $imgsize['mime'];
    $image_c = 'imagejpeg';
    if ($finfof == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source_url);
    } else if ($finfof == 'image/gif') {
        $image = @imagecreatefromgif($source_url);
    } else if ($finfof == 'image/png') {
        $image = @imagecreatefrompng($source_url);
    } else {
        $image = @imagecreatefromjpeg($source_url);
    }
    $quality = 80;
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($source_url);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $image = @imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = @imagerotate($image, -90, 0);
                    break;
                case 8:
                    $image = @imagerotate($image, 90, 0);
                    break;
            }
        }
    }
    @imagejpeg($image, $destination_url, $quality);
    return $destination_url;
}

function So_CropImage($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
    $imgsize = @getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];
    $image = "imagejpeg";
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            break;
        default:
            return false;
            break;
    }
    $dst_img = @imagecreatetruecolor($max_width, $max_height);
    $src_img = @$image_create($source_file);
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($source_file);
        $another_image = false;
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $src_img = @imagerotate($src_img, 180, 0);
                    @imagejpeg($src_img, $dst_dir, $quality);
                    $another_image = true;
                    break;
                case 6:
                    $src_img = @imagerotate($src_img, -90, 0);
                    @imagejpeg($src_img, $dst_dir, $quality);
                    $another_image = true;
                    break;
                case 8:
                    $src_img = @imagerotate($src_img, 90, 0);
                    @imagejpeg($src_img, $dst_dir, $quality);
                    $another_image = true;
                    break;
            }
        }
        if ($another_image == true) {
            $imgsize = @getimagesize($dst_dir);
            if ($width > 0 && $height > 0) {
                $width = $imgsize[0];
                $height = $imgsize[1];
            }
        }
    }
    @$width_new = $height * $max_width / $max_height;
    @$height_new = $width * $max_height / $max_width;
    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
    @imagejpeg($dst_img, $dst_dir, $quality);
    if ($dst_img)
        @imagedestroy($dst_img);
    if ($src_img)
        @imagedestroy($src_img);
    return true;
}

function So_GetPage($page_url = '') {
    global $so;
    $create_file = false;
    $page = './themes/' . $so['config']['theme'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}

function So_SeoLink($query = '') {
    global $so, $config;
    $query = preg_replace(array(
		'/^index\.php\?link1=moments&category_id=([^\/]+)$/i',
		'/^index\.php\?link1=live&type=([^\/]+)$/i',
		'/^index\.php\?link1=adsense&type=([^\/]+)$/i',
        '/^index\.php\?link1=settings&page=([^\/]+)&username=([^\/]+)$/i',
        '/^index\.php\?link1=timeline&username=([^\/]+)&type=([^\/]+)$/i',
        '/^index\.php\?link1=timeline&username=([^\/]+)$/i',
		'/^index\.php\?link1=timeline-app&user_id=([^\/]+)&type=([^\/]+)$/i',
        '/^index\.php\?link1=timeline-app&user_id=([^\/]+)$/i',
        '/^index\.php\?link1=settings&page=([^\/]+)$/i',
        '/^index\.php\?link1=comment&comment_id=([^\/]+)$/i',
        '/^index\.php\?link1=post&post_id=([^\/]+)$/i',
        '/^index\.php\?link1=hashtag&hash=([^\/]+)$/i',
        '/^index\.php\?link1=([^\/]+)$/i',
        '/^index\.php\?link1=welcome$/i',
            ), array(
        $config['site_url'] . '/moments/$1',
        $config['site_url'] . '/live/$1',
        $config['site_url'] . '/adsense/$1',
        $config['site_url'] . '/settings/$1/$2',
        $config['site_url'] . '/t/$1/$2',
        $config['site_url'] . '/t/$1',
		$config['site_url'] . '/t-app/$1/$2',
        $config['site_url'] . '/t-app/$1',
        $config['site_url'] . '/settings/$1',
        $config['site_url'] . '/comment/$1',
        $config['site_url'] . '/post/$1', 
        $config['site_url'] . '/hashtag/$1',       
        $config['site_url'] . '/$1',
        $config['site_url'],
            ), $query);
    return $query;
}

function So_Secure($string, $br = true, $strip = 0) {
    global $Connect;
    $string = trim($string);
    $string = mysqli_real_escape_string($Connect, $string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    if ($br == true) {
        $string = str_replace('\r\n', " <br>", $string);
        $string = str_replace('\n\r', " <br>", $string);
        $string = str_replace('\r', " <br>", $string);
        $string = str_replace('\n', " <br>", $string);
    } else {
        $string = str_replace('\r\n', "", $string);
        $string = str_replace('\n\r', "", $string);
        $string = str_replace('\r', "", $string);
        $string = str_replace('\n', "", $string);
    }
    if ($strip == 1) {
        $string = stripslashes($string);
    }
    $string = str_replace('&amp;#', '&#', $string);
    return $string;
}

function So_Time($ptime) {
    global $so;
    $etime = time() - $ptime;
    if ($etime < 1) {
        return $so['lang']['now_time'];
    }
    $a = array(
        365 * 24 * 60 * 60 => $so['lang']['year'],
        30 * 24 * 60 * 60 => $so['lang']['month'],
        24 * 60 * 60 => $so['lang']['day'],
        60 * 60 => $so['lang']['hour'],
        60 => $so['lang']['minute'],
        1 => $so['lang']['second']
    );
    $a_plural = array(
        $so['lang']['year'] => $so['lang']['years'],
        $so['lang']['month'] => $so['lang']['months'],
        $so['lang']['day'] => $so['lang']['days'],
        $so['lang']['hour'] => $so['lang']['hours'],
        $so['lang']['minute'] => $so['lang']['minutes'],
        $so['lang']['second'] => $so['lang']['seconds']
    );
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            $time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . $so['lang']['time_ago'];
            return $time_ago;
        }
    }
}

function So_GetIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && So_ValidateIp($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (So_ValidateIp($ip))
                    return $ip;
            }
        } else {
            if (So_ValidateIp($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && So_ValidateIp($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && So_ValidateIp($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && So_ValidateIp($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && So_ValidateIp($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}

function So_ValidateIp($ip) {
    if (strtolower($ip) === 'unknown')
        return false;
    $ip = ip2long($ip);
    if ($ip !== false && $ip !== -1) {
        $ip = sprintf('%u', $ip);
        if ($ip >= 0 && $ip <= 50331647)
            return false;
        if ($ip >= 167772160 && $ip <= 184549375)
            return false;
        if ($ip >= 2130706432 && $ip <= 2147483647)
            return false;
        if ($ip >= 2851995648 && $ip <= 2852061183)
            return false;
        if ($ip >= 2886729728 && $ip <= 2887778303)
            return false;
        if ($ip >= 3221225984 && $ip <= 3221226239)
            return false;
        if ($ip >= 3232235520 && $ip <= 3232301055)
            return false;
        if ($ip >= 4294967040)
            return false;
    }
    return true;
}

function So_GetConfig() {
    global $so, $Connect;

    $data = array();

    $select = "SELECT * FROM " . T_CONFIG;
    $query = mysqli_query($Connect, $select);
    while ($assoc = mysqli_fetch_assoc($query)) {
        $data[$assoc['name']] = $assoc['value'];
    }

    return $data;
}

function So_ExistUser($user_id = 0) {
    global $so, $Connect;

    if (empty($user_id)) {
        return false;
    }

    $user_id = So_Secure($user_id);

    $select = mysqli_query($Connect, "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` = '{$user_id}'");
    if (mysqli_num_rows($select) > 0) {
        return true;
    } else {
        return false;
    }
}

function So_SendEmail($data_r = array()) {
    global $so, $Connect, $mail;

	
	//$mail->SMTPDebug = 3; 
    $mail->IsSMTP(); // Define que a mensagem será SMTP
	
	$mail->Host = $so['config']['smtp_host']; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
	$mail->SMTPAuth = true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
	$mail->Username = $so['config']['smtp_email']; // Usuário do servidor SMTP (endereço de email)
	$mail->Password = $so['config']['smtp_password']; // Senha do servidor SMTP (senha do email usado)
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;  
	$mail->setFrom($so['config']['smtp_email'], $so['config']['site_name']); //Seu e-mail
	$mail->addAddress($data_r['email'], $data_r['name']);
	$mail->isHTML(true); 
	$mail->Subject = $data_r['subject']; //Assunto do e-mail
	$mail->Body = $data_r['body'];
	if ($mail->Send()) {
		return true;
	} else {
		return false;
	}
}

function So_CheckPurschaseCode($code = '') {
	if (empty($code)) {
		return false;
	}
	
	$url = 'https://api.envato.com/v3/market/buyer/purchase?code=' . $code;
	
	return $url;
}

?>