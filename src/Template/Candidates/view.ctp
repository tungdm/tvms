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
?>

<?php $this->assign('title', 'Thông tin ứng viên ' . $candidateName); ?>
<?php $this->start('content-header'); ?>
    <h1><?= __('THÔNG TIN ỨNG VIÊN') ?></h1>
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

<?php if ($permission == 0): ?>
    <?php $this->start('floating-button'); ?>
        <div class="zoom" id="draggable-button">
            <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
            <ul class="zoom-menu">
                <?php if ($candidate->del_flag): ?>
                    <li>
                        <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                        ['action' => 'recover', $candidate->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Phục hồi',
                            'confirm' => __('Bạn có chắc chắn muốn phục hồi ứng viên {0}?', $candidateName)
                        ]) ?>
                    </li>
                <?php else: ?>
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
                        <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                            ['action' => 'edit', $candidate->id],
                            [   
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                                'data-toggle' => 'tooltip',
                                'title' => 'Sửa',
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
                <?php endif; ?>
            </ul>
        </div>
    <?php $this->end(); ?>
<?php endif; ?>

<div class="form-horizontal form-label-left">
    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin cơ bản') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="source"><?= __('Nguồn') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidateSource[$candidate->source] ?></div>
                        </div>
                    </div>
                    <?php if ($candidate->source == 1): ?>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="fb_name"><?= __('Tên Facebook') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <?= $this->Html->link(
                                        h($candidate->fb_name),
                                        $candidate->fb_link,
                                        ['escape' => false, 'target' => '_blank']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="fullname"><?= __('Họ tên') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= !empty($candidate->fullname) ? $candidate->fullname : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="phone"><?= __('Số điện thoại') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->phone ? $this->Phone->makeEdit($candidate->phone) : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="zalo_phone"><?= __('Số Zalo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->zalo_phone ? $this->Phone->makeEdit($candidate->zalo_phone) : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="gender"><?= __('Giới tính') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->gender ? $gender[$candidate->gender] : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="birthday"><?= __('Ngày sinh') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->birthday ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="city"><?= __('Địa chỉ') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->city ? $candidate->city->name : 'N/A' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin bổ sung') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="potential"><?= __('Tiềm năng') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?php 
                                    if (!$candidate->potential) {
                                        echo '<i class="fa fa-circle-o red-color" style="font-size: 1.5em;"></i>';
                                    } else {
                                        echo '<i class="fa fa-check-circle-o green-color" style="font-size: 1.5em;"></i>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="contact_date"><?= __('Ngày liên hệ') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->contact_date ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="edu_level"><?= __('Học vấn') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $candidate->educational_level ? $eduLevel[$candidate->educational_level] : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="cur_job"><?= __('Công việc hiện tại') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= !empty($candidate->cur_job) ? $candidate->cur_job : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="job"><?= __('Công việc muốn đăng ký') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= !empty($candidate->job) ? $candidate->job : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="message"><?= __('Tin nhắn') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view"><?= !empty($candidate->message) ? nl2br($candidate->message) : 'N/A' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin hệ thống') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($candidate->created_by_user) ? $candidate->created_by_user->fullname : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($candidate->created) ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($candidate->modified_by_user)): ?>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $candidate->modified_by_user->fullname ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($candidate->modified) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin tư vấn') ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Ngày tư vấn') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Tư vấn viên') ?></th>
                                <th scope="col" class="col-md-6"><?= __('Nội dung') ?></th>
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
                                        </td>
                                        <td class="cell consultantUser">
                                            <?= h($consultant_note->user->fullname) ?>
                                        </td>
                                        <td class="cell notes">
                                            <?= nl2br($consultant_note->note) ?>
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
</div>