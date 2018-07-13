<?php
use Cake\Core\Configure;
use Cake\Log\Log;

$gender = Configure::read('gender');
$yesNoQuestion = Configure::read('yesNoQuestion');

$city = Configure::read('city');
$city = array_map('array_shift', $city);

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
$studentSubject = Configure::read('studentSubject');
$religion = Configure::read('religion');
$nation = Configure::read('nation');
$addressType = array_keys(Configure::read('addressType'));
$cardType = array_keys(Configure::read('cardType'));
$bloodGroup = Configure::read('bloodGroup');
$preferredHand = Configure::read('preferredHand');
$relationship = Configure::read('relationship');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->css('cropper.css', ['block' => 'styleTop']);
$this->Html->css('student.css', ['block' => 'styleTop']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('cropper.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('student.js', ['block' => 'scriptBottom']);
?>

<?= $this->Form->create($student, [
    'class' => 'form-horizontal form-label-left', 
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

<?php $this->start('content-header'); ?>
<h1><?= __('THÔNG TIN CHI TIẾT') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chính',
            '/',
            ['escape' => false]) ?>
    </li>
    <li>
        <?= $this->Html->link(__('Lao Động'), [
            'controller' => 'Students',
            'action' => 'index']) ?>
    </li>
    <li class="active">Thông Tin</li>
</ol>
<?php $this->end(); ?>

<?= $this->Form->button('Lưu lại', [
    'class' => 'btn btn-round btn-success mobile-only create-student-btn', 
    'type' => 'button', 
    'id' => 'create-student-mobile-btn',
    ]) ?>

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
            <li>
                <?= $this->Form->button('Lưu lại', [
                    'class' => 'btn btn-round btn-success create-student-btn', 
                    'type' => 'button', 
                    'id' => 'create-student-desktop-btn',
                    ]) ?>
            </li>
        </ul>
        <div id="student-tab-content" class="tab-content">
            <div role="tabpanel" class="tab-pane root-tab-pane fade active in" id="tab_content1">
                <div class="rows">
                    <div class="col-md-6 col-xs-12 left-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Sơ yếu lý lịch') ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fullname"><?= __('Họ tên (VN)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('fullname', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fullname_kata"><?= __('Họ tên (JP)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('fullname_kata', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="email"><?= __('Email') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('email', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12']) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="status"><?= __('Trạng thái') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('status', [
                                            'options' => $studentStatus, 
                                            'required' => true, 
                                            'empty' => true,
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-status',
                                            'data-parsley-class-handler' => '#select2-status',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-status"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="image"><?= __('Hình ảnh') ?></label>
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="subject"><?= __('Đối tượng') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('subject', [
                                            'options' => $studentSubject, 
                                            'required' => true, 
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
                                            'class' => 'form-control col-md-7 col-xs-12'
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="nation"><?= __('Dân tộc') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('nation', [
                                            'options' => $nation, 
                                            'required' => true, 
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="religion"><?= __('Tôn giáo') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('religion', [
                                            'options' => $religion, 
                                            'required' => true, 
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="country"><?= __('Quốc tịch') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('country', [
                                            'options' => $country, 
                                            'required' => true, 
                                            'label' => false, 
                                            'empty' => true,
                                            'data-parsley-errors-container' => '#error-country',
                                            'data-parsley-class-handler' => '#select2-country',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-country"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="presenter"><?= __('Người giới thiệu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('presenter_id', [
                                            'options' => $presenters, 
                                            'label' => false, 
                                            'empty' => true,
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="expectationJobs"><?= __('Nghề mong muốn') ?></label>
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
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                            'value' => $expectArr
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
                            </div>
                            <div class="box-body">
                                <ul id="address-tabs" class="nav nav-tabs">
                                    <li class="active"><a href="#household" data-toggle="tab"><?= __('Hộ khẩu thường trú') ?></a></li>
                                    <li><a href="#current-address" data-toggle="tab"><?= __('Nơi ở hiện tại') ?></a></li>
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
                                                <?= $this->Form->control('addresses.0.city', [
                                                    'options' => $city, 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-city-0',
                                                    'data-parsley-class-handler' => '#select2-addresses-0-city',
                                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme select-city'
                                                    ]) ?>
                                                <span id="error-city-0"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="district"><?= __('Quận/Huyện') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?php if (!empty($student->addresses[0]->district)): ?>
                                                <?php
                                                    $district0 = $district[$student->addresses[0]->city];
                                                    $district0 = array_map('array_shift', $district0);
                                                ?>
                                                <?= $this->Form->control('addresses.0.district', [
                                                    'options' => $district0, 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-district-0',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-district select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.0.district', [
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
                                                <?php if (!empty($student->addresses[0]->ward)): ?>
                                                <?php 
                                                    $ward0 = $ward[$student->addresses[0]->district];
                                                    $ward0 = array_map('array_shift', $ward0);
                                                ?>
                                                <?= $this->Form->control('addresses.0.ward', [
                                                    'options' => $ward0, 
                                                    'required' => true,
                                                    'empty' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-ward-0',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-ward select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.0.ward', [
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
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="street"><?= __('Số nhà - Đường') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.0.street', [
                                                    'required' => true, 
                                                    'label' => false,
                                                    'class' => 'form-control col-md-7 col-xs-12'
                                                    ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="current-address">
                                        <?php if (!empty($student->addresses)): ?>
                                        <?= $this->Form->hidden('addresses.1.id', ['value' => $student->addresses[1]->id]) ?>
                                        <?php endif; ?>
                                        <?= $this->Form->hidden('addresses.1.type', ['value' => $addressType[1]]) ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.1.city', [
                                                    'options' => $city, 
                                                    'required' => true,
                                                    'empty' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-city-1',
                                                    'data-parsley-class-handler' => '#select2-addresses-1-city',
                                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme select-city'
                                                    ]) ?>
                                                <span id="error-city-1"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="district"><?= __('Quận/Huyện') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                            <?php if (!empty($student->addresses[1]->district)): ?>
                                                <?php
                                                    $district1 = $district[$student->addresses[1]->city];
                                                    $district1 = array_map('array_shift', $district1);
                                                ?>
                                                <?= $this->Form->control('addresses.1.district', [
                                                    'options' => $district1, 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-district-1',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-district select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.1.district', [
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
                                            <?php if (!empty($student->addresses[1]->ward)): ?>
                                                <?php 
                                                    $ward1 = $ward[$student->addresses[1]->district];
                                                    $ward1 = array_map('array_shift', $ward1);
                                                ?>
                                                <?= $this->Form->control('addresses.1.ward', [
                                                    'options' => $ward1, 
                                                    'required' => true,
                                                    'empty' => true,
                                                    'label' => false,
                                                    'data-parsley-errors-container' => '#error-ward-1',
                                                    'class' => 'form-control col-md-7 col-xs-12 select-ward select2-theme'
                                                    ]) ?>
                                                <?php else: ?>
                                                <?= $this->Form->control('addresses.1.ward', [
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
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="street"><?= __('Số nhà - Đường') ?></label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?= $this->Form->control('addresses.1.street', [
                                                    'required' => true, 
                                                    'label' => false,
                                                    'class' => 'form-control col-md-7 col-xs-12'
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
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="blood_group"><?= __('Nhóm máu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('blood_group', [
                                            'options' => $bloodGroup, 
                                            'required' => true, 
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="height"><?= __('Chiều cao (cm)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('height', [
                                            'label' => false,
                                            'min' => 0,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="weight"><?= __('Cân nặng (kg)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('weight', [
                                            'label' => false,
                                            'min' => 0,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="left_eye_sight"><?= __('Thị lực (trái)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('left_eye_sight', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10,
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true,
                                            'placeholder' => 'Đo tại trường'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-4 col-sm-offset-4 col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('left_eye_sight_hospital', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Đo tại bệnh viện'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="right_eye_sight"><?= __('Thị lực (phải)') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('right_eye_sight', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true,
                                            'placeholder' => 'Đo tại trường'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-4 col-sm-offset-4 col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('right_eye_sight_hospital', [
                                            'label' => false,
                                            'min' => 0,
                                            'max' => 10, 
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'placeholder' => 'Đo tại bệnh viện'
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="color_blind"><?= __('Mù màu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('color_blind', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="preferred_hand"><?= __('Tay thuận') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('preferred_hand', [
                                            'options' => $preferredHand, 
                                            'required' => true, 
                                            'empty' => true, 
                                            'label' => false, 
                                            'data-parsley-errors-container' => '#error-preferred-hand',
                                            'data-parsley-class-handler' => '#select2-preferred-hand',
                                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                            ]) ?>
                                        <span id="error-preferred-hand"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 right-col">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Thông tin nộp hồ sơ') ?></h3>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rows">
                    <div class="col-md-12 col-xs-12 no-padding">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Quan hệ gia đình') ?></h3>
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-family" id="add-member-top" onclick="showAddMemberModal();">
                                    <?= __('Thêm thành viên') ?>
                                </button>
                                <table class="table table-bordered custom-table family-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= __('STT') ?></th>
                                            <th scope="col"><?= __('Họ và tên') ?></th>
                                            <th scope="col"><?= __('Ngày sinh') ?></th>
                                            <th scope="col"><?= __('Quan hệ') ?></th>
                                            <th scope="col"><?= __('Nghề nghiệp') ?></th>
                                            <th scope="col"><?= __('Số ĐT') ?></th>
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
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('families.' . $key . '.bank_num', [
                                                    'label' => false,
                                                    'class' => 'form-control bank_num',
                                                    ]) ?>
                                            </td>
                                            <td class="hidden">
                                                <?= $this->Form->control('families.' . $key . '.cmnd_num', [
                                                    'label' => false,
                                                    'class' => 'form-control cmnd_num',
                                                    ]) ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <?= $value->phone ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('families.' . $key . '.phone', [
                                                        'label' => false, 
                                                        'class' => 'form-control phone',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell action-btn">
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
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->cards)): ?>
                                <?= $this->Form->hidden('cards.0.id', ['value' => $student->cards[0]->id]) ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('cards.0.type', ['value' => $cardType[0]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="code"><?= __('Số CMND') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.0.code', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'required' => true]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="from_date"><?= __('Ngày cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="input-group date input-picker" id="cmnd-from-date">
                                            <?= $this->Form->control('cards.0.from_date', [
                                                'type' => 'text',
                                                'label' => false, 
                                                'class' => 'form-control',
                                                'placeholder' => 'yyyy-mm-dd',
                                                'required' => true,
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="issued_at"><?= __('Nơi cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.0.issued_at', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'required' => true
                                            ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __('Thị thực (Visa)') ?></h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->cards)): ?>
                                <?= $this->Form->hidden('cards.2.id', ['value' => $student->cards[2]->id]) ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('cards.2.type', ['value' => $cardType[2]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="code"><?= __('Số Visa') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.2.code', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12 visa-group',
                                            'data-parsley-validate-if-empty' => '',
                                            'data-parsley-check-empty' => '.visa-group',
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="registration_date"><?= __('Ngày đăng kí') ?></label>
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="from_date"><?= __('Ngày cấp') ?></label>
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="to_date"><?= __('Ngày hết hạn') ?></label>
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
                            </div>
                            <div class="box-body">
                                <?php if (!empty($student->cards)): ?>
                                <?= $this->Form->hidden('cards.1.id', ['value' => $student->cards[1]->id]) ?>
                                <?php endif; ?>
                                <?= $this->Form->hidden('cards.1.type', ['value' => $cardType[1]])?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="code"><?= __('Số hộ chiếu') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.1.code', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12 passport-group',
                                            'data-parsley-validate-if-empty' => '',
                                            'data-parsley-check-empty' => '.passport-group',
                                            ]) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="from_date"><?= __('Ngày cấp') ?></label>
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="to_date"><?= __('Ngày hết hạn') ?></label>
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
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="issued_at"><?= __('Nơi cấp') ?></label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <?= $this->Form->control('cards.1.issued_at', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'data-parsley-validate-if-empty' => '',
                                            'data-parsley-check-empty' => '.passport-group',
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
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-educations" id="add-eduhis-top" onclick="showAddEduHisModal();">
                                    <?= __('Thêm lịch sử') ?>
                                </button>
                                <table class="table table-bordered custom-table educations-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= __('STT') ?></th>
                                            <th scope="col"><?= __('Thời gian') ?></th>
                                            <th scope="col"><?= __('Cấp học') ?></th>
                                            <th scope="col"><?= __('Tên trường') ?></th>
                                            <th scope="col"><?= __('Chuyên ngành') ?></th>
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
                                                </div>
                                            </td>
                                            <td class="cell action-btn">
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
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-languages" id="add-language-top" onclick="showAddLangModal();">
                                    <?= __('Thêm ngôn ngữ') ?>
                                </button>
                                <table class="table table-bordered custom-table languages-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= __('STT') ?></th>
                                            <th scope="col"><?= __('Ngôn ngữ') ?></th>
                                            <th scope="col"><?= __('Bằng cấp') ?></th>
                                            <th scope="col"><?= __('Thời hạn hiệu lực') ?></th>
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
                                            <td class="cell col-md-3">
                                                <?= $value->certificate ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('language_abilities.' . $key . '.certificate', [
                                                        'type' => 'text',
                                                        'label' => false, 
                                                        'class' => 'form-control certificate',
                                                        ]) ?>
                                                </div>
                                            </td>
                                            <td class="cell col-md-3">
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
                                            <td class="cell action-btn">
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
                            </div>
                            <div class="box-body table-responsive">
                                <button type="button" class="btn btn-primary btn-work-exp" id="add-exp-top" onclick="showAddExpModal();">
                                    <?= __('Thêm lịch sử') ?>
                                </button>
                                <table class="table table-bordered custom-table work-exp-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= __('STT') ?></th>
                                            <th scope="col"><?= __('Thời gian') ?></th>
                                            <th scope="col"><?= __('Công việc') ?></th>
                                            <th scope="col"><?= __('Công ty') ?></th>
                                            <th scope="col"><?= __('Mức lương') ?></th>
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
                                            <td class="cell action-btn">
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
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered custom-table document-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= __('STT') ?></th>
                                            <th scope="col"><?= __('Loại hồ sơ') ?></th>
                                            <th scope="col"><?= __('Số lượng') ?></th>
                                            <th scope="col"><?= __('Hoàn thành') ?></th>
                                            <th scope="col"><?= __('Ngày nộp') ?></th>
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
                <h4 class="modal-title">a</h4>
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
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <form method="post" accept-charset="utf-8" class="form-horizontal form-label-left" data-parsley-validate id="add-member-form">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Họ tên') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.fullname', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthday"><?= __('Ngày sinh') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="input-group date input-picker" id="member-birthday">
                                    <?= $this->Form->control('modal.birthday', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control',
                                        'placeholder' => 'yyyy-mm-dd',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-member-birthday'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-member-birthday"></span>
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
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                    ]) ?>
                                <span id="error-job"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address"><?= __('Địa chỉ') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.address', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="bank_num"><?= __('Số TKNH') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.bank_num', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cmnd_num"><?= __('Số CMND') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.cmnd_num', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cmnd_num"><?= __('Số Điện Thoại') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?= $this->Form->control('modal.phone', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true
                                    ]) ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-member-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
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
        </td>
        <td class="hidden">
            <?= $this->Form->control('{{bankNum}}', [
                'label' => false,
                'class' => 'form-control bank_num',
                'value' => '{{bankNumVal}}'
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
            {{phoneVal}}
            <div class="hidden">
                <?= $this->Form->control('{{phone}}', [
                    'label' => false, 
                    'class' => 'form-control phone',
                    'value' => '{{phoneVal}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell action-btn">
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
    <div class="modal-dialog">
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="from_date"><?= __('Thời gian') ?></label>
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="school"><?= __('Tên trường') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('edu.school', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address"><?= __('Địa chỉ') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('edu.address', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="specialized"><?= __('Chuyên ngành') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('edu.specialized', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                ]) ?>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
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
            </div>
        </td>
        <td class="cell action-btn">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">KINH NGHIỆM LÀM VIỆC</h4>
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
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-exp-job"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="company"><?= __('Công ty') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.company', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="company_jp"><?= __('Công ty (JP)') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.company_jp', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12',
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="salary"><?= __('Mức lương') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.salary', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address"><?= __('Địa chỉ') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('exp.address', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true
                                ]) ?>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
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
        <td class="cell action-btn">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">NĂNG LỰC NGÔN NGỮ</h4>
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
                            'required' => true
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="valid-time"><?= __('Thời gian') ?></label>
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
        <td class="cell col-md-3">
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
        <td class="cell col-md-3">
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
        <td class="cell action-btn">
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note"><?= __('Ghi chú') ?></label>
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
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submit-document-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-document-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>