<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$currentUser = $this->request->session()->read('Auth.User');
$gender = Configure::read('gender');
$yesNoQuestion = Configure::read('yesNoQuestion');
$bank = Configure::read('bank');
$input_test = array_keys(Configure::read('input_test'));
$iqtest = Configure::read('iqtest');

$country = Configure::read('country');
$country = array_map('array_shift', $country);

$district = Configure::read('district');
$ward = Configure::read('ward');

$eduLevel = Configure::read('eduLevel');
$eduLevel = array_map('array_shift', $eduLevel);

$language = Configure::read('language');
$language = array_map('array_shift', $language);

$document = Configure::read('document');

$studentStatus = Configure::read('studentStatus');

$maritalStatus = Configure::read('maritalStatus');
$maritalStatus = array_map('array_shift', $maritalStatus);

$studentSubject = Configure::read('studentSubject');
$religion = Configure::read('religion');
$nation = Configure::read('nation');
$addressType = array_keys(Configure::read('addressType'));
$cardType = array_keys(Configure::read('cardType'));
$bloodGroup = Configure::read('bloodGroup');
$preferredHand = Configure::read('preferredHand');

$relationship = Configure::read('relationship');
$relationship = array_map('array_shift', $relationship);

$smokedrink = Configure::read('smokedrink');
$smokedrink = array_map('array_shift', $smokedrink);

$now = Time::now();

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->css('cropper.css', ['block' => 'styleTop']);
$this->Html->css('student.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('cropper.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('student.js', ['block' => 'scriptBottom']);
?>

<?= $this->Form->create($student, [
    'class' => 'form-horizontal form-label-left form-check-status', 
    'id' => 'create-student-form', 
    'type' => 'file',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) 
?>
<?= $this->Form->unlockField('b64code') ?>
<?= $this->Form->unlockField('image') ?>
<?= $this->Form->unlockField('addresses') ?>
<?= $this->Form->unlockField('expectationJobs') ?>
<?= $this->Form->unlockField('families') ?>
<?= $this->Form->unlockField('educations') ?>
<?= $this->Form->unlockField('experiences') ?>
<?= $this->Form->unlockField('language_abilities') ?>
<?= $this->Form->unlockField('documents') ?>
<?= $this->Form->unlockField('iq_tests') ?>

<?php if ($action == "add"): ?>
    <?php $this->assign('title', 'Thêm mới lao động'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('THÊM MỚI LAO ĐỘNG') ?></h1>
        <button class="btn btn-success create-student-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách lao động'), [
                    'controller' => 'Students',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Thêm mới lao động</li>
        </ol>
    <?php $this->end(); ?>
<?php elseif ($action == "edit"): ?>
    <?php $this->assign('title', $student->fullname . ' - Cập nhật thông tin lao động'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('CẬP NHẬT THÔNG TIN LAO ĐỘNG') ?></h1>
        <button class="btn btn-success create-student-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách lao động'), [
                    'controller' => 'Students',
                    'action' => 'index']) ?>
            </li>
            <li class="active"><?= $student->fullname ?></li>
        </ol>
    <?php $this->end(); ?>
<?php endif; ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($action === 'edit'): ?>
                <?php if ($student->status > 1): ?>
                <li data-toggle="tooltip" title="Xuất hồ sơ">
                    <a class="zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out" 
                    data-toggle="modal" 
                    data-target="#export-student-modal">
                        <i class="fa fa-book" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                        ['action' => 'view', $student->id],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Xem chi tiết',
                            'escape' => false
                        ]) ?>
                </li>
                <?php endif; ?>
                <li>
                    <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                        ['action' => 'delete', $student->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Xóa',
                            'confirm' => __('Bạn có chắc chắn muốn xóa lao động {0}?', $student->fullname)
                        ]) ?>
                </li>
            <?php endif; ?>
            <li>
                <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out create-student-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>


<div class="clearfix"></div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <ul id="student-tabs" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true"><?= __('Thông tin cơ bản') ?></a>
            </li>
            <li role="presentation" class="">
                <a href="#tab_content2" role="tab" id="personal-document-tab" data-toggle="tab" aria-expanded="false"><?= __('Giấy tờ tùy thân') ?></a>
            </li>
            <li role="presentation" class="">
                <a href="#tab_content3" role="tab" id="experience-tab" data-toggle="tab" aria-expanded="false"><?= __('Học tập - Làm việc') ?></a>
            </li>
            <li role="presentation" class="">
                <a href="#tab_content4" role="tab" id="document-tab" data-toggle="tab" aria-expanded="false"><?= __('Hồ sơ bổ sung') ?></a>
            </li>
            <li role="presentation" class="">
                <a href="#tab_content5" role="tab" id="input-test-tab" data-toggle="tab" aria-expanded="false"><?= __('Kiểm tra đầu vào') ?></a>
            </li>
            <?php if (!empty($student->id)): ?>
            <li role="presentation" class="">
                <a href="#tab_content6" role="tab" id="histories-tab" data-toggle="tab" aria-expanded="false"><?= __('Ghi chú hoạt động') ?></a>
            </li>
            <?php endif; ?>
        </ul>
        <div id="student-tab-content" class="tab-content">
            <div role="tabpanel" class="tab-pane root-tab-pane fade active in" id="tab_content1">
                <div class="rows">
                    <div class="col-md-6 col-xs-12 left-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Sơ yếu lý lịch') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fullname"><?= __('Họ tên (VN)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('fullname', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true,
                                            'placeholder' => 'Nhập họ tên của lao động bằng tiếng Việt'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fullname_kata"><?= __('Họ tên (JP)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('fullname_kata', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập họ tên phiên âm của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="gender"><?= __('Giới tính') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('gender', [
                                            'options' => $gender, 
                                            'required' => true, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-gender',
                                            'data-parsley-class-handler' => '#select2-gender',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-gender"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="exempt"><?= __('Đăng ký miễn học') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('exempt', [
                                            'options' => $yesNoQuestion, 
                                            'required' => true, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-exempt',
                                            'data-parsley-class-handler' => '#select2-exempt',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-exempt"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="email"><?= __('Email') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('email', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập địa chỉ mail của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="status"><?= __('Trạng thái') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?php 
                                            $defaultStatus = $student->status;
                                            if ($defaultStatus == '1') {
                                                $defaultStatus = '2';
                                            }
                                        ?>
                                        <?= $this->Form->control('status', [
                                            'options' => $studentStatus, 
                                            'required' => true, 
                                            'empty' => true,
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-status',
                                            'data-parsley-class-handler' => '#select2-student-status',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                            'id' => 'student-status',
                                            'value' => $defaultStatus
                                            ]) ?>
                                        <span id="error-status"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="enrolled_date"><?= __('Ngày nhập học') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="enrolled-date">
                                            <?= $this->Form->control('enrolled_date', [
                                                'type' => 'text',
                                                'label' => false,
                                                'class' => 'form-control',
                                                'required' => true, 
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-errors-container' => '#error-enrolled-date'
                                            ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-enrolled-date"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="image"><?= __('Hình ảnh') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('image', [
                                            'type' => 'file',
                                            'accept' => 'image/*',
                                            'label' => false, 
                                            'class' => 'form-control avatar-image col-md-7 col-xs-12',
                                            'onchange' => 'readURL(this)'
                                            ]) ?>
                                        <div id="cropped_result" class="col-md-7 col-xs-12">
                                            <?php if(!empty($student->image)):?>
                                            <?= $this->Html->image($student->image) ?>
                                            <?php endif; ?>
                                        </div> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="marial"><?= __('Tình trạng hôn nhân') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('marital_status', [
                                            'options' => $maritalStatus, 
                                            'required' => true, 
                                            'empty' => true, 
                                            'label' => false,
                                            'data-parsley-errors-container' => '#error-marital-status',
                                            'data-parsley-class-handler' => '#select2-marital-status',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-marital-status"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="subject"><?= __('Đối tượng') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('subject', [
                                            'options' => $studentSubject, 
                                            'empty' => true, 
                                            'label' => false,
                                            'data-parsley-errors-container' => '#error-student-subject',
                                            'data-parsley-class-handler' => '#select2-subject',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-student-subject"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="phone"><?= __('Số điện thoại') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('phone', [
                                            'label' => false, 
                                            'required' => true, 
                                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập số điện thoại của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="birthday"><?= __('Ngày sinh') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="candidate-birthday">
                                            <?= $this->Form->control('birthday', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'required' => true,
                                                'data-parsley-errors-container' => '#error-birthday'
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-birthday"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="edu_level"><?= __('Trình độ học vấn') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('educational_level', [
                                            'options' => $eduLevel, 
                                            'required' => true, 
                                            'label' => false, 
                                            'empty' => true,
                                            'data-parsley-errors-container' => '#error-educational-level',
                                            'data-parsley-class-handler' => '#select2-educational-level',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-educational-level"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="nation"><?= __('Dân tộc') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('nation', [
                                            'options' => $nation, 
                                            'label' => false, 
                                            'empty' => true, 
                                            'data-parsley-errors-container' => '#error-nation',
                                            'data-parsley-class-handler' => '#select2-nation',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-nation"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="religion"><?= __('Tôn giáo') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('religion', [
                                            'options' => $religion, 
                                            'label' => false, 
                                            'empty' => true,
                                            'data-parsley-errors-container' => '#error-religion',
                                            'data-parsley-class-handler' => '#select2-religion',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-religion"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="country"><?= __('Quốc tịch') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('country', [
                                            'options' => $country, 
                                            'label' => false, 
                                            'empty' => true,
                                            'data-parsley-errors-container' => '#error-country',
                                            'data-parsley-class-handler' => '#select2-country',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                            'value' => '01'
                                            ]) ?>
                                        <span id="error-country"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="presenter"><?= __('Người giới thiệu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('presenter_id', [
                                            'options' => $presenters, 
                                            'label' => false, 
                                            'empty' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="expectationJobs"><?= __('Nghề mong muốn') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?php 
                                            $expectArr = explode(',', $student->expectation);
                                            array_shift($expectArr);
                                            array_pop($expectArr);
                                        ?>
                                        <?= $this->Form->control('expectationJobs[]', [
                                            'options' => $jobs,
                                            'label' => false,
                                            'empty' => true,
                                            'multiple' => 'multiple',
                                            'class' => 'form-control col-md-7 col-xs-12 select-job',
                                            'value' => $expectArr
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Thông tin nộp hồ sơ') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="is_lived_in_japan"><?= __('Đã từng đi nhật') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('is_lived_in_japan', [
                                            'options' => $yesNoQuestion, 
                                            'required' => true, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-lived-japan',
                                            'data-parsley-class-handler' => '#select2-is-lived-in-japan',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                            'value' => $student->is_lived_in_japan ?? 'N'
                                            ]) ?>
                                        <span id="error-lived-japan"></span>
                                    </div>
                                </div>
                                <div class="form-group time-lived-jp<?php if (empty($student->is_lived_in_japan) || $student->is_lived_in_japan !== 'Y'): ?> hidden <?php endif; ?>">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="time_lived_in_japan"><?= __('Thời gian') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="time-lived">
                                            <?php if($student->is_lived_in_japan === 'Y'): ?>
                                            <?= $student->lived_from ?> ～ <?= $student->lived_to ?>
                                            <?php endif;?>
                                        </div>
                                        <div class="hidden">
                                            <?= $this->Form->control('lived_from', [
                                                'type' => 'text',
                                                'label' => false,
                                                'class' => 'form-control',
                                                ])?>
                                            <?= $this->Form->control('lived_to', [
                                                'type' => 'text',
                                                'label' => false,
                                                'class' => 'form-control',
                                                ])?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ln_solid"></div>
                                
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="reject"><?= __('Từng bị từ chối lưu trú') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('reject_stay', [
                                            'options' => $yesNoQuestion, 
                                            'required' => true, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-reject-stay',
                                            'data-parsley-class-handler' => '#select2-reject-stay',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                            'value' => $student->reject_stay ?? 'N'
                                            ]) ?>
                                        <span id="error-reject-stay"></span>
                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="purpose"><?= __('Mục đích XKLĐ') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('purpose', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập mục đích đi xuất khẩu lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="salary"><?= __('Thu nhập hiện tại') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="col-md-5" style="padding-left: 0px">
                                            <?= $this->Form->control('salary', [
                                                'label' => false,
                                                'type' => 'text',
                                                'data-parsley-type' => 'number',
                                                'class' => 'form-control col-md-7 col-xs-12',
                                                'placeholder' => '万円'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-control form-control-view">đơn vị: 万円/月</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="saving_expected"><?= __('Số tiền mong muốn') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="col-md-5" style="padding-left: 0px">
                                            <?= $this->Form->control('saving_expected', [
                                                'label' => false,
                                                'class' => 'form-control col-md-7 col-xs-12',
                                                'placeholder' => '万円'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-control form-control-view">đơn vị: 万円</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="after_plan"><?= __('Dự định sau khi về') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('after_plan', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập dự định sau khi về nước'
                                            ]) ?>
                                    </div>
                                </div>

                                <div class="ln_solid"></div>

                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="strength"><?= __('Điểm mạnh') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('strength', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập thông tin điểm mạnh'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="weakness"><?= __('Điểm yếu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('weakness', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập thông tin điểm yếu'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="genitive"><?= __('Tính cách') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('genitive', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập tính cách của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 right-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Địa chỉ cư trú') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <ul id="address-tabs" class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#household" data-toggle="tab"><?= __('Hộ khẩu thường trú') ?></a>
                                    </li>
                                    <li>
                                        <a href="#current-address" data-toggle="tab"><?= __('Nơi ở hiện tại') ?></a>
                                    </li>
                                </ul>
                                <div id="address-tabs-content" class="tab-content">
                                    <div class="tab-pane fade in active" id="household">
                                        <?php if (!empty($student->addresses)): ?>
                                        <?= $this->Form->hidden('addresses.0.id', ['value' => $student->addresses[0]->id]) ?>
                                        <?php endif; ?>
                                        <?= $this->Form->hidden('addresses.0.type', ['value' => $addressType[0]]) ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.0.city_id', [
                                                    'options' => $cities, 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-city-0',
                                                    'data-parsley-class-handler' => '#select2-addresses-0-city-id',
                                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme select-city'
                                                    ]) ?>
                                                <span id="error-city-0"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="district"><?= __('Quận/Huyện') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?php if (!empty($student->addresses[0]->city_id)): ?>
                                                <?= $this->Form->control('addresses.0.district_id', [
                                                    'options' => $districts[0], 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-district-0',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-district select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.0.district_id', [
                                                    'options' => [], 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'disabled' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-district-0',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-district'
                                                    ]) ?>
                                                <?php endif; ?>
                                                <span id="error-district-0"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="ward"><?= __('Phường/Xã') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?php if (!empty($student->addresses[0]->district_id)): ?>
                                                <?= $this->Form->control('addresses.0.ward_id', [
                                                    'options' => $wards[0], 
                                                    'required' => true,
                                                    'empty' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-ward-0',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-ward select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.0.ward_id', [
                                                    'options' => [], 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'disabled' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-ward-0',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-ward'
                                                    ]) ?>
                                                <?php endif; ?>
                                                <span id="error-ward-0"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="street"><?= __('Số nhà - Đường') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.0.street', [
                                                    'label' => false,
                                                    'class' => 'form-control col-md-7 col-xs-12',
                                                    'placeholder' => 'Nhập số nhà, đường'
                                                    ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="current-address">
                                        <?php if (!empty($student->addresses[1])): ?>
                                        <?= $this->Form->hidden('addresses.1.id', ['value' => $student->addresses[1]->id]) ?>
                                        <?php endif; ?>
                                        <?= $this->Form->hidden('addresses.1.type', ['value' => $addressType[1]]) ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.1.city_id', [
                                                    'options' => $cities, 
                                                    'required' => true,
                                                    'empty' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-city-1',
                                                    'data-parsley-class-handler' => '#select2-addresses-1-city-id',
                                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme select-city'
                                                    ]) ?>
                                                <span id="error-city-1"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="district"><?= __('Quận/Huyện') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                            <?php if (!empty($student->addresses[1]->city_id)): ?>
                                                <?= $this->Form->control('addresses.1.district_id', [
                                                    'options' => $districts[1], 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-district-1',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-district select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.1.district_id', [
                                                    'options' => [], 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'disabled' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-district-1',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-district'
                                                    ]) ?>
                                                <?php endif; ?>
                                                <span id="error-district-1"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="ward"><?= __('Phường/Xã') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                            <?php if (!empty($student->addresses[1]->district_id)): ?>
                                                <?= $this->Form->control('addresses.1.ward_id', [
                                                    'options' => $wards[1], 
                                                    'required' => true,
                                                    'empty' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-ward-1',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-ward select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.1.ward_id', [
                                                    'options' => [], 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'disabled' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-ward-1',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-ward'
                                                    ]) ?>
                                                <?php endif; ?>
                                                <span id="error-ward-1"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="street"><?= __('Số nhà - Đường') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.1.street', [
                                                    'label' => false,
                                                    'class' => 'form-control col-md-7 col-xs-12',
                                                    'placeholder' => 'Nhập số nhà, đường'
                                                    ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 right-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Tình trạng sức khỏe') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="height"><?= __('Chiều cao') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="col-md-5" style="padding-left: 0px">
                                            <?= $this->Form->control('height', [
                                                'label' => false,
                                                'min' => 0,
                                                'class' => 'form-control col-md-7 col-xs-12', 
                                                'required' => true,
                                                'placeholder' => 'Nhập chiều cao của lao động'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-control form-control-view">đơn vị: centimet</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="weight"><?= __('Cân nặng (kg)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="col-md-5" style="padding-left: 0px">
                                            <?= $this->Form->control('weight', [
                                                'label' => false,
                                                'min' => 0,
                                                'class' => 'form-control col-md-7 col-xs-12', 
                                                'required' => true,
                                                'placeholder' => 'Nhập cân nặng của lao động'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-control form-control-view">đơn vị: kilogram</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="blood_group"><?= __('Nhóm máu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('blood_group', [
                                            'options' => $bloodGroup, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-blood-group',
                                            'data-parsley-class-handler' => '#select2-blood-group',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-blood-group"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="right_hand_force"><?= __('Lực bóp tay phải') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('right_hand_force', [
                                            'label' => false,
                                            'min' => 0,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập lực bóp tay phải của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="left_hand_force"><?= __('Lực bóp tay trái') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('left_hand_force', [
                                            'label' => false,
                                            'min' => 0,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập lực bóp tay trái của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="back_force"><?= __('Lực kéo lưng') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('back_force', [
                                            'label' => false,
                                            'min' => 0,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập lực kéo lưng của lao động'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="left_eye_sight"><?= __('Thị lực (trái)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('left_eye_sight', [
                                            'label' => false,
                                            'type' => 'text',
                                            'min' => 0,
                                            'max' => 10,
                                            'data-parsley-type' => 'number',
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập thị lực mắt trái (đo tại trường)'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-4 col-sm-offset-4 col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('left_eye_sight_hospital', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10,
                                            'type' => 'text',
                                            'data-parsley-type' => 'number',
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập thị lực mắt trái (đo tại bệnh viện)'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="right_eye_sight"><?= __('Thị lực (phải)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('right_eye_sight', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10, 
                                            'type' => 'text',
                                            'data-parsley-type' => 'number',
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập thị lực mắt phải (đo tại trường)'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-4 col-sm-offset-4 col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('right_eye_sight_hospital', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10, 
                                            'type' => 'text',
                                            'data-parsley-type' => 'number',
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Nhập thị lực mắt phải (đo tại bệnh viện)'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="color_blind"><?= __('Mù màu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('color_blind', [
                                            'options' => $yesNoQuestion, 
                                            'label' => false, 
                                            'empty' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="preferred_hand"><?= __('Tay thuận') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('preferred_hand', [
                                            'options' => $preferredHand, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-preferred-hand',
                                            'data-parsley-class-handler' => '#select2-preferred-hand',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-preferred-hand"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="smoke"><?= __('Hút thuốc') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('smoke', [
                                            'options' => $smokedrink, 
                                            'label' => false, 
                                            'empty' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="drink"><?= __('Uống rượu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('drink', [
                                            'options' => $smokedrink, 
                                            'label' => false, 
                                            'empty' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Quan hệ gia đình') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-family" id="add-member-top" onclick="showAddMemberModal();">
                                    <?= __('Thêm thành viên') ?>
                                </button>
                                <table class="table table-bordered custom-table family-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Họ tên') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Ngày sinh') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Quan hệ') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Nghề nghiệp') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Số ĐT') ?></th>
                                            <th scope="col" class="actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="family-container">
                                        <?php if (!empty($student->families)): ?>
                                        <?php $counter = 0 ?>
                                        <?php foreach ($student->families as $key => $value): ?>
                                        <div class="hidden member-id" id="member-<?=$counter?>-id">
                                            <?= $this->Form->hidden('families.'  . $key . '.id', ['value' => $value->id]) ?>
                                        <div>
                                        <tr class="row-member" id="row-member-<?=$counter?>">
                                            <td class="cell col-md-1 stt-col">
                                                <?php echo $counter + 1; ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->fullname ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('families.' . $key . '.fullname', [
                                                        'label' => false, 
                                                        'required' => true,
                                                        'class' => 'form-control fullname',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->birthday ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('families.' . $key . '.birthday', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control birthday',
                                                        ])?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $relationship[$value->relationship] ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('families.' . $key . '.relationship', [
                                                        'options' => $relationship,
                                                        'label' => false,
                                                        'class' => 'form-control relationship'
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->job->job_name ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('families.' . $key . '.job_id', [
                                                        'options' => $jobs,
                                                        'label' => false,
                                                        'class' => 'form-control job_id',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('families.' . $key . '.address', [
                                                    'label' => false,
                                                    'class' => 'form-control address',
                                                    ]) ?>
                                                <?= $this->Form->control('families.' . $key . '.living_at', [
                                                    'options' => $country,
                                                    'label' => false,
                                                    'class' => 'form-control living_at',
                                                    ])  ?>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('families.' . $key . '.bank_num', [
                                                    'label' => false,
                                                    'class' => 'form-control bank_num',
                                                    ]) ?>
                                                <?= $this->Form->control('families.' . $key . '.bank_name', [
                                                    'options' => $bank,
                                                    'label' => false,
                                                    'class' => 'form-control bank_name',
                                                    ]) ?>
                                                <?= $this->Form->control('families.' . $key . '.bank_branch', [
                                                    'label' => false,
                                                    'class' => 'form-control bank_branch',
                                                    ]) ?>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('families.' . $key . '.cmnd_num', [
                                                    'label' => false,
                                                    'class' => 'form-control cmnd_num',
                                                    ]) ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= h($this->Phone->makeEdit($value->phone)) ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('families.' . $key . '.phone', [
                                                        'label' => false, 
                                                        'class' => 'form-control phone',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell action-btn actions">
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-pencil"></i>', 
                                                    'javascript:;',
                                                    [
                                                        'escape' => false,
                                                        'onClick' => "showEditMemberModal(this)"
                                                    ]) 
                                                ?>
                                                
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                    'javascript:;',
                                                    [
                                                        'escape' => false, 
                                                        'onClick' => "removeMember(this, true)"
                                                    ]
                                                )?>
                                                <?php $counter++; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?> 
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content2">
                <div class="rows">
                    <div class="col-md-6 col-xs-12 left-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Chứng minh nhân dân') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->cards)): ?>
                                <?= $this->Form->hidden('cards.0.id', ['value' => $student->cards[0]->id]) ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('cards.0.type', ['value' => $cardType[0]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="code"><?= __('Số CMND') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.0.code', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập số chứng minh nhân dân'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="from_date"><?= __('Ngày cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="cmnd-from-date">
                                            <?= $this->Form->control('cards.0.from_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-errors-container' => '#error-cmnd-from-date'
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-cmnd-from-date"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="issued_at"><?= __('Nơi cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.0.issued_at', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập nơi cấp CMND'
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Thị thực (Visa)') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->cards)): ?>
                                <?= $this->Form->hidden('cards.2.id', ['value' => $student->cards[2]->id]) ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('cards.2.type', ['value' => $cardType[2]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="code"><?= __('Số Visa') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.2.code', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12 visa-group',
                                            'data-parsley-validate-if-empty' => '',
                                            'data-parsley-check-empty' => '.visa-group',
                                            'placeholder' => 'Nhập số Visa'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="registration_date"><?= __('Ngày đăng kí') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="visa-registration-date">
                                            <?= $this->Form->control('cards.2.registration_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'from-date-picker form-control visa-group',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-validate-if-empty' => '',
                                                'data-parsley-errors-container' => '#error-visa-registration-date',
                                                'data-parsley-before-date' => '#cards-2-from-date',
                                                'data-parsley-check-empty' => '.visa-group',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-visa-registration-date"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="from_date"><?= __('Ngày cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="visa-from-date">
                                            <?= $this->Form->control('cards.2.from_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'from-date-picker form-control visa-group',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-validate-if-empty' => '',
                                                'data-parsley-errors-container' => '#error-visa-from-date',
                                                'data-parsley-before-date' => '#cards-2-to-date',
                                                'data-parsley-check-empty' => '.visa-group',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-visa-from-date"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="to_date"><?= __('Ngày hết hạn') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="visa-to-date">
                                            <?= $this->Form->control('cards.2.to_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'to-date-picker form-control visa-group',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-validate-if-empty' => '',
                                                'data-parsley-errors-container' => '#error-visa-to-date',
                                                'data-parsley-after-date' => '#cards-2-from-date',
                                                'data-parsley-check-empty' => '.visa-group',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-visa-to-date"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 right-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Hộ chiếu (Passport)') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->cards)): ?>
                                <?= $this->Form->hidden('cards.1.id', ['value' => $student->cards[1]->id]) ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('cards.1.type', ['value' => $cardType[1]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="code"><?= __('Số hộ chiếu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.1.code', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12 passport-group',
                                            'data-parsley-validate-if-empty' => '',
                                            'data-parsley-check-empty' => '.passport-group',
                                            'placeholder' => 'Nhập số hộ chiếu'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="from_date"><?= __('Ngày cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="passport-from-date">
                                            <?= $this->Form->control('cards.1.from_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'from-date-picker form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-errors-container' => '#error-passport-from-date',
                                                'data-parsley-validate-if-empty' => '',
                                                'data-parsley-check-empty' => '.passport-group',
                                                'data-parsley-before-date' => '#cards-1-to-date',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-passport-from-date"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="to_date"><?= __('Ngày hết hạn') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="passport-to-date">
                                            <?= $this->Form->control('cards.1.to_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'to-date-picker form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'data-parsley-errors-container' => '#error-passport-to-date',
                                                'data-parsley-validate-if-empty' => '',
                                                'data-parsley-check-empty' => '.passport-group',
                                                'data-parsley-after-date' => '#cards-1-from-date'
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span id="error-passport-to-date"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="issued_at"><?= __('Nơi cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.1.issued_at', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'data-parsley-validate-if-empty' => '',
                                            'data-parsley-check-empty' => '.passport-group',
                                            'placeholder' => 'Nhập nơi cấp Passport'
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content3">
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Quá trình học tập') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-educations" id="add-eduhis-top" onclick="showAddEduHisModal();">
                                    <?= __('Thêm lịch sử') ?>
                                </button>
                                <table class="table table-bordered custom-table educations-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Thời gian') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Cấp học') ?></th>
                                            <th scope="col" class="col-md-3"><?= __('Tên trường') ?></th>
                                            <th scope="col" class="col-md-3"><?= __('Chuyên ngành') ?></th>
                                            <th scope="col" class="actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="edu-container">
                                        <?php if (!empty($student->educations)): ?>
                                        <?php $counter = 0 ?>
                                        <?php foreach ($student->educations as $key => $value): ?>
                                        <div class="hidden edu-id" id="edu-his-<?=$counter?>-id">
                                            <?= $this->Form->hidden('educations.'  . $key . '.id', ['value' => $value->id]) ?>
                                        <div>
                                        <tr class="row-edu-his" id="row-edu-his-<?=$counter?>">
                                            <td class="cell col-md-1 stt-col">
                                                <?php echo $counter + 1; ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->from_date ?> ～ <?= $value->to_date ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('educations.' . $key . '.from_date', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control from_date',
                                                        ])?>
                                                    <?= $this->Form->control('educations.' . $key . '.to_date', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control to_date',
                                                        ])?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $eduLevel[$value->degree] ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('educations.' . $key . '.degree', [
                                                        'options' => $eduLevel,
                                                        'label' => false,
                                                        'class' => 'form-control degree',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-3">
                                                <?= $value->school ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('educations.' . $key . '.school', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control school',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('educations.' . $key . '.address', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control address',
                                                    ]) ?>
                                            </td>
                                            <td class="cell col-md-3">
                                                <?= $value->specialized ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('educations.' . $key . '.specialized', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control specialized',
                                                        ]) ?>
                                                    <?= $this->Form->control('educations.' . $key . '.specialized_jp', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control specialized_jp',
                                                        ]) ?>
                                                    <?= $this->Form->control('educations.' . $key . '.certificate', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control certificate',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell action-btn actions">
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-pencil"></i>', 
                                                    'javascript:;',
                                                    [
                                                        'escape' => false,
                                                        'onClick' => "showEditEduHisModal(this)"
                                                    ]) 
                                                ?>
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                    'javascript:;',
                                                    [
                                                        'escape' => false, 
                                                        'onClick' => "removeEduHis(this, true)"
                                                    ]
                                                )?>
                                                <?php $counter++; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?> 
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Năng lực ngôn ngữ') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-languages" id="add-language-top" onclick="showAddLangModal();">
                                    <?= __('Thêm ngôn ngữ') ?>
                                </button>
                                <table class="table table-bordered custom-table languages-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Ngôn ngữ') ?></th>
                                            <th scope="col" class="col-md-4"><?= __('Bằng cấp') ?></th>
                                            <th scope="col" class="col-md-4"><?= __('Thời hạn hiệu lực') ?></th>
                                            <th scope="col" class="actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="lang-container">
                                        <?php if (!empty($student->language_abilities)): ?>
                                        <?php $counter = 0 ?>
                                        <?php foreach ($student->language_abilities as $key => $value): ?>
                                        <div class="hidden lang-id" id="lang-<?=$counter?>-id">
                                            <?= $this->Form->hidden('language_abilities.'  . $key . '.id', ['value' => $value->id]) ?>
                                        <div>
                                        <tr class="row-lang" id="row-lang-<?=$counter?>">
                                            <td class="cell col-md-1 stt-col">
                                                <?php echo $counter + 1; ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $language[$value->lang_code]?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('language_abilities.' . $key . '.lang_code', [
                                                        'options' => $language,
                                                        'label' => false,
                                                        'class' => 'form-control lang_code',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-4">
                                                <?= $value->certificate ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('language_abilities.' . $key . '.certificate', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control certificate',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-4">
                                                <?= $value->from_date ?> ～ <?= $value->to_date ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('language_abilities.' . $key . '.from_date', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control from_date',
                                                        ])?>
                                                    <?= $this->Form->control('language_abilities.' . $key . '.to_date', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control to_date',
                                                        ])?>
                                                </div>
                                            </td>
                                            <td class="cell action-btn actions">
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-pencil"></i>', 
                                                    'javascript:;',
                                                    [
                                                        'escape' => false,
                                                        'onClick' => "showEditLangModal(this)"
                                                    ]) 
                                                ?>
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                    'javascript:;',
                                                    [
                                                        'escape' => false, 
                                                        'onClick' => "removeLang(this, true)"
                                                    ]
                                                )?>
                                                <?php $counter++; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Kinh nghiệm làm việc') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-work-exp" id="add-exp-top" onclick="showAddExpModal();">
                                    <?= __('Thêm lịch sử') ?>
                                </button>
                                <table class="table table-bordered custom-table work-exp-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Thời gian') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Công việc') ?></th>
                                            <th scope="col" class="col-md-3"><?= __('Công ty') ?></th>
                                            <th scope="col" class="col-md-3"><?= __('Mức lương') ?></th>
                                            <th scope="col" class="actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="exp-container">
                                        <?php if (!empty($student->experiences)): ?>
                                        <?php $counter = 0 ?>
                                        <?php foreach ($student->experiences as $key => $value): ?>
                                        <div class="hidden exp-id" id="exp-<?=$counter?>-id">
                                            <?= $this->Form->hidden('experiences.'  . $key . '.id', ['value' => $value->id]) ?>
                                        <div>
                                        <tr class="row-exp" id="row-exp-<?=$counter?>">
                                            <td class="cell col-md-1 stt-col">
                                                <?php echo $counter + 1; ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->from_date ?> ～ <?= $value->to_date ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('experiences.' . $key . '.from_date', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control from_date',
                                                        ])?>
                                                    <?= $this->Form->control('experiences.' . $key . '.to_date', [
                                                        'type' => 'text',
                                                        'label' => false,
                                                        'class' => 'form-control to_date',
                                                        ])?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->job->job_name ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('experiences.' . $key . '.job_id', [
                                                        'options' => $jobs,
                                                        'label' => false,
                                                        'class' => 'form-control job_id',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-3">
                                                <?= $value->company ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('experiences.' . $key . '.company', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control company',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('experiences.' . $key . '.company_jp', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control company_jp',
                                                    ]) ?>
                                            </td>
                                            <td class="cell col-md-3">
                                                <?= $value->salary ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('experiences.' . $key . '.salary', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control salary',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('experiences.' . $key . '.address', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control address',
                                                    ]) ?>
                                            </td>
                                            <td class="cell action-btn actions">
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-pencil"></i>', 
                                                    'javascript:;',
                                                    [
                                                        'escape' => false,
                                                        'onClick' => "showEditExpModal(this)"
                                                    ]) 
                                                ?>
                                                
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                    'javascript:;',
                                                    [
                                                        'escape' => false, 
                                                        'onClick' => 'removeExp(this, true)'
                                                    ]
                                                )?>
                                            </td>
                                            <?php $counter++; ?>
                                        </tr>
                                        <?php endforeach; ?> 
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content4">
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Danh sách hồ sơ') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered custom-table document-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-5"><?= __('Loại hồ sơ') ?></th>
                                            <th scope="col" class="col-md-1"><?= __('Số lượng') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Hoàn thành') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Ngày nộp') ?></th>
                                            <th scope="col" class="actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $counter = 0; ?>
                                    <?php if (!empty($student->documents)): ?>
                                    <div class="hidden">
                                    <?php foreach($student->documents as $key => $value): ?>
                                        <?= $this->Form->control('documents.'. $key .'.id') ?>
                                    <?php endforeach; ?> 
                                    </div>
                                    <?php endif;?>
                                    <?php foreach($document as $key => $value): ?>
                                    <tr class="row-document" id="row-document-<?=$counter?>">
                                        <td class="cell col-md-1 stt-col">
                                            <?php echo $counter + 1; ?>
                                            <div class="hidden">
                                                <?= $this->Form->control('documents.' . $counter . '.type', [
                                                    'type' => 'text',
                                                    'label' => false,
                                                    'class' => 'form-control',
                                                    'value' => $key
                                                    ]) ?>
                                            </div>
                                        </td>
                                        <td class="cell col-md-4">
                                            <?= $value['type'] ?>
                                        </td>
                                        <td class="cell col-md-1" style="width: 12.499999995%;">
                                            <?= $value['quantity'] ?>
                                        </td>
                                        <td class="cell col-md-1" style="width: 12.499999995%;">
                                            <?php 
                                                if (empty($student->documents) || $student->documents[$counter]->status == '0') {
                                                    $checked = false;
                                                } else {
                                                    $checked = true;
                                                }
                                            ?>
                                            <?= $this->Form->checkbox('documents.'. $counter .'.status', [
                                                'class' => 'js-switch js-check-change', 
                                                // 'hiddenField' => false,
                                                'checked' => $checked
                                                ]) ?>
                                        </td>
                                        <td class="cell col-md-2">
                                            <span class="submit-date-txt">
                                                <?php 
                                                    if(empty($student->documents) || empty($student->documents[$counter]->submit_date)) {
                                                        echo '-';
                                                    } else {
                                                        echo $student->documents[$counter]->submit_date;
                                                    }
                                                ?>
                                            </span>
                                            <div class="hidden">
                                                <?= $this->Form->control('documents.' . $counter . '.submit_date', [
                                                    'type' => 'text',
                                                    'label' => false,
                                                    'class' => 'form-control submit_date',
                                                    ]) ?>
                                            </div>
                                            <div class="hidden">
                                                <?= $this->Form->control('documents.' . $counter . '.note', [
                                                    'type' => 'textarea',
                                                    'class' => 'form-control submit_note',
                                                    ]) ?>
                                            </div>
                                        </td>
                                        <td class="cell action-btn">
                                            <?= $this->Html->link(
                                                '<i class="fa fa-2x fa-pencil"></i>', 
                                                'javascript:;',
                                                [
                                                    'escape' => false,
                                                    'onClick' => "showEditDocModal(this)"
                                                ]) 
                                            ?>
                                        </td>
                                        <?php $counter++; ?>
                                    </tr>
                                    <?php endforeach; ?> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content5">
                <div class="rows">
                    <div class="col-md-6 col-xs-12 left-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Tính toán cơ bản') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->input_tests)): ?>
                                <?= $this->Form->hidden('input_tests.0.id') ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('input_tests.0.type', ['value' => $input_test[0]])?>
                                
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="test_date"><?= __('Ngày thi') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="input-test-0-test-date">
                                            <?= $this->Form->control('input_tests.0.test_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="score"><?= __('Điểm thi') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('input_tests.0.score', [
                                            'label' => false, 
                                            'min' => 0,
                                            'max' => 100,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập điểm thi toán cơ bản'
                                            ]) ?>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Tính toán nâng cao') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->input_tests)): ?>
                                <?= $this->Form->hidden('input_tests.1.id') ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('input_tests.1.type', ['value' => $input_test[1]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="test_date"><?= __('Ngày thi') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="input-test-1-test-date">
                                            <?= $this->Form->control('input_tests.1.test_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="score"><?= __('Điểm thi') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('input_tests.1.score', [
                                            'label' => false, 
                                            'min' => 0,
                                            'max' => 999,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập điểm thi toán nâng cao'
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-6 col-xs-12 right-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Tiếng Nhật') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->input_tests)): ?>
                                <?= $this->Form->hidden('input_tests.2.id') ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('input_tests.2.type', ['value' => $input_test[2]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="test_date"><?= __('Ngày thi') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="input-test-2-test-date">
                                            <?= $this->Form->control('input_tests.2.test_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                ])?>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="score"><?= __('Điểm thi') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('input_tests.2.score', [
                                            'label' => false, 
                                            'min' => 0,
                                            'max' => 100,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'placeholder' => 'Nhập điểm thi tiếng Nhật'
                                            ]) ?>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>                   
                </div>
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Kiểm tra IQ') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->iq_tests)): ?>
                                <?= $this->Form->hidden('iq_tests.0.id') ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('iq_tests.0.total', ['id' => 'iqtest_total']) ?>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label col-md-5 col-sm-5 col-xs-12 optional" for="question"><?= __('Ngày thi') ?></label>
                                    <div class="input-group col-md-4 col-sm-4 col-xs-12 date input-picker" id="input-iq-test-date">
                                        <?= $this->Form->control('iq_tests.0.test_date', [
                                            'type' => 'text',
                                            'label' => false, 
                                            'class' => 'form-control',
                                            'placeholder' => 'yyyy-mm-dd',
                                            ])?>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <?php  foreach ($iqtest as $key => $value): ?>
                                <div class="col-md-offset-1">
                                    <div class="form-group col-md-3 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12 optional" for="question"><?= $value ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <?= $this->Form->control('iq_tests.0.q'. ($key+1), [
                                                'label' => false, 
                                                'min' => 0,
                                                'max' => 15,
                                                'class' => 'form-control col-md-7 col-xs-12 iqtest_score', 
                                                ]) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($student->id)): ?>
            <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content6">
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Lịch sử hoạt động') ?></h3>
                                <div class="box-tools pull-right">
                                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                    <a href="javascript:;" class="btn btn-box-tool" onclick="showAddHistoryModal(<?= $student->id ?>, 'main', 'students')"><i class="fa fa-plus"></i></a>
                                    <a href="javascript:;" class="btn btn-box-tool" onclick="getAllHistories(<?= $student->id ?>, 'main', 'list-history-overlay', 'students')"><i class="fa fa-refresh"></i></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="overlay hidden" id="list-history-overlay">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                                <ul class="timeline">
                                    <li class="time-label" id="now-tl">
                                        <span class="bg-black"><?= h($now) ?></span>
                                    </li>
                                    <?php foreach($student->histories as $key => $value): ?>
                                    <li class="history-detail" id="history-<?= $key ?>" history="<?= $value->id ?>">
                                        <?php if (empty($value->users_created_by->image)): ?>
                                            <?= $this->Html->image(Configure::read('noAvatar'), ['class' => 'img-circle timeline-avatar']) ?>
                                        <?php else: ?>
                                            <?= $this->Html->image($value->users_created_by->image, ['class' => 'img-circle timeline-avatar']) ?>
                                        <?php endif; ?>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> <?= $value->created ?></span>
                                            <h3 class="timeline-header"><?= $value->title ?></h3>
                                            <div class="timeline-body">
                                                <?= !empty($value->note) ? nl2br($value->note) : '' ?>
                                            </div>
                                            <div class="timeline-footer">
                                                <?php if ($currentUser['id'] == $value->created_by): ?>
                                                    <button type="button" class="btn btn-primary btn-xs" id="edit-history-btn" onclick="showEditHistoryModal(this, 'students')">Chỉnh sửa</button>
                                                    <button type="button" class="btn btn-danger btn-xs" id="delete-history-btn" onclick="deleteHistory(this, 'students')">Xóa</button>
                                                <?php else: ?>
                                                <span class="history-creater">Người tạo: <?= h($value->users_created_by->fullname) ?></span>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="time-label">
                                        <span class="bg-blue" id="student-created"><?= h($student->created) ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->Form->hidden('b64code')?>
<?= $this->Form->end() ?>

<div id="cropper-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="image_container col-md-12 col-xs-12">
                    <img id="avatar" src />
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="crop-btn" data-dismiss="modal">Cắt ảnh</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="add-member-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI QUAN HỆ</h4>
            </div>
            <?= $this->Form->create(null, [
                'type' => 'post',
                'class' => 'form-horizontal form-label-left',
                'id' => 'add-member-form',
                'data-parsley-validate' => '',
                'templates' => [
                    'inputContainer' => '{{content}}'
                    ]
                ]) ?>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Họ tên') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.fullname', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true,
                                    'placeholder' => 'Nhập họ tên của thành viên'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="birthday"><?= __('Ngày sinh') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="input-group date input-picker" id="member-birthday">
                                    <?= $this->Form->control('modal.birthday', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control',
                                        'placeholder' => 'yyyy-mm-dd',
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="cmnd_num"><?= __('Số CMND') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.cmnd_num', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'placeholder' => 'Nhập số chứng minh nhân dân'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="relationship"><?= __('Quan hệ') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.relationship', [
                                    'options' => $relationship, 
                                    'required' => true, 
                                    'empty' => true,
                                    'label' => false, 
                                    'data-parsley-errors-container' => '#error-relationship',
                                    'data-parsley-class-handler' => '#select2-modal-relationship',
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                    ]) ?>
                                <span id="error-relationship"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="job_id"><?= __('Nghề nghiệp') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.job', [
                                    'options' => $jobs, 
                                    'required' => true, 
                                    'empty' => true,
                                    'label' => false, 
                                    'data-parsley-errors-container' => '#error-job',
                                    'data-parsley-class-handler' => '#select2-modal-job',
                                    'class' => 'form-control col-md-7 col-xs-12 select-job'
                                    ]) ?>
                                <span id="error-job"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="address"><?= __('Địa chỉ') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.address', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'placeholder' => 'Nhập địa chỉ cư trú'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="living_at"><?= __('Đang sống tại') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.living_at', [
                                    'label' => false, 
                                    'options' => $country, 
                                    'empty' => true,
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'data-parsley-errors-container' => '#error-living-at',
                                    'data-parsley-class-handler' => '#select2-modal-living-at',
                                    'required' => true,
                                    ]) ?>
                                <span id="error-living-at"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="bank_num"><?= __('Số TKNH') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.bank_num', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'placeholder' => 'Nhập số tài khoản ngân hàng'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="bank_name"><?= __('Tên Ngân hàng') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.bank_name', [
                                    'options' => $bank, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                    'placeholder' => 'Nhập tên ngân hàng'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="bank_branch"><?= __('Chi nhánh') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.bank_branch', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'placeholder' => 'Nhập chi nhánh ngân hàng'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="cmnd_num"><?= __('Số điện thoại') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.phone', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                                    'placeholder' => 'Nhập số điện thoại của thành viên'
                                    ]) ?>
                            </div>
                        </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-member-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script id="family-template" type="text/x-handlebars-template">    
    <tr class="row-member" id="row-member-{{counter}}">
        <td class="cell col-md-1 stt-col">
            {{row}}
        </td>
        <td class="cell col-md-2">
            {{fullnameVal}}
            <div class="hidden">
                <?= $this->Form->control('{{fullname}}', [
                    'label' => false, 
                    'required' => true,
                    'class' => 'form-control fullname',
                    'value' => '{{fullnameVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-2">
            {{birthdayVal}}
            <div class="hidden">
                <?= $this->Form->control('{{birthday}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control birthday',
                    'value' => '{{birthdayVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell col-md-2">
            {{relationshipText}}
            <div class="hidden">
                <?= $this->Form->control('{{relationship}}', [
                    'options' => $relationship,
                    'label' => false,
                    'class' => 'form-control relationship',
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-2">
            {{jobText}}
            <div class="hidden">
                <?= $this->Form->control('{{job}}', [
                    'options' => $jobs,
                    'label' => false,
                    'class' => 'form-control job_id',
                    ]) ?>
            </div>
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{address}}', [
                'label' => false, 
                'class' => 'form-control address',
                'value' => '{{addressVal}}'
                ]) ?>
            <?= $this->Form->control('{{livingAt}}', [
                'options' => $country,
                'label' => false,
                'class' => 'form-control living_at',
                ])  ?>
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{bankNum}}', [
                'label' => false,
                'class' => 'form-control bank_num',
                'value' => '{{bankNumVal}}'
                ]) ?>
            <?= $this->Form->control('{{bankName}}', [
                'options' => $bank,
                'label' => false,
                'class' => 'form-control bank_name',
                ]) ?>
            <?= $this->Form->control('{{bankBranch}}', [
                'label' => false,
                'class' => 'form-control bank_branch',
                'value' => '{{bankBranchVal}}'
                ]) ?>
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{cmndNum}}', [
                'label' => false,
                'id' => 'cmnd_num',
                'class' => 'form-control cmnd_num',
                'value' => '{{cmndNumVal}}'
                ]) ?>
        </td>
        <td class="cell col-md-2">
            {{phoneFormat phoneVal}}
            <div class="hidden">
                <?= $this->Form->control('{{phone}}', [
                    'label' => false, 
                    'class' => 'form-control phone',
                    'value' => '{{phoneVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell action-btn actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditMemberModal(this)"
                ]) 
            ?>
            
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => 'removeMember(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>

<div id="add-edu-his-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM LỊCH SỬ HỌC TẬP</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                <?= $this->Form->create(null, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-edu-his-form', 
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="from_date"><?= __('Thời gian học') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="edu-his-from">
                                    <?= $this->Form->control('edu.from_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control from-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-edu-his-from',
                                        'data-parsley-before-date' => '#edu-to-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-edu-his-from"></span>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="edu-his-to">
                                    <?= $this->Form->control('edu.to_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control to-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-edu-his-to',
                                        'data-parsley-after-date' => '#edu-from-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-edu-his-to"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="level"><?= __('Cấp học') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('edu.level', [
                                'options' => $eduLevel, 
                                'required' => true, 
                                'label' => false, 
                                'empty' => true,
                                'id' => 'modal-edu-level',
                                'data-parsley-errors-container' => '#error-edu-his-level',
                                'data-parsley-class-handler' => '#select2-modal-edu-level',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-edu-his-level"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="certificate"><?= __('Ngày nhận bằng') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="input-group date input-picker month-mode" id="edu-certificate-div">
                                <?= $this->Form->control('edu.certificate', [
                                    'type' => 'text',
                                    'label' => false, 
                                    'class' => 'form-control',
                                    'placeholder' => 'yyyy-mm',
                                    'data-parsley-errors-container' => '#error-certificate',
                                    ])?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <span id="error-certificate"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="school"><?= __('Trường học') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('edu.school', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true,
                                'placeholder' => 'Nhập tên trường'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="address"><?= __('Địa chỉ') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('edu.address', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập địa chỉ của trường học'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="specialized"><?= __('Chuyên ngành') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <?= $this->Form->control('edu.specialized', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'placeholder' => 'Nhập bằng tiếng Việt'
                                    ]) ?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <?= $this->Form->control('edu.specialized_jp', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'placeholder' => 'Nhập bằng tiếng Nhật'
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-edu-his-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-edu-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="edu-template" type="text/x-handlebars-template">
    <tr class="row-edu-his" id="row-edu-his-{{counter}}">
        <td class="cell col-md-1 stt-col">
            {{row}}
        </td>
        <td class="cell col-md-2">
            {{fromdateVal}} ～ {{todateVal}}
            <div class="hidden">
                <?= $this->Form->control('{{fromdate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control from_date',
                    'value' => '{{fromdateVal}}'
                    ])?>
                <?= $this->Form->control('{{todate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control to_date',
                    'value' => '{{todateVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell col-md-2">
            {{degreeText}}
            <div class="hidden">
                <?= $this->Form->control('{{degree}}', [
                    'options' => $eduLevel,
                    'label' => false,
                    'class' => 'form-control degree',
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-3">
            {{schoolVal}}
            <div class="hidden">
                <?= $this->Form->control('{{school}}', [
                    'label' => false, 
                    'class' => 'form-control school',
                    'value' => '{{schoolVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{address}}', [
                'label' => false, 
                'class' => 'form-control address',
                'value' => '{{addressVal}}'
                ]) ?>
        </td>
        <td class="cell col-md-3">
            {{specializedVal}}
            <div class="hidden">
                <?= $this->Form->control('{{specialized}}', [
                    'label' => false, 
                    'class' => 'form-control specialized',
                    'value' => '{{specializedVal}}'
                    ]) ?>
                <?= $this->Form->control('{{specializedJP}}', [
                    'label' => false, 
                    'class' => 'form-control specialized_jp',
                    'value' => '{{specializedJPVal}}'
                    ]) ?>
                <?= $this->Form->control('{{certificate}}', [
                    'type' => 'text',
                    'label' => false, 
                    'class' => 'form-control certificate',
                    'value' => '{{certificateVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell action-btn actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditEduHisModal(this)"
                ]) 
            ?>
            
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => 'removeEduHis(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>

<div id="add-exp-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM KINH NGHIỆM LÀM VIỆC</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                <?= $this->Form->create(null, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-exp-form', 
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="from_date"><?= __('Thời gian') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="exp-from">
                                    <?= $this->Form->control('exp.from_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control from-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-exp-from',
                                        'data-parsley-before-date' => '#exp-to-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-exp-from"></span>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="exp-to">
                                    <?= $this->Form->control('exp.to_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control to-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-exp-to',
                                        'data-parsley-after-date' => '#exp-from-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-exp-to"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="job_id"><?= __('Công việc') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.job', [
                                'options' => $jobs, 
                                'required' => true, 
                                'empty' => true,
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-exp-job',
                                'data-parsley-class-handler' => '#select2-exp-job',
                                'class' => 'form-control col-md-7 col-xs-12 select-job'
                                ]) ?>
                            <span id="error-exp-job"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="company"><?= __('Công ty / Nơi làm việc') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.company', [
                                'label' => false, 
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập tên công ty / nơi làm việc bằng tiếng Việt'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.company_jp', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12',
                                'placeholder' => 'Nhập tên công ty / nơi làm việc bằng tiếng Nhật'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="address"><?= __('Địa chỉ') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.address', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập địa chỉ công ty'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="salary"><?= __('Mức lương') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.salary', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập mức lương cho công việc đã chọn'
                                ]) ?>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add-exp-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-exp-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="exp-template" type="text/x-handlebars-template">
    <tr class="row-exp" id="row-exp-{{counter}}">
        <td class="cell col-md-1 stt-col">
            {{row}}
        </td>
        <td class="cell col-md-2">
            {{fromdateVal}} ～ {{todateVal}}
            <div class="hidden">
                <?= $this->Form->control('{{fromdate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control from_date',
                    'value' => '{{fromdateVal}}'
                    ])?>
                <?= $this->Form->control('{{todate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control to_date',
                    'value' => '{{todateVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell col-md-2">
            {{jobText}}
            <div class="hidden">
                <?= $this->Form->control('{{job}}', [
                    'options' => $jobs,
                    'label' => false,
                    'class' => 'form-control job_id',
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-3">
            {{companyVal}}
            <div class="hidden">
                <?= $this->Form->control('{{company}}', [
                    'type' => 'text',
                    'label' => false, 
                    'class' => 'form-control company',
                    'value' => '{{companyVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{company_jp}}', [
                'type' => 'text',
                'label' => false,
                'class' => 'form-control company_jp',
                'value' => '{{companyJPVal}}'
                ]) ?>
        </td>
        <td class="cell col-md-3">
            {{salaryVal}}
            <div class="hidden">
                <?= $this->Form->control('{{salary}}', [
                    'type' => 'text',
                    'label' => false, 
                    'class' => 'form-control salary',
                    'value' => '{{salaryVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{address}}', [
                'type' => 'text',
                'label' => false, 
                'class' => 'form-control address',
                'value' => '{{addressVal}}'
                ]) ?>
        </td>
        <td class="cell action-btn actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditExpModal(this)"
                ]) 
            ?>
            
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => 'removeExp(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>

<div id="lived-japan-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THỜI ĐIỂM SỐNG TẠI NHẬT</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'set-lived-japan-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="from_date"><?= __('Thời gian') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="jp-lived-from">
                                    <?= $this->Form->control('jp.from_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control from-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-lived-from',
                                        'data-parsley-before-date' => '#jp-to-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-lived-from"></span>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="jp-lived-to">
                                    <?= $this->Form->control('jp.to_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control to-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-lived-to',
                                        'data-parsley-after-date' => '#jp-from-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-lived-to"></span>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="set-lived-japan-btn" onclick="setTimeLived()">Hoàn tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="add-lang-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM NĂNG LỰC NGÔN NGỮ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                <?= $this->Form->create(null, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-lang-form', 
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="lang"><?= __('Ngôn ngữ') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('lang.name', [
                                'options' => $language,
                                'required' => true, 
                                'label' => false,
                                'empty' => true,
                                'data-parsley-errors-container' => '#error-lang-name',
                                'data-parsley-class-handler' => '#select2-lang-name',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-lang-name"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="certificate"><?= __('Bằng cắp') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('lang.certificate', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true,
                                'placeholder' => 'Nhập tên bằng đã nhận'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="valid-time"><?= __('Thời hạn hiệu lực') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="lang-from">
                                    <?= $this->Form->control('lang.from_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control from-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-lang-from',
                                        'data-parsley-before-date' => '#lang-to-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-lang-from"></span>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="lang-to">
                                    <?= $this->Form->control('lang.to_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control to-date-picker',
                                        'placeholder' => 'yyyy-mm',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-lang-to',
                                        'data-parsley-after-date' => '#lang-from-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-lang-to"></span>
                            </div>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-lang-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-lang-modal-btn" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<script id="lang-template" type="text/x-handlebars-template">
    <tr class="row-lang" id="row-lang-{{counter}}">
        <td class="cell col-md-1 stt-col">
            {{row}}
        </td>
        <td class="cell col-md-2">
            {{languageText}}
            <div class="hidden">
                <?= $this->Form->control('{{language}}', [
                    'options' => $jobs,
                    'label' => false,
                    'class' => 'form-control lang_code',
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-4">
            {{certVal}}
            <div class="hidden">
                <?= $this->Form->control('{{cert}}', [
                    'type' => 'text',
                    'label' => false, 
                    'class' => 'form-control certificate',
                    'value' => '{{certVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-4">
            {{fromdateVal}} ～ {{todateVal}}
            <div class="hidden">
                <?= $this->Form->control('{{fromdate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control from_date',
                    'value' => '{{fromdateVal}}'
                    ])?>
                <?= $this->Form->control('{{todate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control to_date',
                    'value' => '{{todateVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell action-btn actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditLangModal(this)"
                ]) 
            ?>
            
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => 'removeLang(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>

<div id="document-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">NGÀY TIẾP NHẬN HỒ SƠ - GHI CHÚ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'document-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="submit_date"><?= __('Ngày nộp') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="input-group date input-picker" id="document-submit-date">
                                <?= $this->Form->control('modal.submit_date', [
                                    'type' => 'text',
                                    'label' => false, 
                                    'class' => 'form-control',
                                    'placeholder' => 'yyyy-mm-dd',
                                    'required' => true,
                                    'data-parsley-errors-container' => '#error-submit-date'
                                    ])?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <span id="error-submit-date"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="note"><?= __('Ghi chú') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('modal.note', [
                                'label' => false, 
                                'type' => 'textarea',
                                'class' => 'form-control col-md-7 col-xs-12', 
                                ]) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submit-document-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-document-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="export-student-modal" class="modal fade" role="dialog">
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
                                <td class="cell"><?= __('1') ?></td>
                                <td class="cell"><?= __('Sơ yếu lý lịch') ?></td>
                                <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportResume', $student->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell"><?= __('2') ?></td>
                                <td class="cell"><?= __('Hợp đồng lao động (tiếng Nhật)') ?></td>
                                <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportContract', $student->id, '?' => ['lang' => 'jp']],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell"><?= __('3') ?></td>
                                <td class="cell"><?= __('Hợp đồng lao động (tiếng Việt)') ?></td>
                                <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportContract', $student->id, '?' => ['lang' => 'vn']],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell"><?= __('4') ?></td>
                                <td class="cell"><?= __('Thủ tục công nhận kế hoạch đào tạo') ?></td>
                                <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportEduPlan', $student->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell"><?= __('5') ?></td>
                                <td class="cell"><?= __('Tóm tắt và cam kết của tổ chức nước ngoài') ?></td>
                                <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportCompanyCommitment', $student->id],
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