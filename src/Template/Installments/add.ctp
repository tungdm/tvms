<?php
use Cake\Core\Configure;

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('installments.js', ['block' => 'scriptBottom']);

$action = $this->request->getParam('action');

$installmentStatus = Configure::read('installmentStatus');
?>

<?php if ($action === 'add'): ?>
    <?php $this->assign('title', 'Thêm mới đợt thu phí'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('THÊM MỚI ĐỢT THU PHÍ') ?></h1>
        <button class="btn btn-success submit-installment-btn" type="button">Lưu lại</button>
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
            <li class="active">Thêm mới</li>
        </ol>
    <?php $this->end(); ?>
<?php else: ?>
    <?php $this->assign('title', $installment->name . ' - Cập nhật đợt thu phí'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('CẬP NHẬT ĐỢT THU PHÍ') ?></h1>
        <button class="btn btn-success submit-installment-btn" type="button">Lưu lại</button>
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
<?php endif; ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($action === 'edit'): ?>
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
                    <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                        ['action' => 'view', $installment->id],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Xem chi tiết',
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
                    <?= $this->Html->link(__('<i class="fa fa-plus" aria-hidden="true"></i>'), 
                        ['action' => 'add'],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Thêm mới',
                            'escape' => false
                        ]) ?>
                </li>
            <?php endif; ?>
            <li>
                <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out submit-installment-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>

<?= $this->Form->create($installment, [
    'class' => 'form-horizontal form-label-left',
    'id' => 'add-installment-form',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
<?= $this->Form->unlockField('installment_fees') ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= __('Thông tin đợt thu phí') ?>
                </h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"><?= __('Tên đợt thu') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('name', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên đợt thu'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="admin-company"><?= __('Phân nhánh') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('admin_company_id', [
                            'options' => $adminCompanies, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select-job',
                            'data-parsley-errors-container' => '#error-admin-company',
                            ]) ?>
                        <span id="error-admin-company"></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary btn-fees" id="add-fees-top" onclick="showAddFeesModal()"><?= __('Thêm nghiệp đoàn') ?></button>
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
                                    <th scope="col" class="col-md-2"><?= __('Ghi chú') ?></th>
                                    <th scope="col" class="col-md-1 actions"><?= __('Thao tác') ?></th>
                                </tr>
                            </thead>
                            <tbody id="installment-fees-container">
                                <?php if (!empty($installment->installment_fees)): ?>
                                <?php 
                                    $counter = $total_vn = $total_jp = $sum_management_fee = $sum_air_ticket_fee = $sum_training_fee = $sum_other_fees = 0;
                                ?>
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
                                <div class="hidden installment-fee-id" id="installment-fee-id-<?=$counter?>">
                                    <?= $this->Form->hidden('installment_fees.'  . $key . '.id', ['value' => $value->id]) ?>
                                <div>
                                <tr class="row-fee" id="row-fee-<?=$counter?>">
                                    <td class="cell">
                                        <div class="guild-txt"><?= $value->guild->name_romaji ?></div>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.guild_id', [
                                                'type' => 'number',
                                                'label' => false, 
                                                'required' => true,
                                                'class' => 'form-control guild',
                                                ]) ?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="management-fee-txt">- Phí quản lý: <?= number_format($value->management_fee) ?> ¥</div>
                                        <div class="air-ticket-fee-txt">- Vé máy bay: <?= number_format($value->air_ticket_fee) ?> ¥</div>
                                        <div class="training-fee-txt">- Phí đào tạo: <?= number_format($value->training_fee) ?> ¥</div>
                                        <div class="other-fees-txt">- Khoản khác: <?= number_format($value->other_fees) ?> ¥</div>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.management_fee', [
                                                'type' => 'number',
                                                'label' => false,
                                                'class' => 'form-control management-fee',
                                                ])?>
                                            <?= $this->Form->control('installment_fees.'  . $key . '.air_ticket_fee', [
                                                'type' => 'number',
                                                'label' => false,
                                                'class' => 'form-control air-ticket-fee',
                                                ])?>
                                            <?= $this->Form->control('installment_fees.'  . $key . '.training_fee', [
                                                'type' => 'number',
                                                'label' => false,
                                                'class' => 'form-control training-fee',
                                                ])?>
                                            <?= $this->Form->control('installment_fees.'  . $key . '.other_fees', [
                                                'type' => 'number',
                                                'label' => false,
                                                'class' => 'form-control other-fees',
                                                ])?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="total-jp-txt"><?= number_format($value->total_jp) ?> ¥</div>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.total_jp', [
                                                'type' => 'number',
                                                'label' => false,
                                                'class' => 'form-control total-jp',
                                                ])?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="total-vn-txt"><?= isset($value->total_vn) ? number_format($value->total_vn) . ' ₫' : ''?></div>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.total_vn', [
                                                'type' => 'number',
                                                'label' => false,
                                                'class' => 'form-control total-vn',
                                                ])?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <?= $value->invoice_date ?>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.invoice_date', [
                                                'type' => 'text',
                                                'label' => false,
                                                'class' => 'form-control invoice-date',
                                                ])?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <?= $value->receiving_money_date ?>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.receiving_money_date', [
                                                'type' => 'text',
                                                'label' => false,
                                                'class' => 'form-control rev-money-date',
                                                ])?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <?= $installmentStatus[$value->status] ?>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.status', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'required' => true,
                                                'class' => 'form-control status',
                                                ]) ?>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <?= nl2br($value->notes) ?>
                                        <div class="hidden">
                                            <?= $this->Form->control('installment_fees.'  . $key . '.notes', [
                                                'label' => false, 
                                                'type' => 'textarea',
                                                'class' => 'form-control notes', 
                                                ]) ?>
                                        </div>
                                    </td>
                                    <td class="cell action-btn actions">
                                        <?= $this->Html->link(
                                            '<i class="fa fa-2x fa-pencil"></i>', 
                                            'javascript:;',
                                            [
                                                'escape' => false,
                                                'onClick' => "showEditFeesModal(this)"
                                            ]) 
                                        ?>
                                        <?= $this->Html->link(
                                            '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                            'javascript:;',
                                            [
                                                'escape' => false, 
                                                'onClick' => 'removeFees(this, true)'
                                            ]
                                        )?>
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
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>


<div id="add-fees-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM NGHIỆP ĐOÀN</h4>
            </div>
            <div class="modal-body">
            <?= $this->Form->create(null, [
                    'type' => 'post',
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'add-fees-form',
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="guild"><?= __('Nghiệp đoàn') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('modal.guild', [
                            'options' => $guilds, 
                            'required' => true, 
                            'empty' => true,
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-guild',
                            'data-parsley-class-handler' => '#select2-modal-guild',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-guild"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status"><?= __('Trạng thái') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('modal.status', [
                            'options' => $installmentStatus, 
                            'required' => true, 
                            'empty' => true,
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-status',
                            'data-parsley-class-handler' => '#select2-modal-status',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-status"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="management-fee"><?= __('Phí quản lý') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-6" style="padding-left: 0px">
                            <?= $this->Form->control('modal.management_fee_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'add-management-fee-txt',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'add-management-fee', 
                                'placeholder' => 'Nhập phí quản lý'
                                ]) ?>
                            <?= $this->Form->control('modal.management_fee', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'add-management-fee',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="air-ticket-fee"><?= __('Vé máy bay') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-6" style="padding-left: 0px">
                            <?= $this->Form->control('modal.air_ticket_fee_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'add-air-ticket-fee-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'add-air-ticket-fee', 
                                'placeholder' => 'Nhập tiền vé máy bay'
                                ]) ?>
                            <?= $this->Form->control('modal.air_ticket_fee', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'add-air-ticket-fee',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="training_fee"><?= __('Phí đào tạo') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-6" style="padding-left: 0px">
                            <?= $this->Form->control('modal.training_fee_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'add-training-fee-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'add-training-fee', 
                                'placeholder' => 'Nhập phí đào tạo'
                                ]) ?>
                            <?= $this->Form->control('modal.training_fee', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'add-training-fee',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="other_fees"><?= __('Các khoản phí khác') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-6" style="padding-left: 0px">
                            <?= $this->Form->control('modal.other_fees_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'add-other-fees-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'add-other-fees', 
                                'placeholder' => 'Nhập tổng các khoản phí khác'
                                ]) ?>
                            <?= $this->Form->control('modal.other_fees', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'add-other-fees',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="total-vn"><?= __('Tổng tiền vào tài khoản') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-6" style="padding-left: 0px">
                            <?= $this->Form->control('modal.total_vn_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'add-total-vn-txt',
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'add-total-vn', 
                                'placeholder' => 'Nhập tổng số tiền vào tài khoản'
                                ]) ?>
                            <?= $this->Form->control('modal.total_vn', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'add-total-vn',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-control form-control-view">đơn vị: ₫</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="invoice-date"><?= __('Ngày gửi hóa đơn') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="add-invoice-date-div">
                            <?= $this->Form->control('modal.invoice_date', [
                                'type' => 'text',
                                'id' => 'add-invoice-date',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-add-invoice-date'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-add-invoice-date"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="receiving-money-date"><?= __('Ngày nhận tiền') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="add-receiving-money-date-div">
                            <?= $this->Form->control('modal.receiving_money_date', [
                                'type' => 'text',
                                'id' => 'add-receiving-money-date',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-add-receiving-money-date'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-add-receiving-money-date"></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="notes"><?= __('Ghi chú') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('modal.notes', [
                            'label' => false, 
                            'type' => 'textarea',
                            'rows' => 5,
                            'id' => 'add-notes',
                            'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                            'placeholder' => 'Nhập ghi chú'
                            ]) ?>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
            <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-fees-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<script id="fee-template" type="text/x-handlebars-template">    
    <tr class="row-fee" id="row-fee-{{counter}}">
        <td class="cell">
            <div class="guild-txt">{{guildTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{guild}}', [
                    'type' => 'number',
                    'label' => false, 
                    'required' => true,
                    'class' => 'form-control guild',
                    'value' => '{{guildVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell">
            <div class="management-fee-txt">- Phí quản lý: {{managementFeeTxt}} ¥</div>
            <div class="air-ticket-fee-txt">- Vé máy bay: {{airTicketFeeTxt}} ¥</div>
            <div class="training-fee-txt">- Phí đào tạo: {{trainingFeeTxt}} ¥</div>
            <div class="other-fees-txt">- Khoản khác: {{otherFeesTxt}} ¥</div>
            <div class="hidden">
                <?= $this->Form->control('{{managementFee}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control management-fee',
                    'value' => '{{managementFeeVal}}'
                    ])?>
                <?= $this->Form->control('{{airTicketFee}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control air-ticket-fee',
                    'value' => '{{airTicketFeeVal}}'
                    ])?>
                <?= $this->Form->control('{{trainingFee}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control training-fee',
                    'value' => '{{trainingFeeVal}}'
                    ])?>
                <?= $this->Form->control('{{otherFees}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control other-fees',
                    'value' => '{{otherFeesVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            <div class="total-jp-txt">{{totalJpTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{totalJp}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control total-jp',
                    'value' => '{{totalJpVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            <div class="total-vn-txt">{{totalVnTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{totalVn}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control total-vn',
                    'value' => '{{totalVnVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            {{invoiceDateTxt}}
            <div class="hidden">
                <?= $this->Form->control('{{invoiceDate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control invoice-date',
                    'value' => '{{invoiceDateVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            {{revMoneyDateTxt}}
            <div class="hidden">
                <?= $this->Form->control('{{revMoneyDate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control rev-money-date',
                    'value' => '{{revMoneyDateVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            {{statusTxt}}
            <div class="hidden">
                <?= $this->Form->control('{{status}}', [
                    'type' => 'text',
                    'label' => false, 
                    'required' => true,
                    'class' => 'form-control status',
                    'value' => '{{statusVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell">
            {{{notesTxt}}}
            <div class="hidden">
                <?= $this->Form->control('{{notes}}', [
                    'label' => false, 
                    'type' => 'textarea',
                    'class' => 'form-control notes', 
                    'value' => '{{notesVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell action-btn actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditFeesModal(this)"
                ]) 
            ?>
            
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => 'removeFees(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>