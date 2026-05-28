<div class="pull-right">
    <form method="post" style="float: right;">
        <input type="hidden" name="action" value="generate"/>
        <button style="float: right;" class="btn btn-success" type="submit">Сгенерировать</button>
    </form>
</div>
<ul class="breadcrumb">
    <li><a href="/admin/system/settings"><strong>Настройки</strong></a></li>
    <li><?= $title; ?> <strong>sitemap.xml</strong></li>
</ul>

<form class="form-horizontal" role="form" method="post">
    <input name="action" value="save" type="hidden"/>
    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-4 alert alert-danger">
                <?= implode('<br />', $vars['errors']); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($vars['saved']): ?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-8 alert alert-success">
                Файл sitemap.xml успешно сохранен
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <div class="col-sm-12 ">
            <textarea class="form-control" name="sitemap" rows="30"><?= $vars['sitemap']; ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-primary">Сохранить</button>
            <a class="btn btn-danger" href="/admin/system/settings">Отмена</a>
        </div>
    </div>
</form>