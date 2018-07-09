<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$scope = Configure::read('scope');
$confPermission = Configure::read('permission');

# Additional style + script
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('user.css', ['block' => 'styleTop']);
$this->Html->css('cropper.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('cropper.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);
?>

<?= $this->Form->unlockField('b64code') ?>
<?= $this->Form->unlockField('image') ?>

<?php $this->start('content-header'); ?>
<h1><?= __('Chỉnh sửa hồ sơ') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Home',
            '/',
            ['escape' => false]) ?>
    </li>
    <li>
        <?= $this->Html->link(__('Users'), ['controller' => 'Users', 'action' => 'index']) ?>
    </li>
    <li class="active">Update profile</li>
</ol>
<?php $this->end(); ?>

<?= $this->Form->button('Lưu lại', [
    'class' => 'btn btn-round btn-success mobile-only update-user-btn', 
    'type' => 'button', 
    'id' => 'update-user-mobile-btn',
    'disabled' => 'disabled'
    ]) ?>

<div class="clearfix"></div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <?= $this->Form->create($user, [
            'class' => 'form-horizontal form-label-left', 
            'data-parsley-validate' => '',
            'templates' => [
                'inputContainer' => '{{content}}'
                ]
            ]) ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= __('Thông tin hồ sơ') ?>
                </h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Fullname') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('fullname', [
                            'label' => false,
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'User\'s fullname'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email"><?= __('Email') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('email', [
                            'label' => false,
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Phone') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12',
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$'
                            ]) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthday"><?= __('Birthday') ?></label>
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

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image"><?= __('Hình ảnh') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('image', [
                            'type' => 'file',
                            'accept' => 'image/*',
                            'label' => false, 
                            'class' => 'form-control avatar-image col-md-7 col-xs-12 square-img',
                            'onchange' => 'readURL(this)'
                            ]) ?>
                        <div id="cropped_result" class="col-md-7 col-xs-12">
                            <?php if(!empty($user->image)):?>
                            <?= $this->Html->image($user->image) ?>
                            <?php endif; ?>
                        </div> 
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password"><?= __('Password') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#change-password-modal"><?= __('Thay đổi Password') ?></button>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <?= $this->Form->hidden('b64code')?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>


<div id="cropper-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <div class="image_container col-md-12 col-xs-12">
                    <img id="avatar" src />
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="crop-btn" data-dismiss="modal">Crop</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="change-password-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'url' => ['controller' => 'Users', 'action' => 'changePassword'],
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'change-password-form',
                        'controller' => 'Users',
                        'data-parsley-validate' => '',
                        'data-parsley-trigger' => 'keyup',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password"><?= __('Current Password') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('current-password', [
                                'label' => false,
                                'type' => 'password',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12', 
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password"><?= __('New Password') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('new-password', [
                                'label' => false,
                                'type' => 'password',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12',
                                'data-parsley-minlength' => '8',
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password"><?= __('Confirm Password') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('confirm-password', [
                                'label' => false,
                                'type' => 'password',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'data-parsley-equalto' => '#new-password'
                                ]) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="change-password-btn">Sumbit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>