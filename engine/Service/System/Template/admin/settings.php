<div style="float: right">
    <a class="btn btn-primary" href="/admin/system/settings/sitemap">sitemap.xml</a>
    <a class="btn btn-primary" href="/admin/system/settings/robot">robot.txt</a>
</div>
<h2>Настройки</h2>
<form class="form-horizontal" role="form" method="post">
    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-6 bg-danger">
                <?= implode('<br />', $vars['errors']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label class="col-sm-2 control-label">Секретный ключ</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="secret" placeholder="Укажите ключ" maxlength="255"
                    value="<?= $vars['settings']['secret']; ?>"
                    data-toggle="tooltip" data-placement="right" title="ключ"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Сервисный токен</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="service" placeholder="Укажите токен" maxlength="255"
                    value="<?= $vars['settings']['service']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Токен"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Токен для проверок</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="token" placeholder="Укажите токен" maxlength="255"
                    value="<?= $vars['settings']['token']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Токен"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Токен 2 для проверок</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="token2" placeholder="Укажите токен" maxlength="255"
                    value="<?= $vars['settings']['token2']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Токен"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Токен 3 для проверок</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="token3" placeholder="Укажите токен" maxlength="255"
                    value="<?= $vars['settings']['token3']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Токен"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Токен 4 для проверок</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="token4" placeholder="Укажите токен" maxlength="255"
                    value="<?= $vars['settings']['token4']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Токен"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Токен 5 для проверок</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="token5" placeholder="Укажите токен" maxlength="255"
                    value="<?= $vars['settings']['token5']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Токен"
            />
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-2">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="reset" class="btn btn-danger">Очистить</button>
        </div>
    </div>
</form>