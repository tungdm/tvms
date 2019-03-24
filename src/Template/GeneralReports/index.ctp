<?php
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');

$studentStatus = Configure::read('studentStatus');
$eduLevel = Configure::read('eduLevel');
$eduLevel = array_map('array_shift', $eduLevel);
$gender = Configure::read('gender');
$lessons = Configure::read('lessons');
$financeStatus = Configure::read('financeStatus');
$physResult = Configure::read('physResult');
$interviewResult = Configure::read('interviewResult');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('report.js', ['block' => 'scriptBottom']);
?>

<?php $this->start('content-header'); ?>
    <?php $this->assign('title', 'Xuất báo cáo'); ?>
    <h1><?= __('XUẤT BÁO CÁO') ?></h1>
    <button class="btn btn-success export-report-btn" type="button">Tải về</button>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Xuất báo cáo</li>
    </ol>
<?php $this->end(); ?>


<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('CHỌN NỘI DUNG') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12 col-xs-12 table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Thông tin') ?></th>
                                <th scope="col" class="col-md-8"><?= __('Điều kiện') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $this->Form->create(null, [
                                'class' => 'form-horizontal form-label-left', 
                                'url' => ['action' => 'exportGeneralInfo'],
                                'id' => 'report-form', 
                                'data-parsley-validate' => '',
                                'templates' => [
                                    'inputContainer' => '{{content}}'
                                    ]
                                ]) ?>
                            <?= $this->Form->unlockField('std') ?>
                            <?= $this->Form->unlockField('order') ?>
                            <?= $this->Form->unlockField('add') ?>
                            <tr>
                                <td colspan="3">
                                    I. THÔNG TIN CÁ NHÂN
                                </td>
                            </tr>
                            <tr class="group-1">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="std[status_chk]" type="checkbox" class="js-switch student-group">  Trạng thái của lao động
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <?= $this->Form->control('std.status', [
                                            'options' => $studentStatus, 
                                            'empty' => true,
                                            'label' => false, 
                                            'disabled' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme filter'
                                            ]) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="group-1">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="std[gender_chk]" type="checkbox" class="js-switch student-group">  Giới tính
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <?= $this->Form->control('std.gender', [
                                            'options' => $gender, 
                                            'empty' => true,
                                            'label' => false, 
                                            'disabled' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme filter'
                                            ]) ?>
                                    </div>
                                </td>
                            </tr>
                            <?php if (in_array($role['name'], ['admin', 'accountant', 'recruiter'])): ?>
                                <tr class="group-1">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="std[presenter_chk]" type="checkbox" class="js-switch student-group">  Người giới thiệu
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('std.presenter', [
                                                'options' => $presenters, 
                                                'empty' => true,
                                                'label' => false, 
                                                'disabled' => true,
                                                'class' => 'form-control col-md-7 col-xs-12 select2-theme filter'
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group-1">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="std[edulevel_chk]" type="checkbox" class="js-switch student-group">  Trình độ học vấn
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('std.edulevel', [
                                                'options' => $eduLevel, 
                                                'empty' => true,
                                                'label' => false, 
                                                'disabled' => true,
                                                'class' => 'form-control col-md-7 col-xs-12 select2-theme filter'
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr class="group-1">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="std[birthday_chk]" type="checkbox" class="js-switch student-group">  Ngày sinh
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="input-group date input-picker" id="std-birthday-from-div">
                                            <?= $this->Form->control('std.birthday_from', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control filter from-date-picker',
                                                'data-parsley-before-date' => '#std-birthday-to',
                                                'disabled' => true,
                                                'placeholder' => 'dd-mm-yyyy',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="input-group date input-picker" id="std-birthday-to-div">
                                            <?= $this->Form->control('std.birthday_to', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control filter to-date-picker',
                                                'data-parsley-after-date' => '#std-birthday-from',
                                                'disabled' => true,
                                                'placeholder' => 'dd-mm-yyyy',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php if (in_array($role['name'], ['admin', 'accountant', 'recruiter', 'manager'])): ?>
                                <tr class="group-1">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="std[phone_chk]" type="checkbox" class="js-switch student-group">  Số điện thoại
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('std.phone', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'disabled' => true,
                                                'class' => 'form-control col-md-7 col-xs-12 filter',
                                                'placeholder' => 'Nhập số điện thoại của lao động'
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (in_array($role['name'], ['admin', 'accountant', 'recruiter', 'manager', 'teacher'])): ?>
                                <tr class="group-1">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="std[hometown_chk]" type="checkbox" class="js-switch student-group">  Quê quán
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('std.hometown', [
                                                'options' => $cities, 
                                                'empty' => true,
                                                'disabled' => true,
                                                'label' => false, 
                                                'class' => 'form-control col-md-7 col-xs-12 select2-theme filter'
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group-1">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="std[enrolled_date_chk]" type="checkbox" class="js-switch student-group">  Thời gian nhập học
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <div class="input-group date input-picker" id="enrolled-from">
                                                <?= $this->Form->control('std.enrolled_from', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control filter from-date-picker',
                                                    'disabled' => true,
                                                    'data-parsley-before-date' => '#std-enrolled-to',
                                                    'placeholder' => 'dd-mm-yyyy',
                                                    ])?>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <div class="input-group date input-picker" id="enrolled-to">
                                                <?= $this->Form->control('std.enrolled_to', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control filter to-date-picker',
                                                    'data-parsley-after-date' => '#std-enrolled-from',
                                                    'disabled' => true,
                                                    'placeholder' => 'dd-mm-yyyy',
                                                    ])?>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td colspan="3">II. PHỎNG VẤN - ĐƠN HÀNG</td>
                            </tr>
                            <tr class="group-2">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="order[order_chk]" type="checkbox" class="js-switch order-group">  Đơn hàng
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <?= $this->Form->control('order.id', [
                                            'options' => $orders, 
                                            'empty' => true,
                                            'label' => false, 
                                            'disabled' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme filter', 
                                            ]) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="group-2">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="order[interview_date_chk]" type="checkbox" class="js-switch order-group">  Ngày phỏng vấn
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="input-group date input-picker" id="order-interview-date-from-div">
                                            <?= $this->Form->control('order.interview_date_from', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control filter from-date-picker',
                                                'data-parsley-before-date' => '#order-interview-date-to',
                                                'disabled' => true,
                                                'placeholder' => 'dd-mm-yyyy',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="input-group date input-picker" id="order-interview-date-to-div">
                                            <?= $this->Form->control('order.interview_date_to', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control filter to-date-picker',
                                                'data-parsley-after-date' => '#order-interview-date-from',
                                                'disabled' => true,
                                                'placeholder' => 'dd-mm-yyyy',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="group-2">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="order[interview_result_chk]" type="checkbox" class="js-switch order-group">  Kết quả phỏng vấn
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <?= $this->Form->control('order.interview_result', [
                                            'label' => false, 
                                            'options' => $interviewResult,
                                            'empty' => true,
                                            'disabled' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme filter', 
                                            ]) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="group-2">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell">
                                    <div class="checkbox">
                                        <label>
                                            <input name="order[departure_month_chk]" type="checkbox" class="js-switch order-group">  Ngày xuất cảnh dự kiến
                                        </label>
                                    </div>
                                </td>
                                <td class="cell">
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="input-group date input-picker month-mode" id="departure-month-from-div">
                                            <?= $this->Form->control('order.departure_month_from', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control filter from-date-picker',
                                                'data-parsley-before-date' => '#order-departure-month-to',
                                                'disabled' => true,
                                                'placeholder' => 'mm-yyyy',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="input-group date input-picker month-mode" id="departure-month-to-div">
                                            <?= $this->Form->control('order.departure_month_to', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control filter to-date-picker',
                                                'data-parsley-after-date' => '#order-departure-month-from',
                                                'disabled' => true,
                                                'placeholder' => 'mm-yyyy',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php if (in_array($role['name'], ['admin', 'staff'])): ?>
                                <tr class="group-2">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="order[departure_date_chk]" type="checkbox" class="js-switch order-group">  Ngày xuất cảnh chính thức
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <div class="input-group date input-picker" id="order-departure-date-from-div">
                                                <?= $this->Form->control('order.departure_date_from', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control filter from-date-picker',
                                                    'data-parsley-before-date' => '#order-departure-date-to',
                                                    'disabled' => true,
                                                    'placeholder' => 'dd-mm-yyyy',
                                                    ])?>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <div class="input-group date input-picker" id="order-departure-date-to-div">
                                                <?= $this->Form->control('order.departure_date_to', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control filter to-date-picker',
                                                    'data-parsley-after-date' => '#order-departure-date-from',
                                                    'disabled' => true,
                                                    'placeholder' => 'dd-mm-yyyy',
                                                    ])?>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group-2">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="order[discompany_chk]" type="checkbox" class="js-switch order-group">  Công ty phái cử
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('order.discompany', [
                                                'label' => false, 
                                                'options' => $disCompanies,
                                                'empty' => true,
                                                'disabled' => true,
                                                'class' => 'form-control col-md-7 col-xs-12 select2-theme filter', 
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group-2">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="order[company_chk]" type="checkbox" class="js-switch order-group">  Công ty tiếp nhận
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('order.company', [
                                                'label' => false, 
                                                'options' => $companies,
                                                'empty' => true,
                                                'disabled' => true,
                                                'class' => 'form-control col-md-7 col-xs-12 select2-theme filter', 
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php if (in_array($role['name'], ['admin', 'staff', 'manager', 'teacher', 'recruiter'])): ?>
                                <tr class="group-2">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell">
                                        <div class="checkbox">
                                            <label>
                                                <input name="order[guild_chk]" type="checkbox" class="js-switch order-group">  Nghiệp đoàn quản lý
                                            </label>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <?= $this->Form->control('order.guild', [
                                                'label' => false, 
                                                'options' => $guilds,
                                                'empty' => true,
                                                'disabled' => true,
                                                'class' => 'form-control col-md-7 col-xs-12 select2-theme filter', 
                                                ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <tr>
                                <td colspan="4">III. THÔNG TIN KÈM THEO</td>
                            </tr>
                            <?php if (in_array($role['name'], ['admin', 'accountant', 'recruiter'])): ?>
                                <tr class="group-3">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell" colspan="3">
                                        <div class="checkbox">
                                            <label>
                                                <input name="add[deposit_chk]" type="checkbox" class="js-switch">  Tiền cọc
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group-3">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell" colspan="3">
                                        <div class="checkbox">
                                            <label>
                                                <input name="add[healthcheck_chk]" type="checkbox" class="js-switch">  Ngày khám sức khỏe
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group-3">
                                    <td class="cell text-center stt-col"></td>
                                    <td class="cell" colspan="3">
                                        <div class="checkbox">
                                            <label>
                                                <input name="add[healthcheck_result_chk]" type="checkbox" class="js-switch">  Kết quả khám sức khỏe
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr class="group-3">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell" colspan="3">
                                    <div class="checkbox">
                                        <label>
                                            <input name="add[jclass_chk]" type="checkbox" class="js-switch jclass-group-main">  Lớp học
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr class="group-3">
                                <td class="cell text-center stt-col"></td>
                                <td class="cell" colspan="3">
                                    <div class="checkbox">
                                        <label>
                                            <input name="add[lesson_chk]" type="checkbox" class="js-switch jclass-group">  Bài học
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <?= $this->Form->end() ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
