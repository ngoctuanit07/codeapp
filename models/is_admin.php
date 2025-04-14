<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}




$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();

if (isSecureCookie('admin_login') != true) {
    redirect(base_url('client/logout'));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".$_COOKIE['admin_login']."' AND `admin` > 0  ");
    // chuyển hướng đăng nhập khi thông tin login không tồn tại
    if (!$getUser) {
        // Rate limit
        checkBlockIP('ADMIN', 5);
        redirect(base_url('client/logout'));
    }
    // Khác thiết bị khi login thì đăng xuất
    // if ($_COOKIE['user_agent'] != $Mobile_Detect->getUserAgent()){
    //     redirect(base_url('client/logout'));
    // }
    if ($getUser['device'] != $Mobile_Detect->getUserAgent()){
        redirect(base_url('client/logout'));
    }
    // chuyển hướng khi bị khoá tài khoản
    if ($getUser['banned'] != 0) {
        redirect(base_url('common/banned'));
    }
    if($getUser['admin'] <= 0){
        // Rate limit
        checkBlockIP('ADMIN', 5);
        redirect(base_url('client/logout'));
    }
    // khoá tài khoản trường hợp âm tiền, tránh bug
    if ($getUser['money'] < 0) {
        $User = new users();
        $User->Banned($getUser['id'], 'Tài khoản âm tiền, ghi vấn bug');
        redirect(base_url('common/banned'));
    }
    // kiểm tra ip có trong whitelist
    if($CMSNT->site('status_security') == 1){
        if(!$CMSNT->get_row("SELECT * FROM `ip_white` WHERE `ip` = '".myip()."' ")){
            redirect(base_url('common/block'));
        }
    }
    if($CMSNT->site('status_only_ip_login_admin') == 1){
        if($getUser['ip'] != myip()){
            $token = md5(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 32).uniqid());
            $CMSNT->update('users', [
                'token'     => $token
            ], " `id` = '".$getUser['id']."' ");
            redirect(base_url('client/logout'));
        }
    }

    // Xóa IP bị đánh dấu ra
    $CMSNT->remove('failed_attempts', " `ip_address` = '".myip()."' ");

    /* cập nhật thời gian online */
    $CMSNT->update("users", [
        'time_session'  => time()
    ], " `id` = '".$getUser['id']."' ");
}

 