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
$this->Html->script('characteristic.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý tính cách');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ TÍNH CÁCH') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách tính cách</li>
    </ol>
<?php $this->end(); ?>

<?php if ($permission == 0): ?>
    <?php $this->start('floating-button'); ?>
        <div class="zoom" id="draggable-button">
            <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
            <ul class="zoom-menu">
                <li>
                    <a href="javascript:;" 
                        onclick="showAddCharModal()"
                        class="zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out" 
                        data-toggle="tooltip" 
                        title="" 
                        data-original-title="Thêm mới">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
        </div>
    <?php $this->end(); ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH') ?></h3>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Characteristics', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-chars-overlay">
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
                                <?= $this->Paginator->sort('name', 'Tính cách (VN)')?>
                            </th>
                            <th scope="col" class="nameJpCol">
                                <?= $this->Paginator->sort('name_jp', 'Tính cách (JP)')?>
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
                                <?= $this->Form->control('char_name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['char_name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-3 nameJpCol">
                                <?= $this->Form->control('char_name_jp', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['char_name_jp'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 createdByCol">
                                <?= $this->Form->control('created_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['created_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 modifiedByCol">
                                <?= $this->Form->control('modified_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['modified_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($characteristics)->isEmpty()): ?>
                            <tr>
                                <td colspan="6" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($characteristics as $char): ?>
                                <?php $counter++ ?>
                                <tr>
                                    <td class="cell text-center <?= $char->del_flag ? 'deletedRecord' : '' ?>"><?= $counter ?></td>
                                    <td class="cell nameVnCol">
                                        <a href="javascript:;" onclick="viewChar(<?= $char->id ?>)"><?= h($char->name) ?></a>
                                    </td>
                                    <td class="cell nameJpCol"><?= h($char->name_jp) ?></td>
                                    <td class="cell createdByCol">
                                        <?= !empty($char->created_by_user) ? h($char->created_by_user->fullname) : 'N/A' ?>
                                    </td>
                                    <td class="cell modifiedByCol">
                                        <?= !empty($char->modified_by_user) ? h($char->modified_by_user->fullname) : 'N/A' ?>
                                    </td>
                                    <td class="actions cell">
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:;" onClick="viewChar(<?= $char->id ?>)">
                                                        <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                                    </a>
                                                </li>
                                                <?php if ($permission == 0): ?>
                                                    <?php if ($char->del_flag): ?>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                            ['action' => 'recover', $char->id], 
                                                            [
                                                                'escape' => false, 
                                                                'confirm' => __('Bạn có chắc chắn muốn phục hồi tính cách {0}?', $char->name)
                                                            ]) ?>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <a href="javascript:;" onClick="showEditCharModal('<?= $char->id ?>')">
                                                            <i class="fa fa-edit"></i> Sửa</a>
                                                        </li>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                                ['action' => 'delete', $char->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn xóa tính cách {0}?', $char->name)
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

<div class="modal fade" id="add-char-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay hidden" id="add-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI TÍNH CÁCH</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-char-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Characteristics', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name"><?= __('Tính cách (VN)') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập bằng kí tự romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="name_jp"><?= __('Tính cách (JP)') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_jp', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji'
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

<div class="modal fade" id="edit-char-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay hidden" id="add-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT TÍNH CÁCH</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'edit-char-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Characteristics', 'action' => 'edit'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-char-id']) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name"><?= __('Tính cách (VN)') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập bằng kí tự romaji',
                            'id' => 'edit-char-name'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="name_jp"><?= __('Tính cách (JP)') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_jp', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji',
                            'id' => 'edit-char-name-jp'
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

<div id="view-char-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÔNG TIN TÍNH CÁCH</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal form-label-left">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tính cách (VN)') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-char-name"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name_jp"><?= __('Tính cách (JP)') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-char-name-jp"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-char-created-by"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-char-created"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group modified">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12 " for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-char-modified-by"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group modified">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-char-modified"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
