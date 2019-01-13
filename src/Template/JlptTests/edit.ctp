<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\JlptTest $jlptTest
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $jlptTest->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $jlptTest->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Jlpt Tests'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Jlpt Contents'), ['controller' => 'JlptContents', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Jlpt Content'), ['controller' => 'JlptContents', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="jlptTests form large-9 medium-8 columns content">
    <?= $this->Form->create($jlptTest) ?>
    <fieldset>
        <legend><?= __('Edit Jlpt Test') ?></legend>
        <?php
            echo $this->Form->control('test_date', ['empty' => true]);
            echo $this->Form->control('level');
            echo $this->Form->control('status');
            echo $this->Form->control('flag');
            echo $this->Form->control('created_by');
            echo $this->Form->control('modified_by');
            echo $this->Form->control('students._ids', ['options' => $students]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
