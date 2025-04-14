<!-- Dev By CMSNT.CO | FB.COM/CMSNT.CO | ZALO.ME/0947838128 | MMO Solution -->
<?php
define("IN_SITE", true);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__.'/libs/db.php');
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/libs/lang.php');
require_once(__DIR__.'/libs/helper.php');
require_once(__DIR__.'/libs/database/users.php');
$CMSNT = new DB();
 

// Thời gian chờ reset giới hạn (ví dụ: 10 giây)
$rateLimitWindow = 10;

// Số request tối đa cho phép trong khoảng thời gian trên
$maxRequests = 10;

// Lấy IP của người dùng
$userIP = myip();

// Thời gian hiện tại
$currentTime = time();

// Khởi tạo thông tin truy cập nếu chưa có
if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = [];
}
if (!isset($_SESSION['rate_limit'][$userIP])) {
    $_SESSION['rate_limit'][$userIP] = [
        'start_time' => $currentTime,
        'request_count' => 1
    ];
} else {
    // Lấy thông tin truy cập của IP hiện tại
    $userData = &$_SESSION['rate_limit'][$userIP];
    // Kiểm tra nếu đã hết thời gian giới hạn
    if ($currentTime - $userData['start_time'] > $rateLimitWindow) {
        // Reset giới hạn
        $userData['start_time'] = $currentTime;
        $userData['request_count'] = 1;
    } else {
        // Tăng số lượng request
        $userData['request_count']++;
    }
    // Nếu vượt quá số request cho phép, từ chối truy cập
    if ($userData['request_count'] > $maxRequests) {
        header("HTTP/1.1 429 Too Many Requests");
        checkAccessAttempts(30);
        die("Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.");
    }
}



$module = !empty($_GET['module']) ? check_path($_GET['module']) : 'client';
$home   = $module == 'client' ? $CMSNT->site('home_page') : 'home';
$action = !empty($_GET['action']) ? check_path($_GET['action']) : $home;

if($module == 'client'){
    if ($CMSNT->site('status') != 1 && isSecureCookie('admin_login') != true) {
        require_once(__DIR__.'/resources/views/common/maintenance.php');
        exit();
    }
}

if($module == 'admin'){
    require_once __DIR__.'/models/is_admin.php';
}

if($action == 'footer' || $action == 'header' || $action == 'sidebar' || $action == 'nav'){
    require_once(__DIR__.'/resources/views/common/404.php');
    exit();
}
$path = "resources/views/$module/$action.php";
if (file_exists($path)) {
    require_once(__DIR__.'/'.$path);
    exit();
} else {
    require_once(__DIR__.'/resources/views/common/404.php');
    exit();
}
?>
