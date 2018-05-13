<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Core\Configure;
?>

<div class="x_panel">
    <div class="x_title">
        <h2><?= __('User Profile') ?></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
            <div class="profile_img">
                <div id="crop-avatar">
                    <?php if (empty($this->request->session()->read('Auth.User.avatar'))): ?>
                        <?= $this->Html->image(Configure::read('noAvatar'), ['class' => 'img-responsive avatar-view']) ?>
                    <?php else: ?>
                        <?= $this->Html->image($this->request->session()->read('Auth.User.avatar'), ['class' => 'img-responsive avatar-view user-profile']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <div class="profile_title">
                <div class="col-md-6">
                    <h2>User Information</h2>
                </div>
            </div>
            <div class="columns content">
                <table class="vertical-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?= __('Username') ?></th>
                            <td><?= h($user->username) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Email') ?></th>
                            <td><?= h($user->email) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Phone') ?></th>
                            <td><?= h($user->phone) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Fullname') ?></th>
                            <td><?= h($user->fullname) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Address') ?></th>
                            <td><?= h($user->address) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

