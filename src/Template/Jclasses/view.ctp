<?php
use Cake\Core\Configure;

$gender = Configure::read('gender');
$lessons = Configure::read('lessons');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');

$this->Html->css('class.css', ['block' => 'styleTop']);
$this->Html->script('class.js', ['block' => 'scriptBottom']);

$this->assign('title', 'Lớp ' . $jclass->name . ' - Thông tin chi tiết');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('THÔNG TIN CHI TIẾT') ?></h1>
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

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0 || $currentUser['id'] == $jclass->user_id): ?>
            <li>
                <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                    ['action' => 'edit', $jclass->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Sửa',
                        'escape' => false
                    ]) ?>
            </li>
            <?php endif; ?>
            <?php if ($permission == 0): ?>
            <li>
                <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                    ['action' => 'delete', $jclass->id], 
                    [
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                        'escape' => false, 
                        'data-toggle' => 'tooltip',
                        'title' => 'Xóa',
                        'confirm' => __('Bạn có chắc chắn muốn xóa lớp {0}?', $jclass->name)
                    ]) ?>
            </li>
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin lớp học') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="name"><?= __('Tên lớp') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jclass->name) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="start"><?= __('Ngày bắt đầu') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jclass->start) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="user_id"><?= __('Giáo viên chủ nhiệm') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $jclass->user ? h($jclass->user->fullname) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12" for="current_lesson"><?= __('Bài đang học') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($lessons[$jclass->current_lesson]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Thông tin hệ thống') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($jclass->created_by_user) ? $jclass->created_by_user->fullname : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jclass->created) ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($jclass->modified_by_user)): ?>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $jclass->modified_by_user->fullname ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jclass->modified) ?>
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
                    <h3 class="box-title"><?= __('Danh sách học sinh') ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered custom-table students-table">
                        <thead>
                            <tr>
                                <th scope="col col-md-1"><?= __('STT') ?></th>
                                <th scope="col col-md-2"><?= __('Họ tên') ?></th>
                                <th scope="col col-md-1"><?= __('Giới tính') ?></th>
                                <th scope="col col-md-2"><?= __('Ngày sinh') ?></th>
                                <th scope="col col-md-3"><?= __('Ngày nhập học') ?></th>
                                <th scope="col col-md-3"><?= __('Quê quán') ?></th>
                            </tr>
                        </thead>
                        <tbody id="student-container">
                        <?php foreach ($jclass->students as $key => $value): ?>
                            <tr class="row-std">
                                <td class="cell stt-col text-center">
                                    <?= $key+1 ?>
                                </td>
                                <td class="cell">
                                    <a href="javascript:;" onclick="viewStudent(<?=$value->id?>);"><?= h($value->fullname) ?></a>
                                </td>
                                <td class="cell text-center">
                                    <?= $gender[$value->gender]?>
                                </td>
                                <td class="cell">
                                    <?= $value->birthday ?>
                                </td>
                                <td class="cell">
                                    <?= !empty($value->enrolled_date) ? $value->enrolled_date : 'N/A' ?>
                                </td>
                                <td class="cell">
                                    <?= h($value->addresses[0]->city->name) ?>
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
