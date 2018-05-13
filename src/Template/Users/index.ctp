<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller);
$counter = 0;
$currentUser = $this->request->session()->read('Auth.User');
?>
<div class="x_panel">
    <div class="x_title">
        <h2><?= __('Users') ?></h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            <li><?= $this->Html->link('<i class="fa fa-plus"></i>', ['action' => 'add'], ['escape' => false]) ?></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="clearfix"></div>        
    </div>
    <div class="x_content table-responsive">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th scope="col"><?= __('No.') ?></th>
                    <th scope="col"><?= __('Username') ?></th>
                    <th scope="col"><?= __('Email') ?></th>
                    <th scope="col"><?= __('Phone') ?></th>
                    <th scope="col"><?= __('Fullname') ?></th>
                    <th scope="col"><?= __('Role') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <?php $counter++ ?>
                <tr>
                    <td><?= h($counter) ?></td>
                    <td><?= h($user->username) ?></td>
                    <td><?= h($user->profile->email) ?></td>
                    <td><?= h($user->profile->phone) ?></td>
                    <td><?= h($user->profile->fullname) ?></td>
                    <td><?= h($user->role->name) ?></td>
                    
                    <td class="actions">
                        <!-- <?= $this->Html->link('<i class="fa fa-folder"></i> View ', ['action' => 'view', $user->id], ['escape' => false, 'class' => 'btn btn-primary btn-xs']) ?> -->
                        <?php if (($permission == 0 && $user->role->name != 'admin') || $user->id == $currentUser['id']): ?>
                        <?= $this->Html->link('<i class="fa fa-pencil"></i> Edit ', ['action' => 'edit', $user->id], ['escape' => false, 'class' => 'btn btn-info btn-xs']) ?>
                        <?php endif; ?>
                        
                        <?php if ( ($permission == 0 || $currentUser['role']['name'] == 'admin') && $user->role->name !== 'admin'): ?>
                        <?= $this->Form->postLink('<i class="fa fa-trash-o"></i> Delete ', ['action' => 'delete', $user->id], ['escape' => false, 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete {0}?', $user->username)]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
