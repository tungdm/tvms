<?php 
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('home.js', ['block' => 'scriptBottom']);
$this->assign('title', 'TVMS - Trang Chủ');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<?php $this->start('content-header'); ?>
    <h1><?= __('BÁO CÁO TÌNH HÌNH HOẠT ĐỘNG') ?></h1>
    <ol class="breadcrumb">
        <li class="active">
            <?= $this->Html->link(
                '<i class="fa fa-home"></i> Trang Chủ',
                '/',
                ['escape' => false]) ?>
        </li>
    </ol>
<?php $this->end(); ?>
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue-2">
            <div class="inner">
                <h2><strong><?= $this->Number->format($data['newOrder']) ?></strong></h2>
                <p><?= __('Đơn hàng mới') ?></p>
            </div>
            <div class="icon">
                <i class="fa fa-suitcase" aria-hidden="true"></i>
            </div>
            <?= $this->Html->link('Xem thêm <i class="fa fa-arrow-circle-right"></i>',
                [
                    'controller' => 'Orders', 
                    'action' => 'index',
                    '?' => ['created' => $data['firstDayOfMonth']]
                ],
                [
                    'class' => 'small-box-footer',
                    'escape' => false
                ]) ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green-2">
            <div class="inner">
                <h2><strong><?= $this->Number->format($data['newStudent']) ?></strong></h2>
                <p><?= __('Học viên mới') ?></p>
            </div>
            <div class="icon">
                <i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <?= $this->Html->link('Xem thêm <i class="fa fa-arrow-circle-right"></i>',
                [
                    'controller' => 'Students', 
                    'action' => 'index',
                    '?' => ['enrolled_date' => $data['firstDayOfMonth']]
                ],
                [
                    'class' => 'small-box-footer',
                    'escape' => false
                ]) ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow-2">
            <div class="inner">
                <h2><strong><?= $this->Number->format($data['newPassedCount']) ?></strong></h2>
                <p>Đậu phỏng vấn</p>
            </div>
            <div class="icon">
                <i class="fa fa-graduation-cap" aria-hidden="true"></i>
            </div>
            <a href="javascript:;" class="small-box-footer" onclick="showPassedStudent()">Xem thêm <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple-2">
        <div class="inner">
            <h2><strong><?= $this->Number->format($data['returnStudent'])?></strong></h2>
            <p>Về nước</p>
        </div>
        <div class="icon">
            <i class="fa fa-paper-plane" aria-hidden="true"></i>
        </div>
        <?= $this->Html->link('Xem thêm <i class="fa fa-arrow-circle-right"></i>',
            [
                'controller' => 'Students', 
                'action' => 'index',
                '?' => ['return_from' => $data['firstDayOfMonth'], 'return_to' => $data['lastDayOfMonth']]
            ],
            [
                'class' => 'small-box-footer',
                'escape' => false
            ]) ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">PHÂN BỐ LAO ĐỘNG</h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="chart-responsive">
                            <canvas id="northPopulation" height="200"></canvas>
                        </div>
                        <h4 class="pie-chart-name">MIỀN BẮC</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-responsive">
                            <canvas id="middlePopulation" height="200"></canvas>
                        </div>
                        <h4 class="pie-chart-name">MIỀN TRUNG</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-responsive">
                            <canvas id="southPopulation" height="200"></canvas>
                        </div>
                        <h4 class="pie-chart-name">MIỀN NAM</h4>
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
                <h3 class="box-title"><?= __('TÌNH HÌNH TỔNG QUAN') ?></h3>
                <div class="box-tools pull-right">
                    <a href="javascript:;" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-chevron-up"></i></a>
                    <a href="javascript:;" class="btn btn-box-tool" id="download-btn" onclick="downloadChart('line-chart')"><i class="fa fa-cloud-download"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 col-xs-12">
                        <canvas id="line-chart" height="300"></canvas>
                    </div>
                    <div class="col-md-4 col-xs-12" style="padding-top:50px;">
                        <div class="info-box bg-green">
                            <span class="info-box-icon">
                                <i class="fa fa-check-square-o" aria-hidden="true"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Đậu phỏng vấn</span>
                                <span class="info-box-number"><?= $this->Number->format($data['totalPassed'], ['locale' => 'vn_VN']) ?> người</span>

                                <div class="progress">
                                    <div class="progress-bar" style="width:100%"></div>
                                </div>
                                <span class="progress-description">
                                <?= $data['rateImmi'] ?>% lao động đã xuất cảnh <?= $data['rateImmi'] != 0 ? '(' . $data['totalImmigrationCount'] . ' người)' : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-box bg-red">
                            <span class="info-box-icon">
                                <i class="fa fa-external-link" aria-hidden="true"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Về nước</span>
                                <span class="info-box-number"><?= $this->Number->format($data['totalReturn'], ['locale' => 'vn_VN']) ?> người</span>

                                <div class="progress">
                                    <div class="progress-bar" style="width:100%"></div>
                                </div>
                                <span class="progress-description">
                                    <?=$data['rateWithdraw']?>% lao động rút hồ sơ <?= $data['rateWithdraw'] != 0 ? '(' . $data['totalWithdraw'] . ' người)': '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div id="newly-passed-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">DANH SÁCH ĐẬU PHỎNG VẤN</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12 table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th scope="col" class="col-md-1"><?= __('STT') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Đơn hàng') ?></th>
                                <th scope="col" class="col-md-2"><?= __('Ngày phỏng vấn') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Thực tập sinh') ?></th>
                                <th scope="col" class="col-md-3"><?= __('Số điện thoại') ?></th>
                            </tr>
                        </thead>
                        <tbody id="newly-passed-container">
                        </tbody>
                    </table>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="close-modal-btn" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<script id="newly-passed-template" type="text/x-handlebars-template">
    {{#each this}}
    <tr>
        <td>{{inc @index}}</td>
        <td>{{order.name}}</td>
        <td>{{dateTimeFormat order.interview_date}}</td>
        <td>{{student.fullname}}</td>
        <td>{{phoneFormat student.phone}}</td>
    </tr>
    {{/each}}
</script>

<script type="text/javascript">
    var totalData = <?= json_encode($data['totalData']) ?>;
    var northPopulation = <?= json_encode($data['northPopulation']) ?>;
    var middlePopulation = <?= json_encode($data['middlePopulation']) ?>;
    var southPopulation = <?= json_encode($data['southPopulation']) ?>;
</script>
