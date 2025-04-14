<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}


$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();

if (isSecureCookie('user_login') != true) { 
    redirect(base_url('client/logout'));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_COOKIE['user_login'])."'  ");
    // Chuyển hướng đăng nhập khi thông tin login không tồn tại
    if (!$getUser) {
        redirect(base_url('client/logout'));
    }
    // Chuyển hướng khi bị khoá tài khoản
    if ($getUser['banned'] != 0) {
        redirect(base_url('common/banned'));
    }
    // Khác thiết bị khi login thì đăng xuất
    if ($getUser['device'] != $Mobile_Detect->getUserAgent()){
        redirect(base_url('client/logout'));
    }
    // khoá tài khoản trường hợp âm tiền, tránh bug
    if ($getUser['money'] < 0) {
        $User = new users();
        $User->Banned($getUser['id'], 'Tài khoản âm tiền, ghi vấn bug');
        redirect(base_url('common/banned'));
    }
    if($CMSNT->site('status_active_member') == 1){
        if($getUser['active'] != 1){
            redirect(base_url('common/not-active'));
        }
    }
    $CMSNT->update('users', [
        'update_date'   => gettime()
    ], " `id` = '".$getUser['id']."' ");
}

 
 