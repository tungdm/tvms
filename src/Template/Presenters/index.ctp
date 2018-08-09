<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Presenter[]|\Cake\Collection\CollectionInterface $presenters
 */
use Cake\Core\Configure;
$recordsDisplay = Configure::read('recordsDisplay');
$presenterType = Configure::read('presenterType');

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');
$counter = 0;

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('presenter.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý Cộng tác viên');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ CỘNG TÁC VIÊN') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Cộng tác viên</li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
            <li>
                <a  data-toggle='tooltip' title='Thêm mới'
                    class="zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out" 
                    onclick="showAddPresenterModal()">
                    <i class="fa fa-plus"></i>
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
                'url' => ['controller' => 'Presenters', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-presenter-overlay">
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
                            <th scope="col" class="nameCol">
                                <?= $this->Paginator->sort('name', 'Cộng tác viên')?>
                            </th>
                            <th scope="col" class="addressCol">
                                <?= $this->Paginator->sort('address' ,'Địa chỉ') ?>
                            </th>
                            <th scope="col" class="phoneCol">
                                <?= $this->Paginator->sort('phone', 'Số điện thoại') ?>
                            </th>
                            <th scope="col" class="typeCol">
                                <?= __('Phân loại') ?>
                            </th>
                            <th scope="col" class="actions" ><?= __('Thao tác') ?></th>
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
                            <td class="col-md-3 addressCol">
                                <?= $this->Form->control('address', [
                                    'label' => false,                             
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['address'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 phoneCol">
                                <?= $this->Form->control('phone', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['phone'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 typeCol">
                                <?= $this->Form->control('presenter_type', [
                                    'options' => $presenterType,
                                    'label' => false, 
                                    'empty' => true, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                    'value' => $query['presenter_type'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($presenters)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($presenters as $presenter): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell"><?= h($counter) ?></td>
                            <td class="cell nameCol"><?= h($presenter->name) ?></td>
                            <td class="cell addressCol"><?= h($presenter->address) ?></td>
                            <td class="cell phoneCol"><?= h($this->Phone->makeEdit($presenter->phone)) ?></td>
                            <td class="cell typeCol"><?= h($presenterType[$presenter->type]) ?></td>
                            
                            
                            <td class="actions cell">                              
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="viewPresenter(<?= $presenter->id ?>)">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                            </a>
                                        </li>
                                        <?php if ($permission == 0): ?>
                                        <li>
                                            <a href="javascript:;" id="edit-presenter-btn" onClick="editPresenter('<?= $presenter->id ?>')">
                                            <i class="fa fa-edit"></i> Sửa</a>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $presenter->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa cộng tác viên {0}?', $presenter->name)
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
                        <?= $this->Paginator->next(__('Trang kế') . ' >') ?>
                        <?= $this->Paginator->last(__('Trang cuối') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Trang thứ {{page}} trên tổng {{pages}} trang, {{current}} trên tổng số {{count}} bản ghi')]) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add-presenter-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI CỘNG TÁC VIÊN</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-presenter-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Presenters', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name">
                        <?= __('Cộng tác viên') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập tên cộng tác viên']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="address">
                        <?= __('Địa chỉ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="phone">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                            'placeholder' => 'Nhập số điện thoại của cộng tác viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="type">
                        <?= __('Loại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('type', [
                            'options' => $presenterType,
                            'label' => false, 
                            'required' => true, 
                            'empty' => true, 
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-type',
                            'data-parsley-class-handler' => '#select2-type',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-type"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="add-presenter-submit-btn" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>


<div class="modal fade" id="edit-presenter-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT THÔNG TIN CỘNG TÁC VIÊN</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'edit-presenter-form',
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Presenters', 'action' => 'edit'],
                    'templates' => ['inputContainer' => '{{content}}']
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-id']) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name">
                        <?= __('Cộng tác viên') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name', [
                            'label' => false,
                            'id' => 'edit-name',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên cộng tác viên']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="address">
                        <?= __('Địa chỉ') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address', [
                            'label' => false,
                            'id' => 'edit-address', 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ bằng tiếng Việt']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="phone">
                        <?= __('Số Điện Thoại') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'id' => 'edit-phone',
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số điện thoại của cộng tác viên']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="type">
                        <?= __('Loại') ?></label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <?= $this->Form->control('type', [
                                'options' => $presenterType,
                                'label' => false, 
                                'required' => true, 
                                'empty' => true, 
                                'label' => false, 
                                'id' => 'edit-type',
                                'data-parsley-errors-container' => '#error-edit-type',
                                'data-parsley-class-handler' => '#select2-edit-type',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-edit-type"></span>
                        </div>
                    </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="edit-presenter-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="setting-presenter-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Chọn Lọc</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'type' => 'get',
                    'id' => 'setting-presenter-form',
                    'class' => 'form-horizontal form-label-left',
                    ]) ?>
                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12 control-label" for="display_field">
                        <?= __('Tiêu Chí') ?> </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="nameCol" value checked>
                                <?= __('Tên') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addressCol" value checked>
                                <?= __('Địa chỉ') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="phoneCol" value checked>
                                <?= __('Điện thoại') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="typeCol" value checked>
                                <?= __('Loại') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="setting-presenter-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-default" id="setting-presenter-close-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>