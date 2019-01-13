<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$lessons = Configure::read('lessons');
$skillsArr = Configure::read('skills');
$score = Configure::read('score');
$testStatus = Configure::read('testStatus');

$skillTest = [];
$teachers = [];
$avg = [];
$avgTotal = 0;
$totalStudent = count($jtest->students) == 0 ? 1 : count($jtest->students);

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');

$supervisory = false;
if (!empty($jtest->jtest_contents)) {
    foreach ($jtest->jtest_contents as $key => $content) {
        if ($content->user_id == $currentUser['id']) {
            // current user only have read access but they are the supervisory
            $supervisory = true;
            break;
        }
    }
}
$status = 0;
$now = Time::now()->i18nFormat('yyyy-MM-dd');
$test_date = $jtest->test_date->i18nFormat('yyyy-MM-dd');
if ($jtest->status == "4" || $jtest->status == "5") {
    $status = (int) $jtest->status;
} elseif ($now < $test_date) {
    $status = 1;
} elseif ($now == $test_date) {
    $status = 2;
} else {
    $status = 3;
}

$this->assign('title', 'Kì thi ' . $jtest->test_date . ' - Thông tin chi tiết');
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
            <?= $this->Html->link(__('Danh sách kì thi'), [
                'controller' => 'Jtests',
                'action' => 'index']) ?>
        </li>
        <li class="active">Kì thi <?= $jtest->test_date ?></li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
                <?php if ($jtest->del_flag): ?>
                    <li>
                        <?= $this->Form->postLink(__('<i class="fa fa-undo" aria-hidden="true"></i>'), 
                            ['action' => 'recover', $jtest->id], 
                            [
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out',
                                'escape' => false, 
                                'data-toggle' => 'tooltip',
                                'title' => 'Phục hồi',
                                'confirm' => __('Bạn có chắc chắn muốn phục hồi kì thi {0}?', $jtest->test_date)
                            ]) ?>
                    </li>
                <?php else: ?>
                    <?php if ($status != 5 || $currentUser['role_id']==1): ?>
                        <li>
                            <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                                ['action' => 'edit', $jtest->id],
                                [   
                                    'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Sửa',
                                    'escape' => false
                                ]) ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($status == 4): ?>
                        <li>
                            <?= $this->Form->postLink('<i class="fa fa-lock" aria-hidden="true"></i>', 
                            ['action' => 'finish', $jtest->id], 
                            [
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-close scale-transition scale-out',
                                'data-toggle' => 'tooltip',
                                'title' => 'Đóng',
                                'escape' => false, 
                                'confirm' => __('Bạn có chắc chắn muốn đóng kì thi {0}?', $jtest->test_date)
                            ]) ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                            ['action' => 'delete', $jtest->id], 
                            [
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                                'escape' => false, 
                                'data-toggle' => 'tooltip',
                                'title' => 'Xóa',
                                'confirm' => __('Bạn có chắc chắn muốn xóa kì thi {0}?', $jtest->test_date)
                            ]) ?>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (($currentUser['role_id'] == 1 || $status < 5 && $status >= 2 && ($supervisory == true || $permission == 0)) && $jtest->del_flag == FALSE): ?>
                <li>
                    <?= $this->Html->link('<i class="fa fa-check" aria-hidden="true"></i>', 
                        ['action' => 'setScore', $jtest->id],
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Nhập điểm',
                            'escape' => false
                        ]) ?>
                </li>
            <?php endif; ?>
            <?php if (($status == 4 || $status == 5) && $jtest->del_flag == FALSE): ?>
                <li>
                    <?= $this->Html->link('<i class="fa fa-book" aria-hidden="true"></i>', 
                        ['action' => 'exportResult', $jtest->id],
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Xuất kết quả',
                            'escape' => false
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
                    <h3 class="box-title"><?= __('Thông tin kì thi') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="test_date"><?= __('Ngày thi') ?>: </label>
                        <div class="ccol-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jtest->test_date) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="jclass_id"><?= __('Lớp thi') ?>: </label>
                        <div class="ccol-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jtest->jclass->name) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="test_lessons"><?= __('Bài thi') ?>: </label>
                        <div class="ccol-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($lessons[$jtest->lesson_from]) ?> ～ <?= h($lessons[$jtest->lesson_to]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="status"><?= __('Trạng thái') ?>: </label>
                        <div class="ccol-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $testStatus[(string) $status] ?>
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
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $jtest->created_by_user->fullname ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jtest->created) ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($jtest->modified_by_user)): ?>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $jtest->modified_by_user->fullname ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jtest->modified) ?>
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
                    <h3 class="box-title"><?= __('Điểm thi') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered custom-table span-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Thí sinh') ?></th>
                                <?php foreach ($jtest->jtest_contents as $key => $value): ?>
                                <?php 
                                    array_push($skillTest, ['skillName' => $value->skill, 'avg' => 0]);
                                    array_push($teachers, $value->user->fullname)
                                ?>
                                <th scope="col" class="col-md-1" style="width: 12.499999995%;"><?= h($skillsArr[$value->skill]) ?></th>
                                <?php endforeach; ?>
                                <th scope="col" class="col-md-2"><?= __('Tổng') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jtest->students as $key => $value): ?>
                            <?php $total = 0; ?>
                            <tr class="row-score">
                                <td class="cell stt-col text-center">
                                    <?= $key+1 ?>
                                </td>
                                <td class="cell col-md-2">
                                    <a href="javascript:;" onclick="viewStudent(<?=$value->id?>);"><?= h($value->fullname) ?></a>
                                </td>
                                <?php foreach ($skillTest as $key => $skill): ?>
                                <td class="cell">
                                    <?php 
                                        $total += $value->_joinData[$score[$skill['skillName']]];
                                        $skillTest[$key]['avg'] += $value->_joinData[$score[$skill['skillName']]];
                                    ?>
                                    <?= $value->_joinData[$score[$skill['skillName']]] ?? 'N/A' ?>
                                </td>
                                <?php endforeach; ?>
                                <td class="cell">
                                    <?= $total ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr span="2" class="span-row">
                                <?php 
                                    $totalSkill = count($skillTest);
                                ?>
                                <td class="cell" colspan="2"><strong><?= __('ĐIỂM TRUNG BÌNH') ?></strong></td>

                                <?php foreach ($skillTest as $key => $skill): ?>
                                <?php 
                                if ($skill['avg'] == 0) {
                                    $totalSkill--;
                                    $testAvg = 'N/A';
                                } else {
                                    $avgTotal += $skill['avg'];
                                    $testAvg = round(($skill['avg']/$totalStudent), 1);
                                } ?>
                                <td class="cell">
                                    <strong><?= $testAvg ?></strong>
                                </td>
                                <?php endforeach; ?>
                                <td class="cell">
                                    <?php if ($totalSkill == 0): ?>
                                    <strong><?= round($avgTotal/($totalStudent), 1) ?></strong>
                                    <?php else: ?>
                                    <strong><?= round($avgTotal/($totalStudent*$totalSkill), 1) ?></strong>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr span="2" class="span-row">
                                <td class="cell" colspan="2"><strong><?= __('GIÁO VIÊN') ?></strong></td>
                                <?php foreach($teachers as $teacher): ?>
                                <td class="cell">
                                    <strong><?= h($teacher) ?></strong>
                                </td>
                                <?php endforeach; ?>
                                <td class="cell"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
