<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();

if (!isset($_COOKIE['ctv_login'])) {
    redirect(base_url('client/logout'));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `ctv` = 1 AND `token` = '".$_COOKIE['ctv_login']."'  ");
    // chuyển hướng đăng nhập khi thông tin login không tồn tại
    if (!$getUser) {
        redirect(base_url('client/logout'));
    }
    // Khác thiết bị khi login thì đăng xuất
    if ($_COOKIE['user_agent'] != $Mobile_Detect->getUserAgent()){
        redirect(base_url('client/logout'));
    }
    // chuyển hướng khi bị khoá tài khoản
    if ($getUser['banned'] != 0) {
        redirect(base_url('common/banned'));
    }
    // khoá tài khoản trường hợp âm tiền, tránh bug
    if ($getUser['money'] < 0) {
        $User = new users();
        $User->Banned($getUser['id'], 'Tài khoản âm tiền, ghi vấn bug');
        redirect(base_url('common/banned'));
    }
    /* cập nhật thời gian online */
    $CMSNT->update("users", [
        'time_session'  => time()
    ], " `id` = '".$getUser['id']."' ");
}

