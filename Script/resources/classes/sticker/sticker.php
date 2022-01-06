<?php


class Sticker {

    function So_RegisterPackageSticker($registration_data = array()) {
        global $so, $Connect;

        if ($so['loggedin'] == false || $so['user']['admin'] == 0) {
            return false;
        }

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_STICKERS_STORE . " ({$fields}) VALUES ({$data})");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function So_RegisterSticker($registration_data = array()) {
        global $so, $Connect;

        if ($so['loggedin'] == false || $so['user']['admin'] == 0) {
            return false;
        }

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_STICKERS . " ({$fields}) VALUES ({$data})");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    
    function So_RegisterUserSticker($registration_data = array()) {
        global $so, $Connect;

        if ($so['loggedin'] == false) {
            return false;
        }

        $fields = '`' . implode('`,`', array_keys($registration_data)) . '`';
        $data = '\'' . implode('\', \'', $registration_data) . '\'';
        $query = mysqli_query($Connect, "INSERT INTO " . T_USER_STICKERS . " ({$fields}) VALUES ({$data})");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function So_StickerStoreData($sticker_id = 0) {
        global $so, $Connect;

        $sticker_id = So_Secure($sticker_id);

        $data = array();

        $select = "SELECT * FROM " . T_STICKERS_STORE . " WHERE `id` = '{$sticker_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        $assoc['image'] = $so['config']['site_url'] . '/' . $assoc['image'];

        $data = $assoc;

        return $data;
    }

    function So_StickerData($sticker_id = 0) {
        global $so, $Connect;

        $sticker_id = So_Secure($sticker_id);

        $data = array();

        $select = "SELECT * FROM " . T_STICKERS . " WHERE `id` = '{$sticker_id}'";
        $query = mysqli_query($Connect, $select);
        $assoc = mysqli_fetch_assoc($query);

        $assoc['image'] = $so['config']['site_url'] . '/' . $assoc['image'];

        $data = $assoc;

        return $data;
    }

    function So_GetAllStickersStore() {
        global $so, $Connect;

        $data = array();

        $select = "SELECT `id` FROM " . T_STICKERS_STORE . " ORDER BY `id` DESC";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_StickerStoreData($assoc['id']);
        }

        return $data;
    }

    function So_GetAllStickers($sticker_id = 0) {
        global $so, $Connect;

        $sticker_id = So_Secure($sticker_id);

        $data = array();

        $select = "SELECT `id` FROM " . T_STICKERS . " WHERE `sticker_id` = '{$sticker_id}' ORDER BY `id` DESC";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_StickerData($assoc['id']);
        }

        return $data;
    }

    function So_HaveStickerPackage($sticker_id = 0) {
        global $so, $Connect;

        $sticker_id = So_Secure($sticker_id);

        $data = array();

        $select = "SELECT `id` FROM " . T_USER_STICKERS . " WHERE `sticker_id` = '{$sticker_id}' AND `user_id` = '{$so['user']['user_id']}'";
        $query = mysqli_query($Connect, $select);
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    function So_GetMyAllStickers() {
        global $so, $Connect;
        $data = array();

        $select = "SELECT `sticker_id` FROM " . T_USER_STICKERS . " WHERE `user_id` = '{$so['user']['user_id']}' ORDER BY `id` DESC";
        $query = mysqli_query($Connect, $select);
        while ($assoc = mysqli_fetch_assoc($query)) {
            $data[] = $this->So_StickerStoreData($assoc['sticker_id']);
        }

        return $data;
    }

}
