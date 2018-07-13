<?php
use Cake\Core\Configure;

$gender = Configure::read('gender');
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
$studentSubject = Configure::read('studentSubject');
$religion = Configure::read('religion');
$nation = Configure::read('nation');
$addressType = array_keys(Configure::read('addressType'));
$cardType = array_keys(Configure::read('cardType'));
$bloodGroup = Configure::read('bloodGroup');
$preferredHand = Configure::read('preferredHand');
$relationship = Configure::read('relationship');

$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('student.js', ['block' => 'scriptBottom']);
?>

<?php $this->start('content-header'); ?>
<h1><?= __('Thông tin thực tập sinh') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Home',
            '/',
            ['escape' => false]) ?>
    </li>
    <li>
        <?= $this->Html->link(__('Students'), [
            'controller' => 'Students',
            'action' => 'index']) ?>
    </li>
    <li class="active">Info</li>
</ol>
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
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->fullname ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fullname_kata"><?= __('Họ tên (JP)') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->fullname_kata ?? 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="gender"><?= __('Giới tính') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $gender[$student->gender] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="email"><?= __('Email') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->email ?? 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="status"><?= __('Trạng thái') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $studentStatus[$student->status] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="enrolled_date"><?= __('Ngày nhập học') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->enrolled_date ?? 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="image"><?= __('Hình ảnh') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
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
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->marital_status) ? $maritalStatus[$student->marital_status] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="subject"><?= __('Đối tượng') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->subject) ? $studentSubject[$student->subject] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="phone"><?= __('Số điện thoại') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->phone) ? $this->Phone->makeEdit($student->phone) : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="birthday"><?= __('Ngày sinh') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->birthday ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="edu_level"><?= __('Trình độ học vấn') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->educational_level) ? $eduLevel[$student->educational_level] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="nation"><?= __('Dân tộc') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->nation) ? $nation[$student->nation] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="religion"><?= __('Tôn giáo') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->religion) ? $religion[$student->religion] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="country"><?= __('Quốc tịch') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->country) ? $country[$student->country] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="presenter"><?= __('Người giới thiệu') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->presenter) ? $student->presenter->name : 'N/A' ?>
                                            </div>
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
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= !empty($student->addresses[0]->city) ? $student->addresses[0]->city->name : 'N/A' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="district"><?= __('Quận/Huyện') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <?php if (!empty($student->addresses[0]->district)): ?>
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[0]->district->name ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="ward"><?= __('Phường/Xã') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <?php if (!empty($student->addresses[0]->ward)): ?>
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[0]->ward->name ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="street"><?= __('Số nhà - Đường') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[0]->street ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="current-address">
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="city"><?= __('Tỉnh/Thành phố') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[1]->city->name ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="district"><?= __('Quận/Huyện') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?php if (!empty($student->addresses[1]->district)): ?>
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[1]->district->name ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="ward"><?= __('Phường/Xã') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                <?php if (!empty($student->addresses[1]->ward)): ?>
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[1]->ward->name ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="street"><?= __('Số nhà - Đường') ?></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <div class="form-control form-control-view col-md-7 col-xs-12">
                                                        <?= $student->addresses[1]->street ?>
                                                    </div>
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
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="blood_group"><?= __('Nhóm máu') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->blood_group) ? $bloodGroup[$student->blood_group] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="height"><?= __('Chiều cao (cm)') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->height ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="weight"><?= __('Cân nặng (kg)') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->weight ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="left_eye_sight"><?= __('Thị lực (trái)') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->left_eye_sight ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-offset-4 col-sm-offset-4 col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->left_eye_sight_hospital ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="right_eye_sight"><?= __('Thị lực (phải)') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->right_eye_sight ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-offset-4 col-sm-offset-4 col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->right_eye_sight_hospital ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="color_blind"><?= __('Mù màu') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $student->color_blind ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="preferred_hand"><?= __('Tay thuận') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->preferred_hand) ? $preferredHand[$student->preferred_hand] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12 right-col">
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
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->is_lived_in_japan) ? $yesNoQuestion[$student->is_lived_in_japan] : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group time-lived-jp<?php if (empty($student->is_lived_in_japan) || $student->is_lived_in_japan !== 'Y'): ?> hidden <?php endif; ?>">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="time_lived_in_japan"><?= __('Thời gian') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
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
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="reject"><?= __('Từng bị từ chối lưu trú') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->reject_stay) ? $yesNoQuestion[$student->reject_stay] : 'N/A' ?>
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
                                    <h3 class="box-title"><?= __('Quan hệ gia đình') ?></h3>
                                    <div class="box-tools pull-right">
                                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-body table-responsive">
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
                                                <td class="hidden family-cmnd-num">
                                                    <?= $value->cmnd_num ?>
                                                </td>
                                                <td class="cell col-md-2 family-phone">
                                                    <?= $this->Phone->makeEdit($value->phone) ?>
                                                </td>
                                                <td class="cell action-btn">
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
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="code"><?= __('Số CMND') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[0]->code) ? $student->cards[0]->code : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="from_date"><?= __('Ngày cấp') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[0]->from_date) ? $student->cards[0]->from_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="issued_at"><?= __('Nơi cấp') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
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
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="code"><?= __('Số Visa') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->code) ? $student->cards[2]->code : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="registration_date"><?= __('Ngày đăng kí') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->registration_date) ? $student->cards[2]->registration_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="from_date"><?= __('Ngày cấp') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[2]->from_date) ? $student->cards[2]->from_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="to_date"><?= __('Ngày hết hạn') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
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
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="code"><?= __('Số hộ chiếu') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->code) ? $student->cards[1]->code : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="from_date"><?= __('Ngày cấp') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->from_date) ? $student->cards[1]->from_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="to_date"><?= __('Ngày hết hạn') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($student->cards[1]->to_date) ? $student->cards[1]->to_date : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="issued_at"><?= __('Nơi cấp') ?></label>
                                        <div class="col-md-7 col-sm-7 col-xs-12">
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
                                            <tr>
                                                <th scope="col"><?= __('STT') ?></th>
                                                <th scope="col"><?= __('Thời gian') ?></th>
                                                <th scope="col"><?= __('Cấp học') ?></th>
                                                <th scope="col"><?= __('Tên trường') ?></th>
                                                <th scope="col"><?= __('Địa chỉ') ?></th>
                                                <th scope="col"><?= __('Chuyên ngành') ?></th>
                                            </tr>
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
                                                <td class="cell col-md-3 edu-school">
                                                    <?= $value->school ?>
                                                </td>
                                                <td class="cell col-md-2 edu-address">
                                                    <?= $value->address ?>
                                                </td>
                                                <td class="cell col-md-3 edu-specialized">
                                                    <?= $value->specialized ?>
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
                                                <th scope="col"><?= __('STT') ?></th>
                                                <th scope="col"><?= __('Ngôn ngữ') ?></th>
                                                <th scope="col"><?= __('Bằng cấp') ?></th>
                                                <th scope="col"><?= __('Thời hạn hiệu lực') ?></th>
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
                                                <td class="cell col-md-3">
                                                    <?= $value->certificate ?>
                                                </td>
                                                <td class="cell col-md-3">
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
                                                <th scope="col"><?= __('STT') ?></th>
                                                <th scope="col"><?= __('Thời gian') ?></th>
                                                <th scope="col"><?= __('Công việc') ?></th>
                                                <th scope="col"><?= __('Công ty') ?></th>
                                                <th scope="col"><?= __('Mức lương') ?></th>
                                                <th scope="col"><?= __('Địa chỉ') ?></th>
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
                                                <td class="cell col-md-2">
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
                                                <th scope="col"><?= __('STT') ?></th>
                                                <th scope="col"><?= __('Loại hồ sơ') ?></th>
                                                <th scope="col"><?= __('Số lượng') ?></th>
                                                <th scope="col"><?= __('Hoàn thành') ?></th>
                                                <th scope="col"><?= __('Ngày nộp') ?></th>
                                                <th scope="col"><?= __('Ghi chú') ?></th>
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
                                            <td class="cell col-md-4">
                                                <?= $value['type'] ?>
                                            </td>
                                            <td class="cell col-md-1" style="width: 12.499999995%;">
                                                <?= $value['quantity'] ?>
                                            </td>
                                            <td class="cell col-md-1" style="width: 12.499999995%;">
                                                <?php 
                                                    if (empty($student->documents) || $student->documents[$counter]->status == '0') {
                                                        echo '<i class="fa fa-2x fa-circle-o"></i>';
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
                                            <td class="cell col-md-2">
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
            </div>
        </div>
    </div>
</div>

<div id="member-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <div class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Họ tên') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-fullname"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthday"><?= __('Ngày sinh') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-birthday"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="relationship"><?= __('Quan hệ') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-relationship"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="job_id"><?= __('Nghề nghiệp') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-job"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address"><?= __('Địa chỉ') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-address"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="bank_num"><?= __('Số TKNH') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-bank-num"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cmnd_num"><?= __('Số CMND') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span class="modal-cmnd-num"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cmnd_num"><?= __('Số Điện Thoại') ?></label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
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
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>