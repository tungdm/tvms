<?php 
use Cake\Core\Configure;
use Cake\I18n\Time;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;

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
            <li data-toggle="tooltip" title="Xuất hồ sơ">
                <a class="zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out" 
                   data-toggle="modal" 
                   data-target="#export-order-modal">
                    <i class="fa fa-book" aria-hidden="true"></i>
                </a>
            </li>
            <?php if ($permission == 0): ?>
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
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tên đơn hàng') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->name ?></div>
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
                                <?= $this->Number->format($order->salary_from, ['locale' => 'vn_VN']) ?> ～ <?= $this->Number->format($order->salary_to, ['locale' => 'vn_VN']) ?> (¥/tháng)
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
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="company_id"><?= __('Nghiệp đoàn quản lý') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <a href="javascript:;" onclick="viewGuild(<?= $order->company->guild->id ?>)"><?= h($order->company->guild->name_romaji) ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="company_id"><?= __('Công ty tiếp nhận') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <a href="javascript:;" onclick="viewCompany(<?= $order->company->id ?>)"><?= h($order->company->name_romaji) ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="skill_test"><?= __('Thi tay nghề') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $yesNoQuestion[$order->skill_test] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="interview_type"><?= __('Hình thức phỏng vấn') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $interviewType[$order->interview_type] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="departure_date"><?= __('Ngày xuất cảnh') ?>: </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->departure_date ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="row">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __('Yêu cầu tuyển chọn') ?></h3>
                        <div class="box-tools pull-right">
                            <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                        </div>
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
                                <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->male_num ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="female_num"><?= __('Số lượng nữ') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->female_num ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="age_interval"><?= __('Độ tuổi') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <?= $order->age_from ?> ～ <?= $order->age_to ?>
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
            </div>
            <div class="row">
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
                                    <?= $order->created_by_user->fullname ?>
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
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Danh sách ứng viên') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered custom-table candidate-table">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('STT') ?></th>
                                <th scope="col"><?= __('Họ tên') ?></th>
                                <th scope="col"><?= __('Tuổi') ?></th>
                                <th scope="col"><?= __('Giới tính') ?></th>
                                <th scope="col"><?= __('Số ĐT') ?></th>
                                <th scope="col"><?= __('Kết quả') ?></th>
                                <th scope="col" class="actions">Thao tác</th>
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
                                    <td class="cell col-md-1 stt-col">
                                        <?= $counter+1 ?>
                                    </td>
                                    <td class="cell col-md-3">
                                        <a href="javascript:;" onclick="viewCandidate(<?=$value->id?>);"><?= h($value->fullname) ?></a>
                                    </td>
                                    <td class="cell col-md-1">
                                        <?= h(($now->diff($value->birthday))->y) ?>
                                    </td>
                                    <td class="cell col-md-1">
                                        <?= $gender[$value->gender]?>
                                    </td>
                                    <td class="cell col-md-3">
                                        <?= $this->Phone->makeEdit($value->phone) ?>
                                    </td>
                                    <td class="cell col-md-1">
                                        <span class="result-text"><?= $interviewResult[$value->_joinData->result] ?></span>
                                    </td>
                                    <td class="actions cell">
                                        <?= $this->Html->link('<i class="fa fa-2x fa-book"></i>',
                                            [
                                                'controller' => 'Orders', 
                                                'action' => 'exportCv', 
                                                '?' => [
                                                    'studentId' => $value->id,
                                                    'serial' => $key+1
                                                ]
                                                
                                            ],
                                            [
                                                'escape' => false,
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Xuất CV',
                                            ])?>
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
                                <td class="cell"><?= __('1') ?></td>
                                <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
                                <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportDispatchLetter', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell"><?= __('2') ?></td>
                                <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
                                <td class="cell"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportDispatchLetterXlsx', $order->id],
                                        ['escape' => false]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="cell"><?= __('3') ?></td>
                                <td class="cell"><?= __('Danh sách ứng viên phỏng vấn') ?></td>
                                <td class="cell"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
                                <td class="actions cell">
                                    <?= $this->Html->link('<i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về', 
                                        ['action' => 'exportCandidates', $order->id],
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