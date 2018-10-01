<?php
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');

$recordsDisplay = Configure::read('recordsDisplay');
$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$this->Html->script('job.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý nghề nghiệp');
?>

<?php $this->start('content-header'); ?>
<h1><?= __('QUẢN LÝ NGHỀ NGHIỆP') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Danh sách nghề nghiệp</li>
</ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
            <li>
                <a href="javascript:;" 
                    onclick="showAddJobModal()"
                    class="zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out" 
                    data-toggle="tooltip" 
                    title="" 
                    data-original-title="Thêm mới">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
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
                'url' => ['controller' => 'Jobs', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-jobs-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
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
                            <th scope="col" class="nameVnCol">
                                <?= $this->Paginator->sort('job_name', 'Tên (VN)')?>
                            </th>
                            <th scope="col" class="nameJpCol">
                                <?= $this->Paginator->sort('job_name_jp', 'Tên (JP)')?>
                            </th>
                            <th scope="col" class="createdByCol">
                                <?= __('Người tạo') ?>
                            </th>
                            <th scope="col" class="modifiedByCol">
                                <?= __('Người sửa cuối') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-1"></td>
                            <td class="col-md-3 nameVnCol">
                                <?= $this->Form->control('f_job_name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['f_job_name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-3 nameJpCol">
                                <?= $this->Form->control('f_job_name_jp', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['f_job_name_jp'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 createdByCol">
                                <?= $this->Form->control('f_created_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_created_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 modifiedByCol">
                                <?= $this->Form->control('f_modified_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_modified_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($jobs)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell text-center"><?= $counter ?></td>
                            <td class="cell nameVnCol">
                                <a href="javascript:;" onclick="viewJob(<?= $job->id ?>)"><?= h($job->job_name) ?></a>
                            </td>
                            <td class="cell nameJpCol"><?= h($job->job_name_jp) ?></td>
                            <td class="cell createdByCol">
                                <?= !empty($job->created_by_user) ? h($job->created_by_user->fullname) : '' ?>
                            </td>
                            <td class="cell modifiedByCol">
                                <?= !empty($job->modified_by_user) ? h($job->modified_by_user->fullname) : '' ?>
                            </td>
                            <td class="actions cell">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onClick="viewJob(<?= $job->id ?>)">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                            </a>
                                        </li>
                                        <?php if ($permission == 0): ?>
                                        <li>
                                            <a href="javascript:;" onClick="showEditJobModal('<?= $job->id ?>')">
                                            <i class="fa fa-edit"></i> Sửa</a>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $job->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa nghề {0}?', $job->job_name)
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

<div class="modal fade" id="add-job-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay hidden" id="add-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI NGHỀ NGHIỆP</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-job-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Jobs', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="job_name">
                        <?= __('Tên nghề nghiệp') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('job_name', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập bằng kí tự romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('job_name_jp', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji'
                            ]) ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="description"><?= __('Ghi chú') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('description', [
                            'label' => false, 
                            'type' => 'textarea',
                            'rows' => 5,
                            'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                            'placeholder' => 'Nhập miêu tả chi tiết'
                            ]) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="addJob()">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-job-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay hidden" id="edit-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT NGHỀ NGHIỆP</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'edit-job-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Jobs', 'action' => 'edit'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit_job_id']) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="job_name">
                        <?= __('Tên nghề nghiệp') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('job_name', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập bằng kí tự romaji',
                            'id' => 'edit_job_name'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('job_name_jp', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji',
                            'id' => 'edit_job_name_jp'
                            ]) ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="description"><?= __('Ghi chú') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('description', [
                            'label' => false, 
                            'type' => 'textarea',
                            'rows' => 5,
                            'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                            'placeholder' => 'Nhập miêu tả chi tiết',
                            'id' => 'edit_description'
                            ]) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
