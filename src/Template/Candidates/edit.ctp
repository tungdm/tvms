<?php
use Cake\Core\Configure;
$controller = $this->request->getParam('controller');
$action = $this->request->getParam('action');
$permission = $this->request->session()->read($controller) ?? 0;
$candidateSource = Configure::read('candidateSource');
$gender = Configure::read('gender');
$eduLevel = Configure::read('eduLevel');
$eduLevel = array_map('array_shift', $eduLevel);
$candidateName = $candidate->source == 1 ? $candidate->fb_name : $candidate->fullname;


$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('candidate.js', ['block' => 'scriptBottom']);
?>

<?php $this->assign('title', 'Cập nhật ứng viên' . $candidateName); ?>
<?php $this->start('content-header'); ?>
    <h1><?= __('CẬP NHẬT THÔNG TIN ỨNG VIÊN') ?></h1>
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
        <li class="active"><?= $candidateName ?></li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li>
                <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                    ['action' => 'view', $candidate->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Xem chi tiết',
                        'escape' => false
                    ]) ?>
            </li>
            <li>
                <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                    ['action' => 'delete', $candidate->id], 
                    [
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                        'escape' => false, 
                        'data-toggle' => 'tooltip',
                        'title' => 'Xóa',
                        'confirm' => __('Bạn có chắc chắn muốn xóa ứng viên {0}?', $candidateName)
                    ]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('<i class="fa fa-angle-double-up" style="font-size: 1.3em" aria-hidden="true"></i>'), 
                    ['controller' => 'Students', 'action' => 'info', '?' => ['candidateId' => $candidate->id]],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Kí kết chính thức',
                        'escape' => false
                    ]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('<i class="fa fa-plus" aria-hidden="true"></i>'), 
                    ['action' => 'add'],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Thêm mới',
                        'escape' => false
                    ]) ?>
            </li>
            <?php if ($candidate->status == 4): ?>
                <li>
                    <?= $this->Html->link('<i class="fa fa-briefcase" aria-hidden="true"></i>', 
                        ['action' => 'viewStudent', $candidate->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Thông tin lao động',
                        ]) ?>
                </li>
            <?php endif; ?>
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
    <?= $this->Form->unlockField('potential') ?>
    <?= $this->Form->unlockField('consultant_notes') ?>
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
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="source"><?= __('Nguồn') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
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
                    <div class="form-group facebook-group <?= $candidate->source != 1 ? 'hidden' : '' ?>">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fb_name"><?= __('Tên Facebook') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('fb_name', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập tên facebook của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group facebook-group <?= $candidate->source != 1 ? 'hidden' : '' ?>">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fb_link"><?= __('Link Facebook') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('fb_link', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập link facebook của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="fullname"><?= __('Họ tên') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('fullname', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập tên ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="phone"><?= __('Số điện thoại') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
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
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="zalo_phone"><?= __('Số Zalo') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('zalo_phone', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'type' => 'text',
                                'maxlength' => 11,
                                'data-parsley-type' => 'digits',
                                'placeholder' => 'Nhập số điện thoại Zalo của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="gender"><?= __('Giới tính') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('gender', [
                                'options' => $gender, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="birthday"><?= __('Ngày sinh') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="input-group date input-picker" id="birthday-div">
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
                            <span id="error-birthday"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="city"><?= __('Địa chỉ') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('city_id', [
                                'options' => $cities, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin bổ sung') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="potential"><?= __('Tiềm năng') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <!-- <input name="potential" type="checkbox" class="col-md-7 col-xs-12 js-switch"> -->
                            <?= $this->Form->checkbox('potential', [
                                'class' => 'js-switch', 
                                'checked' => $candidate->potential
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="contact_date"><?= __('Ngày liên hệ') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
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
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="edu_level"><?= __('Học vấn') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('educational_level', [
                                'options' => $eduLevel, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="cur_job"><?= __('Công việc hiện tại') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('cur_job', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập công việc hiện tại của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="job"><?= __('Công việc muốn đăng ký') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('job', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'Nhập công việc mong muốn của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12 optional" for="message"><?= __('Tin nhắn') ?></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?= $this->Form->control('message', [
                                'label' => false, 
                                'type' => 'textarea',
                                'rows' => 6,
                                'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                                'placeholder' => 'Nhập nội dung tin nhắn của ứng viên'
                                ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin tư vấn') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <button type="button" class="btn btn-primary btn-family" id="add-member-top" onclick="showAddConsultantModal();">
                        <?= __('Thêm lịch tư vấn') ?>
                    </button>
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Ngày tư vấn') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Tư vấn viên') ?></th>
                                <th scope="col" class="col-md-6"><?= __('Nội dung') ?></th>
                                <th scope="col" class="col-md-1 actions"><?= __('Thao tác') ?></th>
                            </tr>
                        </thead>
                        <tbody id="consultant-container">
                            <?php if (!empty($candidate->consultant_notes)): ?>
                                <?php foreach ($candidate->consultant_notes as $key => $consultant_note): ?>
                                    <tr class="cons-rec" id="row-<?= $key ?>">
                                        <td class="cell stt-col text-center">
                                            <?= $key + 1 ?>
                                        </td>
                                        <td class="cell">
                                            <span class="consultantDate"><?= h($consultant_note->consultant_date) ?></span>
                                            <div class="hidden">
                                                <?= $this->Form->control('consultant_notes.'.$key.'.id', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control col-md-7 col-xs-12 consultant_id',
                                                    'value' => $consultant_note->id
                                                    ]) ?>
                                                <?= $this->Form->control('consultant_notes.'.$key.'.consultant_date', [
                                                    'type' => 'text',
                                                    'label' => false, 
                                                    'class' => 'form-control col-md-7 col-xs-12 consultant_date',
                                                    'value' => $consultant_note->consultant_date
                                                    ]) ?>
                                                <?= $this->Form->control('consultant_notes.'.$key.'.user_id', [
                                                    'label' => false, 
                                                    'type' => 'text',
                                                    'class' => 'form-control col-md-7 col-xs-12 user_id',
                                                    'value' => $consultant_note->user_id
                                                    ]) ?>
                                                <?= $this->Form->control('consultant_notes.'.$key.'.note', [
                                                    'label' => false, 
                                                    'type' => 'textarea',
                                                    'class' => 'form-control col-md-7 col-xs-12 note',
                                                    'value' => $consultant_note->note
                                                    ]) ?>
                                            </div>
                                        </td>
                                        <td class="cell consultantUser">
                                            <?= h($consultant_note->user->fullname) ?>
                                        </td>
                                        <td class="cell notes">
                                            <?= nl2br($consultant_note->note) ?>
                                        </td>
                                        <td class="actions cell">
                                            <?= $this->Html->link(
                                                '<i class="fa fa-2x fa-pencil"></i>', 
                                                'javascript:;',
                                                [
                                                    'escape' => false,
                                                    'onClick' => "showEditConsultantModal(this)"
                                                ]) 
                                            ?>
                                            <?= $this->Html->link(
                                                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                'javascript:;',
                                                [
                                                    'escape' => false, 
                                                    'onClick' => "deleteConsultant(this, true)"
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

<div class="modal fade" id="consultant-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">LỊCH TƯ VẤN</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <?= $this->Form->create(null, [
                        'class' => 'form-horizontal form-label-left', 
                        'id' => 'consultant-form', 
                        'data-parsley-validate' => '',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                            ]
                        ]) ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="consultant_date"><?= __('Ngày tư vấn') ?></label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <div class="input-group date input-picker" id="consultant-date-div">
                                    <?= $this->Form->control('modal.consultant_date', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control',
                                        'placeholder' => 'dd-mm-yyyy',
                                        'required' => true,
                                        'data-parsley-errors-container' => '#error-consultant-date'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span id="error-consultant-date"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="consultant_user"><?= __('Tư vấn viên') ?></label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <?= $this->Form->control('modal.consultant_user', [
                                    'options' => $consultantUser, 
                                    'required' => true, 
                                    'empty' => true, 
                                    'label' => false, 
                                    'data-parsley-errors-container' => '#error-consultant-user',
                                    'data-parsley-class-handler' => '#select2-modal-consultant-user',
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                    ]) ?>
                                <span id="error-consultant-user"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note"><?= __('Nội dung') ?></label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <?= $this->Form->control('modal.note', [
                                    'label' => false, 
                                    'type' => 'textarea',
                                    'rows' => 6,
                                    'required' => true, 
                                    'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                                    'placeholder' => 'Nhập nội dung tư vấn'
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
                <button type="button" class="btn btn-success" id="submit-consultant-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-consultant-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="consultant-template" type="text/x-handlebars-template">
    <tr class="cons-rec" id="row-{{counter}}">
        <td class="cell stt-col text-center">
            {{inc counter}}
        </td>
        <td class="cell">
            <span class="consultantDate">{{consultantDate}}</span>
            <div class="hidden">
                <?= $this->Form->control('consultant_notes.{{counter}}.consultant_date', [
                    'label' => false, 
                    'class' => 'form-control col-md-7 col-xs-12 consultant_date',
                    'value' => '{{consultantDate}}'
                    ]) ?>
                <?= $this->Form->control('consultant_notes.{{counter}}.user_id', [
                    'label' => false, 
                    'type' => 'text',
                    'class' => 'form-control col-md-7 col-xs-12 user_id',
                    'value' => '{{consultantUserId}}'
                    ]) ?>
                <?= $this->Form->control('consultant_notes.{{counter}}.note', [
                    'label' => false, 
                    'type' => 'textarea',
                    'class' => 'form-control col-md-7 col-xs-12 note',
                    'value' => '{{notesRaw}}' 
                    ]) ?>
            </div>
        </td>
        <td class="cell consultantUser">
            {{consultantUser}}
        </td>
        <td class="cell notes">
            {{{notes}}}
        </td>
        <td class="actions cell">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditConsultantModal(this)"
                ]) 
            ?>
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => "deleteConsultant(this)"
                ]
            )?>
        </td>
    </tr>
</script>