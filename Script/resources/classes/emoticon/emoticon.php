<?php

class Emoticon {

    function So_RegisterEmoticon($key = '') {
        global $so, $Connect;

        if (empty($key)) {
            return false;
        }

        $key = So_Secure($key);

        if ($this->So_CheckExistEmoticon($key) === true) {
            return false;
        }

        $query = mysqli_query($Connect, "INSERT INTO " . T_EMOTICONS . " (`key`) VALUES ('{$key}')");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function So_CheckExistEmoticon($key = '') {
        global $so, $Connect;

        if (empty($key)) {
            return false;
        }

        $key = So_Secure($key);

        $query = mysqli_query($Connect, "SELECT `id` FROM " . T_EMOTICONS . " WHERE `key` = '{$key}'");
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function So_EmoticonData() {
        global $so, $Connect;

        $data = array();

        $query = mysqli_query($Connect, "SELECT * FROM " . T_EMOTICONS);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $assoc;
        }
        
        return $data;
    }

    function So_Emoticon($string = '') {

        foreach ($this->So_EmoticonData() as $so['emo']) {
            $code = $so['emo']['key'];
            $name = '<i class="EmojiPicker-item EmojiPicker-focusable u-userColorFocusRing" data-key="' . $code . '" data-placement="bottom-right" tabindex="-1" title="Grinning face" style="background-image: url(https://abs.twimg.com/emoji/v2/72x72/' . $code . '.png);"></i>';
            $string = str_replace($code, $name, $string);
        }
        return $string;
    }

}
