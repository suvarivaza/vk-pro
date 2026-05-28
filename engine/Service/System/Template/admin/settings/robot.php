<ul class="breadcrumb">
    <li><a href="/admin/system/settings"><strong>Настройки</strong></a></li>
    <li><?= $title; ?> <strong>robot.txt</strong></li>
</ul>
<form class="form-horizontal" role="form" method="post">
    <input type="hidden" name="action" value="save"/>
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
                Файл <strong>robots.txt</strong> успешно сохранен
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <div class="col-sm-12 ">
            <textarea class="form-control" name="robot" rows="30"><?= $vars['robot']; ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-primary">Сохранить</button>
            <a class="btn btn-danger" href="/admin/system/settings">Отмена</a>
        </div>
    </div>
</form>