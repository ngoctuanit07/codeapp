<?php
if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => 'Danh sách yêu cầu rút mã tiền',
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, withdraw code'
];

require_once(__DIR__.'/../../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');

?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Yêu cầu rút mã tiền</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?=BASE_URL('admin/');?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Rút mã tiền</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-wallet mr-1"></i>
                        DANH SÁCH YÊU CẦU RÚT TIỀN
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã giao dịch</th>
                                    <th>Mã tiền</th>
                                    <th>Lý do</th>
                                    <th>Tài khoản</th>
                                    <th>Ngân hàng</th>
                                    <th>Số tài khoản</th>
                                    <th>Chủ tài khoản</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                $list = $CMSNT->get_list("SELECT w.*, c.code, u.username FROM `withdraw_requests` w 
                                                          JOIN `withdraw_codes` c ON w.code_id = c.id
                                                          LEFT JOIN `users` u ON w.user_id = u.id
                                                          ORDER BY w.id DESC");
                                foreach ($list as $row): ?>
                                <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$row['trans_id'];?></td>
                                    <td><b><?=$row['code'];?></b></td>
                                    <td><b><?=$row['reason'];?></b></td>
                                    <td><span class="badge badge-dark"><?=$row['username'] ?? 'Không xác định';?></span></td>
                                    <td><?=$row['bank_name'];?></td>
                                    <td><?=$row['account_number'];?></td>
                                    <td><?=$row['account_holder'];?></td>
                                    <td><b style="color:blue;"><?=format_currency($row['amount']);?></b></td>
                                    <td><?=$row['status'] === 'done' ? 'Đã duyệt': 'Đang chờ';?></td>
                                    <td><?=$row['created_at'];?></td>
                                    <td>
                                        <a href="<?=BASE_URL('admin/withdraw-code-edit/'.$row['id']);?>" class="btn btn-info btn-sm">Xử lý</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once(__DIR__.'/footer.php'); ?>
