<?php
use Cake\Core\Configure;

$lessons = Configure::read('lessons');
$skillsArr = Configure::read('skills');
$score = Configure::read('score');
$skillTest = [];
$teachers = [];
$avg = [];
$avgTotal = 0;
$totalStudent = count($jtest->students);
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('Test Detail') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Home',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('List Tests'), [
                'controller' => 'Jtests',
                'action' => 'index']) ?>
        </li>
        <li class="active">Test Detail</li>
    </ol>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
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
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="test_date"><?= __('Ngày thi') ?></label>
                        <div class="col-md-3 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jtest->test_date) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jclass_id"><?= __('Lớp thi') ?></label>
                        <div class="col-md-3 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($jtest->jclass->name) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="test_lessons"><?= __('Bài thi') ?></label>
                        <div class="col-md-3 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= h($lessons[$jtest->lesson_from]) ?> ～ <?= h($lessons[$jtest->lesson_to]) ?>
                            </div>
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
                                <td class="cell stt-col">
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
