<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Jtest $jtest
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $jtest->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $jtest->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Jtests'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Jclasses'), ['controller' => 'Jclasses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Jclass'), ['controller' => 'Jclasses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="jtests form large-9 medium-8 columns content">
    <?= $this->Form->create($jtest) ?>
    <fieldset>
        <legend><?= __('Edit Jtest') ?></legend>
        <?php
            echo $this->Form->control('jclass_id', ['options' => $jclasses]);
            echo $this->Form->control('test_date');
            echo $this->Form->control('lesson_from');
            echo $this->Form->control('lesson_to');
            echo $this->Form->control('created_by');
            echo $this->Form->control('modified_by');
            echo $this->Form->control('students._ids', ['options' => $students]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
