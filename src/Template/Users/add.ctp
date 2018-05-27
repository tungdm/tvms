<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Core\Configure;
$gender = Configure::read('gender');
$scope = Configure::read('scope');
$permission = Configure::read('permission');

# Additional style + script
$this->Html->css('bootstrap-datetimepicker.min.css', ['block' => 'styleTop']);
$this->Html->css('switchery.min.css', ['block' => 'styleTop']);
$this->Html->css('user.css', ['block' => 'styleTop']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('switchery.min.js', ['block' => 'scriptBottom']);
$this->Html->script('handlebars-v4.0.11.js', ['block' => 'scriptBottom']);
$this->Html->script('bootstrap-datetimepicker.min.js', ['block' => 'scriptBottom']);
$this->Html->script('parsley.min.js', ['block' => 'scriptBottom']);
$this->Html->script('user.js', ['block' => 'scriptBottom']);
?>
<div class="x_panel">
    <div class="x_title">
        <h2><?= __('Add User') ?></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <?= $this->Form->create($user, [
            'class' => 'form-horizontal form-label-left', 
            'id' => 'create-user-form', 
            'data-parsley-validate' => '',
            'templates' => [
                'inputContainer' => '{{content}}'
                ]
            ]) 
        ?>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="username"><?= __('Username') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('username', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'User\'s name']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password"><?= __('Password') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('password', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'User\'s password']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fullname"><?= __('Fullname') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.fullname', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'both name(s) e.g Jon Doe']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email"><?= __('Email') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.email', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'example@email.com']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="job"><?= __('Job') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.job_id', ['options' => $jobs, 'empty' => __('Choose one'), 'required' => true, 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gender"><?= __('Gender') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.gender', ['options' => $gender, 'empty' => __('Choose one'), 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone"><?= __('Phone') ?></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('profile.phone', ['label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthday"><?= __('Birthday') ?></label>
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="input-group date" id="user-birthday">
                    <?= $this->Form->control('profile.birthday', [
                        'type' => 'text',
                        'label' => false, 
                        'class' => 'form-control'
                        ]) 
                    ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="ln_solid"></div>
        
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="role"><?= __('Role') ?> *</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?= $this->Form->control('role_id', ['options' => $roles, 'empty' => __('Choose one'), 'required' => true, 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
            </div>
        </div>
        <div class="form-group permission-group table-responsive">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="permission"><?= __('Permission') ?></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <button type="button" class="btn btn-primary btn-permission" id="add-permission-top"><?= __('Add new permission') ?></button>
                <!-- <div id="permission-container"></div> -->
                <table class="table permission-table">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('Scope') ?> *</th>
                            <th scope="col"><?= __('Permission') ?> *</th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody id="permission-container">
                        <!-- <tr>
                            <td>
                                <?= $this->Form->control('permissions.0.scope_num', ['options' => $scope, 'empty' => __('Choose one'), 'label' => false, 'class' => 'form-control col-md-7 col-xs-12']) ?>
                            </td>
                            <td>
                                <?= $this->Form->select('permissions.0.action', $permission, ['multiple' => 'checkbox', 'class' => 'js-switch']) ?>
                            </td>
                            <td class="action-btn">
                                <i class="fa fa-2x fa-remove"></i>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ln_solid"></div>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-success', 'type' => 'button', 'id' => 'create-user-btn']) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<script id="permission-template" type="text/x-handlebars-template">    
    <tr class="row-permission">
        <td>
            <?= $this->Form->control('{{scope}}', ['options' => $scope, 'empty' => __('Choose one'), 'required' => true, 'data-parsley-not-duplicate-scope' => '', 'label' => false, 'class' => 'select-scope form-control col-md-7 col-xs-12']) ?>
        </td>
        <td>
            <?= $this->Form->control('{{permission}}', ['options' => $permission, 'empty' => __('Choose one'), 'required' => true, 'label' => false, 'class' => 'select-action form-control col-md-7 col-xs-12']) ?>           
        </td>
        <td class="action-btn">
            <?= $this->Html->link(
                '<i class="fa fa-2x fa-remove"></i>',
                'javascript:;',
                ['escape' => false, 
                'class' => 'remove-permission',
                'onClick' => 'removePermission(this)'
                ]
                
            )?>
        </td>
    </tr>
</script>