<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student[]|\Cake\Collection\CollectionInterface $students
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Student'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="students index large-9 medium-8 columns content">
    <h3><?= __('Students') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('human_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('fullname_kata') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_marrired') ?></th>
                <th scope="col"><?= $this->Paginator->sort('height') ?></th>
                <th scope="col"><?= $this->Paginator->sort('weight') ?></th>
                <th scope="col"><?= $this->Paginator->sort('religion') ?></th>
                <th scope="col"><?= $this->Paginator->sort('blood_group') ?></th>
                <th scope="col"><?= $this->Paginator->sort('preferred_hand') ?></th>
                <th scope="col"><?= $this->Paginator->sort('educational_level') ?></th>
                <th scope="col"><?= $this->Paginator->sort('nation') ?></th>
                <th scope="col"><?= $this->Paginator->sort('presenter') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_lived_in_japan') ?></th>
                <th scope="col"><?= $this->Paginator->sort('expectation') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created_by') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modifed') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modifed_by') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= $this->Number->format($student->human_id) ?></td>
                <td><?= h($student->fullname_kata) ?></td>
                <td><?= h($student->is_marrired) ?></td>
                <td><?= $this->Number->format($student->height) ?></td>
                <td><?= $this->Number->format($student->weight) ?></td>
                <td><?= h($student->religion) ?></td>
                <td><?= h($student->blood_group) ?></td>
                <td><?= h($student->preferred_hand) ?></td>
                <td><?= h($student->educational_level) ?></td>
                <td><?= h($student->nation) ?></td>
                <td><?= $this->Number->format($student->presenter) ?></td>
                <td><?= h($student->is_lived_in_japan) ?></td>
                <td><?= h($student->expectation) ?></td>
                <td><?= h($student->created) ?></td>
                <td><?= $this->Number->format($student->created_by) ?></td>
                <td><?= h($student->modifed) ?></td>
                <td><?= $this->Number->format($student->modifed_by) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $student->human_id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $student->human_id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $student->human_id], ['confirm' => __('Are you sure you want to delete # {0}?', $student->human_id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
