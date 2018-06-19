<?php

$this->Html->css('fullcalendar.css', ['block' => 'styleTop']);
$this->Html->css('calendar.css', ['block' => 'styleTop']);
$this->Html->script('parsley.min.js', ['block' => 'scriptBottom']);
$this->Html->script('moment-with-locales.min.js', ['block' => 'scriptBottom']);
$this->Html->script('fullcalendar.js', ['block' => 'scriptBottom']);
$this->Html->script('calendar.js', ['block' => 'scriptBottom']);


?>

<?php $this->start('content-header'); ?>
<h1><?= __('Calendar') ?></h1>
<ol class="breadcrumb">
    <li>
        <?= $this->Html->link(
            '<i class="fa fa-home"></i> Home',
            '/',
            ['escape' => false]) ?>
    </li>
    <li class="active">Calendar</li>
</ol>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">
            <div class="box-body no-padding">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div id="event-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="myModalLabel">New Calendar Entry</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-xs-12">
                <?= $this->Form->create(null, [
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'event-form',
                    'url' => ['controller' => 'Events', 'action' => 'add'],
                    'data-parsley-validate' => '',
                    'templates' => [
                        'inputContainer' => '{{content}}'
                        ]
                    ]) ?>
                    <?= $this->Form->hidden('all_day') ?>
                    <?= $this->Form->hidden('color') ?>

                    <div class="hidden">
                        <?= $this->Form->control('start', [
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            ])?>
                        <?= $this->Form->control('end', [
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            ])?>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="class"><?= __('Color') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                <ul class="fc-color-picker" id="color-chooser">
                                    <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-magenta" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title"><?= __('Title') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12 input-group">
                            <?= $this->Form->control('title', [
                                'label' => false, 
                                'class' => 'form-control col-md-7 col-xs-12', 
                                'required' => true
                                ]) ?>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="title-color">Color</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description"><?= __('Description') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('description', [
                                'label' => false, 
                                'type' => 'textarea',
                                'rows' => 3,
                                'class' => 'form-control col-md-7 col-xs-12', 
                                ]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="scope"><?= __('Scope') ?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $this->Form->control('scope', [
                                'options' => $eventScope, 
                                'required' => true,
                                'label' => false,
                                'empty' => true,
                                'data-parsley-errors-container' => '#error-event-scope',
                                'data-parsley-class-handler' => '#select2-scope',
                                'class' => 'form-control col-md-7 col-xs-12 select2-theme'
                                ]) ?>
                            <span id="error-event-scope"></span>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submit-event-btn">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="close-event-modal-btn">Close</button>
            </div>
        </div>
    </div>
</div>
