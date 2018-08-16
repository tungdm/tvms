<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php
if (extension_loaded('xdebug')) :
    xdebug_print_function_stack();
endif;

$this->end();
endif;
?>

<div class="col-md-12">
    <div class="col-middle">
        <div class="text-center">
            <h1 class="error-number">404</h1>
            <h2>Trang không tồn tại</h2>
            <p><?= __d('cake', 'Địa chỉ muốn truy cập không tồn tại. Xin hãy thử lại sau.', "<strong>'{$url}'</strong>") ?></p>
            <div id="footer">
                <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Quay lại'), 'javascript:history.back()', ['escape' => false, 'class="go-back-link"']) ?>
            </div>
        </div>
    </div>
</div>

