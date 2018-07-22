<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$scope = Configure::read('scope');
$confPermission = Configure::read('permission');
$currentUser = $this->request->session()->read('Auth.User');


# Additional style + script
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('user.css', ['block' => 'styleTop']);
$this->Html->css('cropper.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('cropper.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);

$this->assign('title', 'Cập nhật hồ sơ cá nhân');
?>

<?= $this->Form->unlockField('b64code') ?>
<?= $this->Form->unlockField('image') ?>

<?php $this->start('content-header'); ?>
<h1><?= __('CẬP NHẬT HỒ SƠ CÁ NHÂN') ?></h1>
<button class="btn btn-success submit-user-btn" id="update-profile-btn" type="button">Lưu lại</button>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Hồ sơ cá nhân</li>
</ol>
<?php $this->end(); ?>

<div class="clearfix"></div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <?= $this->Form->create($user, [
            'class' => 'form-horizontal form-label-left', 
            'id' => 'update-profile-form',
            'data-parsley-validate' => '',
            'templates' => [
                'inputContainer' => '{{content}}'
                ]
            ]) ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= __('Thông tin cá nhân') ?>
                </h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Họ và tên') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('fullname', [
                            'label' => false,
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập họ tên của bạn'
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
                            'placeholder' => 'Nhập địa chỉ mail của bạn'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Số điện thoại') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12',
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                            'placeholder' => 'Nhập số điện thoại của bạn'
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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password"><?= __('Mật khẩu') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#change-password-modal"><?= __('Thay đổi Password') ?></button>
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
                <h4 class="modal-title">Chỉnh sửa ảnh</h4>
            </div>
            <div class="modal-body">
                <div class="image_container col-md-12 col-xs-12">
                    <img id="avatar" src />
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="crop-btn" data-dismiss="modal">Cắt hình</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="change-password-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content box">
            <div class="overlay hidden" id="change-password-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THAY ĐỔI MẬT KHẨU</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'url' => ['controller' => 'Users', 'action' => 'changePassword', $currentUser['id']],
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
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password"><?= __('Mật khẩu hiện tại') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <?= $this->Form->control('current-password', [
                                'label' => false,
                                'type' => 'password',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập mật khẩu hiện tại'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password"><?= __('Mật khẩu mới') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <?= $this->Form->control('new-password', [
                                'label' => false,
                                'type' => 'password',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12',
                                'data-parsley-minlength' => '8',
                                'placeholder' => 'Nhập mật khẩu mới'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="password"><?= __('Nhập lại mật khẩu mới') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <?= $this->Form->control('confirm-password', [
                                'label' => false,
                                'type' => 'password',
                                'required' => true,
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'data-parsley-equalto' => '#new-password',
                                'placeholder' => 'Nhập lại mật khẩu mới'
                                ]) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="change-password-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>