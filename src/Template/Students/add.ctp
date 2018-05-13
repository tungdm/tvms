<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student $student
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Students'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="students form large-9 medium-8 columns content">
    <?= $this->Form->create($student) ?>
    <fieldset>
        <legend><?= __('Add Student') ?></legend>
        <?php
            echo $this->Form->control('fullname_kata');
            echo $this->Form->control('is_marrired');
            echo $this->Form->control('height');
            echo $this->Form->control('weight');
            echo $this->Form->control('religion');
            echo $this->Form->control('blood_group');
            echo $this->Form->control('preferred_hand');
            echo $this->Form->control('educational_level');
            echo $this->Form->control('nation');
            echo $this->Form->control('presenter');
            echo $this->Form->control('work_experience');
            echo $this->Form->control('is_lived_in_japan');
            echo $this->Form->control('expectation');
            echo $this->Form->control('purpose_before');
            echo $this->Form->control('purpose_after');
            echo $this->Form->control('created_by');
            echo $this->Form->control('modifed', ['empty' => true]);
            echo $this->Form->control('modifed_by');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
