<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Jtest $jtest
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Jtest'), ['action' => 'edit', $jtest->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Jtest'), ['action' => 'delete', $jtest->id], ['confirm' => __('Are you sure you want to delete # {0}?', $jtest->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Jtests'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Jtest'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Jclasses'), ['controller' => 'Jclasses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Jclass'), ['controller' => 'Jclasses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="jtests view large-9 medium-8 columns content">
    <h3><?= h($jtest->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Jclass') ?></th>
            <td><?= $jtest->has('jclass') ? $this->Html->link($jtest->jclass->name, ['controller' => 'Jclasses', 'action' => 'view', $jtest->jclass->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($jtest->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Lesson From') ?></th>
            <td><?= $this->Number->format($jtest->lesson_from) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Lesson To') ?></th>
            <td><?= $this->Number->format($jtest->lesson_to) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created By') ?></th>
            <td><?= $this->Number->format($jtest->created_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified By') ?></th>
            <td><?= $this->Number->format($jtest->modified_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Test Date') ?></th>
            <td><?= h($jtest->test_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($jtest->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($jtest->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Students') ?></h4>
        <?php if (!empty($jtest->students)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Fullname') ?></th>
                <th scope="col"><?= __('Fullname Kata') ?></th>
                <th scope="col"><?= __('Email') ?></th>
                <th scope="col"><?= __('Phone') ?></th>
                <th scope="col"><?= __('Gender') ?></th>
                <th scope="col"><?= __('Image') ?></th>
                <th scope="col"><?= __('Job Id') ?></th>
                <th scope="col"><?= __('Birthday') ?></th>
                <th scope="col"><?= __('Marital Status') ?></th>
                <th scope="col"><?= __('Subject') ?></th>
                <th scope="col"><?= __('Height') ?></th>
                <th scope="col"><?= __('Weight') ?></th>
                <th scope="col"><?= __('Religion') ?></th>
                <th scope="col"><?= __('Blood Group') ?></th>
                <th scope="col"><?= __('Preferred Hand') ?></th>
                <th scope="col"><?= __('Left Eye Sight') ?></th>
                <th scope="col"><?= __('Right Eye Sight') ?></th>
                <th scope="col"><?= __('Left Eye Sight Hospital') ?></th>
                <th scope="col"><?= __('Right Eye Sight Hospital') ?></th>
                <th scope="col"><?= __('Color Blind') ?></th>
                <th scope="col"><?= __('Educational Level') ?></th>
                <th scope="col"><?= __('Nation') ?></th>
                <th scope="col"><?= __('Country') ?></th>
                <th scope="col"><?= __('Presenter Id') ?></th>
                <th scope="col"><?= __('Is Lived In Japan') ?></th>
                <th scope="col"><?= __('Reject Stay') ?></th>
                <th scope="col"><?= __('Lived From') ?></th>
                <th scope="col"><?= __('Lived To') ?></th>
                <th scope="col"><?= __('Expectation') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col"><?= __('Enrolled Date') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Created By') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Modified By') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($jtest->students as $students): ?>
            <tr>
                <td><?= h($students->id) ?></td>
                <td><?= h($students->code) ?></td>
                <td><?= h($students->fullname) ?></td>
                <td><?= h($students->fullname_kata) ?></td>
                <td><?= h($students->email) ?></td>
                <td><?= h($students->phone) ?></td>
                <td><?= h($students->gender) ?></td>
                <td><?= h($students->image) ?></td>
                <td><?= h($students->job_id) ?></td>
                <td><?= h($students->birthday) ?></td>
                <td><?= h($students->marital_status) ?></td>
                <td><?= h($students->subject) ?></td>
                <td><?= h($students->height) ?></td>
                <td><?= h($students->weight) ?></td>
                <td><?= h($students->religion) ?></td>
                <td><?= h($students->blood_group) ?></td>
                <td><?= h($students->preferred_hand) ?></td>
                <td><?= h($students->left_eye_sight) ?></td>
                <td><?= h($students->right_eye_sight) ?></td>
                <td><?= h($students->left_eye_sight_hospital) ?></td>
                <td><?= h($students->right_eye_sight_hospital) ?></td>
                <td><?= h($students->color_blind) ?></td>
                <td><?= h($students->educational_level) ?></td>
                <td><?= h($students->nation) ?></td>
                <td><?= h($students->country) ?></td>
                <td><?= h($students->presenter_id) ?></td>
                <td><?= h($students->is_lived_in_japan) ?></td>
                <td><?= h($students->reject_stay) ?></td>
                <td><?= h($students->lived_from) ?></td>
                <td><?= h($students->lived_to) ?></td>
                <td><?= h($students->expectation) ?></td>
                <td><?= h($students->status) ?></td>
                <td><?= h($students->enrolled_date) ?></td>
                <td><?= h($students->created) ?></td>
                <td><?= h($students->created_by) ?></td>
                <td><?= h($students->modified) ?></td>
                <td><?= h($students->modified_by) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Students', 'action' => 'view', $students->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Students', 'action' => 'edit', $students->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Students', 'action' => 'delete', $students->id], ['confirm' => __('Are you sure you want to delete # {0}?', $students->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
