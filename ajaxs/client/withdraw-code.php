<?php

define("IN_SITE", true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$rootPath = dirname(dirname(dirname(__DIR__))) . '/public_html';

require_once $rootPath . '/config.php';
require_once $rootPath . '/libs/db.php';
require_once $rootPath . '/libs/lang.php';
require_once $rootPath . '/libs/helper.php';
require_once $rootPath . '/libs/database/users.php';

$CMSNT = new DB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'msg' => __('Phương thức không hợp lệ')]));
}

if ($CMSNT->site('status_demo') != 0) {
    die(json_encode(['status' => 'error', 'msg' => __('Bạn không được dùng chức năng này vì đây là trang web demo')]));
}

if ($CMSNT->site('status') != 1) {
    die(json_encode(['status' => 'error', 'msg' => __('Hệ thống đang bảo trì')]));
}

// Dữ liệu đầu vào
$code   = check_string($_POST['code'] ?? '');
$bank   = check_string($_POST['bank_name'] ?? '');
$stk    = check_string($_POST['account_number'] ?? '');
$name   = check_string($_POST['account_holder'] ?? '');
$amount = (int) ($_POST['amount'] ?? 0);
$token  = check_string($_POST['token'] ?? '');

if (!$code || !$bank || !$stk || !$name || $amount <= 0 || !$token) {
    die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập đầy đủ thông tin')]));
}

// Lấy user từ token
$user = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . addslashes($token) . "' AND `banned` = 0");

if (!$user) {
    die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
}

// Kiểm tra mã code
$safeCode = addslashes($code);
$row = $CMSNT->get_row("SELECT * FROM `withdraw_codes` WHERE `code` = '$safeCode'");
if (!$row) {
    die(json_encode(['status' => 'error', 'msg' => __('Mã tiền không tồn tại')]));
}

// Kiểm tra hạn mức rút tối thiểu
if ($amount < $CMSNT->site('minrut_ref')) {
    die(json_encode(['status' => 'error', 'msg' => __('Số tiền rút tối thiểu phải là ') . format_currency($CMSNT->site('minrut_ref'))]));
}

// Kiểm tra số dư mã
if ($row['balance'] < $amount) {
    die(json_encode(['status' => 'error', 'msg' => __('Số dư mã tiền không đủ')]));
}

// Tạo mã giao dịch
$trans_id = random('QWERTYUPASDFGHKZXCVBN0123456789', 6);

// Trừ tiền khỏi mã
$CMSNT->update("withdraw_codes", [
    'balance' => $row['balance'] - $amount
], "`id` = '{$row['id']}'");

// Ghi log
$CMSNT->insert("log_ref", [
    'user_id' => $user['id'],
    'reason' => 'Rút tiền từ mã ' . $code . ' - Mã giao dịch #' . $trans_id,
    'sotientruoc' => $row['balance'],
    'sotienthaydoi' => -$amount,
    'sotienhientai' => $row['balance'] - $amount,
    'create_gettime' => gettime()
]);

// Check lại nếu số dư âm thì huỷ
$checkBalance = $CMSNT->get_row("SELECT `balance` FROM `withdraw_codes` WHERE `id` = '{$row['id']}'");
if ($checkBalance && $checkBalance['balance'] < 0) {
    die(json_encode(['status' => 'error', 'msg' => __('Hệ thống phát hiện lỗi số dư âm, vui lòng liên hệ ADMIN')]));
}

// Lưu yêu cầu rút tiền
$isInsert = $CMSNT->insert("withdraw_requests", [
    'user_id' => $user['id'],
    'trans_id' => $trans_id,
    'code_id' => $row['id'],
    'bank_name' => $bank,
    'account_number' => $stk,
    'account_holder' => $name,
    'amount' => $amount,
    'status' => 'pending',
    'created_at' => gettime()
]);

if ($isInsert) {
    die(json_encode(['status' => 'success', 'msg' => __('Tạo yêu cầu rút tiền thành công, vui lòng đợi ADMIN xử lý')]));
}

die(json_encode(['status' => 'error', 'msg' => __('Đã xảy ra lỗi, vui lòng thử lại')]));
