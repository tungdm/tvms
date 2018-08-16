<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.ctp');

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
<?php if ($error instanceof Error) : ?>
        <strong>Error in: </strong>
        <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')) :
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;
?>

<div class="col-md-12">
    <div class="col-middle">
        <div class="text-center">
            <h1 class="error-number">500</h1>
            <h2>Đã có lỗi xảy ra</h2>
            <p><?= __d('cake', 'Máy chủ đang có lỗi. Xin hãy thử lại sau.') ?></p>
            <div id="footer">
                <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Quay lại'), 'javascript:history.back()', ['escape' => false, 'class="go-back-link"']) ?>
            </div>
        </div>
    </div>
</div>

