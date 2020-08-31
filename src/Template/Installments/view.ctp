<?php
use Cake\Core\Configure;

$this->assign('title', $installment->name . ' - Thông tin chi tiết');
$installmentStatus = Configure::read('installmentStatus');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('THÔNG TIN CHI TIẾT') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Danh sách đợt thu phí'), [
                'controller' => 'Installments',
                'action' => 'index']) ?>
        </li>
        <li class="active"><?= $installment->name ?></li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li>
                <?= $this->Html->link(__('<i class="fa fa-book" aria-hidden="true"></i>'), 
                    ['action' => 'export', $installment->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Xuất hồ sơ',
                        'escape' => false
                    ]) ?>
            </li>
            <li>
                <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                    ['action' => 'delete', $installment->id], 
                    [
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                        'escape' => false, 
                        'data-toggle' => 'tooltip',
                        'title' => 'Xóa',
                        'confirm' => __('Bạn có chắc chắn muốn xóa {0}?', $installment->name)
                    ]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                    ['action' => 'edit', $installment->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Sửa',
                        'escape' => false
                    ]) ?>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
    <div class="row">    
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin đợt thu phí') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tên đợt thu') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $installment->name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="quarter"><?= __('Quý') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $installment->quarter ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="quarterYear"><?= __('Năm') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $installment->quarter_year ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="adminCompany"><?= __('Phân nhánh') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $installment->admin_company->alias ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <table class="table table-bordered custom-table fees-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?= __('Nghiệp đoàn') ?></th>
                                        <th scope="col" class="col-md-2"><?= __('Các loại phí') ?></th>
                                        <th scope="col"><?= __('Tổng cộng') ?></th>
                                        <th scope="col"><?= __('Tổng tiền vào TK') ?></th>
                                        <th scope="col"><?= __('Ngày gửi hóa đơn') ?></th>
                                        <th scope="col"><?= __('Ngày nhận tiền') ?></th>
                                        <th scope="col"><?= __('Trạng thái') ?></th>
                                        <th scope="col" class="col-md-3"><?= __('Ghi chú') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="installment-fees-container">
                                    <?php if (!empty($installment->installment_fees)): ?>
                                    <?php $counter = $total_vn = $total_jp = $sum_management_fee = $sum_air_ticket_fee = $sum_training_fee = $sum_other_fees = 0; ?>
                                    <?php foreach ($installment->installment_fees as $key => $value): ?>
                                    <?php 
                                        if (isset($value->total_vn)) {
                                            $total_vn += $value->total_vn;
                                        }
                                        if (isset($value->total_jp)) {
                                            $total_jp += $value->total_jp;
                                        }
                                        $sum_management_fee += $value->management_fee;
                                        $sum_air_ticket_fee += $value->air_ticket_fee;
                                        $sum_training_fee += $value->training_fee;
                                        $sum_other_fees += $value->other_fees;
                                    ?>
                                    <div>
                                    <tr class="row-fee" id="row-fee-<?=$counter?>">
                                        <td class="cell">
                                            <div class="guild-txt"><?= $value->guild->name_romaji ?></div>
                                        </td>
                                        <td class="cell">
                                            <div class="management-fee-txt">- Phí quản lý: <?= number_format($value->management_fee) ?> ¥</div>
                                            <div class="air-ticket-fee-txt">- Vé máy bay: <?= number_format($value->air_ticket_fee) ?> ¥</div>
                                            <div class="training-fee-txt">- Phí đào tạo: <?= number_format($value->training_fee) ?> ¥</div>
                                            <div class="other-fees-txt">- Khoản khác: <?= number_format($value->other_fees) ?> ¥</div>
                                        </td>
                                        <td class="cell">
                                            <div class="total-jp-txt"><?= number_format($value->total_jp) ?> ¥</div>
                                        </td>
                                        <td class="cell">
                                            <div class="total-vn-txt"><?= number_format($value->total_vn) ?> ₫</div>
                                        </td>
                                        <td class="cell">
                                            <?= $value->invoice_date ?>
                                        </td>
                                        <td class="cell">
                                            <?= $value->receiving_money_date ?>
                                        </td>
                                        <td class="cell">
                                            <?= $installmentStatus[$value->status] ?>
                                        </td>
                                        <td class="cell">
                                            <?= nl2br($value->notes) ?>
                                        </td>
                                    </tr>
                                    <?php $counter++; ?>
                                    <?php endforeach; ?> 
                                    <tr class="summary">
                                        <td class="cell">
                                            <?= __('Tổng kết') ?>
                                        </td>
                                        <td class="cell">
                                            <div class="summary_fees">
                                                <div id="summary-management-fee">- Phí quản lý: <?= number_format($sum_management_fee) ?> ¥</div>
                                                <div id="summary-air-ticket-fee">- Vé máy bay: <?= number_format($sum_air_ticket_fee) ?> ¥</div>
                                                <div id="summary-training-fee">- Phí đào tạo: <?= number_format($sum_training_fee) ?> ¥</div>
                                                <div id="summary-other-fees">- Khoản khác: <?= number_format($sum_other_fees) ?> ¥</div>
                                            </div>
                                        </td>
                                        <td class="cell">
                                            <div id="summary-total-jp"><?= number_format($total_jp) ?> ¥</div>
                                        </td>
                                        <td class="cell">
                                            <div id="summary-total-vn"><?= number_format($total_vn) ?> ₫</div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>