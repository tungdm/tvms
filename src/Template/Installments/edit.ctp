<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Installment $installment
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $installment->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $installment->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Installments'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Guilds'), ['controller' => 'Guilds', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Guild'), ['controller' => 'Guilds', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="installments form large-9 medium-8 columns content">
    <?= $this->Form->create($installment) ?>
    <fieldset>
        <legend><?= __('Edit Installment') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('created_by');
            echo $this->Form->control('updated_by');
            echo $this->Form->control('guilds._ids', ['options' => $guilds]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
