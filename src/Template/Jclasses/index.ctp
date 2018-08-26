<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Jclass[]|\Cake\Collection\CollectionInterface $jclasses
 */
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');

$recordsDisplay = Configure::read('recordsDisplay');
$lessons = Configure::read('lessons');

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

$this->assign('title', 'Quản lý lớp học');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ LỚP HỌC') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách lớp học</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <li data-toggle="tooltip" title="Xuất báo cáo">
                <?= $this->Html->link(__('<i class="fa fa-fw fa-bar-chart-o" aria-hidden="true"></i>'), 
                    ['action' => 'exportReport'],
                    [   
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-report scale-transition scale-out',
                        'data-toggle' => 'tooltip',
                        'title' => 'Xuất báo cáo',
                        'escape' => false
                    ]) ?>
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
                            <th scope="col" class="nameCol">
                                <?= $this->Paginator->sort('name', 'Lớp học')?>
                            </th>
                            <th scope="col" class="startCol">
                                <?= $this->Paginator->sort('start', 'Ngày bắt đầu') ?>
                            </th>
                            <th scope="col" class="numStudentsCol">
                                <?= __('Sĩ số') ?>
                            </th>
                            <th scope="col" class="userCol">
                                <?= __('Giáo viên chủ nhiệm') ?>
                            </th>
                            <th scope="col" class="currentLessonCol">
                                <?= __('Bài đang học') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="col-md-2 nameCol">
                                <?= $this->Form->control('name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 startCol">
                                <div class="input-group date input-picker" id="interview-date">
                                    <?= $this->Form->control('start', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'dd-mm-yyyy',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['start'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="col-md-2 numStudentsCol">
                                <?= $this->Form->control('num_students', [
                                    'label' => false,
                                    'type' => 'number',
                                    'min' => 0,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['num_students'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 userCol">
                                <?= $this->Form->control('user_id', [
                                    'options' => $teachers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['user_id'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 currentLessonCol">
                                <?= $this->Form->control('current_lesson', [
                                    'options' => $lessons, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['current_lesson'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                            <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($jclasses)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($jclasses as $jclass): ?>
                        <?php 
                            $counter++;
                            $formTeacher = false;
                            if ($currentUser['id'] == $jclass->user_id && $permission != 0) {
                                // current user only have read access but they are the form teacher
                                $formTeacher = true;
                            }
                        ?>
                        <tr>
                            <td class="cell text-center"><?= $counter ?></td>
                            <td class="cell nameCol"><?= h($jclass->name) ?></td>
                            <td class="cell startCol"><?= h($jclass->start) ?></td>
                            <td class="cell numStudentsCol"><?= count($jclass->students) ?></td>
                            <td class="cell userCol"><?= h($jclass->user->fullname) ?></td>
                            <td class="cell currentLessonCol"><?= h($lessons[$jclass->current_lesson]) ?></td>
                            <td class="cell actions">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                        </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                ['action' => 'view', $jclass->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <?php if ($permission == 0 || $formTeacher == true): ?>
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-edit" aria-hidden="true"></i> Sửa', 
                                                ['action' => 'edit', $jclass->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <?php endif; ?>

                                        <?php if ($permission == 0): ?>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $jclass->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa lớp {0}?', $jclass->name)
                                                ]) ?>
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
