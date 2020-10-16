<?php
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');
$action = $this->request->getParam('action');
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('guild.js', ['block' => 'scriptBottom']);
?>

<?php if ($action === 'add'): ?>
    <?php $this->assign('title', 'Thêm mới nghiệp đoàn'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('THÊM MỚI NGHIỆP ĐOÀN') ?></h1>
        <button class="btn btn-success submit-guild-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách nghiệp đoàn'), [
                    'controller' => 'Guilds',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Thêm mới</li>
        </ol>
    <?php $this->end(); ?>
<?php else: ?>
    <?php $this->assign('title', $guild->name_romaji . ' - Cập nhật nghiệp đoàn'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('CẬP NHẬT NGHIỆP ĐOÀN') ?></h1>
        <button class="btn btn-success submit-guild-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách đợt thu phí'), [
                    'controller' => 'Guilds',
                    'action' => 'index']) ?>
            </li>
            <li class="active"><?= $guild->name_romaji ?></li>
        </ol>
    <?php $this->end(); ?>
<?php endif; ?>


<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($action === 'edit'): ?>
                <?php if ($guild->del_flag): ?>
                <li>
                    <?= $this->Form->postLink(__('<i class="fa fa-undo" aria-hidden="true"></i>'), 
                        ['action' => 'recover', $guild->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Phục hồi',
                            'confirm' => __('Bạn có chắc chắn muốn phục hồi {0}?', $guild->name_romaji)
                        ]) ?>
                </li>
                <?php endif; ?>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                        ['action' => 'view', $guild->id],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Xem chi tiết',
                            'escape' => false
                        ]) ?>
                </li>
                <li>
                    <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                        ['action' => 'delete', $guild->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Xóa',
                            'confirm' => __('Bạn có chắc chắn muốn xóa {0}?', $guild->name_romaji)
                        ]) ?>
                </li>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-plus" aria-hidden="true"></i>'), 
                        ['action' => 'add'],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Thêm mới',
                            'escape' => false
                        ]) ?>
                </li>
            <?php endif; ?>
            <li>
                <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out submit-guild-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>

<?= $this->Form->create($guild, [
    'class' => 'form-horizontal form-label-left', 
    'id' => 'guild-form', 
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>
<?= $this->Form->unlockField('companies') ?>
<?= $this->Form->unlockField('admin_companies') ?>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= __('Thông tin cơ bản') ?>
                </h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-5 col-xs-12" for="name_romaji">
                        <?= __('Tên nghiệp đoàn') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_romaji', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12 autoFocus', 
                            'placeholder' => 'Nhập bằng kí tự romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-3 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('name_kanji', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập bằng kí tự kanji'
                            ]) ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-5 col-xs-12 optional" for="license_num">
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
                    <label class="control-label col-md-3 col-sm-5 col-xs-12" for="deputy_name">
                        <?= __('Người đại diện') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('deputy_name_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-3 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12 optional">
                        <?= $this->Form->control('deputy_name_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-5 col-xs-12" for="address_romaji">
                        <?= __('Địa chỉ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_romaji', ['label' => false, 'required' => true, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự romaji']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-3 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('address_kanji', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Nhập bằng kí tự kanji']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-5 col-xs-12" for="phone_vn">
                        <?= __('Số Điện Thoại') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_vn', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'type' => 'text',
                            'minLength' => 10,
                            'maxlength' => 11,
                            'data-parsley-type' => 'digits',
                            'placeholder' => 'Nhập số điện thoại tại Việt Nam']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-3 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('phone_jp', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập số điện thoại tại Nhật Bản']) ?>
                    </div>
                </div>

                <div class="ln_solid"></div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-5 col-xs-12" for="company"><?= __('Công ty tiếp nhận') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary" onclick="addCompany('add-company-container');">
                            <?= __('Thêm công ty') ?>
                        </button>
                        <table class="table table-bordered custom-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-md-8"><?= __('Công ty') ?></th>
                                    <th scope="col" class="col-md-2"><?= __('Thao tác') ?></th>
                                </tr>
                            </thead>
                            <tbody id="add-company-container" class="company-container">
                                <?php if (!empty($guild->companies)): ?>
                                <?php foreach ($guild->companies as $key => $value): ?>
                                <tr class="row-company">
                                    <td class="cell">
                                        <?= $this->Form->control('companies.' . $key . '.id', [
                                            'options' => $companies,
                                            'required' => true, 
                                            'empty' => true, 
                                            'type' => 'select',
                                            'label' => false,   
                                            'class' => 'companyId form-control col-md-7 col-xs-12',
                                            'data-parsley-not-duplicate-company' => '', 
                                            ]) ?>
                                        <div class="hidden">
                                            <?= $this->Form->control('companies.'.$key.'._joinData.id', [
                                                'label' => false, 
                                                'class' => 'recordId form-control col-md-7 col-xs-12',
                                                ]) ?>
                                        </div>
                                    </td>
                                    <td class="actions cell">
                                        <?= $this->Html->link(
                                            '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                            'javascript:;',
                                            [
                                                'escape' => false, 
                                                'onClick' => "deleteCompany(this, true)"
                                            ]
                                        )?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-5 col-xs-12" for="admin-company"><?= __('Công ty quản lý') ?></label>
                    <div class="col-md-9 col-sm-7 col-xs-12 table-responsive">
                        <button type="button" class="btn btn-primary" onclick="showAddAdminCompany();">
                            <?= __('Thêm công ty quản lý') ?>
                        </button>
                        <table class="table table-bordered custom-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-md-1"><?= __('Công ty') ?></th>
                                    <th scope="col" class="col-md-2"><?= __('Ngày ký kết') ?></th>
                                    <th scope="col" class="col-md-2"><?= __('Tiền trợ cấp TTS') ?></th>
                                    <th scope="col" class="col-md-2"><?= __('Phí quản lý 3 năm đầu') ?></th>
                                    <th scope="col" class="col-md-2"><?= __('Phí quản lý 2 năm sau') ?></th>
                                    <th scope="col" class="col-md-2"><?= __('Phí đào tạo trước') ?></th>
                                    <th scope="col" class="col-md-1"><?= __('Thao tác') ?></th>
                                </tr>
                            </thead>
                            <tbody id="admin-company-container">
                                <?php $counter = 0; ?>
                                <?php if (!empty($guild->admin_companies)): ?>
                                <?php foreach($guild->admin_companies as $key => $value): ?>
                                    <?php 
                                        $subsidy = $value->_joinData->subsidy;
                                        $firstThree = $value->_joinData->first_three_years_fee;
                                        $twoLater = $value->_joinData->two_years_later_fee;
                                        $preTraining = $value->_joinData->pre_training_fee;
                                        $signingDate = $value->_joinData->signing_date;
                                    ?>
                                    <div class="hidden guild-admin-company-id" id="guild-admin-company-id-<?=$counter?>">
                                        <?= $this->Form->hidden("admin_companies.{$key}._joinData.id") ?>
                                    <div>
                                    <tr class="row-admin-company" id="row-admin-company-<?= $counter ?>">
                                        <td class="cell">
                                            <div class="admin-company-txt"><?= $value->alias ?></div>
                                            <div class="hidden">
                                                <?= $this->Form->control("admin_companies.{$key}.id", [
                                                    'type' => 'number',
                                                    'label' => false, 
                                                    'required' => true,
                                                    'class' => 'form-control admin-company',
                                                    ]) ?>
                                            </div>
                                        </td>
                                        <td class="cell">
                                            <div class="signing-date-txt">
                                                <?= isset($signingDate) ? $signingDate : '-' ?>
                                            </div>
                                            <div class="hidden">
                                                <?= $this->Form->control("admin_companies.{$key}._joinData.signing_date", [
                                                    'type' => 'text',
                                                    'label' => false,
                                                    'class' => 'form-control signing-date',
                                                    ])?>
                                            </div>
                                        </td>
                                        <td class="cell">
                                            <div class="subsidy-txt">
                                                <?= isset($subsidy) ? number_format($subsidy) . '¥/tháng' : '-' ?>
                                            </div>
                                            <div class="hidden">
                                                <?= $this->Form->control("admin_companies.{$key}._joinData.subsidy", [
                                                    'type' => 'number',
                                                    'label' => false,
                                                    'class' => 'form-control subsidy',
                                                    ])?>
                                            </div>
                                        </td>
                                        <td class="cell">
                                            <div class="first-three-txt">
                                                <?= isset($firstThree) ? number_format($firstThree) . '¥' : '-' ?>
                                            </div>
                                            <div class="hidden">
                                                <?= $this->Form->control("admin_companies.{$key}._joinData.first_three_years_fee", [
                                                    'type' => 'number',
                                                    'label' => false,
                                                    'class' => 'form-control first-three',
                                                    ])?>
                                            </div>
                                        </td>
                                        <td class="cell">
                                            <div class="two-later-txt">
                                                <?= isset($twoLater) ? number_format($twoLater) . '¥' : '-' ?>
                                            </div>
                                            <div class="hidden">
                                                <?= $this->Form->control("admin_companies.{$key}._joinData.two_years_later_fee", [
                                                    'type' => 'number',
                                                    'label' => false,
                                                    'class' => 'form-control two-later',
                                                    ])?>
                                            </div>
                                        </td>
                                        <td class="cell">
                                            <div class="pre-training-txt">
                                                <?= isset($preTraining) ? number_format($preTraining) . '¥' : '-' ?>
                                            </div>
                                            <div class="hidden">
                                                <?= $this->Form->control("admin_companies.{$key}._joinData.pre_training_fee", [
                                                    'type' => 'number',
                                                    'label' => false,
                                                    'class' => 'form-control pre-training',
                                                    ])?>
                                            </div>
                                        </td>
                                        <td class="cell action-btn actions">
                                            <?= $this->Html->link(
                                                '<i class="fa fa-2x fa-pencil"></i>', 
                                                'javascript:;',
                                                [
                                                    'escape' => false,
                                                    'onClick' => "showEditAdminCompanyModal(this)"
                                                ]) 
                                            ?>
                                            
                                            <?= $this->Html->link(
                                                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                                                'javascript:;',
                                                [
                                                    'escape' => false, 
                                                    'onClick' => 'removeAdminCompany(this, true)'
                                                ]
                                            )?>
                                        </td>
                                    </tr>
                                <?php $counter++; ?>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>


<div id="admin-company-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM CÔNG TY QUẢN LÝ</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'type' => 'post',
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'admin-company-form',
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                <?= $this->Form->hidden('modal.flag', ['id' => 'modal-flag']) ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="adminCompany"><?= __('Công ty') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('modal.admin_company', [
                            'options' => $adminCompanies, 
                            'required' => true, 
                            'empty' => true,
                            'label' => false, 
                            'data-parsley-errors-container' => '#error-admin-company',
                            'data-parsley-class-handler' => '#select2-modal-admin-company',
                            'data-parsley-not-duplicate-admin-company' => '',
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                            ]) ?>
                        <span id="error-admin-company"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="signing-date"><?= __('Ngày ký kết') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="signing-date-div">
                            <?= $this->Form->control('modal.signing_date', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'dd-mm-yyyy',
                                ])?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="subsidy"><?= __('Tiền trợ cấp TTS') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('modal.subsidy_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'modal-subsidy-txt',
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'modal-subsidy', 
                                'placeholder' => 'Nhập tiền trợ cấp thục tập sinh'
                                ]) ?>
                            <?= $this->Form->control('modal.subsidy', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'modal-subsidy',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ¥/tháng</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="first-three-years-fee"><?= __('Phí quản lý 3 năm đầu') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('modal.first_three_years_fee_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'modal-first-three-years-fee-txt',
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'modal-first-three-years-fee', 
                                'placeholder' => 'Nhập phí quản lý 3 năm đầu'
                                ]) ?>
                            <?= $this->Form->control('modal.first_three_years_fee', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'modal-first-three-years-fee',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="two-years-later-fee"><?= __('Phí quản lý 2 năm sau') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('modal.two_years_later_fee_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'modal-two-years-later-fee-txt',
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'modal-two-years-later-fee', 
                                'placeholder' => 'Nhập phí quản lý 2 năm sau'
                                ]) ?>
                            <?= $this->Form->control('modal.two_years_later_fee', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'modal-two-years-later-fee',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="pre-training-fee"><?= __('Phí đào tạo trước') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('modal.pre_training_fee_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'modal-pre-training-fee-txt',
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'modal-pre-training-fee', 
                                'placeholder' => 'Nhập phí đào tạo trước'
                                ]) ?>
                            <?= $this->Form->control('modal.pre_training_fee', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'modal-pre-training-fee',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note"><strong>Lưu ý:</strong> Sau khi hoàn tất, vui lòng nhấn nút "Lưu lại" trên đầu trang để lưu thông tin.</p>
                </div>
            <?= $this->Form->end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submit-admin-company-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<script id="company-template" type="text/x-handlebars-template">
    <tr class="row-company">
        <td class="cell">
            <?= $this->Form->control('companies.{{counter}}.id', [
                'options' => $companies, 
                'required' => true, 
                'empty' => true, 
                'label' => false,
                'class' => 'companyId form-control col-md-7 col-xs-12',
                'data-parsley-not-duplicate-company' => '', 
                ]) ?>
        </td>
        <td class="actions cell">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => "deleteCompany(this, false)"
                ]
            )?>
        </td>
    </tr>
</script>


<script id="admin-company-template" type="text/x-handlebars-template">
    <tr class="row-admin-company" id="row-admin-company-{{counter}}">
        <td class="cell">
            <div class="admin-company-txt">{{adminCompanyAlias}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{adminCompany}}', [
                    'type' => 'number',
                    'label' => false, 
                    'required' => true,
                    'class' => 'form-control admin-company',
                    'value' => '{{adminCompanyId}}'
                    ]) ?>
            </div>
        </td>
        <td class="cell">
            <div class="signing-date-txt">{{signingDateTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{signingDate}}', [
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control signing-date',
                    'value' => '{{signingDateVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            <div class="subsidy-txt">{{subsidyTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{subsidy}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control subsidy',
                    'value' => '{{subsidyVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            <div class="first-three-txt">{{firstThreeTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{firstThree}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control first-three',
                    'value' => '{{firstThreeVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            <div class="two-later-txt">{{twoLaterTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{twoLater}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control two-later',
                    'value' => '{{twoLaterVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell">
            <div class="pre-training-txt">{{preTrainingTxt}}</div>
            <div class="hidden">
                <?= $this->Form->control('{{preTraining}}', [
                    'type' => 'number',
                    'label' => false,
                    'class' => 'form-control pre-training',
                    'value' => '{{preTrainingVal}}'
                    ])?>
            </div>
        </td>
        <td class="cell action-btn actions">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-pencil"></i>', 
                'javascript:;',
                [
                    'escape' => false,
                    'onClick' => "showEditAdminCompanyModal(this)"
                ]) 
            ?>
            
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i>',
                'javascript:;',
                [
                    'escape' => false, 
                    'onClick' => 'removeAdminCompany(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>