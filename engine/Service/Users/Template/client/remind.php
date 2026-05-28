<h2>Восстановление пароля</h2>

<?php if ($vars['success']): ?>
    Письмо с инструкциями для восстановления пароля было отправлено на указанный почтовый ящик.
<?php else: ?>
    <?php if (count($vars['errors'])): ?>
        <div class="errors">
            <div class="alert-icon"></div>
            <?= implode('<br />', $vars['errors']); ?>
        </div>
    <?php endif; ?>
    <form class="form-horizontal" method="post" action="">
        <input type="hidden" name="action" value="send"/>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Email</label>
            <div class="col-sm-4">
                <input class="form-control" name="email" value="<?= $vars['user']['email']; ?>"/>
            </div>
            <div class="col-sm-4"><input class="form-control btn btn-success" type="submit" value="Отправить на емайл"/>
            </div>
        </div>
    </form>
<?php endif; ?>