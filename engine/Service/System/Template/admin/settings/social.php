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
        <label class="col-sm-2 control-label">Код Yandex.Metrika</label>
        <div class="col-sm-6">
            <input
                    class="form-control" name="yandex_metrika" placeholder="Yandex Metrika" maxlength="255"
                    value="<?= $vars['settings']['yandex_metrika']; ?>"
                    data-toggle="tooltip" data-placement="right" title="Код Yandex.Metrika"
            />
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="reset" class="btn btn-danger">Очистить</button>
        </div>
    </div>
</form>