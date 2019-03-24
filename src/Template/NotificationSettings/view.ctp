<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\NotificationSetting $notificationSetting
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Notification Setting'), ['action' => 'edit', $notificationSetting->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Notification Setting'), ['action' => 'delete', $notificationSetting->id], ['confirm' => __('Are you sure you want to delete # {0}?', $notificationSetting->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Notification Settings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Notification Setting'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Created By Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Created By User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Modified By Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Modified By User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="notificationSettings view large-9 medium-8 columns content">
    <h3><?= h($notificationSetting->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($notificationSetting->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Exclude') ?></th>
            <td><?= h($notificationSetting->exclude) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created By User') ?></th>
            <td><?= $notificationSetting->has('created_by_user') ? $this->Html->link($notificationSetting->created_by_user->fullname, ['controller' => 'Users', 'action' => 'view', $notificationSetting->created_by_user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified By User') ?></th>
            <td><?= $notificationSetting->has('modified_by_user') ? $this->Html->link($notificationSetting->modified_by_user->fullname, ['controller' => 'Users', 'action' => 'view', $notificationSetting->modified_by_user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($notificationSetting->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Receivers Group') ?></th>
            <td><?= $this->Number->format($notificationSetting->receivers_group) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Send Before') ?></th>
            <td><?= $this->Number->format($notificationSetting->send_before) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($notificationSetting->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($notificationSetting->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Template') ?></h4>
        <?= $this->Text->autoParagraph(h($notificationSetting->template)); ?>
    </div>
</div>
