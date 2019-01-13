<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AfterPlan $afterPlan
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit After Plan'), ['action' => 'edit', $afterPlan->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete After Plan'), ['action' => 'delete', $afterPlan->id], ['confirm' => __('Are you sure you want to delete # {0}?', $afterPlan->id)]) ?> </li>
        <li><?= $this->Html->link(__('List After Plans'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New After Plan'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="afterPlans view large-9 medium-8 columns content">
    <h3><?= h($afterPlan->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($afterPlan->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name Jp') ?></th>
            <td><?= h($afterPlan->name_jp) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($afterPlan->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created By') ?></th>
            <td><?= $this->Number->format($afterPlan->created_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified By') ?></th>
            <td><?= $this->Number->format($afterPlan->modified_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($afterPlan->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($afterPlan->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Del Flag') ?></th>
            <td><?= $afterPlan->del_flag ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
