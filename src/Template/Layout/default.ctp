<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Configure;
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon', 'tvms.png') ?>

    <?= $this->Html->css('bootstrap/bootstrap.min.css') ?>
    <?= $this->Html->css('font-awesome/font-awesome.min.css') ?>
    <?= $this->Html->css('nprogress/nprogress.css') ?>
    <?= $this->Html->css('mCustomScrollbar/jquery.mCustomScrollbar.min.css') ?>
    <?= $this->Html->css('pnotify.css') ?>
    <?= $this->Html->css('pnotify.buttons.css') ?>
    <?= $this->Html->css('pnotify.nonblock.css') ?>
    <?= $this->Html->css('select2.min.css'); ?>
    <?= $this->Html->css('select2-bootstrap.css'); ?>
    <?= $this->Html->css('simditor.css'); ?>

    <?= $this->Html->css('admin.css') ?>
    <?= $this->Html->css('skin-blue.css') ?>
    <?= $this->Html->css('base.css') ?>

    <?= $this->Html->script('jquery.min.js') ?>
    
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    <?= $this->fetch('styleTop') ?>
    <?= $this->fetch('scriptTop') ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="/tvms/" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>TV</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>TVMS</b></span>
            </a>
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <?php if (empty($this->request->session()->read('Auth.User.image'))): ?>
                                    <?= $this->Html->image(Configure::read('noAvatar'), ['class' => 'user-image']) ?>
                                <?php else: ?>
                                    <?= $this->Html->image($this->request->session()->read('Auth.User.image'), ['class' => 'user-image']) ?>
                                <?php endif; ?>
                                <?= $this->request->session()->read('Auth.User.username') ?>
                                <span class="fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li>
                                    <?= $this->Html->link(
                                        __('Thông tin cá nhân'), 
                                        ['controller' => 'Users', 'action' => 'edit', $this->request->session()->read('Auth.User.id')]
                                        ) 
                                    ?>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                    <span class="badge bg-red pull-right">50%</span>
                                    <span>Settings</span>
                                    </a>
                                </li>
                                <li><a href="javascript:;">Help</a></li>
                                <li>
                                    <?=
                                        $this->Html->link(
                                            '<i class="fa fa-sign-out pull-right"></i> Đăng xuất',
                                            ['controller' => 'Users', 'action' => 'logout'],
                                            ['escape' => false]
                                        )
                                    ?>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <section class="sidebar">
                <!-- sidebar menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li>
                        <?= $this->Html->link('<i class="fa fa-home" style="font-size: 1.3em;"></i> <span>TRANG CHỦ</span>', 
                            '/',
                            ['escape' => false]) ?>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span><?= __('NHÂN SỰ') ?></span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Quản Lý Lao Động', 
                                    ['controller' => 'Students', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Quản Lý Nhân Viên', 
                                    ['controller' => 'Users', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <?= $this->Html->link('<i class="fa fa-calendar"></i> <span>LỊCH CÔNG TÁC</span>', 
                            ['controller' => 'Events', 'action' => 'index'],
                            ['escape' => false]) ?>
                    </li>
                    <li>
                        <?= $this->Html->link('<i class="fa fa-table"></i> <span>ĐƠN HÀNG</span>',
                            ['controller' => 'Orders', 'action' => 'index'],
                            ['escape' => false]) ?>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-graduation-cap"></i>
                            <span><?= __('ĐÀO TẠO') ?></span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Quản lý lớp học', 
                                    ['controller' => 'Jclasses', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Quản lý kì thi', 
                                    ['controller' => 'Jtests', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-folder"></i>
                            <span><?= __('ĐỐI TÁC') ?></span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Nghiệp đoàn quản lý', 
                                    ['controller' => 'Guilds', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Công ty tiếp nhận', 
                                    ['controller' => 'Companies', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Người giới thiệu', 
                                    ['controller' => 'Presenters', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                        </ul>
                    </li>
                </ul>
            </section>
        </aside>
        <div class="content-wrapper">
            <section class="content-header">
                <?= $this->fetch('content-header') ?>
            </section>
            <!-- Main content -->
            <section class="content">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </section>
            <div class="clearfix"></div>
            <section class="floating-button">
                <?= $this->fetch('floating-button') ?>
            </section>
        </div>
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
            <strong>Bản quyền thuộc về &copy; <a href="#">Nhật Ngữ Tâm Việt</a>.</strong>.
            </div>
            <b>Phiên bản</b> 0.1
        </footer>
    </div>

    <!-- Global guild modal -->
    <div id="view-guild-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">THÔNG TIN NGHIỆP ĐOÀN</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="name"><?= __('Tên nghiệp đoàn') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-name-romaji"></span><br/>
                                    <span id="view-name-kanji"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="license_num"><?= __('Số giấy phép') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-license-number"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="subsidy"><?= __('Tiền trợ cấp TTS') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-subsidy"></span> ¥/tháng
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="deputy"><?= __('Người đại diện') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-deputy-romaji"></span><br/>
                                    <span id="view-deputy-kanji">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="address"><?= __('Địa chỉ') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <ol class="list-unstyled">
                                        <li><span id="view-address-romaji"></span></li>
                                        <li><span id="view-address-kanji"></span></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="phone"><?= __('Số điện thoại') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <ol class="list-unstyled">
                                        <li>(Việt Nam) <span id="view-phone-vn"></span></li>
                                        <li>(Nhật Bản) <span id="view-phone-jp"></span></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global company modal -->
    <div id="view-company-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">THÔNG TIN CÔNG TY</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="company-name"><?= __('Tên công ty') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-company-name-romaji"></span><br/>
                                    <span id="view-company-name-kanji"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="deputy"><?= __('Người đại diện') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-company-deputy-romaji"></span><br/>
                                    <span id="view-company-deputy-kanji">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="guild-name"><?= __('Nghiệp đoàn') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-guild-name-romaji"></span><br/>
                                    <span id="view-guild-name-kanji"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="address"><?= __('Địa chỉ') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <ol class="list-unstyled">
                                        <li><span id="view-company-address-romaji"></span></li>
                                        <li><span id="view-company-address-kanji"></span></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="phone"><?= __('Số điện thoại') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <ol class="list-unstyled">
                                        <li>(Việt Nam) <span id="view-company-phone-vn"></span></li>
                                        <li>(Nhật Bản) <span id="view-company-phone-jp"></span></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global presenter modal -->
    <div id="view-presenter-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">THÔNG TIN CỘNG TÁC VIÊN</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="presenter-name"><?= __('Tên cộng tác viên') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-presenter-name"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="presenter-address"><?= __('Địa chỉ') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-presenter-address"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="presenter-phone"><?= __('Số điện thoại') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-presenter-phone"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="presenter-type"><?= __('Loại') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-presenter-type"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global history modal -->
    <div id="history-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content box">
                <div class="overlay hidden" id="history-modal-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">GHI CHÚ</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 col-xs-12">
                        <?= $this->Form->create(null, [
                            'class' => 'form-horizontal form-label-left', 
                            'id' => 'add-edit-history-form', 
                            'data-parsley-validate' => '',
                            'templates' => [
                                'inputContainer' => '{{content}}'
                                ]
                            ]) ?>
                        <?= $this->Form->hidden('hitory.type', ['id' => 'history-type'])?>
                        <?= $this->Form->hidden('hitory.student_id', ['id' => 'history-student-id'])?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title"><?= __('Tiêu đề') ?></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <?= $this->Form->control('history.title', [
                                    'label' => false, 
                                    'class' => 'form-control col-md-7 col-xs-12', 
                                    'required' => true,
                                    'placeholder' => 'Nhập tiêu đề ghi chú'
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12 optional" for="note"><?= __('Nội dung') ?></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <?= $this->Form->control('history.note', [
                                    'label' => false,
                                    'type' => 'textarea',
                                    'class' => 'form-control', 
                                    'placeholder' => 'Nhập nội dung ghi chú',
                                    'rows' => 10
                                    ]) ?>
                            </div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submit-history-btn">Hoàn tất</button>
                    <button type="button" class="btn btn-default" id="close-history-modal-btn" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global history template -->
    <script id="history-template" type="text/x-handlebars-template">
        <li class="history-detail" id="history-{{counter}}" history="{{id}}">
            <img src="{{image}}" class="img-circle timeline-avatar" alt="">
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> {{created}}</span>
                <h3 class="timeline-header">{{title}}</h3>
                <div class="timeline-body">
                    {{{note}}}
                </div>
                <div class="timeline-footer">
                    <button type="button" class="btn btn-primary btn-xs" id="edit-history-btn" onclick="showEditHistoryModal(this)">Chỉnh sửa</button>
                    <button type="button" class="btn btn-danger btn-xs" id="delete-history-btn" onclick="deleteHistory(this)">Xóa</button>
                </div>
            </div>
        </li>
    </script>

    <!-- Global all histories template -->
    <script id="all-histories-template" type="text/x-handlebars-template">
        {{#each this}}
            <li class="history-detail" id="history-{{@index}}" history="{{id}}">
                <img src="/tvms/img/{{users_created_by.image}}" class="img-circle timeline-avatar" alt="">
                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> {{created}}</span>
                    <h3 class="timeline-header">{{title}}</h3>
                    <div class="timeline-body">
                        {{{nl2br note}}}
                    </div>
                    <div class="timeline-footer">
                        {{#if owner}}
                        <button type="button" class="btn btn-primary btn-xs" id="edit-history-btn" onclick="showEditHistoryModal(this)">Chỉnh sửa</button>
                        <button type="button" class="btn btn-danger btn-xs" id="delete-history-btn" onclick="deleteHistory(this)">Xóa</button>
                        {{else}}
                        <span class="history-creater">Người tạo: {{users_created_by.fullname}}</span>
                        {{/if}}
                    </div>
                </div>
            </li>
        {{/each}}
    </script>

    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('fastclick.js') ?>
    <?= $this->Html->script('nprogress.js') ?>
    <?= $this->Html->script('pnotify.js') ?>
    <?= $this->Html->script('pnotify.buttons.js') ?>
    <?= $this->Html->script('pnotify.nonblock.js') ?>

    <?= $this->Html->script('select2.full.js'); ?>
    <?= $this->Html->script('parsley.min.js'); ?>
    <?= $this->Html->script('parsley.vn.js'); ?>
    <?= $this->Html->script('handlebars-v4.0.11.js'); ?>

    <?= $this->Html->script('editor/module.js'); ?>
    <?= $this->Html->script('editor/hotkeys.js'); ?>
    <?= $this->Html->script('editor/uploader.js'); ?>
    <?= $this->Html->script('editor/simditor.js'); ?>

    <?= $this->fetch('scriptBottom') ?>
    
    <?= $this->Html->script('admin.js') ?>
</body>
</html>
