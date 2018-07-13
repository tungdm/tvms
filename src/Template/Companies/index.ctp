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
$permission = $this->request->session()->read($controller);
$currentUser = $this->request->session()->read('Auth.User');
$counter = 0;

//$this->Html->css('presenter.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('parsley.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('company.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

?>

<?php $this->start('content-header'); ?>
<h1><?= __('CÔNG TY - XÍ NGHIỆP') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chính',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">DANH SÁCH</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('XÍ NGHIỆP TIẾP NHẬN') ?></h3>
                <div class="box-tools pull-right">  
                    <a data-toggle="modal" data-target="#add-company-modal" href="#"><i class="fa fa-plus"></i></a>
                    <div class="btn-group">
                        <a href="#" class="btn btn-box-tool dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a data-toggle="modal" data-target="#setting-company-modal" href="#">Chọn mục quản lý</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Xuất danh sách</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Companies', 'action' => 'index'],
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
                            <th scope="col" class="col-num"><?= __('No.') ?></th>
                            <th scope="col" class="nameromajiCol">
                                <?= $this->Paginator->sort('name_romaji', 'Tên Romaji')?>
                            </th>
                            <th scope="col" class="namekanjiCol">
                                <?= $this->Paginator->sort('name_kanji', 'Tên Kanji')?>
                            </th>
                            <th scope="col" class="addressromajiCol">
                                <?= $this->Paginator->sort('address_romaji' ,'Địa chỉ Romaji') ?>
                            </th>
                            <th scope="col" class="addresskanjiCol">
                                <?= $this->Paginator->sort('address_kanji' ,'Địa chỉ Kanji') ?>
                            </th>
                            <th scope="col" class="phonevnCol">
                                <?= $this->Paginator->sort('phone_vn', 'Điện thoại VN') ?>
                            </th>
                            <th scope="col" class="phonejpCol hidden">
                                <?= $this->Paginator->sort('phone_jp', 'Điện thoại Nhật') ?>
                            </th>
                            
    
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="col-md-2 nameromajiCol">
                                <?= $this->Form->control('name_romaji', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['name_romaji'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 namekanjiCol">
                                <?= $this->Form->control('name_kanji', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['name_kanji'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 addressromajiCol">
                                <?= $this->Form->control('address_romaji', [
                                    'label' => false,                             
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['address_romaji'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 addresskanjiCol">
                                <?= $this->Form->control('address_kanji', [
                                    'label' => false,                             
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['address_kanji'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 phonevnCol">
                                <?= $this->Form->control('phone_vn', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['phone'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 phonejpCol hidden">
                                <?= $this->Form->control('phone_jp', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['phone_jp'] ?? ''
                                    ])
                                ?>
                            </td>
            
                            
                            <td class="filter-group-btn">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($companies)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('No data available') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($companies as $company): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell"><?= h($counter) ?></td>
                            <td class="cell nameromajiCol"><?= h($company->name_romaji) ?></td>
                            <td class="cell namekanjiCol"><?= h($company->name_kanji) ?></td>
                            <td class="cell addressromajiCol"><?= h($company->address_romaji) ?></td>
                            <td class="cell addresskanjiCol"><?= h($company->address_kanji) ?></td>
                            <td class="cell phonevnCol"><?= h($company->phone_vn) ?></td>
                            <td class="cell phonejpCol hidden"><?= h($company->phone_jp) ?></td>
                            
                            
                            <td class="actions cell">                              
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                        <a href="#" id="edit-company-btn" onClick="editCompany('<?= $company->id ?>')">
                                        <i class="fa fa-pencil"></i> Sửa</a>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $company->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Are you sure you want to delete {0}?', $company->username)
                                                ]) ?>
                                        </li>
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

<div class="modal fade" id="add-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm mới Công ty - Xí nghiệp tiếp nhận</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
            'class' => 'form-horizontal form-label-left', 
            'id' => 'add-presenter-form', 
            'data-parsley-validate' => '',
            'url' => ['controller' => 'Companies', 'action' => 'add'],
            'templates' => [
                'inputContainer' => '{{content}}'
                ]
            ]) 
            ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_kanji">
                        <?= __('Phiên âm Kanji') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên kanji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_romaji">
                        <?= __('Địa chỉ(tiếng Việt)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Việt']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_kanji">
                        <?= __('Địa chỉ(tiếng Việt)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Việt']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số Điện Thoại Việt Nam') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại tại VN']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_jp">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại Nhật Bản']) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="add-company-submit-btn" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-company-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Chỉnh Sửa Thông Tin</h4>
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
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Công ty tiếp nhận') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false,
                            'id' => 'edit-name-romaji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên xí nghiệp bằng romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_kanji">
                        <?= __('Tên Kanji') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false,
                            'id' => 'edit-name-kanji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên xí nghiệp bằng kanji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_romaji">
                        <?= __('Địa chỉ Romaji') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', [
                            'label' => false,
                            'id' => 'edit-address-romaji', 
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Việt']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_kanji">
                        <?= __('Địa chỉ Kanji') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', [
                            'label' => false,
                            'id' => 'edit-address-kanji', 
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Việt']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số Điện Thoại tại VN') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone', ['label' => false, 
                        'id' => 'edit-phone-vn',
                        'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại tại VN']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_jp">
                        <?= __('Số Điện Thoại tại Nhật') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone', ['label' => false, 
                        'id' => 'edit-phone-jp',
                        'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại tại Nhật']) ?>
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
                                <input type="checkbox" name="nameromajiCol" value checked>
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
                                <input type="checkbox" name="addressromajiCol" value checked>
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