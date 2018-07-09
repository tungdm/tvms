<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Jclass $jclass
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $jclass->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $jclass->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Jclasses'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="jclasses form large-9 medium-8 columns content">
    <?= $this->Form->create($jclass) ?>
    <fieldset>
        <legend><?= __('Edit Jclass') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('start');
            echo $this->Form->control('num_students');
            echo $this->Form->control('current_lesson');
            echo $this->Form->control('created_by');
            echo $this->Form->control('modified_by');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
