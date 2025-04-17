<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Rút tiền từ mã tiền').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
require_once(__DIR__.'/../../../models/is_user.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <b><?=mb_strtoupper(__('TẠO YÊU CẦU RÚT TIỀN'), 'UTF-8');?></b>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Form rút tiền -->
                        <div class="form-group">
                            <label><?=__('Nhập mã tiền');?></label>
                            <input type="text" id="withdraw_code" class="form-control" placeholder="VD: U4HJXZYM">
                            <small id="balance_info" class="text-muted"></small>
                        </div>
                        <div class="form-group">
                            <label><?=__('Ngân hàng');?></label>
                            <input type="text" id="bank" class="form-control" placeholder="VD: Vietcombank">
                        </div>
                        <div class="form-group">
                            <label><?=__('Số tài khoản');?></label>
                            <input type="text" id="stk" class="form-control" placeholder="Nhập số tài khoản">
                        </div>
                        <div class="form-group">
                            <label><?=__('Chủ tài khoản');?></label>
                            <input type="text" id="name" class="form-control" placeholder="Nhập tên chủ tài khoản">
                        </div>
                        <div class="form-group">
                            <label><?=__('Số tiền cần rút');?></label>
                            <input type="number" id="amount" class="form-control" placeholder="Nhập số tiền cần rút">
                        </div>
                        <div class="form-group">
                            <label><?=__('Nội dung rút tiền');?></label>
                            <textarea id="note" class="form-control" placeholder="Nhập nội dung rút tiền" rows="3"></textarea>
                        </div>
                        <div class="form-group text-center">
                            <input type="hidden" id="token"
                                value="<?=isset($getUser['token']) ? $getUser['token'] : '';?>"
                                readonly>
                            <button type="button" id="btnWithdrawCode" class="btn btn-danger">
                                <i class="fas fa-money-check-alt"></i> <?=__('RÚT NGAY');?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử rút tiền -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <b><?=__('LỊCH SỬ RÚT TIỀN TỪ MÃ');?></b>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?=__('MÃ');?></th>
                                        <th><?=__('SỐ TIỀN');?></th>
                                        <th><?=__('NGÂN HÀNG');?></th>
                                        <th><?=__('SỐ DƯ CÒN LẠI');?></th>
                                        <th><?=__('NỘI DUNG RÚT TIỀN');?></th>
                                        <th><?=__('THỜI GIAN');?></th>
                                        <th><?=__('TRẠNG THÁI');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; foreach($CMSNT->get_list("SELECT w.*, c.code, c.balance as code_balance FROM `withdraw_requests` w JOIN `withdraw_codes` c ON w.code_id = c.id WHERE w.user_id = '".$getUser['id']."' ORDER BY w.id DESC") as $row): ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=$row['code'];?></td>
                                            <td><?=format_currency($row['amount']);?></td>
                                            <td><?=$row['bank_name'];?> - <?=$row['account_number'];?></td>
                                            <td><b style="color:green;"><?=format_currency($row['code_balance']);?></b></td>
                                            <td><?=$row['reason'];?></td>
                                            <td><?=$row['created_at'];?></td>
                                            <td><?=$row['status'] === 'done' ? 'Đã duyệt' : 'Đang chờ';?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Lịch sử rút tiền -->

    </div>
</div>

<?php require_once(__DIR__.'/footer.php'); ?>

<script>
$('#withdraw_code').on('input', function () {
    let code = $(this).val();
    if (code.length > 0) {
        $.ajax({
            url: "<?=BASE_URL('ajaxs/client/get-code-balance.php');?>",
            method: "POST",
            dataType: "JSON",
            data: { code: code },
            success: function (response) {
                if (response.status === 'success') {
                    $('#balance_info').text('<?=__('Số dư:');?> ' + response.balance);
                } else {
                    $('#balance_info').text(response.msg);
                }
            },
            error: function () {
                $('#balance_info').text('<?=__('Không thể lấy thông tin số dư.');?>');
            }
        });
    } else {
        $('#balance_info').text('');
    }
});

$('#btnWithdrawCode').on('click', function () {
    let code = $('#withdraw_code').val();
    let bank = $('#bank').val();
    let stk = $('#stk').val();
    let name = $('#name').val();
    let amount = $('#amount').val();
    let note = $('#note').val();

    $('#btnWithdrawCode').html('<i class="fa fa-spinner fa-spin"></i> <?=__('Đang xử lý');?>').prop('disabled', true);

    $.ajax({
        url: "<?=BASE_URL('ajaxs/client/withdraw-code.php');?>",
        method: "POST",
        dataType: "JSON",
        data: {
            code: code,
            bank_name: bank,
            account_number: stk,
            account_holder: name,
            amount: amount,
            note: note,
            token: $('#token').val()
        },
        success: function (response) {
            if (response.status === 'success') {
                Swal.fire('<?=__('Thành công');?>', response.msg, 'success');
            } else {
                Swal.fire('<?=__('Thất bại');?>', response.msg, 'error');
            }
            $('#btnWithdrawCode').html('<i class="fas fa-money-check-alt"></i> <?=__('RÚT NGAY');?>').prop('disabled', false);
        }
    });
});
</script>
<style>
    #balance_info {
        font-size: 1.5rem; /* Tăng kích thước chữ */
        font-weight: bold; /* Làm chữ đậm */
        color: #28a745; /* Màu xanh lá nổi bật */
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Thêm hiệu ứng bóng chữ */
        margin-top: 10px; /* Thêm khoảng cách phía trên */
        display: block; /* Đảm bảo nó hiển thị như một khối */
    }
</style>