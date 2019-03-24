<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\NotificationSetting $notificationSetting
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $notificationSetting->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $notificationSetting->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Notification Settings'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Created By Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Created By User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="notificationSettings form large-9 medium-8 columns content">
    <?= $this->Form->create($notificationSetting) ?>
    <fieldset>
        <legend><?= __('Edit Notification Setting') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('template');
            echo $this->Form->control('receivers_group');
            echo $this->Form->control('exclude');
            echo $this->Form->control('send_before');
            echo $this->Form->control('created_by', ['options' => $createdByUsers, 'empty' => true]);
            echo $this->Form->control('modified_by', ['options' => $modifiedByUsers, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
