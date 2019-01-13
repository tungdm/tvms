<?php 
use Cake\Core\Configure;

$skillsArr = Configure::read('jlpt_skills');
$score = Configure::read('jlpt_score');

$this->Html->script('jlpt.js', ['block' => 'scriptBottom']);

$this->assign('title', 'Kì thi ' . $jtest->test_date .  ' - Nhập điểm thi');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('NHẬP ĐIỂM THI') ?></h1>
    <button class="btn btn-success set-score-btn" type="button">Lưu lại</button>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Danh sách kì thi'), [
                'controller' => 'JlptTests',
                'action' => 'index'
            ]) ?>
        </li>
        <li class="active">Nhập điểm thi</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li>
                <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                    ['action' => 'view', $jtest->id],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Xem chi tiết',
                        'escape' => false
                    ]) ?>
            </li>
            <li>
                <a href="#" class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out set-score-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>

<?= $this->Form->create($jtest, [
    'class' => 'form-horizontal form-label-left',
    'id' => 'set-score-form',
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH THÍ SINH') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered custom-table score-table">
                    <thead>
                        <tr>
                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                            <th scope="col" class="col-md-2"><?= __('Họ tên') ?></th>
                            <?php foreach($skill as $value): ?>
                            <th scope="col">Điểm thi <?=$skillsArr[$value] ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jtest->students as $key => $value): ?>
                            <?= $this->Form->hidden('students.' . $key . '.id')?>
                            <tr class="row-score">
                                <td class="cell text-center"><?= $key + 1 ?></td>
                                <td class="cell">
                                    <?= $value->fullname ?>
                                    <div class="hidden">
                                        <?= $this->Form->control('students.' . $key . '._joinData.total_score', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12 totalScore',
                                            ]) ?>
                                    </div>
                                </td>
                                <?php foreach($skill as $value): ?>
                                    <td class="cell">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <?= $this->Form->control('students.' . $key . '._joinData.' . $score[$value], [
                                                'label' => false,
                                                'class' => 'form-control col-md-7 col-xs-12 partScore',
                                                'required' => true,
                                                'min' => 0,
                                                'max' => 60,
                                                'placeholder' => 'Nhập điểm thi từ 0 đến 60'
                                                ]) ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->Form->end() ?>
