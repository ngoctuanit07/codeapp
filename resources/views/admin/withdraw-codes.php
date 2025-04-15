<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => 'Quản lý mã tiền',
    'desc'   => 'CMSNT Panel',
    'keyword' => 'withdraw, cmsnt, mã tiền'
];
$body['header'] = '
    <link rel="stylesheet" href="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
';
$body['footer'] = '
    <script src="'.BASE_URL('public/AdminLTE3/').'plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="'.BASE_URL('public/AdminLTE3/').'plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
';
require_once(__DIR__.'/../../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');

$withdraw_codes = $CMSNT->get_list("SELECT * FROM `withdraw_codes` ORDER BY `id` DESC");
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Danh sách mã tiền</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?=BASE_URL('admin/');?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh sách mã tiền</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <section class="col-lg-12 connectedSortable">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-wallet mr-1"></i>
                                DANH SÁCH MÃ TIỀN
                            </h3>
                            <div class="card-tools">
                                <a href="<?=BASE_URL('admin/withdraw-codes-add');?>" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle mr-1"></i> Thêm mã</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive p-0">
                                <table id="datatable1" class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Mã</th>
                                            <th>Email</th>
                                            <th>Hoa hồng (%)</th>
                                            <th>Số dư</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($withdraw_codes as $row): ?>
                                        <tr>
                                            <td><?=$row['id'];?></td>
                                            <td><b><?=$row['code'];?></b></td>
                                            <td><?=$row['owner_email'];?></td>
                                            <td><?=$row['commission_rate'];?></td>
                                            <td><b><?=number_format($row['balance']);?>đ</b></td>
                                            <td><?=$row['created_at'];?></td>
                                            <td>
                                                <a href="<?=BASE_URL('admin/withdraw-codes-edit/'.$row['id']);?>" class="btn btn-sm btn-info"><i class="fas fa-edit mr-1"></i>Edit</a>
                                                <button onclick="RemoveWithdrawCode('<?=$row['id'];?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash mr-1"></i>Xoá</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<?php require_once(__DIR__.'/footer.php'); ?>

<script>
function RemoveWithdrawCode(id) {
    cuteAlert({
        type: "question",
        title: "Xác nhận xoá",
        message: "Bạn có chắc chắn muốn xoá mã tiền này không?",
        confirmText: "Xoá",
        cancelText: "Hủy"
    }).then((e) => {
        if (e) {
            $.ajax({
                url: "<?=BASE_URL('ajaxs/admin/removeWithdrawCode.php');?>",
                method: "POST",
                dataType: "JSON",
                data: { id: id },
                success: function(respone) {
                    if (respone.status == 'success') {
                        cuteToast({
                            type: "success",
                            message: respone.msg,
                            timer: 3000
                        });
                        location.reload();
                    } else {
                        cuteAlert({
                            type: "error",
                            title: "Lỗi",
                            message: respone.msg,
                            buttonText: "OK"
                        });
                    }
                }
            });
        }
    });
}
$(function () {
    $('#datatable1').DataTable();
});
</script>
