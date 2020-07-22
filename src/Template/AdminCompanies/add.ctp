<?php
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');
$action = $this->request->getParam('action');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('adminCompany.js', ['block' => 'scriptBottom']);
?>

<?php if ($action === 'add'): ?>
    <?php $this->assign('title', 'Thêm mới công ty'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('THÊM MỚI CÔNG TY') ?></h1>
        <button class="btn btn-success submit-admin-company-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách công ty'), [
                    'controller' => 'AdminCompanies',
                    'action' => 'index']) ?>
            </li>
            <li class="active">Thêm mới</li>
        </ol>
    <?php $this->end(); ?>
<?php else: ?>
    <?php $this->assign('title', $adminCompany->alias . ' - Cập nhật công ty'); ?>
    <?php $this->start('content-header'); ?>
        <h1><?= __('CẬP NHẬT CÔNG TY') ?></h1>
        <button class="btn btn-success submit-admin-company-btn" type="button">Lưu lại</button>
        <ol class="breadcrumb">
            <li>
                <?= $this->Html->link(
                    '<i class="fa fa-home"></i> Trang Chủ',
                    '/',
                    ['escape' => false]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Danh sách công ty'), [
                    'controller' => 'AdminCompanies',
                    'action' => 'index']) ?>
            </li>
            <li class="active"><?= $adminCompany->alias ?></li>
        </ol>
    <?php $this->end(); ?>
<?php endif; ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($action === 'edit'): ?>
                <?php if ($adminCompany->del_flag): ?>
                <li>
                    <?= $this->Form->postLink(__('<i class="fa fa-undo" aria-hidden="true"></i>'), 
                        ['action' => 'recover', $adminCompany->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Phục hồi',
                            'confirm' => __('Bạn có chắc chắn muốn phục hồi {0}?', $adminCompany->alias)
                        ]) ?>
                </li>
                <?php endif; ?>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-info" aria-hidden="true"></i>'), 
                        ['action' => 'view', $adminCompany->id],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-info scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Xem chi tiết',
                            'escape' => false
                        ]) ?>
                </li>
                <li>
                    <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i>'), 
                        ['action' => 'delete', $adminCompany->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-delete scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Xóa',
                            'confirm' => __('Bạn có chắc chắn muốn xóa {0}?', $adminCompany->alias)
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
                <a class="zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out submit-admin-company-btn" data-toggle="tooltip" title="Lưu lại">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php $this->end(); ?>


<?= $this->Form->create($adminCompany, [
    'class' => 'form-horizontal form-label-left', 
    'id' => 'admin-company-form', 
    'data-parsley-validate' => '',
    'templates' => [
        'inputContainer' => '{{content}}'
        ]
    ]) ?>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Thông tin cơ bản') ?></h3>
            </div>
            <div class="box-body">
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
                    <div class="col-md-7 col-sm-6 col-xs-12">
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
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="incorporate-date"><?= __('Ngày thành lập') ?></label>
                    <div class="col-md-7 col-sm-12 col-xs-12">
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
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('capital_vn_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'edit-capital-vn-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'edit-capital-vn', 
                                'placeholder' => '₫',
                                'value' => $adminCompany->capital_vn ? number_format($adminCompany->capital_vn): ''
                                ]) ?>
                            <?= $this->Form->control('capital_vn', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-capital-vn',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ₫</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('capital_jp_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'edit-capital-jp-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'edit-capital-jp', 
                                'placeholder' => '¥',
                                'value' => $adminCompany->capital_jp ? number_format($adminCompany->capital_jp): ''
                                ]) ?>
                            <?= $this->Form->control('capital_jp', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-capital-jp',
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
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="revenue_vn">
                        <?= __('Doanh thu năm gần nhất') ?> </label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('latest_revenue_vn_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'edit-capital-jp-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'edit-latest-revenue-vn', 
                                'placeholder' => '₫',
                                'value' => $adminCompany->latest_revenue_vn ? number_format($adminCompany->latest_revenue_vn): ''
                                ]) ?>
                            
                            <?= $this->Form->control('latest_revenue_vn', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-latest-revenue-vn',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ₫</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <div class="col-md-8" style="padding-left: 0px">
                            <?= $this->Form->control('latest_revenue_jp_txt', [
                                'label' => false,
                                'type' => 'text',
                                'id' => 'edit-capital-jp-txt',
                                'required' => true, 
                                'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                'alias' => 'edit-latest-revenue-jp', 
                                'placeholder' => '¥',
                                'value' => $adminCompany->latest_revenue_jp ? number_format($adminCompany->latest_revenue_jp): ''
                                ]) ?>
                            <?= $this->Form->control('latest_revenue_jp', [
                                'label' => false,
                                'type' => 'number',
                                'id' => 'edit-latest-revenue-jp',
                                'min' => '0',
                                'class' => 'form-control col-md-7 col-xs-12 hidden',
                                'placeholder' => '¥'
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-control form-control-view">đơn vị: ¥</div>
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
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Người ký hồ sơ') ?></h3>
            </div>
            <div class="box-body">
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
                        <?= __('Chức vụ') ?> </label>
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
                        <?= __('Chức vụ') ?> </label>
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
                <div class="ln_solid"></div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="dolab">
                        <?= __('Người cục QLLĐ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('dolab_name', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-dolab-name',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập tên người cục QLLĐ'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-5 col-xs-12" for="dolab-role">
                        <?= __('Chức vụ') ?> </label>
                    <div class="col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('dolab_role_vn', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-dolab-role-vn',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người cục QLLĐ bằng romaji'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                        <?= $this->Form->control('dolab_role_jp', [
                            'label' => false, 
                            'required' => true,
                            'id' => 'edit-dolab-role-jp',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'placeholder' => 'Nhập chức vụ của người cục QLLĐ bằng kanji'
                            ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Trung tâm đào tạo') ?></h3>
            </div>
            <div class="box-body">
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
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Danh sách chi phí') ?></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered custom-table fees-table">
                    <thead>
                        <tr>
                            <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                            <th scope="col" class="col-md-3"><?= __('Loại phí') ?></th>
                            <th scope="col" class="col-md-4"><?= __('Số tiền (₫)') ?></th>
                            <th scope="col" class="col-md-4"><?= __('Số tiền (¥)') ?></th>
                        </tr>
                    </thead>
                    <tbody id="candidate-container">
                        <tr>
                            <td class="stt-col text-center">1</td>
                            <td>Học phí đào tạo tiếng Nhật dành cho thực tập sinh kĩ năng (đào tạo cơ bản) - 1 tháng</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('basic_training_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'basic-training-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->basic_training_fee_vn ? number_format($adminCompany->basic_training_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('basic_training_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('basic_training_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'basic-training-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->basic_training_fee_jp ? number_format($adminCompany->basic_training_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('basic_training_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">2</td>
                            <td>Học phí đào tạo tiếng Nhật dành cho thực tập sinh kĩ năng - 3 tháng</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('training_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'training-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->training_fee_vn ? number_format($adminCompany->training_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('training_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('training_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'training-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->training_fee_jp ? number_format($adminCompany->training_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('training_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">3</td>
                            <td>Giáo dục định hướng</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('oriented_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'oriented-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->oriented_fee_vn ? number_format($adminCompany->oriented_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('oriented_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('oriented_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'oriented-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->oriented_fee_jp ? number_format($adminCompany->oriented_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('oriented_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">4</td>
                            <td>Phí tài liệu</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('documents_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'documents-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->documents_fee_vn ? number_format($adminCompany->documents_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('documents_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('documents_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'documents-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->documents_fee_jp ? number_format($adminCompany->documents_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('documents_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">5</td>
                            <td>Khám sức khỏe lần 1</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('health_test_fee_1_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'health-test-fee-1-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->health_test_fee_1_vn ? number_format($adminCompany->health_test_fee_1_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('health_test_fee_1_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('health_test_fee_1_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'health-test-fee-1-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->health_test_fee_1_jp ? number_format($adminCompany->health_test_fee_1_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('health_test_fee_1_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">6</td>
                            <td>Khám sức khỏe lần 2</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('health_test_fee_2_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'health-test-fee-2-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->health_test_fee_2_vn ? number_format($adminCompany->health_test_fee_2_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('health_test_fee_2_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('health_test_fee_2_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'health-test-fee-2-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->health_test_fee_2_jp ? number_format($adminCompany->health_test_fee_2_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('health_test_fee_2_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">7</td>
                            <td>Phí dịch vụ phái cử</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('dispatch_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'dispatch-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->dispatch_fee_vn ? number_format($adminCompany->dispatch_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('dispatch_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('dispatch_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'dispatch-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->dispatch_fee_jp ? number_format($adminCompany->dispatch_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('dispatch_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">8</td>
                            <td>Phí ký túc xá – 4 tháng</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('accommodation_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'accommodation-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->accommodation_fee_vn ? number_format($adminCompany->accommodation_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('accommodation_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('accommodation_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'accommodation-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->accommodation_fee_jp ? number_format($adminCompany->accommodation_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('accommodation_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">9</td>
                            <td>Phí đăng ký xin visa</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('visa_fee_1_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'visa-fee-1-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->visa_fee_1_vn ? number_format($adminCompany->visa_fee_1_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('visa_fee_1_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('visa_fee_1_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'visa-fee-1-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->visa_fee_1_jp ? number_format($adminCompany->visa_fee_1_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('visa_fee_1_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">10</td>
                            <td>Phí làm thủ tục cấp visa</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('visa_fee_2_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'visa-fee-2-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->visa_fee_2_vn ? number_format($adminCompany->visa_fee_2_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('visa_fee_2_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('visa_fee_2_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'visa-fee-2-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->visa_fee_2_jp ? number_format($adminCompany->visa_fee_2_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('visa_fee_2_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">11</td>
                            <td>Phí đóng góp quỹ hỗ trợ việc làm ngoài nước</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('foes_fee_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'foes-fee-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->foes_fee_vn ? number_format($adminCompany->foes_fee_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('foes_fee_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('foes_fee_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'foes-fee-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->foes_fee_jp ? number_format($adminCompany->foes_fee_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('foes_fee_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="stt-col text-center">12</td>
                            <td>Các chi phí khác (đồng phục, vali, đưa ra sân bay…)</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('other_fees_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'other-fees-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->other_fees_vn ? number_format($adminCompany->other_fees_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('other_fees_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('other_fees_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'other-fees-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->other_fees_jp ? number_format($adminCompany->other_fees_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('other_fees_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right;">Tổng cộng</td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('total_fees_vn_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'total-fees-vn', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->total_fees_vn ? number_format($adminCompany->total_fees_vn): ''
                                        ]) ?>
                                    <?= $this->Form->control('total_fees_vn', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <?= $this->Form->control('total_fees_jp_txt', [
                                        'label' => false,
                                        'type' => 'text',
                                        'required' => true, 
                                        'class' => 'form-control col-md-7 col-xs-12 textToNumber',
                                        'alias' => 'total-fees-jp', 
                                        'placeholder' => '₫',
                                        'value' => $adminCompany->total_fees_jp ? number_format($adminCompany->total_fees_jp): ''
                                        ]) ?>
                                    <?= $this->Form->control('total_fees_jp', [
                                        'label' => false,
                                        'type' => 'number',
                                        'min' => '0',
                                        'class' => 'form-control col-md-7 col-xs-12 hidden', 
                                        ]) ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>
<?= $this->Form->end() ?>
