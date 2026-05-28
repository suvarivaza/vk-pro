<?php if ($vars['success'] === true): ?>
    <div class="alert alert-success">
        <h2>Настройки успешно сохранены</h2>
    </div>
<?php endif; ?>
<div class="alert alert-success text-center">
    <h6>Активация автоматического выполнения заданий позволит Вам не тратить время на ожидание и загрузку страниц
        VK.</h6>
    Баллы за задания будут начисляться как при обычном выполнении задания.
</div>
<form method="post" class="form-horizontal" id="i-bot-form">
    <input type="hidden" name="action" value="isBotActive"/>
    <div class="form-group">
        <label class="col-sm-12 control-label" style="text-align: left;">Укажите токен</label>
    </div>
    <div>
        <?= $vars['app']->settings['tip_token']; ?>
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
        <button onclick="ws.botSave();" type="button" class="button-green btn-block">Сохранить токен и активировать
            выполнение заданий
        </button>
    </div>
</form>