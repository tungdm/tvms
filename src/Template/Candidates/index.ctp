<?php
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$recordsDisplay = Configure::read('recordsDisplay');
$candidateSource = Configure::read('candidateSource');
$candidateStatus = Configure::read('candidateStatus');
$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$currentUser = $this->request->session()->read('Auth.User');
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('candidate.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('candidate.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);
?>

<?php $this->start('content-header'); ?>
    <?php $this->assign('title', 'Quản lý tuyển dụng Online'); ?>
    <h1><?= __('QUẢN LÝ TUYỂN DỤNG ONLINE') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách ứng viên</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-user-plus" aria-hidden="true"></i>'), 
                        ['action' => 'add'],
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Thêm mới ứng viên',
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
                'url' => ['controller' => 'Candidates', 'action' => 'index'],
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
                            <th scope="col" class="sttCol col-md-1"><?= __('STT') ?></th>
                            <th scope="col" class="fullnameCol col-md-2">
                                <?= $this->Paginator->sort('fullname', 'Họ tên') ?>
                            </th>
                            <th scope="col" class="contactDateCol col-md-2">
                                <?= $this->Paginator->sort('contact_date', 'Ngày liên hệ')?>
                            </th>
                            <th scope="col" class="phoneCol col-md-2">
                                <?= $this->Paginator->sort('phone', 'Số điện thoại')?>
                            </th>
                            <th scope="col" class="sourceCol col-md-2">
                                <?= __('Nguồn') ?>
                            </th>
                            <th scope="col" class="statusCol col-md-2">
                                <?= __('Trạng thái') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="fullnameCol">
                                <?= $this->Form->control('candidate_name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['candidate_name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            
                            <td class="contactDateCol">
                                <div class="input-group date input-picker" id="select-contact-date">
                                    <?= $this->Form->control('contact_date', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'dd-mm-yyyy',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['contact_date'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="phoneCol">
                                <?= $this->Form->control('phone', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['phone'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="sourceCol">
                                <?= $this->Form->control('source', [
                                    'options' => $candidateSource, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['source'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="statusCol">
                                <?= $this->Form->control('status', [
                                    'options' => $candidateStatus, 
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
                                <?= $this->Form->end() ?>
                            </td>
                        </tr>
                        <?php if (($candidates)->isEmpty()): ?>
                            <tr>
                                <td colspan="7" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($candidates as $candidate): ?>
                                <?php 
                                    $counter++; 
                                    $candidateName = $candidate->source == 1 ? $candidate->fb_name : $candidate->fullname;
                                    $status = '';
                                    if ($candidate->potential) {
                                        $status = 'potential';
                                    } else if ($candidate->status == 2) { {
                                        $status = 'consulted';
                                    }
                                ?>
                                <tr>
                                    <td class="cell text-center <?= $candidate->del_flag ? 'deletedRecord' : '' ?>"><?= h($counter) ?></td>
                                    <td class="cell fullnameCol <?= $status ?>"><?= h($candidateName) ?></td>
                                    <td class="cell text-center contactDateCol"><?= h($candidate->contact_date) ?></td>
                                    <td class="cell text-center phoneCol"><?= $candidate->phone ? h($this->Phone->makeEdit($candidate->phone)) : '' ?></td>
                                    <td class="cell sourceCol">
                                        <?php if (!empty($candidate->source)): ?>
                                            <?php if ($candidate->source == 1 && !empty($candidate->fb_link)): ?>
                                                <?= $this->Html->link(
                                                    h($candidateSource[$candidate->source]),
                                                    $candidate->fb_link,
                                                    ['escape' => false, 'target' => '_blank']) ?>
                                            <?php else: ?>
                                                <?= h($candidateSource[$candidate->source]) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell statusCol">
                                        <?= $candidateStatus[$candidate->status] ?>
                                        <?php if ($candidate->status != 4 && $candidate->potential): ?>
                                            <?= '(Tiềm năng)' ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell actions">
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                <?php if ($candidate->status == 4): ?>
                                                    <li>
                                                        <?= $this->Html->link('<i class="fa fa-briefcase" aria-hidden="true"></i> Lao động', 
                                                            ['action' => 'viewStudent', $candidate->id],
                                                            ['escape' => false]) ?>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                        ['action' => 'view', $candidate->id],
                                                        ['escape' => false]) ?>
                                                </li>
                                                <?php if ($permission == 0): ?>
                                                    <?php if ($candidate->del_flag): ?>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                            ['action' => 'recover', $candidate->id], 
                                                            [
                                                                'escape' => false, 
                                                                'confirm' => __('Bạn có chắc chắn muốn phục hồi ứng viên {0}?', $candidateName)
                                                            ]) ?>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Sửa'), 
                                                                ['action' => 'edit', $candidate->id],
                                                                ['escape' => false]) ?>
                                                        </li>
                                                        <?php if ($candidate->status != 4): ?>
                                                            <li>
                                                                <?= $this->Html->link('<i class="fa fa-angle-double-up" style="font-size: 1.3em" aria-hidden="true"></i> Kí kết chính thức', 
                                                                    ['controller' => 'Students', 'action' => 'info', '?' => ['candidateId' => $candidate->id]],
                                                                    ['escape' => false]) ?>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i> Xóa'), 
                                                                ['action' => 'delete', $candidate->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn xóa ứng viên {0}?', $candidateName)
                                                                ]) ?>
                                                        </li>
                                                    <?php endif; ?>
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
