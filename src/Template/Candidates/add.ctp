<?php
use Cake\Core\Configure;
$controller = $this->request->getParam('controller');
$action = $this->request->getParam('action');
$permission = $this->request->session()->read($controller) ?? 0;
$candidateSource = Configure::read('candidateSource');
$gender = Configure::read('gender');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('candidate.js', ['block' => 'scriptBottom']);
?>

<?php $this->assign('title', 'Thêm ứng viên mới'); ?>
<?php $this->start('content-header'); ?>
    <h1><?= __('THÊM ỨNG VIÊN MỚI') ?></h1>
    <button class="btn btn-success submit-candidate-btn" type="button">Lưu lại</button>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Danh sách ứng viên'), [
                'action' => 'index']) ?>
        </li>
        <li class="active">Thêm ứng viên</li>
    </ol>
<?php $this->end(); ?>


<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li>
                <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out submit-candidate-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>

<?= $this->Form->create($candidate, [
    'class' => 'form-horizontal form-label-left',
    'id' => 'add-candidate-form',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin cơ bản') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="source"><?= __('Nguồn') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('source', [
                                'options' => $candidateSource, 
                                'required' => true, 
                                'empty' => true, 
                                'label' => false, 
                                'data-parsley-errors-container' => '#error-source',
                                'data-parsley-class-handler' => '#select2-source',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-source"></span>
                        </div>
                    </div>
                    <div class="form-group facebook-group hidden">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fb_name"><?= __('Tên Facebook') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('fb_name', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập tên facebook của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group facebook-group hidden">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fb_link"><?= __('Link Facebook') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('fb_link', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập link facebook của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fullname"><?= __('Họ tên') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('fullname', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập tên ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="contact_date"><?= __('Ngày liên hệ') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <div class="input-group date input-picker" id="contact-date-div">
                                <?= $this->Form->control('contact_date', [
                                    'type' => 'text',
                                    'label' => false, 
                                    'class' => 'form-control',
                                    'placeholder' => 'dd-mm-yyyy',
                                    'required' => true,
                                    'data-parsley-errors-container' => '#error-contact-date'
                                    ])?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <span id="error-contact-date"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="phone"><?= __('Số điện thoại') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('phone', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'type' => 'text',
                                'maxlength' => 11,
                                'required' => true,
                                'data-parsley-type' => 'digits',
                                'placeholder' => 'Nhập số điện thoại của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="gender"><?= __('Giới tính') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('gender', [
                                'options' => $gender, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="city"><?= __('Địa chỉ') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('city_id', [
                                'options' => $cities, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="message"><?= __('Tin nhắn') ?></label>
                        <div class="col-md-4 col-sm-7 col-xs-12">
                            <?= $this->Form->control('description', [
                                'label' => false, 
                                'type' => 'textarea',
                                'rows' => 5,
                                'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                                'placeholder' => 'Nhập nội dung tin nhắn của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->Form->end() ?>