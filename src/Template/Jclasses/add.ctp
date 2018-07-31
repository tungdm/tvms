<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$action = $this->request->getParam('action');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;

$gender = Configure::read('gender');
$lessons = Configure::read('lessons');
$currentUser = $this->request->session()->read('Auth.User');

$now = Time::now()->i18nFormat('yyyy-MM-dd');
$historyNow = Time::now()->i18nFormat('dd/MM/yyyy');

$this->Html->css('class.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('class.js', ['block' => 'scriptBottom']);
?>

<?php if ($action === 'add'): ?>
    <?php $this->assign('title', 'Thêm mới lớp học'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('THÊM MỚI LỚP HỌC') ?></h1>
        <button class="btn btn-success submit-class-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách lớp học'), [
                    'controller' => 'Jclasses',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Thêm mới lớp học</li>
        </ol>
    <?php $this->end(); ?>
<?php else: ?>
    <?php $this->assign('title', 'Lớp '  . $jclass->name . ' - Cập nhật thông tin'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('CẬP NHẬT THÔNG TIN LỚP HỌC') ?></h1>
        <button class="btn btn-success submit-class-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách lớp học'), [
                    'controller' => 'Jclasses',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Lớp <?= $jclass->name ?></li>
        </ol>
    <?php $this->end(); ?>
<?php endif; ?>

<?= $this->Form->create($jclass, [
    'class' => 'form-horizontal form-label-left',
    'id' => 'add-class-form',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
<?= $this->Form->unlockField('students') ?>

<?php 
    $testing = 'false'; 
    if (!empty($jclass->jtests)) {
        foreach ($jclass->jtests as $key => $value) {
            if ($now <= $value->test_date) {
                $testing = 'true';
                break;
            }
        }
    }
?>
<?= $this->Form->hidden('have_test', ['value' => $testing])?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Thông tin lớp học') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"><?= __('Tên lớp') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php if ($jclass->user_id == $currentUser['id']): ?>
                        <div class="form-control form-control-view col-md-7 col-xs-12">
                            <?= h($jclass->name) ?>
                        </div>
                        <?php else: ?>
                        <?= $this->Form->control('name', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'required' => true,
                            'error' => false,
                            'placeholder' => 'Nhập tên lớp học'
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start"><?= __('Ngày bắt đầu') ?></label>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <?php if ($jclass->user_id == $currentUser['id']): ?>
                        <div class="form-control form-control-view col-md-7 col-xs-12">
                            <?= h($jclass->start) ?>
                        </div>
                        <?php else: ?>
                        <div class="input-group date input-picker" id="class-start">
                            <?= $this->Form->control('start', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'yyyy-mm-dd',
                                'required' => true,
                                'data-parsley-errors-container' => '#error-start'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-start"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="user_id"><?= __('Giáo viên chủ nhiệm') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php if ($jclass->user_id == $currentUser['id']): ?>
                        <div class="form-control form-control-view col-md-7 col-xs-12">
                            <?= h($jclass->user->fullname) ?>
                        </div>
                        <?php else: ?>
                        <?= $this->Form->control('user_id', [
                            'options' => $teachers, 
                            'required' => true, 
                            'empty' => true, 
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-teacher',
                            'data-parsley-class-handler' => '#select2-user-id',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-teacher"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="current_lesson"><?= __('Bài đang học') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('current_lesson', [
                            'options' => $lessons, 
                            'required' => true, 
                            'empty' => true, 
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-current-lesson',
                            'data-parsley-class-handler' => '#select2-current-lesson',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-current-lesson"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="overlay hidden" id="list-student-class-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Danh sách học sinh') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php if ($permission == 0): ?>
                <button type="button" class="btn btn-primary btn-student" id="add-student" onclick="showAddStudentModal();">
                    <?= __('Thêm học sinh') ?>
                </button>
                <?php endif; ?>
                <table class="table table-bordered custom-table students-table">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('STT') ?></th>
                            <th scope="col"><?= __('Mã TTS') ?></th>
                            <th scope="col"><?= __('Họ tên') ?></th>
                            <th scope="col"><?= __('Giới tính') ?></th>
                            <th scope="col"><?= __('Số điện thoại') ?></th>
                            <th scope="col"><?= __('Ngày nhập học') ?></th>
                            <th scope="col" class="actions"><?= h('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody id="student-container">
                    <?php if (!empty($jclass->students)): ?>
                        <?php $counter = 0; ?>
                        <?php foreach ($jclass->students as $key => $value): ?>
                            <div class="hidden student-id" id="student-<?=$counter?>-id">
                                <?= $this->Form->hidden('students.'  . $key . '.id', ['value' => $value->id]) ?>
                            </div>
                            <div class="hidden class-std-id" id="class-student-<?=$counter?>-id">
                                <?= $this->Form->hidden('students.' . $key . '._joinData.id') ?>
                            </div>
                            <tr class="row-std" id="row-student-<?=$counter?>">
                                <td class="cell col-md-1 stt-col">
                                    <?= $counter+1 ?>
                                </td>
                                <td class="cell hidden">
                                    <?= $this->Form->control('students.'.$key.'._joinData.note', [
                                        'label' => false, 
                                        'type' => 'textarea',
                                        'class' => 'form-control col-md-7 col-xs-12 note', 
                                        ]) ?>
                                </td>
                                <td class="cell col-md-2">
                                    <?= h($value->code) ?>
                                </td>
                                <td class="cell col-md-3">
                                    <a href="javascript:;" onclick="viewStudent('<?=$value->id?>');"><?= h($value->fullname) ?></a>
                                </td>
                                <td class="cell col-md-1">
                                    <?= $gender[$value->gender]?>
                                </td>
                                <td class="cell col-md-2">
                                    <?= $this->Phone->makeEdit($value->phone) ?>
                                </td>
                                <td class="cell col-md-1" style="width: 12.499999995%;">
                                    <?= !empty($value->enrolled_date) ? $value->enrolled_date : 'N/A' ?>
                                </td>
                                <td class="cell actions">
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li>
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-history"></i> Ghi chú', 
                                                    'javascript:;',
                                                    [
                                                        'escape' => false,
                                                        'onClick' => "showHistoryModal($value->id)"
                                                    ]) 
                                                ?>
                                            </li>
                                            <?php if ($permission == 0): ?>
                                            <li>
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-exchange"></i> Chuyển lớp', 
                                                    'javascript:;',
                                                    [
                                                        'escape' => false,
                                                        'onClick' => "showChangeClassModal(this)"
                                                    ])
                                                ?>
                                            </li>
                                            <li>
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-trash"></i> Xóa',
                                                    'javascript:;',
                                                    [
                                                        'escape' => false, 
                                                        'onClick' => "deleteStudent(this, true)"
                                                    ]
                                                )?>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
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
<?= $this->Form->end() ?>

<div id="add-student-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM HỌC VIÊN</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <div class="overlay hidden" id="add-student-modal-overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'add-student-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="input-group">
                                <?= $this->Form->control('student.name', [
                                    'label' => false, 
                                    'options' => [],
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    ]) ?>
                                <span class="input-group-btn">
                                    <button type="button" onclick="preAddStudent()" class="btn btn-primary btn-flat">
                                        <i class="fa fa-fw fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <table class="table table-bordered custom-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                        <th scope="col" class="col-md-4"><?= __('Họ tên') ?></th>
                                        <th scope="col" class="col-md-2"><?= __('Giới tính') ?></th>
                                        <th scope="col" class="col-md-3"><?= __('Số ĐT') ?></th>
                                        <th scope="col" class="actions"></th>
                                    </tr>
                                </thead>
                                <tbody id="pre-add-student-container">
                                </tbody>
                            </table>
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
                <button type="button" class="btn btn-success" id="add-candidate-btn" onclick="addStudent()">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="edit-student-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">BẢNG GHI CHÚ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'edit-student-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <?= $this->Form->control('modal.note', [
                                'label' => false,
                                'type' => 'textarea',
                                'class' => 'form-control edittextarea', 
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
                <button type="button" class="btn btn-success" id="edit-student-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-edit-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="change-class-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content box">
            <div class="overlay hidden" id="change-class-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CHUYỂN LỚP HỌC</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'change-class-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group change-class-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="change_class"><?= __('Chuyển lớp') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('modal.class', [
                                'options' => $classes, 
                                'empty' => true,
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-class',
                                'data-parsley-class-handler' => '#select2-modal-class',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-class"></span>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="change-class-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-change-class-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="pre-add-student-template" type="text/x-handlebars-template">
    <tr class="row-pre" id="row-student-{{counter}}">
        <td class="cell col-md-1 stt-col">
            {{row}}
        </td>
        <td class="hidden">
            <?= $this->Form->control('studentId', [
                'type' => 'text',
                'label' => false,
                'class' => 'form-control',
                'value' => '{{id}}'
                ])?>
            
            <?= $this->Form->control('studentCode', [
                'type' => 'text',
                'label' => false,
                'class' => 'form-control',
                'value' => '{{code}}'
                ])?>

            <?= $this->Form->control('studentEnrolledDate', [
                'type' => 'text',
                'label' => false,
                'class' => 'form-control',
                'value' => '{{enrolledDate}}'
                ])?>
        </td>
        <td class="cell col-md-4">
            <a href="javascript:;" onclick="viewStudent({{id}});">{{fullname}}</a>
            <div class="hidden">
                <?= $this->Form->control('fullname', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control',
                    'value' => '{{fullname}}'
                    ])?>
            </div>
        </td>
        <td class="cell col-md-2">
            {{trans gender}}
            <div class="hidden">
                <?= $this->Form->control('gender', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control',
                    'value' => '{{gender}}'
                    ])?>
            </div>
        </td>
        <td class="cell col-md-3">
            {{phoneFormat phone}}
            <div class="hidden">
                <?= $this->Form->control('phone', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control',
                    'value' => '{{phone}}'
                    ])?>
            </div>
        </td>
        <td>
            <input name="student-{{row}}" type="checkbox" id="std-{{id}}" class="js-switch">
        </td>
    </tr>
</script>

<script id="add-student-template" type="text/x-handlebars-template">
    {{#each this}}
    <tr class="row-std" id="row-student-{{row}}">
        <td class="cell col-md-1 stt-col">
            {{inc row}}
        </td>
        <td class="cell hidden">
            <?= $this->Form->control('students.{{row}}.id', [
                'type' => 'text',
                'label' => false,
                'class' => 'form-control id',
                'value' => '{{id}}'
                ])?>
            <?= $this->Form->control('students.{{row}}._joinData.note', [
                'label' => false, 
                'type' => 'textarea',
                'class' => 'form-control col-md-7 col-xs-12 note', 
                ]) ?>
        </td>
        <td class="cell col-md-2">
            {{code}}
        </td>
        <td class="cell col-md-3">
            <a href="javascript:;" onclick="viewStudent({{id}});">{{fullname}}</a>
        </td>
        <td class="cell col-md-1">
            {{trans gender}}
        </td>
        <td class="cell col-md-2">
            {{phoneFormat phone}}
        </td>
        <td class="cell col-md-1" style="width: 12.499999995%;">
            {{enrolledDate}}
        </td>
        <td class="actions cell">
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">
                    Mở rộng <span class="caret"></span>
                </button>
                <ul role="menu" class="dropdown-menu">
                    <li>
                        <?= $this->Html->link(
                            '<i class="fa fa-trash"></i> Xóa',
                            'javascript:;',
                            [
                                'escape' => false, 
                                'onClick' => "deleteStudent(this)"
                            ]
                        )?>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    {{/each}}
</script>

<div id="all-histories-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content box">
            <div class="overlay hidden" id="list-history-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" id="add-history"><i class="fa fa-plus"></i></a>
                    <a href="javascript:;" class="btn btn-box-tool" id="refresh-history"><i class="fa fa-refresh"></i></a>
                    <a href="javascript:;" class="btn btn-box-tool" data-dismiss="modal" id="close-history"><i class="fa fa-remove"></i></a>
                </div>
                <h4 class="modal-title">BẢNG GHI CHÚ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <ul class="timeline">
                        <li class="time-label" id="now-tl">
                            <span class="bg-black"><?= h($historyNow) ?></span>
                        </li>
                        
                        <li class="time-label">
                            <span class="bg-blue" id="student-created"></span>
                        </li>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="close-all-histories-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>