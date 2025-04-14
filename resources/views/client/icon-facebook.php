<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Icon Facebook').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '

';
$body['footer'] = '

';

if($CMSNT->site('sign_view_product') == 0){
    if (isset($_COOKIE['user_login'])) {
        require_once(__DIR__.'/../../../models/is_user.php');
    }
}else{
    require_once(__DIR__.'/../../../models/is_user.php');
}
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <iframe src="https://www.smileysapp.com/emojiPicker/" width="100%" height="700px"></iframe>
            </div>
        </div>
    </div>
</div>


<?php require_once(__DIR__.'/footer.php');?>