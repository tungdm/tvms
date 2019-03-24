<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student[]|\Cake\Collection\CollectionInterface $students
 */
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');
$addressType = array_keys(Configure::read('addressType'));
$financeStatus = Configure::read('financeStatus');
$physResult = Configure::read('physResult');

$gender = Configure::read('gender');
$yesNoQuestion = Configure::read('yesNoQuestion');
$eduLevel = Configure::read('eduLevel');
$eduLevel = array_map('array_shift', $eduLevel);

$studentStatus = Configure::read('studentStatus');
$recordsDisplay = Configure::read('recordsDisplay');

$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$currentUser = $this->request->session()->read('Auth.User');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('student.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);
?>

<?php $this->start('content-header'); ?>
    <?php $this->assign('title', 'Quản lý lao động'); ?>
    <h1><?= __('QUẢN LÝ LAO ĐỘNG') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách lao động</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-user-plus" aria-hidden="true"></i>'), 
                        ['action' => 'info'],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Thêm mới lao động',
                            'escape' => false
                        ]) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Students', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-student-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="form-group col-md-4 col-sm-6 col-xs-12 records-per-page">
                    <label class="control-label col-md-3 col-sm-3 col-xs-3"><?= __('Hiển thị') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <?= $this->Form->control('records', [
                            'options' => $recordsDisplay, 
                            'empty' => true,
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                            'value' => $query['records'] ?? ''
                            ])
                        ?>
                    </div>
                </div>
                <table class="table table-bordered custom-table">
                    <thead>
                        <tr>
                            <th scope="col" class="col-num"><?= __('STT') ?></th>
                            <th scope="col" class="fullnameCol">
                                <?= $this->Paginator->sort('fullname', 'Họ tên') ?>
                            </th>
                            <?php if (!in_array($role['name'], ['staff'])): ?>
                                <th scope="col" class="enrolledDateCol">
                                    <?= $this->Paginator->sort('enrolled_date', 'Ngày nhập học')?>
                                </th>
                            <?php endif; ?>
                            <th scope="col" class="birthdayCol">
                                <?= $this->Paginator->sort('birthday', 'Ngày sinh')?>
                            </th>
                            <?php if (!in_array($role['name'], ['manager', 'teacher'])): ?>
                                <th scope="col" class="genderCol">
                                    <?= __('Giới tính') ?>
                                </th>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['manager', 'teacher', 'staff'])): ?>
                                <th scope="col" class="presenterCol">
                                    <?= __('Người giới thiệu') ?>
                                </th>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['recruiter', 'accountant'])): ?>
                                <th scope="col" class="hometownCol">
                                    <?= __('Quê quán') ?>
                                </th>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['manager', 'teacher', 'staff'])): ?>
                                <th scope="col" class="interviewDepositCol">
                                    <?= __('Cọc phỏng vấn') ?>
                                </th>
                            <?php endif; ?>
                            <th scope="col" class="statusCol">
                                <?= __('Trạng thái') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="col-md-2 fullnameCol">
                                <?= $this->Form->control('student_name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['student_name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <?php if (!in_array($role['name'], ['staff'])): ?>
                                <td class="col-md-2 enrolledDateCol">
                                    <div class="input-group date input-picker" id="select-enrolled-date">
                                        <?= $this->Form->control('enrolled_date', [
                                            'type' => 'text',
                                            'label' => false,
                                            'placeholder' => 'dd-mm-yyyy',
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'value' => $query['enrolled_date'] ?? ''
                                            ]) 
                                        ?>
                                        <span class="input-group-addon" style="line-height: 1;">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td class="col-md-2 enrolledDateCol">
                                <div class="input-group date input-picker" id="select-birthday">
                                    <?= $this->Form->control('birthday', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'dd-mm-yyyy',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['birthday'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <?php if (!in_array($role['name'], ['manager', 'teacher'])): ?>
                                <td class="col-md-1 genderCol" style="width: 12.499999995%;">
                                    <?= $this->Form->control('student_gender', [
                                        'options' => $gender, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'id' => 'filter-gender',
                                        'value' => $query['student_gender'] ?? ''
                                        ])
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['manager', 'teacher', 'staff'])): ?>
                                <td class="col-md-1 presenterCol">
                                    <?= $this->Form->control('presenter', [
                                        'options' => $presenters, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['presenter'] ?? ''
                                        ])
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['recruiter', 'accountant'])): ?>
                                <td class="col-md-2 hometownCol">
                                    <?= $this->Form->control('hometown', [
                                        'options' => $cities, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['hometown'] ?? ''
                                        ])
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['manager', 'teacher', 'staff'])): ?>
                                <td class="col-md-1 interviewDepositCol" style="width: 12.499999995%;">
                                    <?= $this->Form->control('interview_deposit', [
                                        'options' => $financeStatus, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['interview_deposit'] ?? ''
                                        ])
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td class="col-md-2 statusCol">
                                <?= $this->Form->control('student_status', [
                                    'options' => $studentStatus, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['student_status'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                                <?= $this->Form->end() ?>
                            </td>
                        </tr>
                        <?php if (($students)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($students as $student): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell text-center"><?= h($counter) ?></td>
                            <td class="cell fullnameCol">
                                <?php if ($student->status == 1): ?>
                                    <a href="javascript:;" onclick="viewSCandidate(<?= $student->id ?>)">
                                        <?= h($student->fullname) ?><br>
                                        <?= h($student->fullname_kata)?>
                                    </a>
                                <?php else: ?>
                                    <?= $this->Html->link(h($student->fullname) . '<br/>' . h($student->fullname_kata), 
                                        ['action' => 'view', $student->id],
                                        ['escape' => false]) ?>
                                <?php endif; ?>
                            </td>
                            <?php if (!in_array($role['name'], ['staff'])): ?>
                                <td class="cell enrolledDateCol"><?= h($student->enrolled_date) ?></td>
                            <?php endif; ?>
                            <td class="cell birthdayCol"><?= h($student->birthday) ?></td>
                            <?php if (!in_array($role['name'], ['manager', 'teacher'])): ?>
                                <td class="cell genderCol text-center"><?= h($gender[$student->gender]) ?></td>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['manager', 'teacher', 'staff'])): ?>
                                <td class="cell presenterCol">
                                    <?php if (!empty($student->presenter_id)): ?>
                                    <a href="javascript:;" onclick="viewPresenter(<?= $student->presenter_id ?>)">
                                        <?= $presenters->toArray()[$student->presenter_id] ?>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['recruiter', 'accountant'])): ?>
                                <td class="cell hometownCol"><?= $student->addresses ? h($student->addresses[0]->city->name) : '' ?></td>
                            <?php endif; ?>
                            <?php if (!in_array($role['name'], ['manager', 'teacher', 'staff'])): ?>
                                <td class="cell interviewDepositCol">
                                    <?= (!empty($student->interview_deposit) && !empty($student->interview_deposit->status)) ? $financeStatus[$student->interview_deposit->status] : ''?>
                                </td>
                            <?php endif; ?>
                            <td class="cell statusCol"><?= h($studentStatus[$student->status]) ?></td>
                            
                            <td class="actions cell">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <?php if ($student->status == 1): ?>
                                                <?php $entity = "lịch hẹn" ?>
                                                <a href="javascript:;" onclick="viewSCandidate(<?= $student->id ?>)">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                                </a>
                                            <?php else: ?>
                                                <?php $entity = "lao động" ?>
                                                <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                    ['action' => 'view', $student->id],
                                                    ['escape' => false]) ?>
                                            <?php endif; ?>
                                        </li>
                                        <?php if ($permission == 0): ?>
                                            <?php if ($student->status == 1): ?>
                                            <li>
                                                <a href="javascript:;" onclick="showEditStudentModal(<?= $student->id ?>)">
                                                    <i class="fa fa-edit" aria-hidden="true"></i> Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <?= $this->Html->link('<i class="fa fa-angle-double-up" style="font-size: 1.3em" aria-hidden="true"></i> Kí kết chính thức', 
                                                    ['action' => 'info', $student->id],
                                                    ['escape' => false]) ?>
                                            </li>
                                            <?php else: ?>
                                            <li>
                                                <?= $this->Html->link('<i class="fa fa-edit" aria-hidden="true"></i> Sửa', [
                                                    'action' => 'info', $student->id],
                                                    ['escape' => false]) ?>
                                            </li>
                                            <?php endif; ?>
                                            <li>
                                                <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $student->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa ' . $entity . ' {0}?', $student->fullname)
                                                ]) ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('Trang đầu')) ?>
                        <?= $this->Paginator->prev('< ' . __('Trang trước')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('Trang sau') . ' >') ?>
                        <?= $this->Paginator->last(__('Trang cuối') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Trang thứ {{page}} trên tổng {{pages}} trang, {{current}} trên tổng số {{count}} bản ghi')]) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-candidate-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI LỊCH HẸN</h4>
            </div>
            <?= $this->Form->create(null, [
                'type' => 'post',
                'class' => 'form-horizontal form-label-left form-check-status',
                'id' => 'add-candidate-form',
                'url' => ['action' => 'add'],
                'data-parsley-validate' => '',
                'templates' => [
                    'inputContainer' => '{{content}}'
                    ]
                ]) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fullname"><?= __('Họ tên') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('fullname', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'required' => true,
                            'placeholder' => 'Nhập họ tên ứng viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="gender"><?= __('Giới tính') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('gender', [
                            'options' => $gender, 
                            'empty' => true, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'data-parsley-errors-container' => '#error-gender',
                            'data-parsley-class-handler' => '#select2-gender',
                            ]) ?>
                        <span id="error-gender"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="exempt"><?= __('Đăng ký miễn học') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('exempt', [
                            'options' => $yesNoQuestion, 
                            'empty' => true, 
                            'label' => false, 
                            'required' => true, 
                            'data-parsley-errors-container' => '#error-exempt',
                            'data-parsley-class-handler' => '#select2-exempt',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            ]) ?>
                        <span id="error-exempt"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="phone"><?= __('Số điện thoại') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'type' => 'text',
                            'minLength' => 10,
                            'maxlength' => 11,
                            'data-parsley-type' => 'digits',
                            'placeholder' => 'Nhập số điện thoại của ứng viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="appointment_date"><?= __('Ngày viết CV') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="input-group date input-picker" id="appointment-date">
                            <?= $this->Form->control('appointment_date', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#errors-appointment-date'
                                ])
                            ?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="errors-appointment-date"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="birthday"><?= __('Ngày sinh') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="input-group date input-picker" id="candidate-birthday">
                            <?= $this->Form->control('birthday', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#picker-errors'
                                ])
                            ?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="picker-errors"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="address"><?= __('Quê quán') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->hidden('addresses.0.type', ['value' => $addressType[0]]) ?>
                        <?= $this->Form->control('addresses.0.city_id', [
                            'options' => $cities, 
                            'empty' => true, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'data-parsley-errors-container' => '#error-address',
                            'data-parsley-class-handler' => '#select2-addresses-0-city-id',
                            ]) ?>
                        <span id="error-address"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="edu-level"><?= __('Trình độ học vấn') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('educational_level', [
                            'options' => $eduLevel, 
                            'empty' => true, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'data-parsley-errors-container' => '#error-edu-level',
                            'data-parsley-class-handler' => '#select2-educational-level',
                            ]) ?>
                        <span id="error-edu-level"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="note"><?= __('Ghi chú') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('note', [
                            'label' => false, 
                            'type' => 'textarea',
                            'rows' => 3,
                            'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                            'placeholder' => 'Nhập nội dung ghi chú cuộc hẹn'
                            ]) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-candidate-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="add-candidate-close-btn" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="view-candidate-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÔNG TIN LỊCH HẸN</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <div class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="fullname-name"><?= __('Họ tên') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-name"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="gender"><?= __('Giới tính') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-gender"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="exempt"><?= __('Đăng ký miễn học') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-exempt"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="phone"><?= __('Số điện thoại') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-phone"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="appointment_date"><?= __('Ngày viết CV') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-appointment-date"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="birthday"><?= __('Ngày sinh') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-birthday"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="address"><?= __('Quê quán') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-address"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="edu_level"><?= __('Trình độ học vấn') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-edu-level"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="note"><?= __('Ghi chú') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12" style="height:unset;">
                                    <span id="view-candidate-note"></span>
                                </div>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-created-by"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-created"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group modified">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-modified-by"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group modified">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-candidate-modified"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
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
                        <tbody id="export-container"></tbody>
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

<script id="export-template" type="text/x-handlebars-template">
    <tr>
        <td class="cell"><?= __('1') ?></td>
        <td class="cell"><?= __('Sơ yếu lý lịch') ?></td>
        <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./students/export-resume/{{studentId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell"><?= __('2') ?></td>
        <td class="cell"><?= __('Hợp đồng lao động (tiếng Nhật)') ?></td>
        <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./students/export-contract/{{studentId}}?lang=jp"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell"><?= __('3') ?></td>
        <td class="cell"><?= __('Hợp đồng lao động (tiếng Việt)') ?></td>
        <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./students/export-contract/{{studentId}}?lang=vn"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell"><?= __('4') ?></td>
        <td class="cell"><?= __('1.10') ?></td>
        <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./students/export-edu-plan/{{studentId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell"><?= __('5') ?></td>
        <td class="cell"><?= __('1.13') ?></td>
        <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./students/export-company-commitment/{{studentId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
</script>