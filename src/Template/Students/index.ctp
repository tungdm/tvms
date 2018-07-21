<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student[]|\Cake\Collection\CollectionInterface $students
 */
use Cake\Core\Configure;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller) ?? 0;

$addressType = array_keys(Configure::read('addressType'));

$gender = Configure::read('gender');

$eduLevel = Configure::read('eduLevel');
$eduLevel = array_map('array_shift', $eduLevel);

$studentStatus = Configure::read('studentStatus');
$recordsDisplay = Configure::read('recordsDisplay');

$counter = 0;
$currentUser = $this->request->session()->read('Auth.User');

$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);

$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-tabcollapse.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('student.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);

$this->assign('title', 'Quản lý Lao động');
?>

<?php $this->start('content-header'); ?>
<h1><?= __('QUẢN LÝ LAO ĐỘNG') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Danh sách lao động</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('DANH SÁCH') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    <div class="btn-group">
                        <a href="javascript:;" class="btn btn-box-tool dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-plus"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:;" onclick="showAddStudentModal()">Tạo lịch hẹn</a></li>
                            <li><?= $this->Html->link(__('Thêm mới lao động'), ['action' => 'info']) ?></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="#" class="btn btn-box-tool dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li>
                                <?= $this->Html->link('Xuất danh sách', [
                                    'action' => 'exportXlsx'
                                ]) ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?= $this->Form->create(null, [
                'class' => 'form-horizontal',
                'url' => ['controller' => 'Students', 'action' => 'index'],
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
                            <th scope="col" class="codeCol">
                                <?= $this->Paginator->sort('code', 'Mã TTS')?>
                            </th>
                            <th scope="col" class="fullnameCol">
                                <?= $this->Paginator->sort('fullname', 'Họ và tên') ?>
                            </th>
                            <th scope="col" class="emailCol hidden">
                                <?= $this->Paginator->sort('email') ?>
                            </th>
                            <th scope="col" class="genderCol">
                                <?= __('Giới tính') ?>
                            </th>
                            <th scope="col" class="phoneCol">
                                <?= $this->Paginator->sort('phone', 'Số điện thoại') ?>
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
                            <td class="col-md-2 codeCol">
                                <?= $this->Form->control('code', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['code'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 fullnameCol">
                                <?= $this->Form->control('fullname', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['fullname'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-2 emailCol hidden">
                                <?= $this->Form->control('email', [
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12',
                                    'value' => $query['email'] ?? ''
                                    ]) 
                                ?>
                            </td>
                            <td class="col-md-1 genderCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('gender', [
                                    'options' => $gender, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'id' => 'filter-gender',
                                    'value' => $query['gender'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-1 phoneCol" style="width: 12.499999995%;">
                                <?= $this->Form->control('phone', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'value' => $query['phone'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="col-md-2 statusCol">
                                <?= $this->Form->control('status', [
                                    'options' => $studentStatus, 
                                    'empty' => true,
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12 select2-theme', 
                                    'value' => $query['status'] ?? ''
                                    ])
                                ?>
                            </td>
                            <td class="filter-group-btn actions">
                                <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                                <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                                <?= $this->Form->end() ?>
                            </td>
                        </tr>
                        <?php if (($students)->isEmpty()): ?>
                        <tr>
                            <td colspan="100" class="table-empty"><?= __('Hiện tại chưa có dữ liệu') ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($students as $student): ?>
                        <?php $counter++ ?>
                        <tr>
                            <td class="cell"><?= h($counter) ?></td>
                            <td class="cell codeCol"><?= h($student->code) ?></td>
                            <td class="cell fullnameCol"><?= h($student->fullname) ?></td>
                            <td class="cell emailCol hidden"><?= h($student->email) ?></td>
                            <td class="cell genderCol"><?= h($gender[$student->gender]) ?></td>
                            <td class="cell phoneCol"><?= h($this->Phone->makeEdit($student->phone)) ?></td>
                            <td class="cell statusCol"><?= h($studentStatus[$student->status]) ?></td>
                            
                            <td class="actions cell">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button" aria-expanded="false">Mở rộng <span class="caret"></span>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-info-circle" aria-hidden="true"></i> Chi tiết', 
                                                ['action' => 'view', $student->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <?php if ($permission == 0): ?>
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-edit" aria-hidden="true"></i> Sửa', [
                                                'action' => 'info', $student->id],
                                                ['escape' => false]) ?>
                                        </li>
                                        <li>
                                            <?= $this->Form->postLink('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', 
                                            ['action' => 'delete', $student->id], 
                                            [
                                                'escape' => false, 
                                                'confirm' => __('Bạn có chắc chắn muốn xóa lao động {0}?', $student->fullname)
                                            ]) ?>
                                        </li>
                                        <?php endif; ?>
                                        <li class="divider"></li>
                                        <li>
                                            <?= $this->Html->link('<i class="fa fa-file-word-o" aria-hidden="true"></i> Xuất CV Việt', 
                                            ['action' => 'exportResume', $student->id],
                                            ['escape' => false]) ?>
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

<div class="modal fade" id="add-candidate-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÊM MỚI LỊCH HẸN</h4>
            </div>
            <?= $this->Form->create(null, [
                'type' => 'post',
                'class' => 'form-horizontal form-label-left',
                'id' => 'add-candidate-form',
                'url' => ['action' => 'add'],
                'data-parsley-validate' => '',
                'templates' => [
                    'inputContainer' => '{{content}}'
                    ]
                ]) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Họ tên') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('fullname', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'required' => true,
                            'placeholder' => 'Nhập họ tên ứng viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gender"><?= __('Giới tính') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('gender', [
                            'options' => $gender, 
                            'empty' => true, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'data-parsley-errors-container' => '#error-gender',
                            'data-parsley-class-handler' => '#select2-gender',
                            ]) ?>
                        <span id="error-gender"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Số điện thoại') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'pattern' => '^(09.|011.|012.|013.|014.|015.|016.|017.|018.|019.|08.)\d{7}$',
                            'placeholder' => 'Nhập số điện thoại của ứng viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email"><?= __('Email') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('email', [
                            'label' => false, 
                            'required' => true, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'placeholder' => 'Nhập địa chỉ mail của ứng viên'
                            ]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthday"><?= __('Ngày sinh') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="input-group date input-picker" id="candidate-birthday">
                            <?= $this->Form->control('birthday', [
                                'type' => 'text',
                                'label' => false, 
                                'class' => 'form-control',
                                'placeholder' => 'yyyy-mm-dd',
                                'required' => true,
                                'data-parsley-errors-container' => '#picker-errors'
                                ])
                            ?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span id="picker-errors"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Quê quán') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->hidden('addresses.0.type', ['value' => $addressType[0]]) ?>
                        <?= $this->Form->control('addresses.0.city', [
                            'options' => $cities, 
                            'empty' => true, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'data-parsley-errors-container' => '#error-address',
                            'data-parsley-class-handler' => '#select2-addresses-0-city',
                            ]) ?>
                        <span id="error-address"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Trình độ học vấn') ?></label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <?= $this->Form->control('educational_level', [
                            'options' => $eduLevel, 
                            'empty' => true, 
                            'required' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12 select2-theme',
                            'data-parsley-errors-container' => '#error-edu-level',
                            'data-parsley-class-handler' => '#select2-educational-level',
                            ]) ?>
                        <span id="error-edu-level"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Hoàn tất'), ['class' => 'btn btn-success']) ?>
                <button type="button" class="btn btn-default" id="add-candidate-close-btn" data-dismiss="modal">Đóng</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
