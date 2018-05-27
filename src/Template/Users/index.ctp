<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$controller = $this->request->getParam('controller');
$permission = $this->request->session()->read($controller);
$counter = 0;
$currentUser = $this->request->session()->read('Auth.User');
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('parsley.min.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);

$this->Paginator->setTemplates([
    'sort' => '<a href="{{url}}">{{text}} <i class="fa fa-sort"></i></a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-desc"></i></a></a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}} <i class="fa fa-sort-amount-asc"></i></a></a>',
]);
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
                    <li><a data-toggle="modal" data-target="#setting-modal" href="#">Settings</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="clearfix"></div>        
    </div>
    <div class="x_content table-responsive">
        <table class="table table-bordered custom-table">
            <thead>
                <tr>
                    <th scope="col" class="col-num"><?= __('No.') ?></th>
                    <th scope="col" class="usernameCol">
                        <?= $this->Paginator->sort('username', 'Tên đăng nhập')?>
                    </th>
                    <th scope="col" class="emailCol hidden">
                        <?= $this->Paginator->sort('email') ?>
                    </th>
                    <th scope="col" class="genderCol">
                        <?= $this->Paginator->sort('gender', 'Giới tính') ?>
                    </th>
                    <th scope="col" class="jobCol">
                        <?= $this->Paginator->sort('job_id', 'Nghề nghiệp') ?>
                    </th>
                    <th scope="col" class="phoneCol">
                        <?= $this->Paginator->sort('phone', 'Số điện thoại') ?>
                    </th>
                    <th scope="col" class="fullnameCol">
                        <?= $this->Paginator->sort('fullname', 'Họ và tên') ?>
                    </th>
                    <th scope="col" class="roleCol">
                        <?= $this->Paginator->sort('role_id', 'Chức vụ') ?>
                    </th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?= $this->Form->create(null, [
                        'url' => ['controller' => 'Users', 'action' => 'index'],
                        'type' => 'get',
                        'id' => 'filter-form'
                        ]) ?>
                    <td></td>
                    <td class="col-md-1 usernameCol" style="width: 12.499999995%;">
                        <?= $this->Form->control('username', [
                            'label' => false,
                            'class' => 'form-control col-md-7 col-xs-12',
                            'value' => $query['username'] ?? ''
                            ]) 
                        ?>
                    </td>
                    <td class="col-md-2 emailCol hidden">
                        <?= $this->Form->control('email', [
                            'label' => false, 
                            'type' => 'text',
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'value' => $query['email'] ?? ''
                            ])
                        ?>
                    </td>
                    <td class="col-md-1 genderCol" style="width: 12.499999995%;">
                        <?= $this->Form->control('gender', [
                            'options' => $gender, 
                            'empty' => true,
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'value' => $query['gender'] ?? ''
                            ])
                        ?>
                    </td>
                    <td class="col-md-1 jobCol" style="width: 12.499999995%;">
                        <?= $this->Form->control('job_id', [
                            'options' => $jobs, 
                            'empty' => true,
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'value' => $query['job_id'] ?? ''
                            ])
                        ?>
                    </td>
                    <td class="col-md-2 phoneCol">
                        <?= $this->Form->control('phone', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12', 
                            'value' => $query['phone'] ?? ''
                            ])
                        ?>
                    </td>
                    <td class="col-md-2 fullnameCol">
                        <?= $this->Form->control('fullname', [
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'value' => $query['fullname'] ?? ''
                            ]) 
                        ?>
                    </td>
                    <td class="col-md-1 roleCol" style="width: 12.499999995%;">
                        <?= $this->Form->control('role_id', [
                            'options' => $roles, 
                            'empty' => true, 
                            'label' => false, 
                            'class' => 'form-control col-md-7 col-xs-12',
                            'value' => $query['role_id'] ?? ''
                            ])
                        ?>
                    </td>
                    <td>
                        <?= $this->Form->button(__('<i class="fa fa-refresh"></i>'), ['class' => 'btn btn-default', 'type' => 'button', 'id' => 'filter-refresh-btn']) ?>
                        <?= $this->Form->button(__('<i class="fa fa-search"></i>'), ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                    </td>
                <?= $this->Form->end() ?>
                </tr>
                <?php if (($users)->isEmpty()): ?>
                <tr>
                    <td colspan="100" class="table-empty"><?= __('No data available') ?></td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <?php $counter++ ?>
                <tr>
                    <td class="cell"><?= h($counter) ?></td>
                    <td class="cell usernameCol"><?= h($user->username) ?></td>
                    <td class="cell emailCol hidden"><?= h($user->profile->email) ?></td>
                    <td class="cell genderCol"><?= h($gender[$user->profile->gender]) ?></td>
                    <td class="cell jobCol"><?= h($user->profile->job->job_name) ?></td>
                    <td class="cell phoneCol"><?= h($user->profile->phone) ?></td>
                    <td class="cell fullnameCol"><?= h($user->profile->fullname) ?></td>
                    <td class="cell roleCol"><?= h($user->role->name) ?></td>
                    
                    <td class="actions cell">
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
                <?php endif; ?>                
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
</div>

<script>
    $(document).ready(function() {
        $('.cell').on('mouseenter', function() {
            $(this).attr('id', 'current-cell');
            $(this).closest('tr').addClass('highlight');
            $(this).closest('table').find('.cell:nth-child(' + ($(this).index() + 1) + ')').addClass('highlight');
        });
        $('.cell').on('mouseout', function() {
            $(this).removeAttr('id');
            $(this).closest('tr').removeClass('highlight');
            $(this).closest('table').find('.cell:nth-child(' + ($(this).index() + 1) + ')').removeClass('highlight');
        });
    });
</script>


<div class="modal fade" id="setting-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'type' => 'get',
                    'id' => 'setting-form',
                    'class' => 'form-horizontal form-label-left',
                    ]) ?>
                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12 control-label" for="display_field"><?= __('Display field') ?> *</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="usernameCol" value checked> <?= __('Tên đăng nhập') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="emailCol" value> <?= __('Email') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="genderCol" value checked> <?= __('Gender') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="jobCol" value checked> <?= __('Nghề nghiệp') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="phoneCol" value checked> <?= __('Phone') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="fullnameCol" value checked> <?= __('Fullname') ?></label>
                        </div>
                        <div class="checkbox col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" name="roleCol" value checked> <?= __('Role') ?></label>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="settings-submit-btn">Submit</button>
                <button type="button" class="btn btn-default" id="setting-close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>