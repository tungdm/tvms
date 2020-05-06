<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[]|\Cake\Collection\CollectionInterface $orders
 */
use Cake\Core\Configure;
use Cake\I18n\Time;

$now = Time::now()->i18nFormat('yyyy-MM-dd');

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$currentUser = $this->request->session()->read('Auth.User');

$recordsDisplay = Configure::read('recordsDisplay');
$cityJP = Configure::read('cityJP');
$cityJP = array_map('array_shift', $cityJP);
$interviewStatus = Configure::read('interviewStatus');
$settings = Configure::read('orders');
$cellWidth = Configure::read('cellWidth');
$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('order.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý đơn hàng');
?>

<?php $this->start('content-header'); ?>
<h1><?= __('QUẢN LÝ ĐƠN HÀNG') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Danh sách đơn hàng</li>
</ol>
<?php $this->end(); ?>

<?php $this->start('floating-button'); ?>
    <div class="zoom" id="draggable-button">
        <a class="zoom-fab zoom-btn-large" id="zoomBtn"><i class="fa fa-bars"></i></a>
        <ul class="zoom-menu">
            <?php if ($permission == 0): ?>
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
                'url' => ['controller' => 'Orders', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-order-overlay">
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
                                <?= $this->Paginator->sort('name', 'Đơn hàng')?>
                            </th>
                            <th scope="col" class="interviewDateCol">
                                <?= $this->Paginator->sort('interview_date', 'Ngày tuyển') ?>
                            </th>
                            <th scope="col" class="adCompCol">
                                <?= __('Phân nhánh') ?>
                            </th>
                            <th scope="col" class="guildIdCol">
                                <?= __('Nghiệp đoàn') ?>
                            </th>
                            <th scope="col" class="departureCol">
                                <?= __('Dự kiến xuất cảnh') ?>
                            </th>
                            <th scope="col" class="statusCol">
                                <?= __('Trạng thái') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="col-md-2 nameCol">
                                <?= $this->Form->control('name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-1 interviewDateCol"  style="width: 12.499999995%;">
                                <div class="input-group date input-picker" id="interview-date">
                                    <?= $this->Form->control('interview_date', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'dd-mm-yyyy',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['interview_date'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="col-md-2 adCompCol">
                                <?= $this->Form->control('ad_comp_id', [
                                        'options' => $adminCompanies, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['ad_comp_id'] ?? ''
                                        ])
                                    ?>
                            </td>
                            <td class="col-md-1 guildIdCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('guild_id', [
                                        'options' => $guilds,
                                        'empty' => true,
                                        'label' => false,
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['guild_id'] ?? ''
                                        ])
                                    ?>
                            </td>
                            <td class="col-md-1 departureCol" style="width: 12.499999995%;">
                                <div class="input-group date input-picker month-mode" id="departure-month">
                                    <?= $this->Form->control('departure_month', [
                                        'type' => 'text',
                                        'label' => false, 
                                        'class' => 'form-control',
                                        'placeholder' => 'mm-yyyy',
                                        'value' => $query['departure_month'] ?? ''
                                        ])?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="col-md-1 statusCol">
                                <?= $this->Form->control('status', [
                                    'options' => $interviewStatus, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['status'] ?? ''
                                    ]) ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($orders)->isEmpty()): ?>
                            <tr>
                                <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <?php $counter++ ?>
                                <tr>
                                    <td class="cell text-center <?= $order->del_flag ? 'deletedRecord' : '' ?>"><?= $counter ?></td>
                                    <td class="cell nameCol">
                                        <?= $this->Html->link(h($order->name), 
                                            ['action' => 'view', $order->id],
                                            ['escape' => false]) ?>
                                    </td>
                                    <td class="cell interviewDateCol"><?= h($order->interview_date->i18nFormat('dd-MM-yyyy')) ?></td>
                                    <td class="cell adCompCol"><?= h($order->admin_company->alias) ?></td>
                                    <td class="cell guildIdCol">
                                        <?php if ($order->has('guild')): ?>
                                            <a href="javascript:;" onclick="viewGuild(<?= $order->guild_id ?>)"><?= h($order->guild->name_romaji) ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell companyIdCol">
                                        <?php 
                                            $departureMonth = $order->departure_date ? new Time($order->departure_date . '-01') : ''; 
                                        ?>
                                        <?= $departureMonth ? h($departureMonth->i18nFormat('MM-yyyy')) : '' ?>
                                    </td>
                                    <td class="cell statusCol">
                                        <?php 
                                        $interview_date = $order->interview_date->i18nFormat('yyyy-MM-dd');

                                        if ($order->status == "4" || $order->status == "5") {
                                            $status = (int) $order->status;
                                            echo h($interviewStatus[$order->status]);
                                        } elseif ($now < $interview_date) {
                                            $status = 1;
                                            echo h($interviewStatus["1"]);
                                        } elseif ($now == $interview_date) {
                                            $status = 2;
                                            echo h($interviewStatus["2"]);
                                        } else {
                                            $status = 3;
                                            echo h($interviewStatus["3"]);
                                        } ?>
                                    </td>
                                    <td class="actions cell">
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li>
                                                    <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                        ['action' => 'view', $order->id],
                                                        ['escape' => false]) ?>
                                                </li>
                                                <?php if ($permission == 0): ?>
                                                    <?php if ($order->del_flag): ?>
                                                        <li>
                                                            <?= $this->Form->postLink('<i class="fa fa-undo" aria-hidden="true"></i> Phục hồi', 
                                                            ['action' => 'recover', $order->id], 
                                                            [
                                                                'escape' => false, 
                                                                'confirm' => __('Bạn có chắc chắn muốn phục hồi đơn hàng {0}?', $order->name)
                                                            ]) ?>
                                                        </li>
                                                    <?php else: ?>
                                                        <?php if ($status != 5 || $currentUser['role_id']==1): ?>
                                                            <li>
                                                                <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Sửa'), 
                                                                    ['action' => 'edit', $order->id],
                                                                    ['escape' => false]) ?>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if ($status == 4): ?>
                                                            <?php if (!empty($order->departure)): ?>
                                                                <li>
                                                                    <?= $this->Form->postLink(__('<i class="fa fa-plane" aria-hidden="true"></i> Xuất cảnh'), 
                                                                        ['action' => 'close', $order->id],
                                                                        [
                                                                            'escape' => false,
                                                                            'confirm' => __('Bạn có chắc chắn muốn chuyển đơn hàng {0} sang xuất cảnh?', $order->name)
                                                                        ]) ?>
                                                                </li>
                                                            <?php else: ?>
                                                                <li>
                                                                    <?= $this->Html->link(__('<i class="fa fa-plane" aria-hidden="true"></i> Xuất cảnh'), 
                                                                        ['action' => 'edit', $order->id],
                                                                        [
                                                                            'escape' => false,
                                                                            'confirm' => __('Đơn hàng {0} chưa có ngày bay chính thức. Bạn có muốn bổ sung thông tin?', $order->name)
                                                                        ]) ?>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <li>
                                                            <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i> Xóa'), 
                                                                ['action' => 'delete', $order->id], 
                                                                [
                                                                    'escape' => false, 
                                                                    'confirm' => __('Bạn có chắc chắn muốn xóa đơn hàng {0}?', $order->order)
                                                                ]) ?>
                                                        </li>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (!$order->del_flag): ?>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <?= $this->Html->link(__('<i class="fa fa-calendar" aria-hidden="true"></i> Khóa học'), 
                                                            ['action' => 'schedule', $order->id],
                                                            ['escape' => false]) ?>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" onclick="showExportModal(<?= $order->id ?>)">
                                                            <i class="fa fa-book" aria-hidden="true"></i> Xuất hồ sơ
                                                        </a>
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
                        <?= $this->Paginator->next(__('Trang sau') . ' >') ?>
                        <?= $this->Paginator->last(__('Trang cuối') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Trang thứ {{page}} trên tổng {{pages}} trang, {{current}} trên tổng số {{count}} bản ghi')]) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="export-order-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">DANH SÁCH HỒ SƠ</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12 table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-5"><?= __('Tên tài liệu') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Loại tài liệu') ?></th>
                                <th scope="col" class="actions col-md-3"><?= __('Thao tác') ?></th>
                            </tr>
                        </thead>
                        <tbody id="export-container"></tbody>
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

<script id="export-template" type="text/x-handlebars-template">
    <tr>
        <td class="cell text-center"><?= __('1') ?></td>
        <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./orders/export-dispatch-letter/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('2') ?></td>
        <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
        <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
        <td class="actions cell">
            <a href="./orders/export-dispatch-letter-xlsx/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('3') ?></td>
        <td class="cell"><?= __('Danh sách ứng viên phỏng vấn') ?></td>
        <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
        <td class="actions cell">
            <a href="./orders/export-candidates/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('4') ?></td>
        <td class="cell"><?= __('Bìa hồ sơ phỏng vấn') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./orders/export-cover/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('5') ?></td>
        <td class="cell"><?= __('1.13') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./students/export-company-commitment/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('6') ?></td>
        <td class="cell"><?= __('1.20') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./orders/export-declaration/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('7') ?></td>
        <td class="cell"><?= __('1.28') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./orders/export-certificate/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('8') ?></td>
        <td class="cell"><?= __('Điểm kiểm tra IQ') ?></td>
        <td class="cell text-center"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
        <td class="actions cell">
            <a href="./orders/export-iq-test/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell text-center"><?= __('9') ?></td>
        <td class="cell"><?= __('Thông tin đơn hàng') ?></td>
        <td class="cell text-center"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./orders/export-summary/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
</script>
