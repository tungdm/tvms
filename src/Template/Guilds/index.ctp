<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Guild[]|\Cake\Collection\CollectionInterface $guilds
 */
use Cake\Core\Configure;
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller);
$recordsDisplay = Configure::read('recordsDisplay');
$counter = 0;
//$currentUser = $this->request->session()->read('Auth.User');
//$this->Html->css('guild.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('guild.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);
?>

<?php $this->start('content-header'); ?>
<h1><?= __('DANH SÁCH NGHIỆP ĐOÀN') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Home',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Nghiệp Đoàn</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('CỘNG TÁC VIÊN') ?></h3>
                <div class="box-tools pull-right">  
                    <a class="btn btn-box-tool" data-toggle="modal" data-target="#add-guild-modal" href="#"><i class="fa fa-plus"></i></a>
                    <div class="btn-group">
                        <a href="#" class="btn btn-box-tool dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a data-toggle="modal" data-target="#setting-guild-modal" href="#">Setting Show/Hide field</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Guilds', 'action' => 'index'],
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
                                <?= $this->Paginator->sort('nameromaji', 'Tên')?>
                            </th>
                            <th scope="col" class="namekanjiCol">
                                <?= $this->Paginator->sort('namekanji', 'Tên Nhật')?>
                            </th>
                            <th scope="col" class="addressromajiCol">
                                <?= $this->Paginator->sort('addressromaji' ,'Địa chỉ') ?>
                            </th>
                            <th scope="col" class="addresskanjiCol">
                                <?= $this->Paginator->sort('addresskanji' ,'Địa chỉ Nhật') ?>
                            </th>
                            <th scope="col" class="phonevnCol">
                                <?= $this->Paginator->sort('phonevn', 'Số điện thoại') ?>
                            </th>
                            <th scope="col" class="phonejpCol">
                                <?= $this->Paginator->sort('phonejp', 'Điện thoại Nhật') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
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
                                    'value' => $query['phone_vn'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 phonejpCol">
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
                        <?php if (($guilds)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('No data available') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($guilds as $guild): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell"><?= h($counter) ?></td>
                            <td class="cell nameromajiCol"><?= h($guild->name_romaji) ?></td>
                            <td class="cell namekanjiCol"><?= h($guild->name_kanji) ?></td>
                            <td class="cell addressromajiCol"><?= h($guild->address_romaji) ?></td>
                            <td class="cell addresskanjiCol"><?= h($guild->address_kanji) ?></td>
                            <td class="cell phonevnCol"><?= h($guild->phone_vn) ?></td>
                            <td class="cell phonejpCol"><?= h($guild->phone_jp) ?></td>
                            
                            
                            <td class="actions cell">                              
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                        <a href="#" id="edit-guild-btn" onClick="editGuild('<?= $guild->id ?>')">
                                        <i class="fa fa-pencil"></i> Sửa</a>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                ['action' => 'delete', $guild->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Are you sure you want to delete {0}?', $guild->username)
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
                        <?= $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                        <?= $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
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
                <h4 class="modal-title">Thêm Mới Nghiệp Đoàn</h4>
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
            ]) 
            ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Tên Nghiệp Đoàn') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên nghiệp đoàn']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_kanji">
                        <?= __('組合の名前') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên nghiệp đoàn']) ?>
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
                        <?= __('組合の住所') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Nhật']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại ở Việt Nam']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_jp">
                        <?= __('電番') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại ở Nhật']) ?>
                    </div>
                </div>
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
                <h4 class="modal-title">Chỉnh Sửa Thông Tin</h4>
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
                        <?= __('Tên Nghiệp Đoàn') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false,
                            'id' => 'edit-name-romaji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên nghiệp đoàn']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="name_kanji">
                        <?= __('組合') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false,
                            'id' => 'edit-name-kanji',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập tên nghiệp đoàn']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_romaji">
                        <?= __('Địa chỉ(tiếng Việt)') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', [
                            'label' => false,
                            'id' => 'edit-address-romaji', 
                            'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Việt']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address_kanji">
                        <?= __('Địa chỉ(tiếng Nhật)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', ['label' => false,
                        'id' => 'edit-address-kanji',
                        'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Địa chỉ bằng tiếng Nhật']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số Điện Thoại Việt Nam') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', ['label' => false, 
                        'id' => 'edit-phone-vn',
                        'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại ở Việt Nam']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone_jp">
                        <?= __('Số Điện Thoại Nhật Bản') ?> *</label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', ['label' => false, 
                        'id' => 'edit-phone-jp',
                        'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Vui lòng nhập số điện thoại ở Nhật']) ?>
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
                                <input type="checkbox" name="nameromajiCol" value checked>
                                <?= __('Tên (Việt)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="namekanjiCol" value checked>
                                <?= __('Tên (Nhật)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addressromajiCol" value checked>
                                <?= __('Địa chỉ (Việt)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="addresskanjiCol" value checked>
                                <?= __('Địa chỉ (Nhật)') ?>
                            </label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label>
                                <input type="checkbox" name="phonevnCol" value checked>
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
