<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Điều khoản sử dụng').' | '.$CMSNT->site('title'),
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
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center"><?=__('Điều khoản sử dụng');?></h3>
                        <?=__($CMSNT->site('dieu_khoan_su_dung'));?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once(__DIR__.'/footer.php');?>