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
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('adminCompany.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý công ty');
?>

<?php $this->start('content-header'); ?>
    <h1><?= __('QUẢN LÝ CÔNG TY') ?></h1>
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

<?php if ($permission == 0): ?>
    <?php $this->start('floating-button'); ?>
        <div class="zoom" id="draggable-button">
            <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
            <ul class="zoom-menu">
                
                <li>
                    <a href="javascript:;" 
                        onclick="showAddCompanyModal()"
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
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'AdminCompanies', 'action' => 'index'],
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
                <table class="table table-bordered custom-table">
                    <thead>
                        <tr>
                            <th scope="col" class="col-num"><?= __('STT') ?></th>
                            <th scope="col" class="aliasCol">
                                <?= $this->Paginator->sort('alias', 'Tên công ty')?>
                            </th>
                            <th scope="col" class="deputyCol">
                                <?= $this->Paginator->sort('deputy_name', 'Người đại diện')?>
                            </th>
                            <th scope="col" class="phoneNumberCol">
                                <?= __('Số điện thoại') ?>
                            </th>
                            <th scope="col" class="emailCol">
                                <?= __('Email') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-1"></td>
                            <td class="col-md-3 aliasCol">
                                <?= $this->Form->control('f_alias', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['f_alias'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-3 deputyCol">
                                <?= $this->Form->control('f_deputy_name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['f_deputy_name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 phoneNumberCol">
                                <?= $this->Form->control('f_phone_number', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['f_phone_number'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 emailCol">
                                <?= $this->Form->control('f_email', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['f_email'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($adminCompanies)->isEmpty()): ?>
                            <tr>
                                <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($adminCompanies as $company): ?>
                                <?php $counter++ ?>
                                <tr>
                                    <td class="cell text-center <?= $company->deleted ? 'deletedRecord' : '' ?>"><?= $counter ?></td>
                                    <td class="cell aliasCol">
                                        <a href="javascript:;" onclick="viewAdminCompany(<?= $company->id ?>)"><?= h($company->alias) ?></a>
                                    </td>
                                    <td class="cell deputyCol"><?= h($company->deputy_name) ?></td>
                                    <td class="cell phoneNumberCol"><?= h($company->phone_number) ?></td>
                                    <td class="cell emailCol"><?= h($company->email) ?></td>
                                    <td class="actions cell">
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:;" onClick="viewAdminCompany(<?= $company->id ?>)">
                                                        <i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết
                                                    </a>
                                                </li>
                                                <?php if ($permission == 0): ?>
                                                    <?php if ($company->deleted): ?>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                            ['action' => 'recover', $company->id], 
                                                            [
                                                                'escape' => false, 
                                                                'confirm' => __('Bạn có chắc chắn muốn phục hồi {0}?', $company->alias)
                                                            ]) ?>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <a href="javascript:;" onClick="showEditAdminCompanyModal('<?= $company->id ?>')">
                                                            <i class="fa fa-edit"></i> Sửa</a>
                                                        </li>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                                                ['action' => 'delete', $company->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn xóa {0}?', $company->alias)
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


<div class="modal fade" id="add-ad-company-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="overlay hidden" id="add-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI CÔNG TY</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'add-company-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'AdminCompanies', 'action' => 'add'],
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="alias">
                        <?= __('Mã công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('alias', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập mã công ty'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="short-name">
                        <?= __('Tên công ty (viết tắt)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('short_name', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên viết tắt của công ty bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="company-name">
                        <?= __('Tên công ty (đầy đủ)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_vn', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên đầy đủ của công ty bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_en', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên đầy đủ của công ty bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address">
                        <?= __('Địa chỉ công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_vn', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ công ty bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_en', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ công ty bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="branch">
                        <?= __('Chi nhánh') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('branch_vn', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chi nhánh (nếu có)'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('branch_jp', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chi nhánh bằng kanji'
                            ]) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="license">
                        <?= __('Giáy phép kinh doanh') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('license', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập giấy phép kinh doanh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="license-at"><?= __('Ngày nhận giấy phép') ?></label>
                    <div class="col-md-4 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="license-at-div">
                            <?= $this->Form->control('license_at', [
                                'type' => 'text',
                                'required' => true,
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-licenset-at'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-licenset-at"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone-number">
                        <?= __('Số điện thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_number', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số điện thoại'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="fax-number">
                        <?= __('Số fax') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('fax_number', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số fax'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="email">
                        <?= __('Email') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('email', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập email công ty'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy-name">
                        <?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên người đại diện'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy-role">
                        <?= __('Chức vụ (của người đại diện)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_role_vn', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người đại diện bằng romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_role_jp', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người đại diện bằng kanji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="signer">
                        <?= __('Người ký hồ sơ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('signer_name', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên người ký hồ sơ'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="signer-role">
                        <?= __('Chức vụ (của người ký hồ sơ)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('signer_role_vn', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người ký hồ sơ bằng romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('signer_role_jp', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người ký hồ bằng kanji'
                            ]) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="incorporate-date"><?= __('Ngày thành lập') ?></label>
                    <div class="col-md-4 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="incorporate-date-div">
                            <?= $this->Form->control('incorporation_date', [
                                'type' => 'text',
                                'required' => true,
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-incorporation-date'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-incorporation-date"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="capital_vn">
                        <?= __('Vốn điều lệ') ?> </label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('capital_vn', [
                                'label' => false,
                                'type' => 'number',
                                'required' => true, 
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'VND'
                                ]) ?>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('capital_jp', [
                                'label' => false,
                                'type' => 'number',
                                'required' => true, 
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12',
                                'placeholder' => '円'
                                ]) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="revenue_vn">
                        <?= __('Doanh thu năm gần nhất') ?> </label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('latest_revenue_vn', [
                                'label' => false,
                                'type' => 'number',
                                'required' => true, 
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'VND'
                                ]) ?>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('latest_revenue_jp', [
                                'label' => false,
                                'type' => 'number',
                                'required' => true, 
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12',
                                'placeholder' => '円'
                                ]) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="staff">
                        <?= __('Số lượng nhân viên') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('staffs_number', [
                            'label' => false, 
                            'type' => 'number',
                            'min' => '0',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số lượng nhân viên'
                            ]) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="edu-center-name">
                        <?= __('Trung tâm đào tạo (TTĐT)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_name_vn', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên TTĐT bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_name_jp', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên TTĐT bằng tiếng Nhật'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="edu-center-address">
                        <?= __('Địa chỉ TTĐT') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_address_vn', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ TTĐT bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_address_en', [
                            'label' => false, 
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ TTĐT bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>


<div class="modal fade" id="edit-ad-company-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="overlay hidden" id="add-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CẬP NHẬT THÔNG TIN CÔNG TY</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(false, [
                    'class' => 'form-horizontal form-label-left', 
                    'id' => 'edit-company-form', 
                    'data-parsley-validate' => '',
                    'url' => ['controller' => 'AdminCompanies', 'action' => 'edit'],
                    'templates' => ['inputContainer' => '{{content}}']
                    ]) ?>
                <?= $this->Form->unlockField('id'); ?>
                <?= $this->Form->hidden('id', ['id' => 'edit-id']) ?>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="alias">
                        <?= __('Mã công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('alias', [
                            'label' => false, 
                            'id' => 'edit-alias',
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập mã của công ty'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="short-name">
                        <?= __('Tên công ty (viết tắt)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('short_name', [
                            'label' => false, 
                            'id' => 'edit-short-name',
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập tên viết tắt của công ty bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="company-name">
                        <?= __('Tên công ty (đầy đủ)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_vn', [
                            'label' => false, 
                            'id' => 'edit-name-vn',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên đầy đủ của công ty bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_en', [
                            'label' => false, 
                            'id' => 'edit-name-en',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên đầy đủ của công ty bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address">
                        <?= __('Địa chỉ công ty') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_vn', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-address-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ công ty bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_en', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-address-en',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ công ty bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="branch">
                        <?= __('Chi nhánh') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('branch_vn', [
                            'label' => false, 
                            'id' => 'edit-branch-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chi nhánh công ty'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('branch_jp', [
                            'label' => false, 
                            'id' => 'edit-branch-jp',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chi nhánh công ty bằng kanji'
                            ]) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="license">
                        <?= __('Giáy phép kinh doanh') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('license', [
                            'label' => false, 
                            'id' => 'edit-license',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập giấy phép kinh doanh'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="license-at"><?= __('Ngày nhận giấy phép') ?></label>
                    <div class="col-md-4 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="edit-license-at-div">
                            <?= $this->Form->control('license_at', [
                                'type' => 'text',
                                'required' => true,
                                'id' => 'edit-license-at',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-edit-licenset-at'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-edit-licenset-at"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone-number">
                        <?= __('Số điện thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_number', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-phone-number',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số điện thoại'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="fax-number">
                        <?= __('Số fax') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('fax_number', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-fax-number',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số fax'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="email">
                        <?= __('Email') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('email', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-email',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập email công ty'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy-name">
                        <?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-deputy-name',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên người đại diện'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy-role">
                        <?= __('Chức vụ (của người đại diện)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_role_vn', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-deputy-role-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người đại diện bằng romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_role_jp', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-deputy-role-jp',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người đại diện bằng kanji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="signer">
                        <?= __('Người ký hồ sơ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('signer_name', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-signer',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên người ký hồ sơ'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="signer-role">
                        <?= __('Chức vụ (của người đại diện)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('signer_role_vn', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-signer-role-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người ký hồ sơ bằng romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('signer_role_jp', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-signer-role-jp',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người ký hồ sơ bằng kanji'
                            ]) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="incorporate-date"><?= __('Ngày thành lập') ?></label>
                    <div class="col-md-4 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="edit-incorporate-date-div">
                            <?= $this->Form->control('incorporation_date', [
                                'type' => 'text',
                                'required' => true,
                                'id' => 'edit-incorporation-date',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                'data-parsley-errors-container' => '#error-edit-incorporation-date'
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="error-edit-incorporation-date"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="capital_vn">
                        <?= __('Vốn điều lệ') ?> </label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('capital_vn', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'edit-capital-vn-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'edit-capital-vn', 
                                'placeholder' => 'VND'
                                ]) ?>
                            <?= $this->Form->control('capital_vn', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-capital-vn',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('capital_jp', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-capital-jp',
                                'required' => true, 
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12',
                                'placeholder' => '円'
                                ]) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="revenue_vn">
                        <?= __('Doanh thu năm gần nhất') ?> </label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('latest_revenue_vn', [
                                'label' => false,
                                'type' => 'number',
                                'required' => true, 
                                'id' => 'edit-latest-revenue-vn',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'placeholder' => 'VND'
                                ]) ?>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 seperate-from-to"> ～ </div>
                        <div class="col-md-5 col-sm-5 col-xs-12 group-picker">
                            <?= $this->Form->control('latest_revenue_jp', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-latest-revenue-jp',
                                'required' => true, 
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12',
                                'placeholder' => '円'
                                ]) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="staff">
                        <?= __('Số lượng nhân viên') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('staffs_number', [
                            'label' => false, 
                            'type' => 'number',
                            'min' => '0',
                            'id' => 'edit-staffs-number',
                            'required' => true,
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số lượng nhân viên'
                            ]) ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="edu-center-name">
                        <?= __('Trung tâm đào tạo (TTĐT)') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_name_vn', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-edu-name-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên TTĐT bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_name_jp', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-edu-name-jp',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên TTĐT bằng tiếng Nhật'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="edu-center-address">
                        <?= __('Địa chỉ TTĐT') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_address_vn', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-edu-address-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ TTĐT bằng tiếng Việt'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('edu_center_address_en', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-edu-address-en',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập địa chỉ TTĐT bằng tiếng Anh'
                            ]) ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Hoàn Tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>


<div class="modal fade" id="view-ad-company-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÔNG TIN CÔNG TY</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal form-label-left">
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="alias"><?= __('Mã công ty') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-alias"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="alias"><?= __('Tên công ty (viết tắt)') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-short-name"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tên công ty (đầy đủ)') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <span id="view-name-vn"></span></br>
                                <span id="view-name-en"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="address"><?= __('Địa chỉ') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-address-vn"></span></br>
                                <span id="view-address-en"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="branch"><?= __('Chi nhánh') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-branch"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="license"><?= __('Giấy phép kinh doanh') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-license"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="license"><?= __('Ngày nhận giấy phép kinh doanh') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-license-at"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="phone-number"><?= __('Số điện thoại') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-phone-number"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="fax-number"><?= __('Số fax') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-fax-number"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="email"><?= __('Email') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-email"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="deputy"><?= __('Người đại diện') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-deputy-name"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="email"><?= __('Chức vụ (của người đại diện)') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-deputy-role-vn"></span></br>
                                <span id="view-deputy-role-jp"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="signer"><?= __('Người ký hồ sơ') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-signer"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="email"><?= __('Chức vụ (của người ký hồ sơ)') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-signer-role-vn"></span></br>
                                <span id="view-signer-role-jp"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="incorporate-date"><?= __('Ngày thành lập') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-incorporate-date"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="capital"><?= __('Vốn điều lệ') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-capital"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="revenue"><?= __('Doanh thu năm gần nhất') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-revenue"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="staffs-number"><?= __('Số lượng nhân viên') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-staffs-number"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="edu-center-name"><?= __('Trung tâm đào tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <span id="view-edu-name-vn"></span></br>
                                <span id="view-edu-name-jp"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="edu-center-address"><?= __('Địa chỉ TTĐT') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <span id="view-edu-address-vn"></span></br>
                                <span id="view-edu-address-en"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-job-created-by"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-job-created"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group modified">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12 " for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-job-modified-by"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group modified">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <span id="view-job-modified"></span>
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