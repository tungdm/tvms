<?php 
use Cake\Core\Configure;
use Cake\I18n\Time;

$currentUser = $this->request->session()->read('Auth.User');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');

$now = Time::now()->i18nFormat('yyyy-MM-dd');
$interviewStatus = Configure::read('interviewStatus');
$financeStatus = Configure::read('financeStatus');
$gender = Configure::read('gender');
$interviewResult = Configure::read('interviewResult');
$cityJP = Configure::read('cityJP');
$cityJP = array_map('array_shift', $cityJP);
$yesNoQuestion = Configure::read('yesNoQuestion');
$interviewType = Configure::read('interviewType');
$workTime = Configure::read('workTime');

$this->Html->css('order.css', ['block' => 'styleTop']);
$this->Html->script('order.js', ['block' => 'scriptBottom']);

$this->assign('title', $order->name . ' - Thông tin chi tiết');

$interview_date = $order->interview_date->i18nFormat('yyyy-MM-dd');

if ($order->status == "4" || $order->status == "5") {
    $status = (int) $order->status;
} elseif ($now < $interview_date) {
    $status = 1;
} elseif ($now == $interview_date) {
    $status = 2;
} else {
    $status = 3;
}
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
            <?= $this->Html->link(__('Danh sách đơn hàng'), [
                'controller' => 'Orders',
                'action' => 'index']) ?>
        </li>
        <li class="active"><?= $order->name ?></li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li>
                <?= $this->Html->link(__('<i class="fa fa-calendar" aria-hidden="true"></i>'), 
                    ['action' => 'schedule', $order->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Khóa học',
                        'escape' => false
                    ]) ?>
            </li>
            <?php if ($permission == 0): ?>
                <?php if ($order->del_flag): ?>
                    <li>
                        <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                        ['action' => 'recover', $order->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Phục hồi',
                            'confirm' => __('Bạn có chắc chắn muốn phục hồi đơn hàng {0}?', $order->name)
                        ]) ?>
                    </li>
                <?php else: ?>
                    <li data-toggle="tooltip" title="Xuất hồ sơ">
                        <a class="zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out" 
                        data-toggle="modal" 
                        data-target="#export-order-modal">
                            <i class="fa fa-book" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                            ['action' => 'delete', $order->id], 
                            [
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                                'escape' => false, 
                                'data-toggle' => 'tooltip',
                                'title' => 'Xóa',
                                'confirm' => __('Bạn có chắc chắn muốn xóa đơn hàng {0}?', $order->name)
                            ]) ?>
                    </li>
                    <?php if ($status != 5 || $currentUser['role_id'] == 1): ?>
                        <li>
                            <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                                ['action' => 'edit', $order->id],
                                [   
                                    'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Sửa',
                                    'escape' => false
                                ]) ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($status == 4 && !empty($order->departure)): ?>
                        <li>
                            <?= $this->Form->postLink(__('<i class="fa fa-plane" aria-hidden="true"></i>'), 
                                ['action' => 'close', $order->id],
                                [   
                                    'class' => 'zoom-fab zoom-btn-sm zoom-btn-close scale-transition scale-out',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Xuất cảnh',
                                    'escape' => false,
                                    'confirm' => __('Bạn có chắc chắn muốn chuyển đơn hàng {0} sang xuất cảnh?', $order->name)
                                ]) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
    <div class="row">    
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin cơ bản') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tên đơn hàng') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="admin-company"><?= __('Phân nhánh') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->admin_company->alias ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="job_id"><?= __('Nghề nghiệp') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->job->job_name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="salary"><?= __('Mức lương') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->salary_from ? $this->Number->format($order->salary_from, ['locale' => 'vn_VN']) : 'N/A' ?> ～ <?= $order->salary_to ? $this->Number->format($order->salary_to, ['locale' => 'vn_VN']) : 'N/A' ?> (¥/tháng)
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="interview_date"><?= __('Ngày phỏng vấn') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->interview_date ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="work_at"><?= __('Địa điểm làm việc') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $cityJP[$order->work_at] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="work_time"><?= __('Thời gian làm việc') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $workTime[$order->work_time] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="guild_id"><?= __('Nghiệp đoàn quản lý') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?php if ($order->has('guild')): ?>
                                    <a href="javascript:;" onclick="viewGuild(<?= $order->guild_id ?>)"><?= h($order->guild->name_romaji) ?></a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="company_id"><?= __('Công ty tiếp nhận') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?php if ($order->has('company')): ?>
                                    <a href="javascript:;" onclick="viewCompany(<?= $order->company->id ?>)"><?= h($order->company->name_romaji) ?></a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="dis_company_id"><?= __('Công ty phái cử') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?php if (!empty($order->dis_company)): ?>
                                    <a href="javascript:;" onclick="globalViewDispatchingCompany(<?= $order->dis_company->id ?>)"><?= h($order->dis_company->name_romaji) ?></a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="healthcheck_date"><?= __('Ngày khám sức khỏe') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->healthcheck_date ?? 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="skill_test"><?= __('Thi tay nghề') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->skill_test ? $yesNoQuestion[$order->skill_test] : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="interview_type"><?= __('Hình thức phỏng vấn') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->interview_type ? $interviewType[$order->interview_type] : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="departure_date"><?= __('Ngày xuất cảnh (dự kiến)') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->departure_date ? $this->Month->makeEdit($order->departure_date) : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="departure"><?= __('Ngày bay chính thức') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->departure ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="japanese_airport"><?= __('Sân bay Nhật') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <?= !empty($order->japanese_airport) ? nl2br($order->japanese_airport) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="status"><?= __('Trạng thái') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="form-control form-control-view col-md-7 col-xs-12"><?= $interviewStatus[$status] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Yêu cầu tuyển chọn') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="experience"><?= __('Kinh nghiệm') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <?= !empty($order->experience) ? nl2br($order->experience) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="male_num"><?= __('Số lượng nam') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->male_num ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="female_num"><?= __('Số lượng nữ') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->female_num ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="age_interval"><?= __('Độ tuổi') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->age_from ?? 'N/A' ?> ～ <?= $order->age_to ?? 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="height"><?= __('Chiều cao') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->height) ? $order->height : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="weight"><?= __('Cân nặng') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->weight) ? $order->weight : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="requirement"><?= __('Yêu cầu khác') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <?= !empty($order->requirement) ? nl2br($order->requirement) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin bổ sung') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="application_date"><?= __('Ngày làm hồ sơ') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->application_date ?? 'N/A' ?>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="submitted_date"><?= __('Ngày gửi hồ sơ sang Nhật') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->submitted_date) ? $order->submitted_date : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="visa_apply_date"><?= __('Ngày xin visa') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->visa_apply_date) ? $order->visa_apply_date : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="temporary_stay_date"><?= __('Ngày có tạm trú') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->temporary_stay_date) ? $order->temporary_stay_date : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin hệ thống') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->created_by_user) ? $order->created_by_user->fullname : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($order->created) ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($order->modified_by_user)): ?>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->modified_by_user->fullname ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($order->modified) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Danh sách ứng viên') ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered custom-table candidate-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Họ tên') ?></th>
                                <th scope="col" class="col-md-1"><?= __('Tuổi') ?></th>
                                <th scope="col" class="col-md-1"><?= __('Giới tính') ?></th>
                                <?php if (!in_array($role['name'], ['accountant', 'staff', 'teacher'])): ?>
                                    <th scope="col" class="col-md-1"><?= __('Số ĐT') ?></th>
                                <?php endif; ?>
                                <?php if (!in_array($role['name'], ['manager', 'staff', 'teacher'])): ?>
                                    <th scope="col" class="col-md-2"><?= __('Cọc phỏng vấn') ?></th>
                                <?php endif; ?>
                                <?php if (!in_array($role['name'], ['accountant'])): ?>
                                    <th scope="col" class="col-md-2"><?= __('Quê quán') ?></th>
                                <?php endif; ?>

                                <th scope="col" class="col-md-1"><?= __('Kết quả') ?></th>
                                <th scope="col" class="actions col-md-1">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="candidate-container">
                        <?php if (!empty($order->students)): ?>
                            <?php $counter = 0; $now = Time::now(); ?>
                            <?php foreach ($order->students as $key => $value): ?>
                                <div class="hidden candidate-id" id="candidate-<?=$counter?>-id">
                                    <?= $this->Form->hidden('students.'  . $key . '.id', ['value' => $value->id]) ?>
                                </div>
                                <tr class="row-rec" id="row-candidate-<?=$counter?>">
                                    <td class="cell stt-col text-center">
                                        <?= $counter+1 ?>
                                    </td>
                                    <td class="cell">
                                        <a href="javascript:;" onclick="viewCandidate(<?=$value->id?>);"><?= h($value->fullname) ?></a>
                                    </td>
                                    <td class="cell text-center">
                                        <?= h(($now->diff($value->birthday))->y) ?>
                                    </td>
                                    <td class="cell text-center">
                                        <?= $gender[$value->gender]?>
                                    </td>
                                    <?php if (!in_array($role['name'], ['accountant', 'staff', 'teacher'])): ?>
                                        <td class="cell">
                                            <?= $this->Phone->makeEdit($value->phone) ?>
                                        </td>
                                    <?php endif; ?>
                                    <?php if (!in_array($role['name'], ['manager', 'staff', 'teacher'])): ?>
                                        <td class="cell text-center">
                                            <?= (!empty($value->interview_deposit) && !empty($value->interview_deposit->status)) ? $financeStatus[$value->interview_deposit->status] : ''?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="cell text-center">
                                        <?= !empty($value->addresses) ? $value->addresses[0]->city->name : '' ?>
                                    </td>
                                    <td class="cell text-center">
                                        <span class="result-text <?= $value->_joinData->result == '1' ? 'bold-text' : '' ?>"><?= $interviewResult[$value->_joinData->result] ?></span>
                                    </td>
                                    <td class="actions cell">
                                        <a href="javascript:;" 
                                            onclick="showExportModal2(<?=$order->id?>, <?=$value->id?>, <?=$key+1?>, <?= $value->_joinData->result == '1' ? 'true' : 'false' ?>)">
                                            <i class="fa fa-2x fa-book" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php $counter++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="export-order-modal" role="dialog">
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
                                <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportDispatchLetter', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('2') ?></td>
                                <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportDispatchLetterXlsx', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('3') ?></td>
                                <td class="cell"><?= __('Danh sách ứng viên phỏng vấn') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportCandidates', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('4') ?></td>
                                <td class="cell"><?= __('Bìa hồ sơ phỏng vấn') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportCover', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('5') ?></td>
                                <td class="cell"><?= __('1.13') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['controller' => 'Students', 'action' => 'exportCompanyCommitment', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('6') ?></td>
                                <td class="cell"><?= __('1.20') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportDeclaration', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('7') ?></td>
                                <td class="cell"><?= __('1.28') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportCertificate', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('8') ?></td>
                                <td class="cell"><?= __('Điểm kiểm tra IQ') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportIqTest', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell text-center"><?= __('9') ?></td>
                                <td class="cell"><?= __('Thông tin đơn hàng') ?></td>
                                <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportSummary', $order->id],
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

<div class="modal fade" id="export-order-modal2" role="dialog">
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
                        <tbody id="export-container2"></tbody>
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

<script id="export-template2" type="text/x-handlebars-template">
    <tr>
        <td class="cell text-center"><?= __('1') ?></td>
        <td class="cell"><?= __('CV') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/orders/export-cv?orderId={{orderId}}&studentId={{studentId}}&serial={{serial}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    {{#if passed}}
    <tr>
        <td class="cell text-center"><?= __('2') ?></td>
        <td class="cell"><?= __('Sơ yếu lý lịch') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/students/export-resume/{{studentId}}?order={{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('3') ?></td>
        <td class="cell"><?= __('Hợp đồng lao động (tiếng Nhật)') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/students/export-contract/{{studentId}}?lang=jp&order={{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('4') ?></td>
        <td class="cell"><?= __('Hợp đồng lao động (tiếng Việt)') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/students/export-contract/{{studentId}}?lang=vn&order={{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('5') ?></td>
        <td class="cell"><?= __('1.10') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/students/export-edu-plan/{{studentId}}?order={{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('6') ?></td>
        <td class="cell"><?= __('1.21') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/orders/export-fees/{{orderId}}?studentId={{studentId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('7') ?></td>
        <td class="cell"><?= __('Visa Application Form') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="/orders/export-vaf/{{orderId}}?studentId={{studentId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    {{/if}}
</script>