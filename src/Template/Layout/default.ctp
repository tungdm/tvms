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
    <?= $this->Html->css('datatables.min.css') ?>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('custom.min.css') ?>
    <?= $this->Html->script('jquery.min.js') ?>
    
    
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    <?= $this->fetch('styleTop') ?>
    <?= $this->fetch('scriptTop') ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="#" class="site_title"><i class="fa fa-paw"></i> <span>TMVS</span></a>
                    </div>
                    <div class="clearfix"></div>
                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a><i class="fa fa-home"></i> <?= __('MÀN HÌNH CHÍNH') ?> </a></li>
                                <li><a><i class="fa fa-edit"></i> <?= __('NHÂN SỰ') ?> <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">QUẢN LÝ LAO ĐỘNG</a></li>
                                        <li><?= $this->Html->link(__('QUAN LÝ NHÂN VIÊN'), ['controller' => 'Users', 'action' => 'index']) ?></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-desktop"></i> LỊCH CÔNG TÁC <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="general_elements.html">LỊCH HÔM NAY</a></li>
                                        <li><a href="media_gallery.html">BẢNG LỊCH THÁNG</a></li>
                                        <li><a href="typography.html">THÔNG BÁO CHUNG</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-table"></i> ĐƠN HÀNG <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                <li><a href="tables.html">Tables</a></li>
                                <li><a href="tables_dynamic.html">Table Dynamic</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-bar-chart-o"></i> ĐÀO TẠO <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                <li><a href="chartjs.html">Chart JS</a></li>
                                <li><a href="chartjs2.html">Chart JS2</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-clone"></i>ĐỐI TÁC <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                <li><a href="fixed_sidebar.html">NGHIỆP ĐOÀN, MÔI GIỚI</a></li>
                                <li><a href="fixed_footer.html">XÍ NGHIỆP TIẾP NHẬN</a></li>
                                <li><a href="fixed_footer.html">CÔNG TY QUẢN LÝ</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-clone"></i>TÀI CHÍNH <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                <li><a href="fixed_sidebar.html">NGHIỆP ĐOÀN, MÔI GIỚI</a></li>
                                <li><a href="fixed_footer.html">Fixed Footer</a></li>
                                </ul>
                            </li>
                            </ul>
                        </div>
                    </div>
                    <!-- sidebar menu -->
                </div>
            </div>
            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <!-- <img src="images/img.jpg" alt=""> -->
                                    <?php if (empty($this->request->session()->read('Auth.User.avatar'))): ?>
                                        <?= $this->Html->image(Configure::read('noAvatar')) ?>
                                    <?php else: ?>
                                        <?= $this->Html->image($this->request->session()->read('Auth.User.avatar')) ?>
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
                                        <!-- <i class="fa fa-sign-out pull-right"></i> Log Out</a> -->
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- top navigation -->
            <!-- page content -->
            <div class="right_col" role="main">
                <div class="">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <?= $this->Flash->render() ?>
                                <?= $this->fetch('content') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page content -->
            <!-- footer -->
            <footer>
                <div class="pull-right">TVMS - Proudly a Project By M2Group </div>
                <div class="clearfix"></div>
            </footer>
            <!-- footer -->
        </div>
    </div>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('fastclick.js') ?>
    <?= $this->Html->script('nprogress.js') ?>
    <?= $this->Html->script('pnotify.custom.min.js') ?>
    <?= $this->Html->script('datatables.min.js') ?>
    <?= $this->Html->script('mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js') ?>

    <?= $this->fetch('scriptBottom') ?>
    <?= $this->Html->script('base.js') ?>
</body>
</html>
