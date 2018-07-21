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
    <?= $this->Html->meta('icon') ?>

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
                            <i class="fa fa-bar-chart-o"></i>
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
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Nghiệp đoàn', 
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
                                    <span id="view-name-romaji"></span> (<span id="view-name-kanji"></span>)
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
                                    <span id="view-company-name-romaji"></span> (<span id="view-company-name-kanji"></span>)
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5 col-sm-5 col-xs-12" for="guild-name"><?= __('Nghiệp đoàn') ?>: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-control form-control-view col-md-7 col-xs-12">
                                    <span id="view-guild-name-romaji"></span> (<span id="view-guild-name-kanji"></span>)
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
