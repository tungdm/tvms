<?php
    use Cake\Core\Configure;
    $controller = $this->request->getParam('controller');
    $permission = $this->request->session()->read($controller) ?? 0;
    $dayOffType = Configure::read('dayOffType');

    $this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
    $this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
    $this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
    $this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
    $this->Html->script('schedule.js', ['block' => 'scriptBottom']);
?>

<?php $this->assign('title', 'Khóa học'); ?>
<?php $this->start('content-header'); ?>
    <h1><?= __('KHÓA HỌC') ?></h1>
    <?php if ($permission == 0): ?>
        <button class="btn btn-success submit-schedule-btn" type="button">Lưu lại</button>
    <?php endif; ?>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Danh sách đơn Hàng'), [
                'controller' => 'Orders',
                'action' => 'index']) ?>
        </li>
        <li>
            <?= $this->Html->link($order->name, [
                'controller' => 'Orders',
                'action' => 'view', $order->id]) ?>
        </li>
        <li class="active">Khóa học</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li data-toggle="tooltip" title="Xuất hồ sơ">
                <a class="zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out" 
                data-toggle="modal" 
                data-target="#export-schedule-modal">
                    <i class="fa fa-book" aria-hidden="true"></i>
                </a>
            </li>
            <li>
                <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                    ['action' => 'view', $order->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Xem chi tiết đơn hàng',
                        'escape' => false
                    ]) ?>
            </li>
            <?php if ($permission == 0): ?>
                <?php if ($action === 'edit'): ?>
                    <li>
                        <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                            ['action' => 'deleteSchedule', $schedule->id], 
                            [
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                                'escape' => false, 
                                'data-toggle' => 'tooltip',
                                'title' => 'Xóa',
                                'confirm' => __('Bạn có chắc chắn muốn xóa khóa học của đơn hàng {0}?', $order->name)
                            ]) ?>
                    </li>
                <?php endif; ?>
                <li>
                    <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out submit-schedule-btn" data-toggle="tooltip" title="Lưu lại">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>
<?php if ($permission == 0): ?>
    <?= $this->Form->create($schedule, [
        'class' => 'form-horizontal form-label-left',
        'id' => 'schedule-form',
        'data-parsley-validate' => '',
        'templates' => [
            'inputContainer' => '{{content}}'
            ]
        ]) ?>
    <?= $this->Form->hidden('order_id', ['value' => $order->id]) ?>
    <?= $this->Form->unlockField('holidays') ?>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin cơ bản') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="time"><?= __('Thời gian dự kiến') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="form-control form-control-view">
                                <span id="start-date-div"><?= $start ?></span> ～ <span id="end-date-div"><?= $end ?></span>
                                <a href="javascript:;" onclick="refreshEndDate(<?= $schedule->id ?>)"><i class="fa fa-refresh"></i></a>
                                <div class="hidden">
                                    <?= $this->Form->control('end_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control',
                                        'value' => $end
                                        ])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="holiday"><?= __('Ngày nghỉ') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12 table-responsive">
                            <button type="button" class="btn btn-primary" onclick="showAddDayOffModal();">
                                <?= __('Thêm ngày nghỉ') ?>
                            </button>
                            <table class="table table-bordered custom-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                        <th scope="col" class="col-md-5"><?= __('Ngày nghỉ') ?></th>
                                        <th scope="col" class="col-md-3"><?= __('Loại ngày nghỉ') ?></th>
                                        <th scope="col" class="actions col-md-3"><?= __('Thao tác') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="day-off-container">
                                    <?php if (!empty($schedule->holidays)): ?>
                                        <?php foreach ($schedule->holidays as $key => $holiday): ?>
                                            <tr class="row-rec" id="row-day-off-<?= $key ?>">
                                                <td class="cell text-center stt-col"><?= $key + 1 ?></td>
                                                <td class="cell text-center">
                                                    <span class="holidayTxtDiv"><?= h($holiday->day) ?></span>
                                                    <div class="hidden">
                                                        <?= $this->Form->control('holidays.'.$key.'.id', [
                                                            'type' => 'text',
                                                            'label' => false, 
                                                            'class' => 'form-control holidayId',
                                                            ])?>
                                                        <?= $this->Form->control('holidays.'.$key.'.day', [
                                                            'type' => 'text',
                                                            'label' => false, 
                                                            'class' => 'form-control dayOffDate',
                                                            ])?>
                                                        <?= $this->Form->control('holidays.'.$key.'.type', [
                                                            'type' => 'text',
                                                            'label' => false, 
                                                            'class' => 'form-control dayOffType',
                                                            ])?>
                                                    </div>
                                                </td>
                                                <td class="cell text-center holidayType">
                                                    <?= $dayOffType[$holiday->type] ?>
                                                </td>
                                                <td class="cell actions">
                                                    <?= $this->Html->link(
                                                            '<i class="fa fa-2x fa-pencil"></i>', 
                                                            'javascript:;',
                                                            [
                                                                'escape' => false,
                                                                'onClick' => "showEditDayOffModal(this)"
                                                            ]) 
                                                        ?>
                                                        <?= $this->Html->link(
                                                            '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                            'javascript:;',
                                                            [
                                                                'escape' => false, 
                                                                'onClick' => "deleteDayOff(this, true)"
                                                            ]
                                                        )?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="teacher"><?= __('Giáo viên') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12 table-responsive">
                            <table class="table table-bordered custom-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                        <th scope="col" class="col-md-5"><?= __('Thời gian') ?></th>
                                        <th scope="col" class="col-md-6"><?= __('Giáo viên') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="cell text-center"><?= __('1') ?></td>
                                        <td class="cell text-center"><?= __('Ngày 1 đến ngày 19') ?></td>
                                        <td class="cell">
                                            <?= $this->Form->control('teacher1', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'required' => true,
                                                'class' => 'form-control',
                                                ]) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="cell text-center"><?= __('2') ?></td>
                                        <td class="cell text-center"><?= __('Ngày 20 đến ngày 22') ?></td>
                                        <td class="cell">
                                            <?= $this->Form->control('teacher2', [
                                                'type' => 'text',
                                                'required' => true,
                                                'label' => false, 
                                                'class' => 'form-control',
                                                ]) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="cell text-center"><?= __('3') ?></td>
                                        <td class="cell text-center"><?= __('Ngày 23 đến ngày 25') ?></td>
                                        <td class="cell">
                                            <?= $this->Form->control('teacher3', [
                                                'type' => 'text',
                                                'required' => true,
                                                'label' => false, 
                                                'class' => 'form-control',
                                                ])?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
<?php else: ?>
    <div class="form-horizontal form-label-left">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __('Thông tin cơ bản') ?></h3>
                        <div class="box-tools pull-right">
                            <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="time"><?= __('Thời gian dự kiến') ?>: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <div class="form-control form-control-view">
                                    <span id="start-date-div"><?= $start ?></span> ～ <span id="end-date-div"><?= $end ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="holiday"><?= __('Ngày nghỉ') ?>: </label>
                            <div class="col-md-7 col-sm-7 col-xs-12 table-responsive">
                                <table class="table table-bordered custom-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-5"><?= __('Ngày nghỉ') ?></th>
                                            <th scope="col" class="col-md-6"><?= __('Loại ngày nghỉ') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="day-off-container">
                                        <?php if (!empty($schedule->holidays)): ?>
                                            <?php foreach ($schedule->holidays as $key => $holiday): ?>
                                                <tr class="row-rec" id="row-day-off-<?= $key ?>">
                                                    <td class="cell text-center stt-col"><?= $key + 1 ?></td>
                                                    <td class="cell text-center">
                                                        <span class="holidayTxtDiv"><?= h($holiday->day) ?></span>
                                                    </td>
                                                    <td class="cell text-center holidayType">
                                                        <?= $dayOffType[$holiday->type] ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="teacher"><?= __('Giáo viên') ?>: </label>
                            <div class="col-md-7 col-sm-7 col-xs-12 table-responsive">
                                <table class="table table-bordered custom-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-5"><?= __('Thời gian') ?></th>
                                            <th scope="col" class="col-md-6"><?= __('Giáo viên') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="cell text-center"><?= __('1') ?></td>
                                            <td class="cell text-center"><?= __('Ngày 1 đến ngày 19') ?></td>
                                            <td class="cell text-center">
                                                <?= h($schedule->teacher1) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="cell text-center"><?= __('2') ?></td>
                                            <td class="cell text-center"><?= __('Ngày 20 đến ngày 22') ?></td>
                                            <td class="cell text-center">
                                                <?= h($schedule->teacher2) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="cell text-center"><?= __('3') ?></td>
                                            <td class="cell text-center"><?= __('Ngày 23 đến ngày 25') ?></td>
                                            <td class="cell text-center">
                                                <?= h($schedule->teacher3) ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="day-off-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">NGÀY NGHỈ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'day-off-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="time"><?= __('Ngày nghỉ') ?></label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <div class="input-group date input-picker" id="day-off-div">
                                    <?= $this->Form->control('modal.dayoff', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control',
                                        'placeholder' => 'dd-mm-yyyy',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-day-off'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-day-off"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="type"><?= __('Loại ngày nghỉ') ?></label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <?= $this->Form->control('modal.type', [
                                    'options' => $dayOffType, 
                                    'required' => true, 
                                    'label' => false, 
                                    'empty' => true,
                                    'data-parsley-errors-container' => '#error-modal-type',
                                    'data-parsley-class-handler' => '#select2-modal-type',
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                    ]) ?>
                                <span id="error-modal-type"></span>
                            </div>
                        </div>
                    <?= $this->Form->end() ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id='add-day-off-btn' onclick="addDayOff();">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="day-off-template" type="text/x-handlebars-template">
    <tr class="row-rec" id="row-day-off-{{counter}}">
        <td class="cell text-center stt-col">{{inc counter}}</td>
        <td class="cell text-center">
            <span class="holidayTxtDiv">{{dayOffTxt}}</span>
            <div class="hidden">
                <?= $this->Form->control('holidays.{{counter}}.day', [
                    'type' => 'text',
                    'label' => false, 
                    'class' => 'form-control dayOffDate',
                    'value' => '{{dayOffTxt}}'
                    ])?>
                <?= $this->Form->control('holidays.{{counter}}.type', [
                    'type' => 'text',
                    'label' => false, 
                    'class' => 'form-control dayOffType',
                    'value' => '{{dayOffTypeVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell text-center holidayType">
            {{dayOffTypeTxt}}
        </td>
        <td class="cell actions">
            <?= $this->Html->link(
                    '<i class="fa fa-2x fa-pencil"></i>', 
                    'javascript:;',
                    [
                        'escape' => false,
                        'onClick' => "showEditDayOffModal(this)"
                    ]) 
                ?>
                <?= $this->Html->link(
                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                    'javascript:;',
                    [
                        'escape' => false, 
                        'onClick' => "deleteDayOff(this)"
                    ]
                )?>
        </td>
    </tr>
</script>


<div class="modal fade" id="export-schedule-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">DANH SÁCH HỒ SƠ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12 table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-5"><?= __('Tên tài liệu') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Loại tài liệu') ?></th>
                                <th scope="col" class="actions col-md-3"><?= __('Thao tác') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="cell text-center"><?= __('1') ?></td>
                                <td class="cell"><?= __('1.29') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportScheduleRecord', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('2') ?></td>
                                <td class="cell"><?= __('4.8') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportSchedule', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('3') ?></td>
                                <td class="cell"><?= __('12') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportScheduleReport', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>