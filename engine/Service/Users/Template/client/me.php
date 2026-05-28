<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<h2>Личный кабинет</h2>
<?php if ($vars['success'] === true): ?>
    <div class="alert alert-success">
        <h2>Настройки успешно сохранены</h2>
    </div>
<?php endif; ?>
<form method="post" class="form-horizontal">
    <input type="hidden" name="action" value="user"/>
    <div class="form-group">
        <label class="col-sm-12 control-label" style="text-align: left;">Укажите токен</label>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <div class="input-group">
                <input name="access_token" class="form-control" id="i_user_token" value="<?= $user->access_token; ?>"/>
                <a class="input-group-addon btn btn-primary" href="<?= VK_TOKEN_URL; ?>" target="_blank">
                    Получить токен
                </a>
            </div>

        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</form>