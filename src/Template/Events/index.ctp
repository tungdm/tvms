<?php
$this->Html->css('fullcalendar.css', ['block' => 'styleTop']);
$this->Html->css('calendar.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('fullcalendar.js', ['block' => 'scriptBottom']);
$this->Html->script('fullcalendar-vi.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('event.js', ['block' => 'scriptBottom']);

$this->assign('title', 'Lịch công tác');
?>

<?php $this->start('content-header'); ?>
<h1><?= __('LỊCH CÔNG TÁC') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Trang Chủ',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Lịch Công Tác</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">
            <div class="box-body no-padding">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div id="event-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content box">
            <div class="overlay hidden" id="event-modal-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">THÔNG TIN SỰ KIỆN</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                <?= $this->Form->create(null, [
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'event-form',
                    'url' => ['controller' => 'Events', 'action' => 'add'],
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                    <?= $this->Form->hidden('all_day') ?>
                    <?= $this->Form->hidden('color') ?>

                    <div class="hidden">
                        <?= $this->Form->control('start', [
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            ])?>
                        <?= $this->Form->control('end', [
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            ])?>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="scope"><?= __('Phạm vi') ?></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <?= $this->Form->control('scope', [
                                'options' => $eventScope, 
                                'required' => true,
                                'label' => false,
                                'empty' => true,
                                'data-parsley-errors-container' => '#error-event-scope',
                                'data-parsley-class-handler' => '#select2-scope',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-event-scope"></span>
                        </div>
                    </div>
                    <div class="form-group color-chooser-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="class"><?= __('Màu Sắc') ?></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                <ul class="fc-color-picker" id="color-chooser">
                                    <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-magenta" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title"><?= __('Tiêu đề') ?></label>
                        <div class="col-md-10 col-sm-10 col-xs-12 input-group">
                            <?= $this->Form->control('title', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true,
                                'placeholder' => 'Nhập tiêu đề sự kiện'
                                ]) ?>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="title-color">Tên tiêu đề</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description"><?= __('Nội dung') ?></label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <?= $this->Form->control('description', [
                                'label' => false, 
                                'type' => 'textarea',
                                'rows' => 5,
                                'class' => 'form-control col-md-7 col-xs-12 edittextarea', 
                                'placeholder' => 'Nhập nội dung của sự kiện'
                                ]) ?>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submit-event-btn">Hoàn tất</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="close-event-modal-btn">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div id="event-info-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="event-title">THÔNG TIN HOẠT ĐỘNG</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                    <div id="event-description"></div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <p class="footer-note global-note" id="event-owner"></p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="interview-template" type="text/x-handlebars-template">
    <ul>
        <li><strong>Tên đơn hàng: </strong>{{orderName}}</li>
        <li><strong>Nghiệp đoàn tiếp nhận: </strong>{{guild}}</li>
        <li><strong>Công ty tiếp nhận: </strong>{{company}}</li>
        <li><strong>Nghề nghiệp: </strong>{{job}}</li>
        <li><strong>Nơi làm việc: </strong>{{work_at}}</li>
        <li><strong>Thi tay nghề: </strong>{{skill_test}}</li>
        <li><strong>Hình thức phỏng vấn: </strong>{{interview_type}}</li>
        <li>
            <strong>Danh sách ứng viên: </strong>
            <div class="box-body table-responsive">
                <table class="table table-bordered custom-table candidate-table">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Họ tên</th>
                            <th scope="col">Tuổi</th>
                            <th scope="col">Giới tính</th>
                            <th scope="col"><?= __('Số ĐT') ?></th>
                        </tr>
                    </thead>
                    <tbody id="candidate-container">
                        {{#each candidates}}
                            <tr class="row-rec">
                                <td class="cell col-md-1 stt-col">
                                    {{inc @index}}
                                </td>
                                <td class="cell col-md-3">
                                    {{fullname}}
                                </td>
                                <td class="cell col-md-3">
                                    {{calAge birthday}}
                                </td>
                                <td class="cell col-md-2">
                                    {{trans gender}}
                                </td>
                                <td class="cell col-md-3">
                                    {{phoneFormat phone}}
                                </td>
                            </tr>
                        {{/each}}
                    </tbody>
                </table>
            </div>
                
            
        </li>
    </ul>
</script>

<script id="test-template" type="text/x-handlebars-template">
    <ul>
        <li><strong>Lớp thi: </strong>{{class}}</li>
        <li><strong>Bài thi: </strong>{{lesson_from}} ～ {{lesson_to}}</li>
        <li>
            <strong>Kỹ năng thi: </strong>
            <div class="box-body table-responsive">
                <table class="table table-bordered custom-table test-table">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Kỹ năng</th>
                            <th scope="col">Giáo viên phụ trách</th>
                        </tr>
                    </thead>
                    <tbody id="skill-container">
                        {{#each skills}}
                            <tr class="row-rec">
                                <td class="cell col-md-1 stt-col">
                                    {{inc @index}}
                                </td>
                                <td class="cell col-md-3">
                                    {{skill}}
                                </td>
                                <td class="cell col-md-3">
                                    {{user.fullname}}
                                </td>
                            </tr>
                        {{/each}}
                    </tbody>
                </table>
            </div>
        </li>
    </ul>
</script>