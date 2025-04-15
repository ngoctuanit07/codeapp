<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => 'Xử lý yêu cầu rút mã tiền',
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, withdraw code edit'
];

require_once(__DIR__.'/../../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');

$id = check_string($_GET['id'] ?? 0);
$row = $CMSNT->get_row("SELECT w.*, c.code FROM `withdraw_requests` w JOIN `withdraw_codes` c ON w.code_id = c.id WHERE w.id = '$id'");
if (!$row) {
    redirect(BASE_URL('admin/withdraw-code-list'));
    exit;
}

if (isset($_POST['Save'])) {
    $status = check_string($_POST['status']);
    $reason = check_string($_POST['reason']);

    $CMSNT->update("withdraw_requests", [
        'status' => $status,
        'reason' => $reason
    ], "`id` = '$id'");

    if ($status == 'cancel') {
        // Hoàn tiền về mã tiền
        $CMSNT->cong("withdraw_codes", "balance", $row['amount'], "`id` = '{$row['code_id']}'");

        $CMSNT->insert("log_ref", [
            'user_id' => 0,
            'reason' => 'Hoàn tiền đơn rút bị huỷ từ mã '.$row['code'],
            'sotientruoc' => $row['amount'],
            'sotienthaydoi' => $row['amount'],
            'sotienhientai' => $row['amount'],
            'create_gettime' => gettime()
        ]);
    }
    die('<script>alert("Cập nhật thành công!");window.location.href="'.BASE_URL('admin/withdraw-code-list').'";</script>');
}

?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Xử lý đơn rút mã tiền</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?=BASE_URL('admin/');?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Xử lý đơn rút mã tiền</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thông tin đơn rút</h3>
                </div>
                <form action="" method="POST">
                    <div class="card-body">
                    
                        <div class="form-group">
                            <label>Mã tiền:</label>
                            <input type="text" class="form-control" value="<?=$row['code'];?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Ngân hàng:</label>
                            <input type="text" class="form-control" value="<?=$row['bank_name'];?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Số tài khoản:</label>
                            <input type="text" class="form-control" value="<?=$row['account_number'];?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Chủ tài khoản:</label>
                            <input type="text" class="form-control" value="<?=$row['account_holder'];?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Số tiền:</label>
                            <input type="text" class="form-control" value="<?=format_currency($row['amount']);?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="pending" <?=$row['status'] == 'pending' ? 'selected' : '';?>>Chờ xử lý</option>
                                <option value="done" <?=$row['status'] == 'done' ? 'selected' : '';?>>Đã duyệt</option>
                                <option value="cancel" <?=$row['status'] == 'cancel' ? 'selected' : '';?>>Huỷ bỏ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lý do (nếu huỷ):</label>
                            <textarea name="reason" class="form-control" rows="3"><?=$row['reason'];?></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" name="Save" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once(__DIR__.'/footer.php'); ?>
