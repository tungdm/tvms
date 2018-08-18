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
                            <th scope="col" class="workAtCol">
                                <?= __('Nơi làm việc') ?>
                            </th>
                            <th scope="col" class="guildIdCol">
                                <?= __('Nghiệp đoàn') ?>
                            </th>
                            <th scope="col" class="companyIdCol">
                                <?= __('Công ty tiếp nhận') ?>
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
                            <td class="col-md-2 interviewDateCol">
                                <div class="input-group date input-picker" id="interview-date">
                                    <?= $this->Form->control('interview_date', [
                                        'type' => 'text',
                                        'label' => false,
                                        'placeholder' => 'yyyy-mm-dd',
                                        'class' => 'form-control col-md-7 col-xs-12',
                                        'value' => $query['interview_date'] ?? ''
                                        ]) 
                                    ?>
                                    <span class="input-group-addon" style="line-height: 1;">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="col-md-1 workAtCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('work_at', [
                                        'options' => $cityJP, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['work_at'] ?? ''
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
                            <td class="col-md-1 companyIdCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('company_id', [
                                        'options' => $companies, 
                                        'empty' => true,
                                        'label' => false, 
                                        'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                        'value' => $query['company_id'] ?? ''
                                        ])
                                    ?>
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
                            <td class="cell"><?= $counter ?></td>
                            <td class="cell nameCol"><?= h($order->name) ?></td>
                            <td class="cell interviewDateCol"><?= h($order->interview_date) ?></td>
                            <td class="cell workAtCol"><?= h($cityJP[$order->work_at]) ?></td>
                            <td class="cell guildIdCol">
                                <?php if (!empty($order->company->guild)): ?>
                                <a href="javascript:;" onclick="viewGuild(<?= $order->company->guild->id ?>)"><?= h($order->company->guild->name_romaji) ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="cell companyIdCol">
                                <?php if ($order->has('company')): ?>
                                <a href="javascript:;" onclick="viewCompany(<?= $order->company->id ?>)"><?= h($order->company->name_romaji) ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="cell statusCol">
                                <?php if ($order->status == "4" || $order->status == "5") {
                                    $status = (int) $order->status;
                                    echo h($interviewStatus[$order->status]);
                                } elseif ($now < $order->interview_date) {
                                    $status = 1;
                                    echo h($interviewStatus["1"]);
                                } elseif ($now == $order->interview_date) {
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
                                        <?php if ($status != 5): ?>
                                        <li>
                                            <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Sửa'), 
                                                ['action' => 'edit', $order->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <?php endif; ?>
                                        <?php if ($status == 4): ?>
                                        <li>
                                            <?= $this->Form->postLink(__('<i class="fa fa-lock" aria-hidden="true"></i> Đóng'), 
                                                ['action' => 'close', $order->id],
                                                [
                                                    'escape' => false,
                                                    'confirm' => __('Bạn có chắc chắn muốn đóng đơn hàng {0}?', $order->name)
                                                ]) ?>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <?= $this->Form->postLink(__('<i class="fa fa-trash" aria-hidden="true"></i> Xóa'), 
                                                ['action' => 'delete', $order->id], 
                                                [
                                                    'escape' => false, 
                                                    'confirm' => __('Bạn có chắc chắn muốn xóa đơn hàng {0}?', $order->name)
                                                ]) ?>
                                        </li>
                                        <?php endif; ?>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="javascript:;" data-target="#export-order-modal" onclick="showExportModal(<?= $order->id ?>)">
                                                <i class="fa fa-book" aria-hidden="true"></i> Xuất hồ sơ
                                            </a>
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
        <td class="cell"><?= __('1') ?></td>
        <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
        <td class="cell"><i class="fa fa-file-word-o" aria-hidden="true"></i> MS Word</td>
        <td class="actions cell">
            <a href="./orders/export-dispatch-letter/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell"><?= __('2') ?></td>
        <td class="cell"><?= __('Mẫu đề nghị cấp thư phái cử') ?></td>
        <td class="cell"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
        <td class="actions cell">
            <a href="./orders/export-dispatch-letter-xlsx/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
    <tr>
        <td class="cell"><?= __('3') ?></td>
        <td class="cell"><?= __('Danh sách ứng viên phỏng vấn') ?></td>
        <td class="cell"><i class="fa fa-file-excel-o" aria-hidden="true"></i> MS Excel</td>
        <td class="actions cell">
            <a href="./orders/export-candidates/{{orderId}}"><i class="fa fa-cloud-download" aria-hidden="true"></i> Tải về</a>
        </td>
    </tr>
</script>