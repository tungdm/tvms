<?php
$currentUser = $this->request->session()->read('Auth.User');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$role = $this->request->session()->read('Auth.User.role');

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);

$this->assign('title', $adminCompany->alias . ' - Thông tin chi tiết');
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
            <?= $this->Html->link(__('Danh sách công ty'), [
                'controller' => 'AdminCompanies',
                'action' => 'index']) ?>
        </li>
        <li class="active"><?= $adminCompany->alias ?></li>
    </ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($adminCompany->del_flag): ?>
                <li>
                    <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                    ['action' => 'recover', $adminCompany->id], 
                    [
                        'class' => 'zoom-fab zoom-btn-sm zoom-btn-save scale-transition scale-out',
                        'escape' => false, 
                        'data-toggle' => 'tooltip',
                        'title' => 'Phục hồi',
                        'confirm' => __('Bạn có chắc chắn muốn phục hồi {0}?', $adminCompany->alias)
                    ]) ?>
                </li>
            <?php else: ?>
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
                    <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i>'), 
                        ['action' => 'edit', $adminCompany->id],
                        [   
                            'class' => 'zoom-fab zoom-btn-sm zoom-btn-edit scale-transition scale-out',
                            'data-toggle' => 'tooltip',
                            'title' => 'Sửa',
                            'escape' => false
                        ]) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php $this->end(); ?>


<div class="form-horizontal form-label-left">
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
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->alias ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="short-name">
                            <?= __('Tên công ty (viết tắt)') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->short_name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="company-name">
                            <?= __('Tên công ty (đầy đủ)') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->name_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->name_en ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="address">
                            <?= __('Địa chỉ công ty') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->address_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->address_en ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="license">
                            <?= __('Giáy phép kinh doanh') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->license ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="license-at"><?= __('Ngày nhận giấy phép') ?></label>
                        <div class="col-md-7 col-sm-6 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->license_at ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="phone-number">
                            <?= __('Số điện thoại') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->phone_number ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="fax-number">
                            <?= __('Số fax') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->fax_number ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="email">
                            <?= __('Email') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <a href="mailto:<?=$adminCompany->email?>"><?= $adminCompany->email ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="incorporate-date"><?= __('Ngày thành lập') ?></label>
                        <div class="col-md-7 col-sm-12 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->incorporation_date ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="capital_vn">
                            <?= __('Vốn điều lệ') ?> </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= number_format($adminCompany->capital_vn) ?> ₫
                                (<?= number_format($adminCompany->capital_jp) ?> ¥)
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="revenue_vn">
                            <?= __('Doanh thu năm gần nhất') ?> </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= number_format($adminCompany->latest_revenue_vn) ?> ₫
                                (<?= number_format($adminCompany->latest_revenue_jp) ?> ¥)
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="staff">
                            <?= __('Số lượng nhân viên') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->staffs_number ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12 optional" for="branch">
                            <?= __('Chi nhánh') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->branch_vn ?? 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->branch_jp ?? 'N/A' ?></div>
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
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->signer_name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="signer-role">
                            <?= __('Chức vụ') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->signer_role_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->signer_role_jp ?></div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy-name">
                            <?= __('Người đại diện') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->deputy_name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="deputy-role">
                            <?= __('Chức vụ') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->deputy_role_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->deputy_role_jp ?></div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="dolab">
                            <?= __('Người cục QLLĐ') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->dolab_name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="dolab-role">
                            <?= __('Chức vụ') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->dolab_role_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->dolab_role_jp ?></div>
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
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->edu_center_name_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->edu_center_name_jp ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-5 col-xs-12" for="edu-center-address">
                            <?= __('Địa chỉ TTĐT') ?> </label>
                        <div class="col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->edu_center_address_vn ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 cold-sm-offset-5 col-md-7 col-sm-5 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $adminCompany->edu_center_address_en ?></div>
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
                                    <?= number_format($adminCompany->basic_training_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->basic_training_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">2</td>
                                <td>Học phí đào tạo tiếng Nhật dành cho thực tập sinh kĩ năng - 3 tháng</td>
                                <td>
                                    <?= number_format($adminCompany->training_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->training_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">3</td>
                                <td>Giáo dục định hướng</td>
                                <td>
                                    <?= number_format($adminCompany->oriented_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->oriented_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">4</td>
                                <td>Phí tài liệu</td>
                                <td>
                                    <?= number_format($adminCompany->documents_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->documents_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">5</td>
                                <td>Khám sức khỏe lần 1</td>
                                <td>
                                    <?= number_format($adminCompany->health_test_fee_1_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->health_test_fee_1_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">6</td>
                                <td>Khám sức khỏe lần 2</td>
                                <td>
                                    <?= number_format($adminCompany->health_test_fee_2_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->health_test_fee_2_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">7</td>
                                <td>Phí dịch vụ phái cử</td>
                                <td>
                                    <?= number_format($adminCompany->dispatch_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->dispatch_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">8</td>
                                <td>Phí ký túc xá – 4 tháng</td>
                                <td>
                                    <?= number_format($adminCompany->accommodation_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->accommodation_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">9</td>
                                <td>Phí đăng ký xin visa</td>
                                <td>
                                    <?= number_format($adminCompany->visa_fee_1_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->visa_fee_1_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">10</td>
                                <td>Phí làm thủ tục cấp visa</td>
                                <td>
                                    <?= number_format($adminCompany->visa_fee_2_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->visa_fee_2_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">11</td>
                                <td>Phí đóng góp quỹ hỗ trợ việc làm ngoài nước</td>
                                <td>
                                    <?= number_format($adminCompany->foes_fee_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->foes_fee_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stt-col text-center">12</td>
                                <td>Các chi phí khác (đồng phục, vali, đưa ra sân bay…)</td>
                                <td>
                                    <?= number_format($adminCompany->other_fees_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->other_fees_jp) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right;">Tổng cộng</td>
                                <td>
                                    <?= number_format($adminCompany->total_fees_vn) ?>
                                </td>
                                <td>
                                    <?= number_format($adminCompany->total_fees_jp) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>