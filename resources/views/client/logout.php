<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

// Xóa cookie
setcookie('token', '', time() - 3600, '/', '', true, true);
setcookie('admin_login', '', time() - 3600, '/', '', true, true);
setcookie('user_login', '', time() - 3600, '/', '', true, true);
setcookie('ctv_login', '', time() - 3600, '/', '', true, true);
setcookie('user_agent', '', time() - 3600, '/', '', true, true);

// Xóa session
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy session
redirect(base_url('client/login'));

