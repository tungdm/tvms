<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$recordsDisplay = Configure::read('recordsDisplay');
$scope = Configure::read('scope');
$confPermission = Configure::read('permission');

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');
$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$this->Html->css('user.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý Nhân viên');
?>

<?php $this->start('content-header'); ?>
<h1><?= __('QUẢN LÝ NHÂN VIÊN') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Danh sách nhân viên</li>
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
                        'title' => 'Thêm mới nhân viên',
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
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Users', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden">
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
                            <th scope="col" class="usernameCol">
                                <?= $this->Paginator->sort('username', 'Tên đăng nhập')?>
                            </th>
                            <th scope="col" class="emailCol hidden">
                                <?= $this->Paginator->sort('email') ?>
                            </th>
                            <th scope="col" class="genderCol">
                                <?= __('Giới tính') ?>
                            </th>
                            <th scope="col" class="phoneCol">
                                <?= $this->Paginator->sort('phone', 'Số điện thoại') ?>
                            </th>
                            <th scope="col" class="fullnameCol">
                                <?= $this->Paginator->sort('fullname', 'Họ tên') ?>
                            </th>
                            <th scope="col" class="roleCol">
                                <?= __('Chức vụ') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <?= $this->Form->checkbox('deleted', [
                                    'class' => 'js-switch', 
                                    'checked' => $query['deleted']
                                    ]) ?>
                            </td>
                            <td class="col-md-2 usernameCol">
                                <?= $this->Form->control('username', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['username'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 emailCol hidden">
                                <?= $this->Form->control('email', [
                                    'label' => false, 
                                    'type' => 'text',
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['email'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 genderCol">
                                <?= $this->Form->control('gender', [
                                    'options' => $gender, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['gender'] ?? ''
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
                            <td class="col-md-2 fullnameCol">
                                <?= $this->Form->control('fullname', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['fullname'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-1 roleCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('role', [
                                    'options' => $roles, 
                                    'empty' => true, 
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                                    'value' => $query['role'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($users)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell text-center <?= $user->del_flag ? 'deletedRecord' : '' ?>"><?= h($counter) ?></td>
                            <td class="cell usernameCol"><?= h($user->username) ?></td>
                            <td class="cell emailCol hidden"><?= h($user->email) ?></td>
                            <td class="cell genderCol text-center"><?= h($gender[$user->gender]) ?></td>
                            <td class="cell phoneCol"><?= h($this->Phone->makeEdit($user->phone)) ?></td>
                            <td class="cell fullnameCol"><?= h($user->fullname) ?></td>
                            <td class="cell roleCol"><?= h($user->role->name_vn) ?></td>
                            
                            <td class="actions cell">
                                <?php if ($permission == 0 && $user->role->name != 'admin' && $user->id != $currentUser['id']): ?>
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                        <ul role="menu" class="dropdown-menu">
                                            <?php if ($user->del_flag): ?>
                                                <li>
                                                    <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                    ['action' => 'recover', $user->id], 
                                                    [
                                                        'escape' => false, 
                                                        'confirm' => __('Bạn có chắc chắn muốn phục hồi nhân viên {0}?', $user->fullname)
                                                    ]) ?>
                                                </li>
                                            <?php else: ?>
                                                <li>
                                                    <a href="javascript:;" onclick='showEditPerModal("<?= $user->id ?>", "<?= $user->role->id ?>")'>
                                                        <i class="fa fa-edit" aria-hidden="true"></i> Phân quyền
                                                    </a>
                                                </li>
                                                <li>
                                                    <?= $this->Form->postLink('<i class="fa fa-key" aria-hidden="true"></i> Khôi phục mật khẩu', 
                                                        ['action' => 'resetPassword', $user->id], 
                                                        [
                                                            'escape' => false, 
                                                            'confirm' => __('Bạn có chắc chắn muốn khôi phục mật khẩu mặc định cho nhân viên {0}?', $user->fullname)
                                                        ]) ?>
                                                </li>
                                                <li>
                                                    <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                        ['action' => 'delete', $user->id], 
                                                        [
                                                            'escape' => false, 
                                                            'confirm' => __('Bạn có chắc chắn muốn xóa nhân viên {0}?', $user->fullname)
                                                        ]) ?>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>                
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


<div class="modal fade" id="setting-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CHỌN LỌC TRƯỜNG QUẢN LÝ</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'type' => 'get',
                    'id' => 'setting-form',
                    'class' => 'form-horizontal form-label-left',
                    ]) ?>
                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12 control-label" for="display_field"><?= __('Display field') ?> *</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="usernameCol" value checked> <?= __('Tên đăng nhập') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="emailCol" value> <?= __('Email') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="genderCol" value checked> <?= __('Giới tính') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="phoneCol" value checked> <?= __('Điện thoại') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="fullnameCol" value checked> <?= __('Họ tên') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="roleCol" value checked> <?= __('Chức vụ') ?></label>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="settings-submit-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="setting-close-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-permission-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT PHÂN QUYỀN</h4>
            </div>
            <div class="modal-body">
            <?= $this->Form->create(null, [
                'url' => ['controller' => 'Users', 'action' => 'editPermission'],
                'type' => 'post',
                'id' => 'edit-permission-form',
                'class' => 'form-horizontal form-label-left',
                ]) ?>
                <?= $this->Form->hidden('id') ?>
                
                <div class="form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="role"><?= __('Chức vụ ') ?></label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <?= $this->Form->control('role_id', [
                            'options' => $roles4Edit, 
                            'empty' => true, 
                            'required' => true,
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-role',
                            'data-parsley-class-handler' => '#select2-role-id',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme select-group edit-role',
                            ]) ?>
                        <span id="error-role"></span>
                    </div>
                </div>
                <div class="form-group permission-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="permission"><?= __('Phân quyền') ?></label>
                    <div class="col-md-9 col-sm-9 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary btn-permission" id="add-permission-top"><?= __('Thêm mới') ?></button>
                        <table class="table table-bordered custom-table permission-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-md-6"><?= __('Phạm vi quản lý') ?></th>
                                    <th scope="col" class="col-md-4"><?= __('Quyền hạn') ?></th>
                                    <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                                </tr>
                            </thead>
                            <tbody id="permission-container">
                            </tbody>
                        </table>
                    </div>
                </div>
            <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="edit-permission-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="setting-close-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="edit-permissions-template" type="text/x-handlebars-template">
{{#each this}}
<tr class="row-permission">
    <?= $this->Form->hidden('permissions.{{@index}}.id', ['value' => '{{id}}']) ?>
    <td>
        <?= $this->Form->control('permissions.{{@index}}.scope', [
            'options' => $scope,
            'empty' => true,
            'required' => true,
            'data-parsley-not-duplicate-scope' => '',
            'label' => false,
            'class' => 'select-scope form-control col-md-7 col-xs-12',
            ])
        ?>
    </td>
    <td>
        <?= $this->Form->control('permissions.{{@index}}.action', [
            'options' => $confPermission,
            'empty' => true,
            'required' => true,
            'label' => false,
            'class' => 'form-control col-md-7 col-xs-12',
            ])
        ?>
    </td>
    <td class="action-btn">
        <?= $this->Html->link(
            '<i class="fa fa-2x fa-remove"></i>',
            'javascript:;',
            ['escape' => false, 
            'class' => 'remove-permission',
            'onClick' => 'removePermission(this, true)'
            ]
        )?>
    </td>
</tr>
{{/each}}
</script>

<script id="permission-template" type="text/x-handlebars-template">    
    <tr class="row-permission" class="row-permission-{{counter}}">
        <td>
            <?= $this->Form->control('{{scope}}', [
                'options' => $scope, 
                'empty' => true,
                'required' => true, 
                'data-parsley-not-duplicate-scope' => '', 
                'label' => false, 
                'id' => 'scope-{{counter}}',
                'class' => 'select-scope form-control col-md-7 col-xs-12 select-group',
                ]) ?>
        </td>
        <td>
            <?= $this->Form->control('{{permission}}', [
                'options' => $confPermission, 
                'empty' => true, 
                'required' => true, 
                'label' => false, 
                'id' => 'permission-{{counter}}',
                'class' => 'select-action form-control col-md-7 col-xs-12 select-group',
                ]) ?>
        </td>
        <td class="action-btn">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove"></i>',
                'javascript:;',
                ['escape' => false, 
                'class' => 'remove-permission',
                'onClick' => 'removePermission(this)'
                ]
            )?>
        </td>
    </tr>
</script>