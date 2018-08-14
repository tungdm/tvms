<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$currentUser = $this->request->session()->read('Auth.User');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;

$gender = Configure::read('gender');
$bank = Configure::read('bank');
$yesNoQuestion = Configure::read('yesNoQuestion');

$country = Configure::read('country');
$country = array_map('array_shift', $country);

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
$interviewResult = Configure::read('interviewResult');
$now = Time::now();

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('student.js', ['block' => 'scriptBottom']);
$this->assign('title', $student->fullname . ' - Thông tin chi tiết');
?>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>

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
        <?= $this->Html->link(__('Danh sách lao động'), [
            'controller' => 'Students',
            'action' => 'index']) ?>
    </li>
    <li class="active"><?= $student->fullname ?></li>
</ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li data-toggle="tooltip" title="Xuất báo cáo">
                <a class="zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out" 
                   data-toggle="modal" 
                   data-target="#export-student-modal">
                    <i class="fa fa-book" aria-hidden="true"></i>
                </a>
            </li>
            <?php if ($permission == 0): ?>
            <li>
                <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                    ['action' => 'info', $student->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Sửa',
                        'escape' => false
                    ]) ?>
            </li>
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
        </ul>
    </div>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
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
                    <a href="#tab_content5" role="tab" id="view-input-test-tab" data-toggle="tab" aria-expanded="false"><?= __('Kiểm tra đầu vào') ?></a>
                </li>
                <li role="presentation" class="">
                    <a href="#tab_content6" role="tab" id="histories-tab" data-toggle="tab" aria-expanded="false"><?= __('Ghi chú hoạt động') ?></a>
                </li>
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
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="fullname"><?= __('Họ tên (VN)') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->fullname ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="fullname_kata"><?= __('Họ tên (JP)') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->fullname_kata ?? 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="gender"><?= __('Giới tính') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $gender[$student->gender] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="exempt"><?= __('Đăng ký phỏng vấn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $yesNoQuestion[$student->exempt] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="email"><?= __('Email') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->email ?? 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="status"><?= __('Trạng thái') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $studentStatus[$student->status] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="enrolled_date"><?= __('Ngày nhập học') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->enrolled_date ?? 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="image"><?= __('Hình ảnh') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div id="cropped_result" class="col-md-7 col-xs-12">
                                                <?php if(!empty($student->image)):?>
                                                <?= $this->Html->image($student->image) ?>
                                                <?php endif; ?>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="marial"><?= __('Tình trạng hôn nhân') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->marital_status) ? $maritalStatus[$student->marital_status] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="subject"><?= __('Đối tượng') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->subject) ? $studentSubject[$student->subject] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="phone"><?= __('Số điện thoại') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->phone) ? $this->Phone->makeEdit($student->phone) : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="birthday"><?= __('Ngày sinh') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->birthday ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="edu_level"><?= __('Trình độ học vấn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->educational_level) ? $eduLevel[$student->educational_level] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="nation"><?= __('Dân tộc') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->nation) ? $nation[$student->nation] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="religion"><?= __('Tôn giáo') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->religion) ? $religion[$student->religion] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="country"><?= __('Quốc tịch') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->country) ? $country[$student->country] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="presenter"><?= __('Người giới thiệu') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?php if (!empty($student->presenter)): ?>
                                                <a href="javascript:;" onclick="viewPresenter(<?= $student->presenter->id ?>)">
                                                    <?= $student->presenter->name ?>
                                                </a>
                                                <?php else: ?>
                                                <span>N/A</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="expectationJobs"><?= __('Nghề mong muốn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <?php 
                                                $expectArr = explode(',', $student->expectation);
                                                array_shift($expectArr);
                                                array_pop($expectArr);
                                            ?>
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?php if (!empty($expectArr)) : ?>
                                                <ol class="list-unstyled">
                                                <?php foreach ($expectArr as $key => $value): ?>
                                                <li><?= $jobs[$value] ?></li>
                                                <?php endforeach; ?>
                                                </ol>
                                                <?php else: ?>
                                                <?= __('N/A') ?>
                                                <?php endif; ?>
                                            </div>
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
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="is_lived_in_japan"><?= __('Đã từng đi nhật') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->is_lived_in_japan) ? $yesNoQuestion[$student->is_lived_in_japan] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group time-lived-jp<?php if (empty($student->is_lived_in_japan) || $student->is_lived_in_japan !== 'Y'): ?> hidden <?php endif; ?>">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="time_lived_in_japan"><?= __('Thời gian') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="time-lived">
                                                <?php if($student->is_lived_in_japan === 'Y'): ?>
                                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                                    <?= $student->lived_from ?> ～ <?= $student->lived_to ?>
                                                </div>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ln_solid"></div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="reject"><?= __('Từng bị từ chối lưu trú') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->reject_stay) ? $yesNoQuestion[$student->reject_stay] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>

                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="purpose"><?= __('Mục đích XKLĐ') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->purpose) ? $student->purpose : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="purpose"><?= __('Thu nhập hiện tại') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->salary) ? $student->salary . '万円/月' : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="saving_expected"><?= __('Số tiền mong muốn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->saving_expected) ? $student->saving_expected . '万円' : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="after_plan"><?= __('Dự định sau khi về') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->after_plan) ? $student->after_plan : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="ln_solid"></div>

                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="strength"><?= __('Điểm mạnh') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->strength) ? $student->strength : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="weakness"><?= __('Điểm yếu') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->weakness) ? $student->weakness : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="genitive"><?= __('Tính cách') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->genitive) ? $student->genitive : 'N/A' ?>
                                            </div>
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
                                        <li class="active"><a href="#household" data-toggle="tab"><?= __('Hộ khẩu thường trú') ?></a></li>
                                        <li><a href="#current-address" data-toggle="tab"><?= __('Nơi ở hiện tại') ?></a></li>
                                    </ul>
                                    <div id="address-tabs-content" class="tab-content">
                                        <div class="tab-pane fade in active" id="household">
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[0]->city) ? $student->addresses[0]->city->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="district"><?= __('Quận/Huyện') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[0]->district) ? $student->addresses[0]->district->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="ward"><?= __('Phường/Xã') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[0]->ward) ? $student->addresses[0]->ward->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="street"><?= __('Số nhà - Đường') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[0]->street) ? $student->addresses[0]->street : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="current-address">
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[1]->city) ? $student->addresses[1]->city->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="district"><?= __('Quận/Huyện') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[1]->district) ? $student->addresses[1]->district->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="ward"><?= __('Phường/Xã') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[1]->ward) ? $student->addresses[1]->ward->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-5 col-xs-12" for="street"><?= __('Số nhà - Đường') ?>: </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[1]->street) ? $student->addresses[1]->street : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Tình trạng sức khỏe') ?></h3>
                                    <div class="box-tools pull-right">
                                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="blood_group"><?= __('Nhóm máu') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->blood_group) ? $bloodGroup[$student->blood_group] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="height"><?= __('Chiều cao (cm)') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->height) ? $student->height : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="weight"><?= __('Cân nặng (kg)') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->weight) ? $student->weight : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="right_hand_force"><?= __('Lực bóp tay phải') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->right_hand_force) ? $student->right_hand_force : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="left_hand_force"><?= __('Lực bóp tay trái') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->left_hand_force) ? $student->left_hand_force : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="back_force"><?= __('Lực kéo lưng') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->back_force) ? $student->back_force : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="left_eye_sight"><?= __('Thị lực (trái)') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <ol class="list-unstyled">
                                                    <li>
                                                        (Đo tại trường) <?= !empty($student->left_eye_sight) ? $student->left_eye_sight : 'N/A' ?>
                                                    </li>
                                                    <li>
                                                        (Đo tại bệnh viện) <?= !empty($student->left_eye_sight_hospital) ? $student->left_eye_sight_hospital : 'N/A' ?>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="right_eye_sight"><?= __('Thị lực (phải)') ?></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <ol class="list-unstyled">
                                                    <li>
                                                        (Đo tại trường) <?= !empty($student->right_eye_sight) ? $student->right_eye_sight : 'N/A' ?>
                                                    </li>
                                                    <li>
                                                        (Đo tại bệnh viện) <?= !empty($student->right_eye_sight_hospital) ? $student->right_eye_sight_hospital : 'N/A' ?>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="color_blind"><?= __('Mù màu') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->color_blind) ? $student->color_blind : 'N/A'  ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="preferred_hand"><?= __('Tay thuận') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->preferred_hand) ? $preferredHand[$student->preferred_hand] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Thông tin hệ thống') ?></h3>
                                    <div class="box-tools pull-right">
                                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->created_by_user->fullname ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= h($student->created) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($student->modified_by_user)): ?>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->modified_by_user->fullname ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= h($student->modified) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
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
                                                <td class="cell col-md-2 family-fullname">
                                                    <?= $value->fullname ?>
                                                </td>
                                                <td class="cell col-md-2 family-birthday">
                                                    <?= $value->birthday ?>
                                                </td>
                                                <td class="cell col-md-2 family-relationship">
                                                    <?= $relationship[$value->relationship] ?>
                                                </td>
                                                <td class="cell col-md-2 family-job-name">
                                                    <?= $value->job->job_name ?>
                                                </td>
                                                <td class="hidden family-address">
                                                    <?= $value->address ?>
                                                </td>
                                                <td class="hidden family-bank-num">
                                                    <?= $value->bank_num ?>
                                                </td>
                                                <td class="hidden family-bank-name">
                                                    <?= $value->bank_name ? $bank[$value->bank_name]: '' ?>
                                                </td>
                                                <td class="hidden family-bank-branch">
                                                    <?= $value->bank_branch ?>
                                                </td>
                                                <td class="hidden family-cmnd-num">
                                                    <?= $value->cmnd_num ?>
                                                </td>
                                                <td class="cell col-md-2 family-phone">
                                                    <?= $this->Phone->makeEdit($value->phone)?>
                                                </td>
                                                <td class="cell action-btn actions">
                                                    <?= $this->Html->link(
                                                        '<i class="fa fa-2x fa-eye"></i>', 
                                                        'javascript:;',
                                                        [
                                                            'escape' => false,
                                                            'onClick' => "showMemberModal(this)"
                                                        ]) 
                                                    ?>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="code"><?= __('Số CMND') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[0]->code) ? $student->cards[0]->code : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="from_date"><?= __('Ngày cấp') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[0]->from_date) ? $student->cards[0]->from_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="issued_at"><?= __('Nơi cấp') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[0]->issued_at) ? $student->cards[0]->issued_at : 'N/A' ?>
                                            </div>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="code"><?= __('Số Visa') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->code) ? $student->cards[2]->code : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="registration_date"><?= __('Ngày đăng kí') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->registration_date) ? $student->cards[2]->registration_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="from_date"><?= __('Ngày cấp') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->from_date) ? $student->cards[2]->from_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="to_date"><?= __('Ngày hết hạn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->to_date) ? $student->cards[2]->to_date : 'N/A' ?>
                                            </div>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="code"><?= __('Số hộ chiếu') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->code) ? $student->cards[1]->code : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="from_date"><?= __('Ngày cấp') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->from_date) ? $student->cards[1]->from_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="to_date"><?= __('Ngày hết hạn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->to_date) ? $student->cards[1]->to_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="issued_at"><?= __('Nơi cấp') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->issued_at) ? $student->cards[1]->issued_at : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content3">
                    <?php if (!empty($student->orders)): ?>
                    <div class="rows">
                        <div class="col-md-12 col-xs-12 no-padding">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Lịch sử phỏng vấn') ?></h3>
                                    <div class="box-tools pull-right">
                                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-body table-responsive">
                                    <div class="overlay hidden" id="list-order-overlay">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </div>
                                    <table class="table table-bordered custom-table order-table">
                                        <thead>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Đơn hàng') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Ngày tuyển') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Nghiệp đoàn') ?></th>
                                            <th scope="col" class="col-md-3"><?= __('Công ty tiếp nhận') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Kết quả') ?></th>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($student->orders as $key => $value): ?>
                                            <tr>
                                                <td><?= $key + 1?></td>
                                                <td><?= h($value->name) ?></td>
                                                <td><?= h($value->interview_date) ?></td>
                                                <td>
                                                    <a href="javascript:;" onclick="viewGuild(<?= $value->company->guild->id ?>)"><?= h($value->company->guild->name_romaji) ?></a>
                                                </td>
                                                <td>
                                                    <a href="javascript:;" onclick="viewCompany(<?= $value->company->id ?>)"><?= h($value->company->name_romaji) ?></a>
                                                </td>
                                                <td><?= h($interviewResult[$value->_joinData->result]) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($student->jtests)): ?>
                    <div class="rows">
                        <div class="col-md-12 col-xs-12 no-padding">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Quá trình đào tạo') ?></h3>
                                    <div class="box-tools pull-right">
                                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="col-md-4 col-xs-12">
                                        <canvas id="total-radar-chart" height="300"></canvas>
                                    </div>
                                    <div class="col-md-8 col-xs-12">
                                        <canvas id="jtest-score-line-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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
                                    <table class="table table-bordered custom-table educations-table">
                                        <thead>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Thời gian') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Cấp học') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Tên trường') ?></th>
                                            <th scope="col" class="col-md-3"><?= __('Địa chỉ') ?></th>
                                            <th scope="col" class="col-md-2"><?= __('Chuyên ngành') ?></th>
                                        </thead>
                                        <tbody id="edu-container">
                                            <?php if (!empty($student->educations)): ?>
                                            <?php $counter = 0 ?>
                                            <?php foreach ($student->educations as $key => $value): ?>
                                            <tr class="row-edu-his" id="row-edu-his-<?=$counter?>">
                                                <td class="cell col-md-1 stt-col">
                                                    <?php echo $counter + 1; ?>
                                                </td>
                                                <td class="cell col-md-2 edu-from-to">
                                                    <?= $value->from_date ?> ～ <?= $value->to_date ?>
                                                </td>
                                                <td class="cell col-md-2 edu-level">
                                                    <?= $eduLevel[$value->degree] ?>
                                                </td>
                                                <td class="cell col-md-2 edu-school">
                                                    <?= $value->school ?>
                                                </td>
                                                <td class="cell col-md-3 edu-address">
                                                    <?= $value->address ?>
                                                </td>
                                                <td class="cell col-md-2 edu-specialized">
                                                    <?= $value->specialized ?> <br />
                                                    <?= $value->specialized_jp ?>
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
                                    <table class="table table-bordered custom-table languages-table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                                <th scope="col" class="col-md-2"><?= __('Ngôn ngữ') ?></th>
                                                <th scope="col" class="col-md-4"><?= __('Bằng cấp') ?></th>
                                                <th scope="col" class="col-md-5"><?= __('Thời hạn hiệu lực') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="lang-container">
                                            <?php if (!empty($student->language_abilities)): ?>
                                            <?php $counter = 0 ?>
                                            <?php foreach ($student->language_abilities as $key => $value): ?>
                                            <tr class="row-lang" id="row-lang-<?=$counter?>">
                                                <td class="cell col-md-1 stt-col">
                                                    <?php echo $counter + 1; ?>
                                                </td>
                                                <td class="cell col-md-2">
                                                    <?= $language[$value->lang_code]?>
                                                </td>
                                                <td class="cell col-md-4">
                                                    <?= $value->certificate ?>
                                                </td>
                                                <td class="cell col-md-4">
                                                    <?= $value->from_date ?> ～ <?= $value->to_date ?>
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
                                    <table class="table table-bordered custom-table work-exp-table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                                <th scope="col" class="col-md-2"><?= __('Thời gian') ?></th>
                                                <th scope="col" class="col-md-2"><?= __('Công việc') ?></th>
                                                <th scope="col" class="col-md-2"><?= __('Công ty') ?></th>
                                                <th scope="col" class="col-md-2"><?= __('Mức lương') ?></th>
                                                <th scope="col" class="col-md-4"><?= __('Địa chỉ') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="exp-container">
                                            <?php if (!empty($student->experiences)): ?>
                                            <?php $counter = 0 ?>
                                            <?php foreach ($student->experiences as $key => $value): ?>
                                            <tr class="row-exp" id="row-exp-<?=$counter?>">
                                                <td class="cell col-md-1 stt-col">
                                                    <?php echo $counter + 1; ?>
                                                </td>
                                                <td class="cell col-md-2">
                                                    <?= $value->from_date ?> ～ <?= $value->to_date ?>
                                                </td>
                                                <td class="cell col-md-2">
                                                    <?= $value->job->job_name ?>
                                                </td>
                                                <td class="cell col-md-2">
                                                    <?= $value->company ?>
                                                </td>
                                                <td class="cell col-md-2">
                                                    <?= $value->salary ?>
                                                </td>
                                                <td class="cell col-md-4">
                                                    <?= $value->address ?>
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
                                                <th scope="col" class="col-md-3"><?= __('Loại hồ sơ') ?></th>
                                                <th scope="col" class="col-md-1"><?= __('Số lượng') ?></th>
                                                <th scope="col" class="col-md-1"><?= __('Hoàn thành') ?></th>
                                                <th scope="col" class="col-md-2"><?= __('Ngày nộp') ?></th>
                                                <th scope="col" class="col-md-4"><?= __('Ghi chú') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($student->documents)): ?>
                                        <?php $counter = 0; ?>
                                        <?php foreach($document as $key => $value): ?>
                                        <tr class="row-document" id="row-document-<?=$counter?>">
                                            <td class="cell col-md-1 stt-col">
                                                <?php echo $counter + 1; ?>
                                            </td>
                                            <td class="cell col-md-3">
                                                <?= $value['type'] ?>
                                            </td>
                                            <td class="cell col-md-1" style="width: 12.499999995%;">
                                                <?= $value['quantity'] ?>
                                            </td>
                                            <td class="cell col-md-1" style="width: 12.499999995%;">
                                                <?php 
                                                    if (empty($student->documents) || $student->documents[$counter]->status == '0') {
                                                        echo '<i class="fa fa-2x fa-circle-o red-color"></i>';
                                                    } else {
                                                        echo '<i class="fa fa-2x fa-check-circle-o green-color"></i>';
                                                    }
                                                ?>
                                            </td>
                                            <td class="cell col-md-2">
                                                <span class="submit-date-txt">
                                                    <?php 
                                                        if(empty($student->documents[$counter]->submit_date)) {
                                                            echo '-';
                                                        } else {
                                                            echo $student->documents[$counter]->submit_date;
                                                        }
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="cell col-md-4">
                                                <?= nl2br($student->documents[$counter]->note) ?>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="test_date"><?= __('Ngày thi') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->input_tests[0]->test_date) ? $student->input_tests[0]->test_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="score"><?= __('Điểm thi') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->input_tests[0]->score) ? $student->input_tests[0]->score : 'N/A' ?>
                                            </div>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="test_date"><?= __('Ngày thi') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->input_tests[1]->test_date) ? $student->input_tests[1]->test_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="score"><?= __('Điểm thi') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->input_tests[1]->score) ? $student->input_tests[1]->score : 'N/A' ?>
                                            </div>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="test_date"><?= __('Ngày thi') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->input_tests[2]->test_date) ? $student->input_tests[2]->test_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="score"><?= __('Điểm thi') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->input_tests[2]->score) ? $student->input_tests[2]->score : 'N/A' ?>
                                            </div>
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
                                        <a href="javascript:;" class="btn btn-box-tool" id="download-btn" onclick="downloadIqChart()"><i class="fa fa-cloud-download"></i></a>
                                    </div>
                                </div>
                                <div class="box-body overview">
                                    <?php if (!empty($student->iq_tests)): ?>
                                    <ul id="iqtest-tabs" class="nav nav-tabs">
                                        <li class="active"><a href="#iq-vn" data-toggle="tab"><?= __('Tiếng Việt') ?></a></li>
                                        <li><a href="#iq-jp" data-toggle="tab"><?= __('Tiếng Nhật') ?></a></li>
                                    </ul>
                                    <div id="iqtest-tabs-content" class="tab-content">
                                        <div class="tab-pane fade in active" id="iq-vn">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12" style="width:75%;">
                                                    <canvas id="iq-vn-line-chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="iq-jp">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12" style="width:75%;">
                                                    <canvas id="iq-jp-line-chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content6">
                    <div class="rows">
                        <div class="col-md-12 col-xs-12 no-padding">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Lịch sử hoạt động') ?></h3>
                                    <div class="box-tools pull-right">
                                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                        <a href="javascript:;" class="btn btn-box-tool" onclick="getAllHistories(<?= $student->id ?>, 'main', 'list-history-overlay')"><i class="fa fa-refresh"></i></a>
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
                                                <?= $this->Html->image(Configure::read('noAvatar'), ['class' => 'user-image']) ?>
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
                                                        <button type="button" class="btn btn-primary btn-xs" id="edit-history-btn" onclick="showEditHistoryModal(this)">Chỉnh sửa</button>
                                                        <button type="button" class="btn btn-danger btn-xs" id="delete-history-btn" onclick="deleteHistory(this)">Xóa</button>
                                                    <?php else: ?>
                                                    <span class="history-creater">Người tạo: <?= h($value->users_created_by->fullname) ?></span>
                                                    <?php endif;?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                        <li class="time-label">
                                            <span class="bg-blue"><?= h($student->created) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="member-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÔNG TIN THÀNH VIÊN</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <div class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fullname"><?= __('Họ tên') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-fullname"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="birthday"><?= __('Ngày sinh') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-birthday"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="relationship"><?= __('Quan hệ') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-relationship"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="job_id"><?= __('Nghề nghiệp') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-job"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="address"><?= __('Địa chỉ') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-address"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="bank_num"><?= __('Số TKNH') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-bank-num"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="bank_name"><?= __('Ngân hàng') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-bank-name"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="bank_branch"><?= __('Chi nhánh') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-bank-branch"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="cmnd_num"><?= __('Số CMND') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-cmnd-num"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="cmnd_num"><?= __('Số Điện Thoại') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-phone"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="export-student-modal" role="dialog">
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

<script type="text/javascript">
    var iqtests = <?= json_encode($student->iq_tests) ?>;
    var studentName = '<?= $studentName_EN ?>';
    var studentNameVN = '<?= $studentName_VN ?>';
    var jtestScore = <?= json_encode($jtestScore) ?>;
</script>