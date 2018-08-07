<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page</title>
    <?= $this->Html->meta('icon', 'tvms.png') ?>
    <?= $this->Html->css('bootstrap/bootstrap.min.css') ?>
    <?= $this->Html->css('font-awesome/font-awesome.min.css') ?>
    <?= $this->Html->css('nprogress/nprogress.css') ?>
    <?= $this->Html->css('pnotify.custom.min.css') ?>
    <?= $this->Html->css('admin.css') ?>
    <?= $this->Html->script('jquery.min.js') ?>
</head>
<body class="login">
    <?= $this->Flash->render() ?>
    
    <div>
        <div class="login_wrapper">
            <div class="form login_form">
                <section class="login_content">
                    <?= $this->Form->create() ?>
                    <h1>TVMS</h1>
                    <?= $this->Form->control('username', ['label' => false, 'class' => 'form-control', 'placeholder' => 'Tên tài khoản']) ?>
                    <?= $this->Form->control('password', ['label' => false, 'class' => 'form-control', 'placeholder' => 'Password']) ?>
                    <div class="checkbox remember-me" style="text-align: right;">
                        <label>
                            <?= $this->Form->checkbox('remember_me', ['value' => 'true', 'hiddenField' => 'false']) ?>
                            <?= __('Ghi nhớ mật khẩu') ?>
                        </label>
                    </div>
                    <?= $this->Form->button('ĐĂNG NHẬP', ['class' => 'btn btn-default']) ?>
                    <?= $this->Form->end() ?>
                </section>
            </div>
        </div>
    </div>
    <?= $this->Html->script('pnotify.custom.min.js') ?>
</body>
</html>
