<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Guild[]|\Cake\Collection\CollectionInterface $guilds
 */
use Cake\Core\Configure;
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$recordsDisplay = Configure::read('recordsDisplay');
$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$this->Html->css('flag-icon.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('guild.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý nghiệp đoàn');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ NGHIỆP ĐOÀN') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách nghiệp đoàn</li>
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
                    onclick="showAddGuildModal()">
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
                'url' => ['controller' => 'Guilds', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-guild-overlay">
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
                                <?= $this->Paginator->sort('name_romaji', 'Nghiệp đoàn')?>
                            </th>
                            <th scope="col" class="addressCol">
                                <?= $this->Paginator->sort('address_romaji', 'Địa chỉ') ?>
                            </th>
                            <th scope="col" colspan="2" class="phoneCol">
                                <?= __('Số điện thoại') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-1"></td>
                            <td class="col-md-3 nameCol">
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
                            <td class="col-md-2 phonevnCol">
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
                            <td class="col-md-2 phonejpCol">
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
                            
                            <td class="col-md-1 filter-group-btn">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($guilds)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Không có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($guilds as $guild): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell text-center"><?= h($counter) ?></td>
                            <td class="cell nameCol"><?= h($guild->name_romaji) ?><br/><?= h($guild->name_kanji) ?></td>
                            <td class="cell addressCol"><?= h($guild->address_romaji) ?></td>
                            <td class="cell phonevnCol"><?= h($this->Phone->makeEdit($guild->phone_vn)) ?></td>
                            <td class="cell phonejpCol"><?= h($guild->phone_jp) ?></td>
                            
                            <td class="actions cell">                              
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="viewGuild(<?= $guild->id ?>)">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                            </a>
                                        </li>
                                        <?php if ($permission == 0): ?>
                                        <li>
                                            <a href="javascript:;" id="edit-guild-btn" onClick="editGuild('<?= $guild->id ?>')">
                                            <i class="fa fa-edit"></i> Sửa</a>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $guild->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa nghiệp đoàn {0}?', $guild->name_romaji)
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

<div class="modal fade" id="add-guild-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI NGHIỆP ĐOÀN</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-guild-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Guilds', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Tên nghiệp đoàn') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji'
                            ]) ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="license_num">
                        <?= __('Số giấy phép') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('license_number', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số giấy phép của nghiệp đoàn'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="subsidy">
                        <?= __('Tiền trợ cấp TTS') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <div class="col-md-5" style="padding-left: 0px">
                            <?= $this->Form->control('subsidy', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => '¥'
                                ]) ?>
                        </div>
                        <div class="col-md-7">
                            <div class="form-control form-control-view">đơn vị: ¥/tháng</div>
                        </div>
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
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12 optional">
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
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                            'placeholder' => 'Nhập số điện thoại tại Việt Nam']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập số điện thoại tại Nhật Bản']) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="add-guild-submit-btn" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-guild-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT THÔNG TIN NGHIỆP ĐOÀN</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'edit-guild-form',
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'Guilds', 'action' => 'edit'],
                    'templates' => ['inputContainer' => '{{content}}']
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-id']) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Tên nghiệp đoàn') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false,
                            'id' => 'edit-name-romaji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
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

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="license_num">
                        <?= __('Số giấy phép') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('license_number', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'id' => 'edit-license-number',
                            'placeholder' => 'Nhập số giấy phép của nghiệp đoàn'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="subsidy">
                        <?= __('Tiền trợ cấp TTS') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <div class="col-md-5" style="padding-left: 0px">
                            <?= $this->Form->control('subsidy', [
                                'label' => false, 
                                'id' => 'edit-subsidy',
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => '¥'
                                ]) ?>
                            </div>
                            <div class="col-md-7">
                                <div class="form-control form-control-view">đơn vị: ¥/tháng</div>
                            </div>
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
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số điện thoại') ?></label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', [
                            'label' => false, 
                            'id' => 'edit-phone-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số điện thoại tại Việt Nam']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', [
                            'label' => false, 
                            'required' => true, 
                            'id' => 'edit-phone-jp',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số điện thoại tại Nhật Bản']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="edit-guild-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="setting-guild-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Chọn Lọc</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'type' => 'get',
                    'id' => 'setting-guild-form',
                    'class' => 'form-horizontal form-label-left',
                    ]) ?>
                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12 control-label" for="display_field">
                        <?= __('Tiêu Chí') ?> </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="nameCol" value checked>
                                <?= __('Tên (Việt)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="namekanjiCol" value>
                                <?= __('Tên (Nhật)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addressCol" value checked>
                                <?= __('Địa chỉ (Việt)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addresskanjiCol" value>
                                <?= __('Địa chỉ (Nhật)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="phonevnCol" value>
                                <?= __('Điện thoại') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="phonejpCol" value checked>
                                <?= __('電番') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="setting-guild-submit-btn">Hoàn Tất</button>
                <button type="button" class="btn btn-default" id="setting-guild-close-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
