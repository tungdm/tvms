<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student $student
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Student'), ['action' => 'edit', $student->human_id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Student'), ['action' => 'delete', $student->human_id], ['confirm' => __('Are you sure you want to delete # {0}?', $student->human_id)]) ?> </li>
        <li><?= $this->Html->link(__('List Students'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Student'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="students view large-9 medium-8 columns content">
    <h3><?= h($student->human_id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Fullname Kata') ?></th>
            <td><?= h($student->fullname_kata) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Religion') ?></th>
            <td><?= h($student->religion) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Blood Group') ?></th>
            <td><?= h($student->blood_group) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Preferred Hand') ?></th>
            <td><?= h($student->preferred_hand) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Educational Level') ?></th>
            <td><?= h($student->educational_level) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Nation') ?></th>
            <td><?= h($student->nation) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Expectation') ?></th>
            <td><?= h($student->expectation) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Human Id') ?></th>
            <td><?= $this->Number->format($student->human_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Height') ?></th>
            <td><?= $this->Number->format($student->height) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Weight') ?></th>
            <td><?= $this->Number->format($student->weight) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Presenter') ?></th>
            <td><?= $this->Number->format($student->presenter) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created By') ?></th>
            <td><?= $this->Number->format($student->created_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modifed By') ?></th>
            <td><?= $this->Number->format($student->modifed_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($student->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modifed') ?></th>
            <td><?= h($student->modifed) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Marrired') ?></th>
            <td><?= $student->is_marrired ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Lived In Japan') ?></th>
            <td><?= $student->is_lived_in_japan ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Work Experience') ?></h4>
        <?= $this->Text->autoParagraph(h($student->work_experience)); ?>
    </div>
    <div class="row">
        <h4><?= __('Purpose Before') ?></h4>
        <?= $this->Text->autoParagraph(h($student->purpose_before)); ?>
    </div>
    <div class="row">
        <h4><?= __('Purpose After') ?></h4>
        <?= $this->Text->autoParagraph(h($student->purpose_after)); ?>
    </div>
</div>
