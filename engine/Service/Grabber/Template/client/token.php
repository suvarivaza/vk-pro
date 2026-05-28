<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<?php if ($vars['success'] === true): ?>
    <div class="alert alert-success">
        <h2>Настройки успешно сохранены</h2>
    </div>
<?php endif; ?>
<?php if (isset($vars['token_require']) && $vars['token_require']): ?>
    <div class="alert alert-warning text-center">
        <h2>ВНИМАНИЕ!</h2>
        Для продолжения работы сервиса необходимо обновить токен доступа!
    </div>
<?php endif; ?>
<form method="post" class="form-horizontal">
    <input type="hidden" name="action" value="access_token"/>
    <input type="hidden" name="isFree" value="true"/>
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
    <div class="alert alert-info">
        После нажатия кнопки "Получить токен", скопируйте целиком ссылку из открывшейся вкладки, вставьте её в поле
        выше, и нажмите "Сохранить".
        Не переживайте из-за находящегося во вкладке предупреждения, токен нужен только для полноценной работы всех
        функций сервиса.
    </div>
    <div class="text-right">
        <button onclick="grabber.saveClick();" type="button" class="button-green btn-block">Сохранить токен</button>
    </div>
</form>