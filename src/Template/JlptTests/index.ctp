<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$jlptLevels = Configure::read('jlpt_levels');
$jlptResult = Configure::read('jlptResult');
$now = Time::now()->i18nFormat('yyyy-MM-dd');

$action = $this->request->getParam('action');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('jlpt.js', ['block' => 'scriptBottom']);

$recordsDisplay = Configure::read('recordsDisplay');
$testStatus = Configure::read('testStatus');

$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý JLPT');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ JLPT') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách kì thi</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li data-toggle="tooltip" title="Xuất báo cáo">
                <a class="zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out" onclick="reportJplt()">
                    <i class="fa fa-fw fa-bar-chart-o" aria-hidden="true"></i>
                </a>
            </li>
            <?php if ($permission == 0): ?>
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
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Báo cáo tổng quan') ?></h3>
                    <div class="box-tools pull-right">
                        <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="test_date"><?= __('Số người đậu') ?></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-control form-control-view">
                                    N1: <?= isset($certCount['N1']) ? $certCount['N1'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-control form-control-view">
                                    N2: <?= isset($certCount['N2']) ? $certCount['N2'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-control form-control-view">
                                    N3: <?= isset($certCount['N3']) ? $certCount['N3'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-control form-control-view">
                                    N4: <?= isset($certCount['N4']) ? $certCount['N4'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-control form-control-view">
                                    N5: <?= isset($certCount['N5']) ? $certCount['N5'] : 0 ?>
                                </div>
                            </div>
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
                <h3 class="box-title"><?= __('DANH SÁCH') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="form-group col-md-4 col-sm-6 col-xs-12 records-per-page">
                    <label class="control-label col-md-3 col-sm-3 col-xs-3"><?= __('Hiển thị') ?></label>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <?= $this->Form->control('records', [
                            'options' => $recordsDisplay, 
                            'empty' => true,
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                            'value' => $query['records'] ?? ''
                            ])
                        ?>
                    </div>
                </div>
                <table class="table table-bordered custom-table">
                    <thead>
                        <tr>
                            <th scope="col" class="stt-col col-md-1"><?= __('STT') ?></th>
                            <th scope="col" class="testDateCol col-md-3">
                                <?= $this->Paginator->sort('test_date', 'Ngày thi') ?>
                            </th>
                            <th scope="col" class="levelCol col-md-3">
                                <?= __('Trình độ') ?>
                            </th>
                            <th scope="col" class="numStdCol col-md-2">
                                <?= __('Số người thi') ?>
                            </th>
                            <th scope="col" class="statusCol col-md-2">
                                <?= __('Trạng thái') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="stt-col"></td>
                            <td class="testDateCol">
                                <div class="input-group date input-picker" id="test-date">
                                    <?= $this->Form->control('test_date', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'dd-mm-yyyy',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['test_date'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="levelCol">
                                <div class="group-picker">
                                    <?= $this->Form->control('level', [
                                        'options' => $jlptLevels, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['level'] ?? ''
                                        ])
                                    ?>
                                </div>
                            </td>
                            <td class="numStdCol">
                                <?= $this->Form->control('numOfStd', [
                                    'label' => false,
                                    'type' => 'number',
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['numOfStd'] ?? '',
                                    'placeholder' => 'Nhập thông tin',
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-1 statusCol">
                                <?= $this->Form->control('status', [
                                    'options' => $testStatus, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['status'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                            <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($jlptTests)->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($jlptTests as $jlpt): ?>
                                <?php if (!$jlpt->del_flag || $permission == 0): ?>
                                    <?php 
                                        $counter++;
                                        $supervisory = false;
                                        foreach ($jlpt->jlpt_contents as $key => $content) {
                                            if ($content->user_id == $currentUser['id']) {
                                                // current user only have read access but they are the supervisory
                                                $supervisory = true;
                                            }
                                        }
                                    ?>
                                    <tr class="text-center">
                                        <td class="cell stt-col <?= $jlpt->del_flag ? 'deletedRecord' : '' ?>"><?= $counter ?></td>
                                        <td class="cell testDateCol"><?= $jlpt->test_date?></td>
                                        <td class="cell levelCol"><?= $jlpt->level ?></td>
                                        <td class="cell numStdCol"><?= count($jlpt->students) ?></td>
                                        <td class="cell statusCol">
                                            <?php 
                                                $status = 0;
                                                $test_date = $jlpt->test_date->i18nFormat('yyyy-MM-dd');
                                                if ($jlpt->status == "4" || $jlpt->status == "5") {
                                                    $status = (int) $jlpt->status;
                                                    echo h($testStatus[$jlpt->status]);
                                                } elseif ($now < $test_date) {
                                                    $status = 1;
                                                    echo h($testStatus["1"]);
                                                } elseif ($now == $test_date) {
                                                    $status = 2;
                                                    echo h($testStatus["2"]);
                                                } else {
                                                    $status = 3;
                                                    echo h($testStatus["3"]);
                                                }
                                            ?>
                                        </td>
                                        <td class="cell actions">
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">
                                                    Mở rộng <span class="caret"></span>
                                                </button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li>
                                                        <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                            ['action' => 'view', $jlpt->id],
                                                            ['escape' => false]) ?>
                                                    </li>
                                                    <?php if ($permission == 0): ?>
                                                        <?php if ($jlpt->del_flag): ?>
                                                            <li>
                                                                <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                                ['action' => 'recover', $jlpt->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn phục hồi kì thi {0} ngày {1}?', $jlpt->level, $jlpt->test_date)
                                                                ]) ?>
                                                            </li>
                                                        <?php else: ?>
                                                            <li>
                                                                <?= $this->Html->link('<i class="fa fa-edit" aria-hidden="true"></i> Sửa', 
                                                                    ['action' => 'edit', $jlpt->id], 
                                                                    ['escape' => false]) ?>
                                                            </li>
                                                            <?php if ($status == 4): ?>
                                                                <li>
                                                                    <?= $this->Form->postLink('<i class="fa fa-lock" aria-hidden="true"></i> Đóng', 
                                                                    ['action' => 'finish', $jlpt->id], 
                                                                    [
                                                                        'escape' => false, 
                                                                        'confirm' => __('Bạn có chắc chắn muốn đóng kì thi JLPT {0} ngày {1}?', $jlpt->level, $jlpt->test_date)
                                                                    ]) ?>
                                                                </li>
                                                            <?php endif; ?>
                                                            <li>
                                                                <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                                ['action' => 'delete', $jlpt->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn xóa kì thi JLPT {0} ngày {1}?', $jlpt->level, $jlpt->test_date)
                                                                ]) ?>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if (!$jlpt->del_flag): ?>
                                                        <?php if ($currentUser['role_id'] == 1 || $status < 5 && $status >= 2 && ($supervisory == true || $permission == 0)): ?>
                                                            <li class="divider"></li>
                                                            <li>
                                                                <?= $this->Html->link('<i class="fa fa-check" aria-hidden="true"></i> Nhập điểm', 
                                                                    ['action' => 'setScore', $jlpt->id],
                                                                    ['escape' => false]) ?>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if ($status == 4 || $status == 5): ?>
                                                            <li class="divider"></li>
                                                            <li>
                                                                <?= $this->Html->link('<i class="fa fa-book" aria-hidden="true"></i> Xuất điểm', 
                                                                    ['action' => 'exportResult', $jlpt->id],
                                                                    ['escape' => false]) ?>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="jlpt-report-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">XUẤT BÁO CÁO</h4>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal form-label-left', 
                'id' => 'export-jlpt-form', 
                'url' => ['action' => 'exportReport'],
                'data-parsley-validate' => '',
                'templates' => [
                    'inputContainer' => '{{content}}'
                    ]
                ]) ?>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="level"><?= __('Thời gian thi') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="report-from">
                                    <?= $this->Form->control('jlpt.reportfrom', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control from-date-picker',
                                        'placeholder' => 'mm-yyyy',
                                        'data-parsley-before-date' => '#jlpt-reportto'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                <div class="input-group date input-picker month-mode" id="report-to">
                                    <?= $this->Form->control('jlpt.reportto', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control to-date-picker',
                                        'placeholder' => 'mm-yyyy',
                                        'data-parsley-after-date' => '#jlpt-reportfrom'
                                        ])?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="level"><?= __('Trình độ') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <?= $this->Form->control('jlpt.level', [
                                'options' => $jlptLevels, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="result"><?= __('Kết quả') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <?= $this->Form->control('jlpt.result', [
                                'options' => $jlptResult, 
                                'empty' => true, 
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Hoàn tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>