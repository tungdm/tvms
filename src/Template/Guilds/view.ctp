<?php
use Cake\Core\Configure;

$currentUser = $this->request->session()->read('Auth.User');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');
$installmentStatus = Configure::read('installmentStatus');

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);

$this->assign('title', $guild->name_romaji . ' - Thông tin chi tiết');

?>

<?php $this->start('content-header'); ?>
    <h1><?= __('THÔNG TIN CHI TIẾT') ?></h1>
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
        <li class="active"><?= $guild->name_romaji ?></li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
                <?php if ($guild->del_flag): ?>
                    <li>
                        <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                        ['action' => 'recover', $guild->id], 
                        [
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out',
                            'escape' => false, 
                            'data-toggle' => 'tooltip',
                            'title' => 'Phục hồi',
                            'confirm' => __('Bạn có chắc chắn muốn phục hồi {0}?', $guild->name_romaji)
                        ]) ?>
                    </li>
                <?php else: ?>
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
                        <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                            ['action' => 'edit', $guild->id],
                            [   
                                'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                                'data-toggle' => 'tooltip',
                                'title' => 'Sửa',
                                'escape' => false
                            ]) ?>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>

<div class="form-horizontal form-label-left">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <ul id="guilds-tabs" class="nav nav-tabs bar_tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true"><?= __('Thông tin cơ bản') ?></a>
                </li>
                <li role="presentation" class="">
                    <a href="#tab_content2" role="tab" id="companies-tab" data-toggle="tab" aria-expanded="false"><?= __('Công ty tiếp nhận') ?></a>
                </li>
                <?php if (in_array($role['name'], ['admin', 'accountant'])): ?>
                <li role="presentation" class="">
                    <a href="#tab_content3" role="tab" id="fees-tab" data-toggle="tab" aria-expanded="false"><?= __('Quản lý chi phí') ?></a>
                </li>
                <?php endif; ?>
            </ul>
            <div id="guild-tab-content" class="tab-content">
                <div role="tabpanel" class="tab-pane root-tab-pane fade active in" id="tab_content1">
                    <div class="rows">
                        <div class="col-md-6 col-xs-12 left-col">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Thông tin chi tiết') ?></h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tên nghiệp đoàn') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12 fit-div">
                                                <span id="view-name-romaji"><?= $guild->name_romaji ?></span><br/>
                                                <span id="view-name-kanji"><?= $guild->name_kanji ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="license_num"><?= __('Số giấy phép') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <span id="view-license-number"><?= $guild->license_number ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="signing_date"><?= __('Ngày ký kết') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <span id="view-siging-date"><?= $guild->signing_date ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="subsidy"><?= __('Tiền trợ cấp TTS') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <span id="view-subsidy"><?= number_format($guild->subsidy) ?></span> ¥/tháng
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-three-years-fee"><?= __('Phí quản lý 3 năm đầu') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <span id="view-subsidy"><?= number_format($guild->first_three_years_fee) ?></span> ¥
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="two-years-later-fee"><?= __('Phí quản lý 2 năm sau') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <span id="view-subsidy"><?= number_format($guild->two_years_later_fee) ?></span> ¥
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="pre-training-fee"><?= __('Phí đào tạo trước') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <span id="view-subsidy"><?= number_format($guild->pre_training_fee) ?></span> ¥
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="deputy"><?= __('Người đại diện') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12 fit-div">
                                                <span id="view-deputy-romaji"><?= $guild->deputy_name_romaji ?></span><br/>
                                                <span id="view-deputy-kanji"><?= $guild->deputy_name_kanji ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="address"><?= __('Địa chỉ') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12 fit-div">
                                                <span id="view-address-romaji"><?= $guild->address_romaji ?></span><br/>
                                                <span id="view-address-kanji"><?= $guild->address_kanji ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="phone"><?= __('Số điện thoại') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12 fit-div">
                                                <span id="view-phone-vn">(Việt Nam) <?= $guild->phone_vn ? h($this->Phone->makeEdit($guild->phone_vn)) : '' ?><span><br/>
                                                <span id="view-phone-jp">(Nhật Bản) <?= $guild->phone_jp ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12 right-col">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Thông tin hệ thống') ?></h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created_by"><?= __('Người tạo') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= !empty($guild->created_by_user) ? $guild->created_by_user->fullname : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="created"><?= __('Thời gian khởi tạo') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= h($guild->created) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($guild->modified_by_user)): ?>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified_by"><?= __('Người sửa cuối') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= $guild->modified_by_user->fullname ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-sm-5 col-xs-12" for="modified"><?= __('Thời gian sửa cuối') ?>: </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                                <?= h($guild->modified) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content2">
                    <div class="rows">
                        <div class="col-md-12 col-xs-12 no-padding">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Danh sách công ty') ?></h3>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered custom-table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                                <th scope="col" class="col-md-3"><?= __('Công ty') ?></th>
                                                <th scope="col" class="col-md-5"><?= __('Địa chỉ') ?></th>
                                                <th scope="col" class="col-md-3"><?= __('Người đại diện') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="company-container">
                                            <?php if (empty($guild->companies)): ?>
                                            <tr>
                                                <td colspan="2" class="table-empty"><?= __('Không có dữ liệu') ?></td>
                                            </tr>
                                            <?php else: ?>
                                            <?php $counter = 0; ?>
                                            <?php foreach ($guild->companies as $company): ?>
                                            <?php if (!$company->del_flag || in_array($role['name'], ['admin'])): ?>
                                            <?php $counter++; ?>
                                            <tr class="row-company">
                                                <td class="cell text-center <?= $company->del_flag ? 'deletedRecord' : '' ?>"><?= h($counter) ?></td>
                                                <td class="cell">
                                                    <?= $company->name_romaji ?><br/><?= $company->name_kanji ?>
                                                </td>
                                                <td class="cell"><?= $company->address_romaji ?><br/><?= $company->address_kanji ?></td>
                                                <td class="cell"><?= $company->deputy_name_romaji ?><br/> <?= $company->deputy_name_kanji ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (in_array($role['name'], ['admin', 'accountant'])): ?>
                <div role="tabpanel" class="tab-pane root-tab-pane fade" id="tab_content3">
                    <div class="rows">
                        <div class="col-md-12 col-xs-12 no-padding">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= __('Danh sách đợt thu') ?></h3>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered custom-table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                                <th scope="col"><?= __('Tên đợt thu') ?></th>
                                                <th scope="col"><?= __('Các loại phí') ?></th>
                                                <th scope="col"><?= __('Tổng cộng') ?></th>
                                                <th scope="col"><?= __('Tổng tiền vào tài khoản') ?></th>
                                                <th scope="col"><?= __('Ngày gửi hóa đơn') ?></th>
                                                <th scope="col"><?= __('Ngày nhận tiền') ?></th>
                                                <th scope="col"><?= __('Trạng thái') ?></th>
                                                <th scope="col" class="col-md-3"><?= __('Ghi chú') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="company-container">
                                            <?php if (empty($guild->installment_fees)): ?>
                                            <tr>
                                                <td colspan="9" class="table-empty"><?= __('Không có dữ liệu') ?></td>
                                            </tr>
                                            <?php else: ?>
                                            <?php $counter = 0; ?>
                                            <?php foreach ($guild->installment_fees as $value): ?>
                                            <?php $counter++; ?>
                                            <tr class="row-company">
                                                <td class="cell text-center"><?= h($counter) ?></td>
                                                <td class="cell"><?= $value->installment->name?></td>
                                                <td class="cell">
                                                    <div class="management-fee-txt">- Phí quản lý: <?= number_format($value->management_fee) ?> ¥</div>
                                                    <div class="air-ticket-fee-txt">- Vé máy bay: <?= number_format($value->air_ticket_fee) ?> ¥</div>
                                                    <div class="training-fee-txt">- Phí đào tạo: <?= number_format($value->training_fee) ?> ¥</div>
                                                    <div class="other-fees-txt">- Khoản khác: <?= number_format($value->other_fees) ?> ¥</div>
                                                </td>
                                                <td class="cell">
                                                    <div class="total-jp-txt"><?= number_format($value->total_jp) ?> ¥</div>
                                                </td>
                                                <td class="cell">
                                                    <div class="total-vn-txt"><?= number_format($value->total_vn) ?> ₫</div>
                                                </td>
                                                <td class="cell">
                                                    <?= $value->invoice_date ?>
                                                </td>
                                                <td class="cell">
                                                    <?= $value->receiving_money_date ?>
                                                </td>
                                                <td class="cell">
                                                    <?= $installmentStatus[$value->status] ?>
                                                </td>
                                                <td class="cell">
                                                    <?= nl2br($value->notes) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>