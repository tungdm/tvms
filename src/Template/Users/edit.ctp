<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$scope = Configure::read('scope');
$confPermission = Configure::read('permission');

# Additional style + script
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('user.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('sweet-alert.js', ['block' => 'scriptBottom']);
$this->Html->script('parsley.min.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);
?>

<div class="x_panel">
    <div class="x_title">
        <h2><?= __('Edit User') ?></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <?= $this->Form->create($user, [
            'class' => 'form-horizontal form-label-left', 
            'data-parsley-validate' => '',
            'templates' => [
                'inputContainer' => '{{content}}'
                ]
            ]) 
        ?>
        <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Fullname') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.fullname', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'User\'s fullname']) ?>
            </div>
        </div>
        <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email"><?= __('Email') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.email', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="job"><?= __('Job') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.job_id', ['options' => $jobs, 'empty' => __('Choose one'), 'required' => true, 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Phone') ?></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.phone', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="ln_solid"></div>        
        <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="role"><?= __('Role') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('role_id', ['options' => $roles, 'empty' => __('Choose one'), 'required' => true, 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="item form-group table-responsive">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="permission"><?= __('Permission') ?></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <table class="table permission-table">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('Scope') ?></th>
                            <th scope="col"><?= __('Permission') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody id="permission-container">
                        <?php if (empty($user->permissions)): ?>
                        <button type="button" class="btn btn-primary btn-permission" id="add-permission-top"><?= __('Add new permission') ?></button>
                        <?php endif; ?>
                        <?php foreach ($user->permissions as $key => $permission): ?>
                        <tr class="row-permission">
                            <?= $this->Form->hidden('permissions.'  . $key . '.id', ['value' => $permission->id]) ?>
                            <td>
                                <?= $this->Form->control('permissions.' . $key . '.scope', [
                                    'options' => $scope,
                                    'empty' => __('Choose one'),
                                    'required' => true,
                                    'data-parsley-not-duplicate-scope' => '',
                                    'label' => false,
                                    'class' => 'select-scope form-control col-md-7 col-xs-12'])
                                ?>
                            </td>
                            <td>
                                <?= $this->Form->control('permissions.' . $key . '.action', [
                                    'options' => $confPermission,
                                    'empty' => __('Choose one'),
                                    'required' => true,
                                    'label' => false,
                                    'class' => 'form-control col-md-7 col-xs-12'])
                                ?>
                            </td>
                            <td class="action-btn">
                                <?= $this->Html->link(
                                    '<i class="fa fa-2x fa-remove"></i>',
                                    'javascript:;',
                                    ['escape' => false, 
                                    'class' => 'remove-permission',
                                    'onClick' => 'removePermission(this, true)'
                                    ]
                                    
                                )?>
                            </td>
                        </tr>
                        <?php endforeach; ?>                        
                    </tbody>
                </table>
                <?php if (!empty($user->permissions)): ?>                
                <button type="button" class="btn btn-primary btn-permission" id="add-variants-bottom"><?= __('Add new permission') ?></button>
                <?php endif; ?>
            </div>
        </div>
        <div class="ln_solid"></div>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>


<script id="permission-template" type="text/x-handlebars-template">
    <tr class="row-permission">
        <td>
            <?= $this->Form->control('{{scope}}', ['options' => $scope, 'empty' => __('Choose one'), 'data-parsley-not-duplicate-permission' => '', 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
        </td>
        <td>
            <?= $this->Form->control('{{permission}}', ['options' => $confPermission, 'empty' => __('Choose one'), 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>           
        </td>
        <td class="action-btn">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove"></i>',
                'javascript:;',
                ['escape' => false, 
                'class' => 'remove-permission',
                'onClick' => 'removePermission(this, false)'
                ]
            )?>
        </td>
    </tr>
</script>