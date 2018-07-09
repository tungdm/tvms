<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Jclass $jclass
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Jclass'), ['action' => 'edit', $jclass->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Jclass'), ['action' => 'delete', $jclass->id], ['confirm' => __('Are you sure you want to delete # {0}?', $jclass->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Jclasses'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Jclass'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="jclasses view large-9 medium-8 columns content">
    <h3><?= h($jclass->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($jclass->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $jclass->has('user') ? $this->Html->link($jclass->user->username, ['controller' => 'Users', 'action' => 'view', $jclass->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($jclass->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Num Students') ?></th>
            <td><?= $this->Number->format($jclass->num_students) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Current Lesson') ?></th>
            <td><?= $this->Number->format($jclass->current_lesson) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created By') ?></th>
            <td><?= $this->Number->format($jclass->created_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified By') ?></th>
            <td><?= $this->Number->format($jclass->modified_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Start') ?></th>
            <td><?= h($jclass->start) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($jclass->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($jclass->modified) ?></td>
        </tr>
    </table>
</div>
