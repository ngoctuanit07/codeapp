<?php
define("IN_SITE", true);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$rootPath = dirname(dirname(dirname(__DIR__))) . '/public_html';

require_once $rootPath . '/config.php';
require_once $rootPath . '/libs/db.php';
require_once $rootPath . '/libs/lang.php';
require_once $rootPath . '/libs/helper.php';
require_once $rootPath . '/libs/database/users.php';
$CMSNT = new DB();
if (!isset($_POST['code'])) {
    die(json_encode(['status' => 'error', 'msg' => 'Mã tiền không hợp lệ.']));
}

$code = $_POST['code'];
$codeData = $CMSNT->get_row("SELECT balance FROM `withdraw_codes` WHERE `code` = '$code'");

if ($codeData) {
    die(json_encode(['status' => 'success', 'balance' => format_currency($codeData['balance'])]));
} else {
    die(json_encode(['status' => 'error', 'msg' => 'Mã tiền không tồn tại.']));
}