<?php 
use Cake\Core\Configure;
use Cake\I18n\Time;

$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller);

$gender = Configure::read('gender');
$interviewResult = Configure::read('interviewResult');
$cityJP = Configure::read('cityJP');
$cityJP = array_map('array_shift', $cityJP);
$yesNoQuestion = Configure::read('yesNoQuestion');
$interviewType = Configure::read('interviewType');
$workTime = Configure::read('workTime');

$this->Html->css('order.css', ['block' => 'styleTop']);
$this->Html->script('order.js', ['block' => 'scriptBottom']);
?>

<?php $this->start('content-header'); ?>
<h1><?= __('Order Detail') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Home',
            '/',
            ['escape' => false]) ?>
    </li>
    <li>
        <?= $this->Html->link(__('Orders'), [
            'controller' => 'Orders',
            'action' => 'index']) ?>
    </li>
    <li class="active">Order Detail</li>
</ol>
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
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name"><?= __('Tên đơn hàng') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="job_id"><?= __('Nghề nghiệp') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->job->job_name ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="salary"><?= __('Mức lương') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->salary_from ?> ～ <?= $order->salary_to ?> (¥/tháng)
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="interview_date"><?= __('Ngày phỏng vấn') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->interview_date ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="work_at"><?= __('Địa điểm làm việc') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $cityJP[$order->work_at] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="work_time"><?= __('Thời gian làm việc') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $workTime[$order->work_time] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="company_id"><?= __('Công ty tiếp nhận') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->company->name_romaji ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="skill_test"><?= __('Thi tay nghề') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $yesNoQuestion[$order->skill_test] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="interview_type"><?= __('Hình thức phỏng vấn') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $interviewType[$order->interview_type] ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="departure_date"><?= __('Ngày xuất cảnh') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->departure_date ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Yêu cầu tuyển chọn') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="experience"><?= __('Kinh nghiệm') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <?= !empty($order->experience) ? nl2br($order->experience) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="male_num"><?= __('Số lượng nam') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->male_num ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="female_num"><?= __('Số lượng nữ') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12"><?= $order->female_num ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="age_interval"><?= __('Độ tuổi') ?></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= $order->age_from ?> ～ <?= $order->age_to ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="height"><?= __('Chiều cao') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->height) ? $order->height : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="weight"><?= __('Cân nặng') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12">
                                <?= !empty($order->weight) ? $order->weight : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="requirement"><?= __('Yêu cầu khác') ?></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="form-control form-control-view col-md-7 col-xs-12 textarea-view">
                                <?= !empty($order->requirement) ? nl2br($order->requirement) : 'N/A' ?>
                            </div>
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
                    <h3 class="box-title"><?= __('Danh sách ứng viên') ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered custom-table candidate-table">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('STT') ?></th>
                                <th scope="col"><?= __('Họ và tên') ?></th>
                                <th scope="col"><?= __('Tuổi') ?></th>
                                <th scope="col"><?= __('Giới tính') ?></th>
                                <th scope="col"><?= __('Số ĐT') ?></th>
                                <th scope="col"><?= __('Kết quả') ?></th>
                                <th scope="col" class="actions"></th>
                            </tr>
                        </thead>
                        <tbody id="candidate-container">
                        <?php if (!empty($order->students)): ?>
                            <?php $counter = 0; $now = Time::now(); ?>
                            <?php foreach ($order->students as $key => $value): ?>
                                <div class="hidden candidate-id" id="candidate-<?=$counter?>-id">
                                    <?= $this->Form->hidden('students.'  . $key . '.id', ['value' => $value->id]) ?>
                                </div>
                                <tr class="row-rec" id="row-candidate-<?=$counter?>">
                                    <td class="cell col-md-1 stt-col">
                                        <?= $counter+1 ?>
                                    </td>
                                    <td class="cell col-md-3">
                                        <a href="javascript:;" onclick="viewCandidate('<?=$value->id?>', <?=$permission?>);"><?= h($value->fullname) ?></a>
                                    </td>
                                    <td class="cell col-md-1">
                                        <?= h(($now->diff($value->birthday))->y) ?>
                                    </td>
                                    <td class="cell col-md-1">
                                        <?= $gender[$value->gender]?>
                                    </td>
                                    <td class="cell col-md-3">
                                        <?= $value->phone ?>
                                    </td>
                                    <td class="cell col-md-1">
                                        <span class="result-text"><?= $interviewResult[$value->_joinData->result] ?></span>
                                    </td>
                                    <td class="actions cell">
                                        <?php if ($value->_joinData->result == 1): ?>
                                        <?= $this->Html->link(
                                            '<i class="fa fa-2x fa-folder"></i>',
                                            'javascript:;',
                                            [
                                                'class' => 'edit-doc',
                                                'escape' => false,
                                                'onClick' => "editDoc(this, $permission)"
                                            ])
                                        ?>
                                        <?php endif; ?>
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