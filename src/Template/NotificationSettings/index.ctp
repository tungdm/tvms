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

    $this->Html->script('notification.js', ['block' => 'scriptBottom']);
    $this->Paginator->setTemplates([
        'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
        'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
        'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
    ]);
    
    $this->assign('title', 'Quản lý thông báo');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ THÔNG BÁO') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách thông báo</li>
    </ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH') ?></h3>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-setting-overlay">
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
                            <th scope="col" class="col-num col-md-1"><?= __('STT') ?></th>
                            <th scope="col" class="titleCol col-md-2">
                                <?= $this->Paginator->sort('title', 'Tiêu đề')?>
                            </th>
                            <th scope="col" class="groupCol col-md-3">
                                <?= __('Bộ phận nhận thông báo') ?>
                            </th>
                            <th scope="col" class="sendBeforeCol col-md-2">
                                <?= __('Thông báo trước hạn') ?>
                            </th>
                            <th scope="col" class="modifiedByCol col-md-3">
                                <?= __('Người sửa cuối') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="col-md-4 titleCol">
                                <?= $this->Form->control('f_title', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['f_title'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="groupCol">
                                <?= $this->Form->control('f_group', [
                                    'options' => $groups, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_group'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="sendBeforeCol">
                                <?= $this->Form->control('f_send_before', [
                                    'label' => false,
                                    'type' => 'number',
                                    'min' => 0,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['f_send_before'] ?? ''
                                    ]) ?>
                            </td>
                            <td class="modifiedByCol">
                                <?= $this->Form->control('f_modified_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_modified_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($settings)->isEmpty()): ?>
                            <tr>
                                <td colspan="5" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php $groupsArr = $groups->toArray(); ?>
                            <?php foreach ($settings as $setting): ?>
                                <?php 
                                    $counter++;
                                    $receiversArr = explode(',', $setting->receivers_groups);
                                    array_shift($receiversArr);
                                    array_pop($receiversArr);
                                ?>
                                <tr>
                                    <td class="cell text-center <?= $setting->del_flag ? 'deletedRecord' : '' ?>"><?= $counter ?></td>
                                    <td class="cell titleCol"><?= h($setting->title) ?></td>
                                    <td class="cell groupCol">
                                        <?php if (!empty($receiversArr)) : ?>
                                            <ol class="list-unstyled">
                                                <?php foreach ($receiversArr as $key => $value): ?>
                                                    <li><?= $groupsArr[$value] ?></li>
                                                <?php endforeach; ?>
                                            </ol>
                                        <?php else: ?>
                                            <?= __('N/A') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell sendBeforeCol text-center"><?=h($setting->send_before) ?> ngày</td>
                                    <td class="cell modifiedByCol"><?= !empty($setting->modified_by_user) ? $setting->modified_by_user->fullname : 'N/A' ?></td>
                                    <td class="actions cell">
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:;" onClick="viewSetting(<?= $setting->id ?>)">
                                                        <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                                    </a>
                                                </li>
                                                <?php if ($permission == 0): ?>
                                                    <?php if ($setting->del_flag): ?>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                            ['action' => 'recover', $setting->id], 
                                                            [
                                                                'escape' => false, 
                                                                'confirm' => __('Bạn có chắc chắn muốn phục hồi {0}?', $setting->title)
                                                            ]) ?>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <a href="javascript:;" onClick="showEditSettingModal('<?= $setting->id ?>')">
                                                            <i class="fa fa-edit"></i> Sửa</a>
                                                        </li>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                                ['action' => 'delete', $setting->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn xóa {0}?', $setting->title)
                                                                ]) ?>
                                                        </li>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif;?>
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

<div id="view-setting-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÔNG TIN THÔNG BÁO</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal form-label-left">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="title"><?= __('Tiêu đề') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-title"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="template"><?= __('Nội dung mẫu') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-template"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="groups"><?= __('Nhóm người nhận') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-receiver-groups"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="send_before"><?= __('Thông báo trước hạn') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-send-before"></span> ngày
                            </div>
                        </div>
                    </div>
                    <div class="form-group modified">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12 " for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-modified-by"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group modified">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-modified"></span>
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

<div class="modal fade" id="edit-setting-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay hidden" id="add-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT THIẾT LẬP</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'edit-setting-form', 
                    'data-parsley-validate' => '',
                    'url' => ['action' => 'edit'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->unlockField('groups'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-setting-id']) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="title"><?= __('Tiêu đề') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('title', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập tiêu đề thông báo'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="template"><?= __('Nội dung mẫu') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('template', [
                            'label' => false, 
                            'required' => true, 
                            'type' => 'textarea',
                            'rows' => 5,
                            'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                            'placeholder' => 'Nhập nội dung thông báo'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="group"><?= __('Nhóm người nhận') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('groups[]', [
                            'label' => false,
                            'empty' => true,
                            'required' => true, 
                            'multiple' => 'multiple',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'options' => $groups,
                            'data-parsley-errors-container' => '#error-group',
                            'data-parsley-class-handler' => '#select2-groups',
                            ]) ?>
                        <span id="error-group"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="send_before"><?= __('Thông báo trước hạn') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-5" style="padding-left: 0px">
                            <?= $this->Form->control('send_before', [
                                'label' => false,
                                'type' => 'number',
                                'min' => 0,
                                'default' => 0,
                                'class' => 'form-control col-md-7 col-xs-12',
                                'placeholder' => 'Nhập số ngày trước thời hạn'
                                ]) ?>
                        </div>
                        <div class="col-md-7">
                            <div class="form-control form-control-view">đơn vị: ngày</div>
                        </div>
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