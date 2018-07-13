<?php
use Cake\Core\Configure;

$lessons = Configure::read('lessons');
$skills = Configure::read('skills');

$action = $this->request->getParam('action');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('jtest.js', ['block' => 'scriptBottom']);
?>

<?php $this->start('content-header'); ?>
    <?php if ($action === 'add'): ?>
    <h1><?= __('Add New Test') ?></h1>
    <button class="btn btn-success submit-test-btn" type="button">Submit</button>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Home',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Test'), [
                'controller' => 'Jtests',
                'action' => 'index']) ?>
        </li>
        <li class="active">New Test</li>
    </ol>
    <?php else: ?>
    <h1><?= __('Update Test') ?></h1>
    <button class="btn btn-success submit-test-btn" type="button">Submit</button>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Home',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Classes'), [
                'controller' => 'Jtests',
                'action' => 'index']) ?>
        </li>
        <li class="active">Update Test</li>
    </ol>
    <?php endif; ?>
<?php $this->end(); ?>

<?= $this->Form->create($jtest, [
    'class' => 'form-horizontal form-label-left',
    'id' => 'add-test-form',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
<?= $this->Form->hidden('id') ?>
<?= $this->Form->hidden('changed', ['value' => 'false']) ?>
<?= $this->Form->hidden('flag') ?>

<?= $this->Form->unlockField('jtest_contents') ?>
<?= $this->Form->unlockField('students') ?>
<?= $this->Form->unlockField('changed') ?>
<?= $this->Form->unlockField('flag') ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Thông tin kì thi') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="overlay hidden" id="add-test-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="test_date"><?= __('Ngày thi') ?></label>
                    <div class="col-md-3 col-sm-7 col-xs-12" style="padding-right:5px;">
                        <div class="input-group date input-picker gt-now" id="class-start">
                            <?= $this->Form->control('test_date', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'yyyy-mm-dd',
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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jclass_id"><?= __('Lớp thi') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('jclass_id', [
                            'options' => $jclasses, 
                            'required' => true, 
                            'empty' => true, 
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-jclass',
                            'data-parsley-class-handler' => '#select2-jclass-id',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-jclass"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="test_lessons"><?= __('Bài thi') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('lesson_from', [
                                'options' => $lessons, 
                                'required' => true, 
                                'empty' => true, 
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-lesson-from',
                                'data-parsley-class-handler' => '#select2-lesson-from',
                                'data-parsley-max-message' => '',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme select-lesson-from'
                                ]) ?>
                            <span id="error-lesson-from"></span>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('lesson_to', [
                                'options' => $lessons, 
                                'required' => true, 
                                'empty' => true, 
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-lesson-to',
                                'data-parsley-class-handler' => '#select2-lesson-to',
                                'data-parsley-max-message' => '',
                                'data-parsley-min-message' => '',
                                'data-parsley-range-message' => 'Please select option between lesson from and current lesson',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme select-lesson-to'
                                ]) ?>
                            <span id="error-lesson-to"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contents"><?= __('Kỹ năng thi') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary" id="add-skill" onclick="showAddSkillModal()"><?= __('Add new skill') ?></button>
                        <table class="table table-bordered custom-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-md-2"><?= __('STT') ?></th>
                                    <th scope="col" class="col-md-3"><?= __('Kỹ năng') ?></th>
                                    <th scope="col" class="col-md-4"><?= __('Giáo viên phụ trách') ?></th>
                                    <th scope="col" class="actions"></th>
                                </tr>
                            </thead>
                            <tbody id="skill-container">
                            <?php if (!empty($jtest->jtest_contents)): ?>
                            <?php foreach ($jtest->jtest_contents as $key => $value): ?>
                                <div class="hidden skill-id" id="skill-id-<?=$key?>">
                                    <?= $this->Form->hidden('jtest_contents.'  . $key . '.id') ?>
                                </div>
                                <tr class="row-skill" id="row-skill-<?=$key?>">
                                    <td class="cell col-md-2 stt-col">
                                        <?= $key+1 ?>
                                    </td>
                                    <td class="cell col-md-3">
                                        <span class="skill-name"><?= $skills[$value->skill] ?></span>
                                        <div class="hidden">
                                            <?= $this->Form->control('jtest_contents.'. $key .'.skill', [
                                                'options' => $skills,
                                                'label' => false,
                                                'class' => 'form-control skill'
                                                ]) ?>
                                        </div>
                                    </td>
                                    <td class="cell col-md-4">
                                        <span class="teacher-name"><?= h($value->user->fullname) ?></span>
                                        <div class="hidden">
                                            <?= $this->Form->control('jtest_contents.' . $key . '.user_id', [
                                                'options' => $teachers,
                                                'label' => false,
                                                'class' => 'form-control teacher user_id'
                                                ]) ?>
                                        </div>
                                    </td>
                                    <td class="cell actions">
                                        <?= $this->Html->link(
                                            '<i class="fa fa-2x fa-pencil"></i>', 
                                            'javascript:;',
                                            [
                                                'escape' => false,
                                                'onClick' => "showEditSkillModal(this)"
                                            ]) 
                                        ?>
                                        <?= $this->Html->link(
                                            '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                            'javascript:;',
                                            [
                                                'escape' => false, 
                                                'onClick' => "deleteSkill(this, true)"
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

                <div class="hidden" id="student-test-container">
                <?php if (!empty($jtest->students)): ?>
                <?php foreach ($jtest->students as $key => $value): ?>
                    <?= $this->Form->hidden('students.'.$key.'._joinData.id') ?>
                <?php endforeach; ?>
                <?php endif; ?>
                </div>

                <div class="hidden" id="student-container">
                <?php if (!empty($jtest->students)): ?>
                <?php foreach ($jtest->students as $key => $value): ?>
                    <?= $this->Form->control('students.'.$key.'.id') ?>
                <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>

<div id="add-skill-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                <?= $this->Form->create(null, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-skill-form', 
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="skill"><?= __('Skill') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('modal.skill', [
                                'options' => $skills, 
                                'empty' => true,
                                'label' => false,
                                'required' => true,
                                'data-parsley-errors-container' => '#error-skill',
                                'data-parsley-class-handler' => '#select2-modal-skill',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-skill"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="teacher"><?= __('Teacher') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('modal.teacher', [
                                'options' => $teachers, 
                                'empty' => true,
                                'label' => false,
                                'required' => true,
                                'data-parsley-errors-container' => '#error-teacher',
                                'data-parsley-class-handler' => '#select2-modal-teacher',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-teacher"></span>
                        </div>
                    </div>
                <?= $this->Form->end() ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add-skill-btn" onclick="addSkill()">Submit</button>
                <button type="button" class="btn btn-default" id="close-add-skill-modal-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script id="skill-template" type="text/x-handlebars-template">
    <tr class="row-skill" id="row-skill-{{counter}}">
        <td class="cell col-md-2 stt-col">
            {{row}}
        </td>
        <td class="cell col-md-3">
            <span class="skill-name">{{skillText}}</span>
            <div class="hidden">
                <?= $this->Form->control('jtest_contents.{{counter}}.skill', [
                    'options' => $skills,
                    'label' => false,
                    'class' => 'form-control skill'
                    ]) ?>
            </div>
        </td>
        <td class="cell col-md-4">
            <span class="teacher-name">{{teacherText}}</span>
            <div class="hidden">
                <?= $this->Form->control('jtest_contents.{{counter}}.user_id', [
                    'options' => $teachers,
                    'label' => false,
                    'class' => 'form-control teacher'
                    ]) ?>
            </div>
        </td>
        <td class="cell actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditSkillModal(this)"
                ]) 
            ?>
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => "deleteSkill(this)"
                ]
            )?>
        </td>
    </tr>
</script>

<script id="student-test-template" type="text/x-handlebars-template">
    {{#each this}}
        <?= $this->Form->control('students.{{@index}}._joinData.id', ['value' => '{{id}}', 'label' => false]) ?>
    {{/each}}
</script>

<script id="student-template" type="text/x-handlebars-template">
    {{#each this}}
        <?= $this->Form->control('students.{{@index}}.id', ['value' => '{{student_id}}', 'label' => false]) ?>
    {{/each}}
</script>