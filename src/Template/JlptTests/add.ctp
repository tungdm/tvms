<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$skills = Configure::read('jlpt_skills');
$jlptLevels = Configure::read('jlpt_levels');

$action = $this->request->getParam('action');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');
$supervisory = false;
if (!empty($jtest->jlpt_contents)) {
    foreach ($jtest->jlpt_contents as $key => $content) {
        if ($content->user_id == $currentUser['id']) {
            // current user only have read access but they are the supervisory
            $supervisory = true;
            break;
        }
    }
}
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('jlpt.js', ['block' => 'scriptBottom']);
?>

<?php if ($action === 'add'): ?>
    <?php $this->assign('title', 'Thêm mới kì thi JLPT'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('THÊM MỚI KÌ THI JLPT') ?></h1>
        <button class="btn btn-success submit-test-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách kì thi'), [
                    'controller' => 'JlptTests',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Thêm mới kì thi</li>
        </ol>
    <?php $this->end(); ?>
<?php else: ?>
    <?php 
        $status = 0;
        $now = Time::now()->i18nFormat('yyyy-MM-dd');
        $test_date = $jlptTest->test_date->i18nFormat('yyyy-MM-dd');
        if ($jlptTest->status == "4" || $jlptTest->status == "5") {
            $status = (int) $jlptTest->status;
        } elseif ($now < $test_date) {
            $status = 1;
        } elseif ($now == $test_date) {
            $status = 2;
        } else {
            $status = 3;
        }
    ?>
    <?php $this->assign('title', 'Kì thi ' . $jlptTest->level . ' ngày ' . $jlptTest->test_date . '- Cập nhật thông tin'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('CẬP NHẬT THÔNG TIN KÌ THI JLPT') ?></h1>
        <button class="btn btn-success submit-test-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách kì thi'), [
                    'controller' => 'JlptTests',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Kì thi <?= $jlptTest->level . ' ' . $jlptTest->test_date ?></li>
        </ol>
    <?php $this->end(); ?>
<?php endif; ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($action === 'edit'): ?>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                        ['action' => 'view', $jlptTest->id],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Xem chi tiết',
                            'escape' => false
                        ]) ?>
                </li>
                <?php if ($permission == 0): ?>
                    <?php if ($jlptTest->del_flag): ?>
                        <li>
                            <?= $this->Form->postLink(__('<i class="fa fa-undo" aria-hidden="true"></i>'), 
                                ['action' => 'recover', $jlptTest->id], 
                                [
                                    'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                                    'escape' => false, 
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Phục hồi',
                                    'confirm' => __('Bạn có chắc chắn muốn phục hồi kì thi {0}?', $jlptTest->test_date)
                                ]) ?>
                        </li>
                    <?php else: ?>
                        <?php if ($status == 4): ?>
                            <li>
                                <?= $this->Form->postLink('<i class="fa fa-lock" aria-hidden="true"></i>', 
                                ['action' => 'finish', $jlptTest->id], 
                                [
                                    'class' => 'zoom-fab zoom-btn-sm zoom-btn-close scale-transition scale-out',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Đóng',
                                    'escape' => false, 
                                    'confirm' => __('Bạn có chắc chắn muốn đóng kì thi {0}?', $jlptTest->test_date)
                                ]) ?>
                            </li>
                        <?php endif; ?>
                        <li>
                            <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                                ['action' => 'delete', $jlptTest->id], 
                                [
                                    'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                                    'escape' => false, 
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Xóa',
                                    'confirm' => __('Bạn có chắc chắn muốn xóa kì thi {0}?', $jlptTest->test_date)
                                ]) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($currentUser['role_id'] == 1 || $status < 5 && $status >= 2 && ($supervisory == true || $permission == 0)): ?>
                    <li>
                        <?= $this->Html->link('<i class="fa fa-check" aria-hidden="true"></i>', 
                            ['action' => 'setScore', $jlptTest->id],
                            [
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                                'data-toggle' => 'tooltip',
                                'title' => 'Nhập điểm',
                                'escape' => false
                            ]) ?>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <li>
                <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out submit-test-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>

<?= $this->Form->create($jlptTest, [
    'class' => 'form-horizontal form-label-left',
    'id' => 'add-test-form',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
<?= $this->Form->unlockField('students') ?>
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin kì thi') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="test_date"><?= __('Ngày thi') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="input-group date input-picker" id="jlpt-test-date-div">
                                <?= $this->Form->control('test_date', [
                                    'type' => 'text',
                                    'label' => false, 
                                    'class' => 'form-control',
                                    'placeholder' => 'dd-mm-yyyy',
                                    'required' => true,
                                    'data-parsley-errors-container' => '#error-test-date'
                                    ])?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <span id="error-test-date"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="level"><?= __('Trình độ') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <?= $this->Form->control('level', [
                                'options' => $jlptLevels, 
                                'required' => true, 
                                'empty' => true, 
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-level',
                                'data-parsley-class-handler' => '#select2-level',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-level"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="level"><?= __('Giáo viên canh thi') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <table class="table table-bordered custom-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                        <th scope="col" class="col-md-5"><?= __('Kỹ năng') ?></th>
                                        <th scope="col" class="col-md-6"><?= __('Giáo viên phụ trách') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($skills as $key => $value): ?>
                                        <tr>
                                            <td class="text-center"><?= $key ?></td>
                                            <td>
                                                <?= $skills[$key] ?>
                                                <div class="hidden">
                                                    <?= $this->Form->control('jlpt_contents.'.($key-1).'.skill', [
                                                        'label' => false,
                                                        'class' => 'form-control col-md-7 col-xs-12',
                                                        'value' => $key
                                                        ]) ?>
                                                    <?php if (!empty($jlptTest->jlpt_contents)): ?>
                                                    <?= $this->Form->control('jlpt_contents.'.($key-1).'.id', [
                                                        'label' => false,
                                                        'class' => 'form-control col-md-7 col-xs-12',
                                                        'value' => $jlptTest->jlpt_contents[$key-1]->id
                                                        ]) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?= $this->Form->control('jlpt_contents.'.($key-1).'.user_id', [
                                                    'options' => $teachers, 
                                                    'required' => true, 
                                                    'empty' => true, 
                                                    'label' => false, 
                                                    'data-parsley-errors-container' => '#error-teacher-'.($key-1),
                                                    'data-parsley-class-handler' => '#select2-jlpt-contents-'.($key-1).'-user-id',
                                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                                    ]) ?>
                                                <span id="error-teacher-<?= ($key-1)?>"></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Danh sách thí sinh') ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <button type="button" class="btn btn-primary" id="add-student" onclick="showAddStudentModal();">
                        <?= __('Thêm thí sinh') ?>
                    </button>
                    <table class="table table-bordered custom-table candidate-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-4"><?= __('Họ tên') ?></th>
                                <th scope="col" class="col-md-5"><?= __('Lớp') ?></th>
                                <th scope="col" class="actions col-md-2"><?= __('Thao tác') ?></th>
                            </tr>
                        </thead>
                        <tbody id="jlpt-students-container">
                            <?php if(!empty($jlptTest->students)): ?>
                                <?php foreach($jlptTest->students as $key => $student): ?>
                                    <tr class="row-student" id="row-student-<?=$key?>">
                                        <td class="cell stt-col text-center">
                                            <?= $key + 1 ?>
                                        </td>
                                        <td class="cell">
                                            <a href="javascript:;" onclick="viewStudent(<?= $student->id ?>);"><?= h($student->fullname)?></a>
                                            <div class="hidden">
                                                <?= $this->Form->control('students.'.$key.'.id', [
                                                    'label' => false,
                                                    'class' => 'form-control col-md-7 col-xs-12 jlptStudentId',
                                                    'value' => $student->id
                                                    ]) ?>
                                            </div>
                                        </td>
                                        <td class="cell text-center">
                                            <?= h($student->jclasses ? $student->jclasses[0]->name : 'N/A') ?>
                                        </td>
                                        <td class="actions cell">
                                            <?= $this->Html->link(
                                                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                    'javascript:;',
                                                    [
                                                        'escape' => false, 
                                                        'onClick' => "deleteStudent(this, true)"
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
        </div>
    </div>
<?= $this->Form->end() ?>


<div id="add-student-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content box">
            <div class="overlay hidden" id="add-student-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM THÍ SINH</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'add-student-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="class"><?= __('Lớp thi') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <?= $this->Form->control('modal.jclass', [
                                'options' => $jclasses, 
                                'required' => true, 
                                'empty' => true, 
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-jclass',
                                'data-parsley-class-handler' => '#select2-modal-jclass',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-jclass"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-offset-3 col-sm-offset-3 col-md-8 col-sm-8 col-xs-12 table-responsive">
                                <table class="table table-bordered custom-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                            <th scope="col" class="col-md-8"><?= __('Họ tên') ?></th>
                                            <th scope="col" class="actions col-md-3"><?= __('Thao tác')?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="jclass-container">
                                    </tbody>
                                </table>
                            </div>
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
                <button type="button" class="btn btn-success" id="add-student-btn" onclick="addStudent()">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-add-student-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="jclass-student-template" type="text/x-handlebars-template">
    {{#each this}}
    <tr class="row-rec">
        <td class="cell stt-col text-center">
            {{inc @index}}
        </td>
        <td class="cell">
            <a href="javascript:;" onclick="viewStudent({{id}});" class="student-name-contain">{{fullname}}</a>
            <div class="hidden">
                <?= $this->Form->control('jclass.{{@index}}.studentId', [
                    'label' => false,
                    'class' => 'form-control col-md-7 col-xs-12 studentId',
                    'value' => '{{id}}'
                    ]) ?>
            </div>
        </td>
        <td class="actions cell">
            <input name="student-{{@index}}" type="checkbox" class="js-switch" checked>
        </td>
    </tr>
    {{/each}}
</script>

<script id="jlpt-student-template" type="text/x-handlebars-template">
    {{#each this}}
    <tr class="row-student" id="row-student-{{row}}">
        <td class="cell stt-col text-center">
            {{inc row}}
        </td>
        <td class="cell">
            <a href="javascript:;" onclick="viewStudent({{id}});">{{fullname}}</a>
            <div class="hidden">
                <?= $this->Form->control('students.{{row}}.id', [
                    'label' => false,
                    'class' => 'form-control col-md-7 col-xs-12 jlptStudentId',
                    'value' => '{{id}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell text-center">
            {{jclass}}
        </td>
        <td class="actions cell">
            <?= $this->Html->link(
                    '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                    'javascript:;',
                    [
                        'escape' => false, 
                        'onClick' => "deleteStudent(this)"
                    ]
                )?>
        </td>
    </tr>
    {{/each}}
</script>

<script>
    var jlptId = '<?= $jlptTest->id ?>';
</script>