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
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);

$this->assign('title', 'Thêm mới nhân viên');
?>

<?= $this->Form->unlockField('permissions') ?>

<?php $this->start('content-header'); ?>
<h1><?= __('THÊM MỚI NHÂN VIÊN') ?></h1>
<button class="btn btn-success submit-user-btn" id="create-user-btn" type="button">Lưu lại</button>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li>
        <?= $this->Html->link(__('Danh sách nhân viên'), ['controller' => 'Users', 'action' => 'index']) ?>
    </li>
    <li class="active">Thêm mới nhân viên</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= __('Thông tin nhân viên') ?>
                </h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
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
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="username"><?= __('Tài khoản') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('username', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên tài khoản đăng nhập'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fullname"><?= __('Tên nhân viên') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('fullname', [
                            'label' => false,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập họ tên của nhân viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="email"><?= __('Email') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('email', [
                            'label' => false,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ mail của nhân viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="gender"><?= __('Giới tính') ?></label>
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
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="phone"><?= __('Điện thoại') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                            'class' => 'form-control col-md-7 col-xs-12',
                            'placeholder' => 'Nhập số điện thoại của nhân viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="birthday"><?= __('Ngày sinh') ?></label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <div class="input-group date" id="user-birthday">
                            <?= $this->Form->control('birthday', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-birthday'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
        
                <div class="ln_solid"></div>
                
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="role"><?= __('Chức vụ') ?></label>
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
                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="permission"><?= __('Quyền hạn') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary btn-permission" id="add-permission-top"><?= __('Thêm quyền') ?></button>
                        <table class="table table-bordered custom-table permission-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-md-5"><?= __('Phạm vi') ?></th>
                                    <th scope="col" class="col-md-5"><?= __('Quyền hạn') ?></th>
                                    <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                                </tr>
                            </thead>
                            <tbody id="permission-container">
                            </tbody>
                        </table>
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
                'class' => 'select-scope form-control col-md-5 select-group',
                ]) ?>
        </td>
        <td>
            <?= $this->Form->control('{{permission}}', [
                'options' => $permission, 
                'empty' => true, 
                'required' => true, 
                'label' => false, 
                'id' => 'permission-{{counter}}',
                'class' => 'select-action form-control col-md-5 select-group',
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