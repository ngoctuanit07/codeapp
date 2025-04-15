<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => 'Thêm mã tiền',
    'desc'   => 'CMSNT Panel',
    'keyword' => 'mã tiền, withdraw code'
];
require_once(__DIR__.'/../../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');

if (isset($_POST['AddWithdrawCode'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("Không được dùng chức năng này vì đây là trang web demo.")){window.history.back().location.reload();}</script>');
    }

    $isInsert = $CMSNT->insert("withdraw_codes", [
        'code' => check_string($_POST['code']),
        'owner_email' => check_string($_POST['owner_email']),
        'commission_rate' => (float)check_string($_POST['commission_rate']),
        'balance' => (int)check_string($_POST['balance']),
        'created_at' => gettime()
    ]);

    if ($isInsert) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => $Mobile_Detect->getUserAgent(),
            'createdate' => gettime(),
            'action' => "Thêm mã tiền (".check_string($_POST['code']).") cho ".check_string($_POST['owner_email'])
        ]);
        die('<script type="text/javascript">if(!alert("Thêm thành công!")){location.href = "'.BASE_URL('admin/withdraw-codes').'";}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Thêm thất bại!")){window.history.back().location.reload();}</script>');
    }
}
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Thêm mã tiền</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?=BASE_URL('admin/');?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Thêm mã tiền</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <section class="col-lg-6">
                    <div class="mb-3">
                        <a class="btn btn-danger btn-icon-left m-b-10" href="<?=BASE_URL('admin/withdraw-codes');?>" type="button">
                            <i class="fas fa-undo-alt mr-1"></i>Quay Lại
                        </a>
                    </div>
                </section>
                <section class="col-lg-6"></section>
                <section class="col-lg-12 connectedSortable">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus-circle mr-1"></i>
                                THÊM MÃ TIỀN
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn bg-success btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn bg-warning btn-sm" data-card-widget="maximize">
                                    <i class="fas fa-expand"></i>
                                </button>
                                <button type="button" class="btn bg-danger btn-sm" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <form action="" method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="code">Mã (code)</label>
                                    <div class="input-group">
                                        <input type="text" id="code" class="form-control" name="code" placeholder="Nhập mã hoặc nhấn tạo ngẫu nhiên" required>
                                        <button class="btn btn-secondary" type="button" onclick="randomCode()">Tạo ngẫu nhiên</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="owner_email">Email người nhận</label>
                                    <input type="email" class="form-control" name="owner_email" placeholder="Nhập email người nhận" required>
                                </div>
                                <div class="form-group">
                                    <label for="commission_rate">% Hoa hồng</label>
                                    <input type="number" step="0.01" class="form-control" name="commission_rate" placeholder="Nhập phần trăm hoa hồng">
                                </div>
                                <div class="form-group">
                                    <label for="balance">Số dư ban đầu</label>
                                    <input type="number" class="form-control" name="balance" placeholder="Nhập số dư ban đầu">
                                </div>
                            </div>
                            <div class="card-footer clearfix">
                                <button name="AddWithdrawCode" class="btn btn-info btn-icon-left m-b-10" type="submit">
                                    <i class="fas fa-plus mr-1"></i>Thêm Ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script>
function random(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}
function randomCode(){
    document.getElementById('code').value = random(8);
}
</script>
<?php require_once(__DIR__.'/footer.php'); ?>
