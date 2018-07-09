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
$cakeDescription = 'TVMS';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('bootstrap/bootstrap.min.css') ?>
    <?= $this->Html->css('font-awesome/font-awesome.min.css') ?>
    <?= $this->Html->css('nprogress/nprogress.css') ?>
    <?= $this->Html->css('mCustomScrollbar/jquery.mCustomScrollbar.min.css') ?>
    <?= $this->Html->css('pnotify.custom.min.css') ?>
    <?= $this->Html->css('select2.min.css'); ?>
    <?= $this->Html->css('select2-bootstrap.css'); ?>

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
<body class="hold-transition skin-blue sidebar-mini fixed">
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
                                        __('Update Profile'), 
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
                                            '<i class="fa fa-sign-out pull-right"></i> Log Out',
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
                        <?= $this->Html->link('<i class="fa fa-home"></i> <span>MÀN HÌNH CHÍNH</span>', 
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
                                    ['controller' => 'Test', 'action' => 'index'],
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
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Công ty tiếp nhận', 
                                    ['controller' => 'Companies', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Nghiệp đoàn', 
                                    ['controller' => 'Guilds', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('<i class="fa fa-circle-o"></i> Người giới thiệu', 
                                    ['controller' => 'Presenters', 'action' => 'index'],
                                    ['escape' => false]) ?>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-th"></i>
                            <span><?= __('TÀI CHÍNH') ?></span>
                        </a>
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
                <b>Version</b> 0.1
            </div>
            <strong>Copyright &copy; 2018-2020 <a href="#">M2Group</a>.</strong> All rights reserved.
        </footer>
    </div>

    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('fastclick.js') ?>
    <?= $this->Html->script('nprogress.js') ?>
    <?= $this->Html->script('pnotify.custom.min.js') ?>
    <?= $this->Html->script('select2.full.js', ['block' => 'scriptBottom']); ?>
    <?= $this->Html->script('parsley.min.js', ['block' => 'scriptBottom']); ?>
    <?= $this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']); ?>
    
    <?= $this->fetch('scriptBottom') ?>
    
    <?= $this->Html->script('admin.js') ?>
</body>
</html>
