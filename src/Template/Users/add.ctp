<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$scope = Configure::read('scope');
$permission = Configure::read('permission');

# Additional style + script
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('user.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);
?>

<?= $this->Form->unlockField('permissions') ?>

<?php $this->start('content-header'); ?>
<h1><?= __('TẠO MỚI NHÂN VIÊN') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang chính',
            '/',
            ['escape' => false]) ?>
    </li>
    <li>
        <?= $this->Html->link(__('Nhân viên'), ['controller' => 'Users', 'action' => 'index']) ?>
    </li>
    <li class="active">Tạo Mới</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= __('Thông tin cơ bản') ?>
                </h3>
            </div>
            <div class="box-body">
                <?= $this->Form->create($user, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'create-user-form', 
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) 
                ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="username"><?= __('Tên tài khoản') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('username', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Vui lòng nhập tên tài khoản'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Tên nhân viên') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('fullname', [
                            'label' => false,
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Vui lòng nhập tên nhân viên. Ví dụ: Nguyễn Văn A'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email"><?= __('Email') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('email', [
                            'label' => false,
                            'required' => true,                            
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'nguyenvana@email.com'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gender"><?= __('Giới tính') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('gender', [
                            'options' => $gender, 
                            'required' => true, 
                            'empty' => true, 
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-gender',
                            'data-parsley-class-handler' => '#select2-gender',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme select-group'
                            ]) ?>
                        <span id="error-gender"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Điện thoại') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'required' => true,
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthday"><?= __('Ngày sinh') ?></label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <div class="input-group date" id="user-birthday">
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
        
                <div class="ln_solid"></div>
                
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="role"><?= __('Chức vụ') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('role_id', [
                            'options' => $roles, 
                            'empty' => true, 
                            'required' => true,
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-role',
                            'data-parsley-class-handler' => '#select2-role-id',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme select-group add-role'
                            ]) ?>
                        <span id="error-role"></span>
                    </div>
                </div>
                <div class="form-group permission-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="permission"><?= __('Permission') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary btn-permission" id="add-permission-top"><?= __('Thêm quyền') ?></button>
                        <table class="table table-bordered custom-table permission-table">
                            <thead>
                                <tr>
                                    <th scope="col"><?= __('Phạm vi') ?></th>
                                    <th scope="col"><?= __('Quyền hạn') ?></th>
                                    <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                                </tr>
                            </thead>
                            <tbody id="permission-container">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <?= $this->Form->button(__('Hoàn tất'), ['class' => 'btn btn-success', 'type' => 'button', 'id' => 'create-user-btn']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script id="permission-template" type="text/x-handlebars-template">    
    <tr class="row-permission" class="row-permission-{{counter}}">
        <td>
            <?= $this->Form->control('{{scope}}', [
                'options' => $scope, 
                'empty' => true,
                'required' => true, 
                'data-parsley-not-duplicate-scope' => '', 
                'label' => false, 
                'id' => 'scope-{{counter}}',
                'class' => 'select-scope form-control col-md-7 col-xs-12 select-group',
                ]) ?>
        </td>
        <td>
            <?= $this->Form->control('{{permission}}', [
                'options' => $permission, 
                'empty' => true, 
                'required' => true, 
                'label' => false, 
                'id' => 'permission-{{counter}}',
                'class' => 'select-action form-control col-md-7 col-xs-12 select-group',
                ]) ?>
        </td>
        <td class="action-btn">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove"></i>',
                'javascript:;',
                ['escape' => false, 
                'class' => 'remove-permission',
                'onClick' => 'removePermission(this)'
                ]
            )?>
        </td>
    </tr>
</script>