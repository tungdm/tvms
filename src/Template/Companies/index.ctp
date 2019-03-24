<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 */

use Cake\Core\Configure;
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
$this->Html->css('flag-icon.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('company.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý Công ty - Xí nghiệp');
?>

<?php $this->start('content-header'); ?>
    <?php if ($mode == 1): ?>
        <h1><?= __('QUẢN LÝ CÔNG TY - XÍ NGHIỆP PHÁI CỬ') ?></h1>
    <?php else: ?>
        <h1><?= __('QUẢN LÝ CÔNG TY - XÍ NGHIỆP TIẾP NHẬN') ?></h1>
    <?php endif; ?>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách công ty</li>
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
                    onclick="showAddCompanyModal(<?= $mode?>)">
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
                'url' => ['controller' => 'Companies', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-company-overlay">
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
                <?php if ($mode == 1): ?>
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-num"><?= __('STT') ?></th>
                                <th scope="col" class="nameCol">
                                    <?= $this->Paginator->sort('name_romaji', 'Công ty')?>
                                </th>
                                <th scope="col" class="addressCol">
                                    <?= $this->Paginator->sort('address_romaji', 'Địa chỉ') ?>
                                </th>
                                <th scope="col" class="deputyCol">
                                    <?= __('Người đại diện') ?>
                                </th>
                                <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="hidden">
                                        <?= $this->Form->control('type', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'value' => '1'
                                            ]) 
                                        ?>
                                    </div>
                                </td>
                                <td class="col-md-3 nameCol">
                                    <?= $this->Form->control('name', [
                                        'label' => false,
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['name'] ?? ''
                                        ]) 
                                    ?>
                                </td>
                                <td class="col-md-4 addressCol">
                                    <?= $this->Form->control('address', [
                                        'label' => false,                             
                                        'class' => 'form-control col-md-7 col-xs-12', 
                                        'value' => $query['address'] ?? ''
                                        ])
                                    ?>
                                </td>
                                <td class="col-md-3 deputyCol">
                                    <?= $this->Form->control('deputy', [
                                        'label' => false,                             
                                        'class' => 'form-control col-md-7 col-xs-12', 
                                        'value' => $query['deputy'] ?? ''
                                        ])
                                    ?>
                                </td>
                                <td class="filter-group-btn actions">
                                    <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                    <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                                </td>
                            <?= $this->Form->end() ?>
                            </tr>
                            <?php if (($companies)->isEmpty()): ?>
                            <tr>
                                <td colspan="100" class="table-empty"><?= __('Hiện tại không có dữ liệu') ?></td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($companies as $company): ?>
                            <?php $counter++ ?>
                            <tr>
                                <td class="cell text-center <?= $company->del_flag ? 'deletedRecord' : '' ?>"><?= h($counter) ?></td>
                                <td class="cell nameCol">
                                    <a href="javascript:;" onclick="viewDispatchingCompany(<?= $company->id ?>)">
                                        <?= h($company->name_romaji) ?><br/><?= h($company->name_kanji) ?>
                                    </a>
                                </td>
                                <td class="cell addressCol"><?= h($company->address_romaji) ?></td>
                                <td class="cell deputyCol">
                                    <?= h($company->deputy_name_romaji) ?>
                                </td>
                                <td class="actions cell">                              
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="viewDispatchingCompany(<?= $company->id ?>)">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                                </a>
                                            </li>
                                            <?php if ($permission == 0): ?>
                                                <?php if ($company->del_flag): ?>
                                                    <li>
                                                        <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                        ['action' => 'recover', $company->id], 
                                                        [
                                                            'escape' => false, 
                                                            'confirm' => __('Bạn có chắc chắn muốn phục hồi công ty {0}?', $company->name_romaji)
                                                        ]) ?>
                                                    </li>
                                                <?php else: ?>
                                                    <li>
                                                        <a href="javascript:;" id="edit-company-btn" onClick="editCompany('<?= $company->id ?>', <?= $mode ?>)">
                                                        <i class="fa fa-edit"></i> Sửa</a>
                                                    </li>
                                                    <li>
                                                        <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                            ['action' => 'delete', $company->id], 
                                                            [
                                                                'escape' => false, 
                                                                'confirm' => __('Bạn có chắc chắn muốn xóa công ty {0}?', $company->name_romaji)
                                                            ]) ?>
                                                    </li>
                                                <?php endif;  ?>
                                            <?php endif;  ?>
                                        </ul>
                                    </div>                                      
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>                
                        </tbody>
                    </table>
                <?php else: ?>
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-num col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="nameCol col-md-3">
                                    <?= $this->Paginator->sort('name_romaji', 'Công ty')?>
                                </th>
                                <th scope="col" class="addressCol col-md-4">
                                    <?= $this->Paginator->sort('address_romaji', 'Địa chỉ') ?>
                                </th>
                                <th scope="col" colspan="2" class="phoneCol col-md-3">
                                    <?= __('Số điện thoại') ?>
                                </th>
                                <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="hidden">
                                        <?= $this->Form->control('type', [
                                            'label' => false,
                                            'class' => 'form-control col-md-7 col-xs-12',
                                            'value' => '2'
                                            ]) 
                                        ?>
                                    </div>
                                </td>
                                <td class="nameCol">
                                    <?= $this->Form->control('name', [
                                        'label' => false,
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['name'] ?? ''
                                        ]) 
                                    ?>
                                </td>
                                <td class="addressCol">
                                    <?= $this->Form->control('address', [
                                        'label' => false,                             
                                        'class' => 'form-control col-md-7 col-xs-12', 
                                        'value' => $query['address'] ?? ''
                                        ])
                                    ?>
                                </td>
                                <td class="phonevnCol">
                                    <div class="input-group">
                                        <?= $this->Form->control('phone_vn', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'value' => $query['phone_vn'] ?? ''
                                            ])
                                        ?>
                                        <span class="input-group-addon" style="line-height: 1;">
                                            <span class="flag-icon flag-icon-vn"></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="phonejpCol">
                                    <div class="input-group">
                                        <?= $this->Form->control('phone_jp', [
                                            'label' => false, 
                                            'class' => 'form-control col-md-7 col-xs-12', 
                                            'value' => $query['phone_jp'] ?? ''
                                            ])
                                        ?>
                                        <span class="input-group-addon" style="line-height: 1;">
                                                <span class="flag-icon flag-icon-jp"></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="filter-group-btn actions">
                                    <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                    <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                                </td>
                            <?= $this->Form->end() ?>
                            </tr>
                            <?php if (($companies)->isEmpty()): ?>
                            <tr>
                                <td colspan="100" class="table-empty"><?= __('Hiện tại không có dữ liệu') ?></td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($companies as $company): ?>
                                    <?php $counter++ ?>
                                    <tr>
                                        <td class="cell text-center <?= $company->del_flag ? 'deletedRecord' : '' ?>"><?= h($counter) ?></td>
                                        <td class="cell nameCol">
                                            <a href="javascript:;" onclick="viewCompany(<?= $company->id ?>)">
                                                <?= h($company->name_romaji) ?><br/><?= h($company->name_kanji) ?>
                                            </a>
                                        </td>
                                        <td class="cell addressCol"><?= h($company->address_romaji) ?></td>
                                        <td class="cell phonevnCol"><?= h($this->Phone->makeEdit($company->phone_vn)) ?></td>
                                        <td class="cell phonejpCol"><?= h($company->phone_jp) ?></td>
                                        <td class="actions cell">                              
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li>
                                                        <a href="javascript:;" onclick="viewCompany(<?= $company->id ?>)">
                                                            <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                                        </a>
                                                    </li>
                                                    <?php if ($permission == 0): ?>
                                                        <?php if ($company->del_flag): ?>
                                                            <li>
                                                                <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                                ['action' => 'recover', $company->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn phục hồi công ty {0}?', $company->name_romaji)
                                                                ]) ?>
                                                            </li>
                                                        <?php else: ?>
                                                            <li>
                                                                <a href="javascript:;" id="edit-company-btn" onClick="editCompany('<?= $company->id ?>', <?= $mode ?>)">
                                                                <i class="fa fa-edit"></i> Sửa</a>
                                                            </li>
                                                            <li>
                                                                <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                                    ['action' => 'delete', $company->id], 
                                                                    [
                                                                        'escape' => false, 
                                                                        'confirm' => __('Bạn có chắc chắn muốn xóa công ty {0}?', $company->name_romaji)
                                                                    ]) ?>
                                                            </li>
                                                            <li class="divider"></li>
                                                            <li>
                                                                <a href="javascript:;" onclick="showListWorkersModal(<?= $company->id ?>)">
                                                                    <i class="fa fa-users" aria-hidden="true"></i> Danh sách lao động
                                                                </a>
                                                            </li>
                                                        <?php endif;  ?>
                                                    <?php endif;  ?>
                                                </ul>
                                            </div>                                      
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>                
                        </tbody>
                    </table>
                <?php endif; ?>
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

<div class="modal fade" id="add-dispatching-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI CÔNG TY PHÁI CỬ</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-dispatching-company-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Companies', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) 
                    ?>
                <div class="hidden">
                    <?= $this->Form->control('type', [
                        'label' => false, 
                        'class' => 'form-control col-md-7 col-xs-12',
                        'value' => '1'
                        ]) ?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji"><?= __('Tên công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập tên công ty bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên công ty bằng tiếng Anh/Nhật'
                            ]) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="deputy_name"><?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_romaji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên người đại diện bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="address_romaji"><?= __('Địa chỉ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="add-company-submit-btn" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="add-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI CÔNG TY TIẾP NHẬN</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-company-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Companies', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) 
                    ?>
                <div class="hidden">
                    <?= $this->Form->control('type', [
                        'label' => false, 
                        'class' => 'form-control col-md-7 col-xs-12',
                        'value' => '2'
                        ]) ?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Tên công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12 autoFocus', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy_name">
                        <?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_romaji">
                        <?= __('Địa chỉ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="phone_vn">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', [
                            'label' => false, 
                            'type' => 'text',
                            'minLength' => 10,
                            'maxlength' => 11,
                            'data-parsley-type' => 'digits',
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập số điện thoại tại Việt Nam'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập số điện thoại tại Nhật Bản']) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="add-company-submit-btn" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-dispatching-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT THÔNG TIN CÔNG TY PHÁI CỬ</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'edit-dispatching-company-form',
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Companies', 'action' => 'edit'],
                    'templates' => ['inputContainer' => '{{content}}']
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-dis-id']) ?>
                <div class="hidden">
                    <?= $this->Form->control('type', [
                        'label' => false, 
                        'class' => 'form-control col-md-7 col-xs-12',
                        'value' => '1'
                        ]) ?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji"><?= __('Tên công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false,
                            'id' => 'edit-dis-name-romaji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập tên công ty bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false,
                            'id' => 'edit-dis-name-kanji',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên công ty bằng tiếng Anh/Nhật'
                            ]) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="deputy_name"><?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_romaji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'id' => 'edit-dis-deputy-name-romaji', 
                            'placeholder' => 'Nhập tên người đại diện bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="address_romaji"><?= __('Địa chỉ') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', [
                            'label' => false,
                            'id' => 'edit-dis-address-romaji', 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="phone_vn">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', [
                            'label' => false, 
                            'type' => 'text',
                            'minLength' => 10,
                            'maxlength' => 11,
                            'data-parsley-type' => 'digits',
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập số điện thoại'
                            ]) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="edit-dis-company-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT THÔNG TIN CÔNG TY</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'edit-company-form',
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Companies', 'action' => 'edit'],
                    'templates' => ['inputContainer' => '{{content}}']
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-id']) ?>
                <div class="hidden">
                    <?= $this->Form->control('type', [
                        'label' => false, 
                        'class' => 'form-control col-md-7 col-xs-12',
                        'value' => '2'
                        ]) ?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Tên công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false,
                            'id' => 'edit-name-romaji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false,
                            'id' => 'edit-name-kanji',
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy_name">
                        <?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_romaji', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'id' => 'edit-deputy-name-romaji', 
                            'placeholder' => 'Nhập bằng kí tự romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_kanji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'id' => 'edit-deputy-name-kanji', 
                            'placeholder' => 'Nhập bằng kí tự kanji',
                            ]) ?>
                    </div>
                </div>
                
                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_romaji">
                        <?= __('Địa chỉ') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', [
                            'label' => false,
                            'required' => true, 
                            'id' => 'edit-address-romaji', 
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', [
                            'label' => false,
                            'id' => 'edit-address-kanji', 
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="phone_vn">
                        <?= __('Số Điện Thoại') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', ['label' => false, 
                            'id' => 'edit-phone-vn',
                            'type' => 'text',
                            'minLength' => 10,
                            'maxlength' => 11,
                            'data-parsley-type' => 'digits',
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập số điện thoại tại Việt Nam']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', ['label' => false, 
                        'id' => 'edit-phone-jp',
                        'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập số điện thoại tại Nhật Bản']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="edit-company-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="setting-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Chọn Lọc</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'type' => 'get',
                    'id' => 'setting-company-form',
                    'class' => 'form-horizontal form-label-left',
                    ]) ?>
                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12 control-label" for="display_field">
                        <?= __('Tiêu Chí') ?> </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="nameCol" value checked>
                                <?= __('Tên Romaji') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="namekanjiCol" value checked>
                                <?= __('Tên Kanji') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addressCol" value checked>
                                <?= __('Địa chỉ Romaji') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addresskanjiCol" value checked>
                                <?= __('Địa chỉ Kanji') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="phonevnCol" value checked>
                                <?= __('Điện thoại Việt') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="phonejpCol" value>
                                <?= __('Điện thoại Nhật') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="setting-company-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-default" id="setting-company-close-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view-workers-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">DANH SÁCH <span class="total-count"></span> LAO ĐỘNG</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12 table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Họ tên') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Ngày bay chính thức') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Thời gian làm việc') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Quê quán') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Nghề nghiệp') ?></th>
                            </tr>
                        </thead>
                        <tbody id="workers-container"></tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<script id="workers-template" type="text/x-handlebars-template">
    {{#each this}}
    <tr>
        <td class="cell text-center">{{inc @index}}</td>
        <td class="cell">
            <a href="javascript:;" onclick="viewWorkers({{id}});">{{fullname}}</a>
        </td>
        <td class="cell">{{dateTimeFormat _matchingData.Orders.departure}}</td>
        <td class="cell">{{_matchingData.Orders.work_time}} năm</td>
        <td class="cell">{{addresses.0.city.name}}</td>
        <td class="cell">
            <a href="javascript:;" onclick="viewOrder({{_matchingData.Orders.id}});">{{job}}</a>
        </td>
    </tr>
    {{else}}
    <tr>
        <td colspan="6" class="table-empty"><?= __('Hiện tại không có dữ liệu') ?></td>
    </tr>
    {{/each}}
</script>