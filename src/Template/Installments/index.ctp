<?php
use Cake\Core\Configure;
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;
$recordsDisplay = Configure::read('recordsDisplay');
$currentUser = $this->request->session()->read('Auth.User');
$counter = 0;
if (!empty($query['page'])) {
    $counter = ((int)$query['page'] -1) * $query['records'];
}
$this->Html->css('flag-icon.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('installments.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Phí quản lý nghiệp đoàn');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<?php $this->start('content-header'); ?>
    <h1><?= __('PHÍ QUẢN LÝ NGHIỆP ĐOÀN') ?></h1>
    <ol class="breadcrumb">
        <li>
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
        <li class="active">Danh sách đợt thu phí</li>
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

<?php if (!empty($report)): ?>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">BIỂU ĐỒ CHI PHÍ 2 NĂM GẦN NHẤT</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <canvas id="installments-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH ĐỢT THU PHÍ') ?></h3>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Installments', 'action' => 'index'],
                'type' => 'get',
                'id' => 'filter-form'
                ]) ?>
            <div class="box-body table-responsive">
                <div class="overlay hidden" id="list-installments-overlay">
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
                                <?= $this->Paginator->sort('name', 'Đợt thu')?>
                            </th>
                            <th scope="col" class="adminCompanyCol">
                                <?= __('Phân nhánh') ?>
                            </th>
                            <th scope="col" class="createdByCol">
                                <?= __('Người tạo') ?>
                            </th>
                            <th scope="col" class="modifiedByCol">
                                <?= __('Người sửa cuối') ?>
                            </th>
                            <th scope="col" class="actions"><?= __('Thao tác') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-1"></td>
                            <td class="col-md-2 nameCol">
                                <?= $this->Form->control('name', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['name'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 adminCompanyCol">
                                <?= $this->Form->control('f_admin_company', [
                                    'options' => $adminCompanies, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_admin_company'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-3 createdByCol">
                                <?= $this->Form->control('f_created_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_created_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-3 modifiedByCol">
                                <?= $this->Form->control('f_modified_by', [
                                    'options' => $allUsers, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['f_modified_by'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 filter-group-btn">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                            </td>
                        <?= $this->Form->end() ?>
                        </tr>
                        <?php if (($installments)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Không có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($installments as $installment): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell text-center"><?= h($counter) ?></td>
                            <td class="cell nameCol">
                                <?= $this->Html->link(h($installment->name), 
                                    ['action' => 'view', $installment->id],
                                    ['escape' => false]) ?>
                            </td>
                            <td class="cell adminCompanyCol">
                                <?= !empty($installment->admin_company) ? h($installment->admin_company->alias) : '' ?>
                            </td>
                            <td class="cell createdByCol">
                                <?= !empty($installment->created_by_user) ? h($installment->created_by_user->fullname) : '' ?>
                            </td>
                            <td class="cell modifiedByCol">
                                <?= !empty($installment->modified_by_user) ? h($installment->modified_by_user->fullname) : '' ?>
                            </td>
                            
                            <td class="actions cell">                              
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-book" aria-hidden="true"></i> Xuất hồ sơ', 
                                                ['action' => 'export', $installment->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                ['action' => 'view', $installment->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Sửa'), 
                                                ['action' => 'edit', $installment->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                            ['action' => 'delete', $installment->id], 
                                            [
                                                'escape' => false, 
                                                'confirm' => __('Bạn có chắc chắn muốn xóa {0}?', $installment->name)
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


<script type="text/javascript">
    var report = <?= json_encode($report) ?>;
</script>
