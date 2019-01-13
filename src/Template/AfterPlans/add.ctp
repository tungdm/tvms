<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AfterPlan $afterPlan
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List After Plans'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="afterPlans form large-9 medium-8 columns content">
    <?= $this->Form->create($afterPlan) ?>
    <fieldset>
        <legend><?= __('Add After Plan') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('name_jp');
            echo $this->Form->control('del_flag');
            echo $this->Form->control('created_by');
            echo $this->Form->control('modified_by');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
