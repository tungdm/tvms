<?php 
use Cake\Core\Configure;

$skillsArr = Configure::read('skills');
$score = Configure::read('score');

$this->Html->script('jtest.js', ['block' => 'scriptBottom']);
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('Nhập điểm thi') ?></h1>
    <button class="btn btn-success set-score-btn" type="button">Submit</button>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Home',
                '/',
                ['escape' => false]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Danh sách kì thi'), [
                'controller' => 'Jtests',
                'action' => 'index'
            ]) ?>
        </li>
        <li class="active">Điểm thi</li>
    </ol>
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
                <h3 class="box-title"><?= __('Danh sách học sinh thi') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered custom-table score-table">
                    <thead>
                        <tr>
                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                            <th scope="col" class="col-md-3"><?= __('Họ và tên') ?></th>
                            <th scope="col"><?=$skillsArr[$skill] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    <?php foreach ($jtest->students as $key => $value): ?>
                        <?= $this->Form->hidden('students.' . $key . '.id')?>
                        <tr>
                            <td class="cell"><?= $key + 1 ?></td>
                            <td class="cell"><?= $value->fullname ?></td>
                            <td class="cell">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <?= $this->Form->control('students.' . $key . '._joinData.' . $score[$skill], [
                                        'label' => false,
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'required' => true,
                                        'min' => 0,
                                        'max' => 100,
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->Form->end() ?>
