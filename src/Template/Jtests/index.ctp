<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');
$now = Time::now()->i18nFormat('yyyy-MM-dd');

$recordsDisplay = Configure::read('recordsDisplay');
$lessons = Configure::read('lessons');
$testStatus = Configure::read('testStatus');

$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý thi cử');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ THI CỬ') ?></h1>
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
                            <th scope="col" class="col-num"><?= __('STT') ?></th>
                            <th scope="col" class="testDateCol">
                                <?= $this->Paginator->sort('test_date', 'Ngày thi') ?>
                            </th>
                            <th scope="col" class="testLessonCol">
                                <?= __('Bài thi') ?>
                            </th>
                            <th scope="col" class="classIdCol">
                                <?= __('Lớp thi') ?>
                            </th>
                            <th scope="col" class="statusCol">
                                <?= __('Trạng thái') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="col-md-2 testDateCol">
                                <div class="input-group date input-picker" id="test-date">
                                    <?= $this->Form->control('test_date', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'yyyy-mm-dd',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['test_date'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="col-md-5 testLessonCol">
                                <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                    <?= $this->Form->control('lesson_from', [
                                        'options' => $lessons, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['lesson_from'] ?? ''
                                        ])
                                    ?>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                                <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                                    <?= $this->Form->control('lesson_to', [
                                        'options' => $lessons, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['lesson_to'] ?? ''
                                        ])
                                    ?>
                                </div>
                            </td>
                            <td class="col-md-1 classIdCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('class_id', [
                                    'options' => $jclasses, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['class_id'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 statusCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('status', [
                                    'options' => $testStatus, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['status'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                            <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($jtests)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($jtests as $jtest): ?>
                        <?php 
                            $counter++;
                            $supervisory = false;
                            foreach ($jtest->jtest_contents as $key => $content) {
                                if ($content->user_id == $currentUser['id']) {
                                    // current user only have read access but they are the supervisory
                                    $supervisory = true;
                                }
                            }
                        ?>
                        <tr>
                            <td class="cell"><?= $counter ?></td>
                            <td class="cell testDateCol">
                                <?= h($jtest->test_date) ?>
                            </td>
                            <td class="cell testLessonCol">
                                <?= $lessons[$jtest->lesson_from] ?> ～ <?= $lessons[$jtest->lesson_to] ?>
                            </td>
                            <td class="cell classIdCol">
                                <?= h($jtest->jclass->name) ?>
                            </td>
                            <td class="cell statusCol">
                                <?php 
                                    $status = 0;
                                    if ($jtest->status == "4" || $jtest->status == "5") {
                                        $status = (int) $jtest->status;
                                        echo h($testStatus[$jtest->status]);
                                    } elseif ($now < $jtest->test_date) {
                                        $status = 1;
                                        echo h($testStatus["1"]);
                                    }
                                    elseif ($now == $jtest->test_date) {
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
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                ['action' => 'view', $jtest->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <?php if ($permission == 0): ?>
                                            <?php if ($status == 1): ?>
                                            <li>
                                                <?= $this->Html->link('<i class="fa fa-edit" aria-hidden="true"></i> Sửa', 
                                                    ['action' => 'edit', $jtest->id], 
                                                    ['escape' => false]) ?>
                                            </li>
                                            <?php elseif ($status == 4): ?>
                                            <li>
                                                <?= $this->Form->postLink('<i class="fa fa-lock" aria-hidden="true"></i> Đóng', 
                                                ['action' => 'finish', $jtest->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn đóng kì thi {0}?', $jtest->test_date)
                                                ]) ?>
                                            </li>
                                            <?php endif; ?>
                                            <li>
                                                <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $jtest->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa kì thi {0}?', $jtest->test_date)
                                                ]) ?>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($status < 5 && $status >= 2 && $supervisory == true): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <?= $this->Html->link('<i class="fa fa-check" aria-hidden="true"></i> Nhập điểm', 
                                                    ['action' => 'setScore', $jtest->id],
                                                    ['escape' => false]) ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('Trang đầu')) ?>
                        <?= $this->Paginator->prev('< ' . __('Trang trước')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('Trang sau') . ' >') ?>
                        <?= $this->Paginator->last(__('Trang cuối') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Trang thứ {{page}} trên tổng {{pages}} trang, {{current}} trên tổng số {{count}} bản ghi')]) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
