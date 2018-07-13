<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[]|\Cake\Collection\CollectionInterface $orders
 */
use Cake\Core\Configure;
use Cake\I18n\Time;

$now = Time::now()->i18nFormat('yyyy-MM-dd');

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller);
$currentUser = $this->request->session()->read('Auth.User');

$recordsDisplay = Configure::read('recordsDisplay');
$cityJP = Configure::read('cityJP');
$cityJP = array_map('array_shift', $cityJP);
$interviewStatus = Configure::read('interviewStatus');

$counter = 0;

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('order.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);
?>

<?php $this->start('content-header'); ?>
<h1><?= __('ĐƠN HÀNG PHỎNG VẤN') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chính',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Đơn Hàng</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH') ?></h3>
                <div class="box-tools pull-right">
                    <?= $this->Html->link('<i class="fa fa-plus"></i>', ['action' => 'add'], ['class' => 'btn btn-box-tool','escape' => false]) ?>
                    <div class="btn-group">
                        <a href="javascript:;" class="btn btn-box-tool dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li>
                                Export File
                                <!-- <?= $this->Html->link('Export Xlsx', [
                                    'action' => 'exportXlsx'
                                ]) ?> -->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Orders', 'action' => 'index'],
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
                            <th scope="col" class="nameCol">
                                <?= $this->Paginator->sort('name', 'Tên đơn hàng')?>
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
                            <td class="col-md-2 nameCol">
                                <div class="input-group date input-picker gt-now" id="interview-date">
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
                                        ])
                                    ?>
                            </td>
                            <td class="filter-group-btn">
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
                                <?php if ($order->company->has('guild')): ?>
                                <?= $this->Html->link(h($order->company->guild->name_romaji), [
                                    'controller' => 'Guilds', 
                                    'action' => 'index', 
                                    '?' => [
                                        'name_romaji' => $order->company->guild->name_romaji
                                    ]],
                                    ['target' => '_blank']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="cell companyIdCol">
                                <?php if ($order->has('company')): ?>
                                <?= $this->Html->link(h($order->company->name_romaji), [
                                    'controller' => 'Companies', 
                                    'action' => 'index', 
                                    '?' => [
                                        'name_romaji' => $order->company->name_romaji
                                    ]],
                                    ['target' => '_blank']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="cell statusCol">
                                <?php if ($now < $order->interview_date): ?>
                                    <?= h($interviewStatus["1"]) ?>
                                <?php elseif ($now == $order->interview_date) :?>
                                    <?= h($interviewStatus["2"]) ?>
                                <?php else: ?>
                                    <?= h($interviewStatus["3"]) ?>
                                <?php endif; ?>
                            </td>
                            <td class="actions cell">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <?php if ($permission == 0 || $currentUser['role']['name'] == 'admin'): ?>
                                        <li>
                                            <?= $this->Html->link(__('Cập nhật'), ['action' => 'edit', $order->id]) ?>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink(__('Xóa'), 
                                            ['action' => 'delete', $order->id], 
                                            [
                                                'escape' => false, 
                                                'confirm' => __('Bạn có chắc chắn muốn xóa đơn hàng {0} này?', $order->name)
                                            ]) ?>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <?= $this->Html->link(__('Đóng'), ['action' => 'close', $order->id]) ?>
                                        </li>
                                        <?php else: ?>
                                        <li>
                                            <?= $this->Html->link(__('Chi tiết'), ['action' => 'view', $order->id]) ?>
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