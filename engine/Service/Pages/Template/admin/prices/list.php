<?php
/** @var \Service\Pages\Model_Prices_Price $l */
/** @var \System\App $app */
$app = $vars['app'];
?>
<h1>
    Список прайсов
    <small class="pull-right">
        <a class="btn btn-info" href="/admin/pages/prices/add">Добавить прайс</a>
    </small>
</h1>
<?php if (count($vars['errors'])): ?>
    <div class="errors">
        <?= implode('<br />', $vars['errors']); ?>
    </div>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <div style="text-align: right;">
        <input type="hidden" name="action" value="all"/>
        <input name="line" size="2" value="2"/>
        <input type="file" name="Price" style="display: inline-block;"/>
        <input class="btn btn-success" type="submit" value="Загрузить одним файлом"/>
    </div>
</form>
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="prices"/>
    <table class="table">
        <tr>
            <th>Наименование</th>
            <th>Видно на сайте</th>
            <th>Загрузка для пользователей</th>
            <th></th>
        </tr>
        <?php foreach ($vars['list'] as $l): ?>
            <tr>
                <td>
                    <div><strong><?= Lib_DateTime::SimplyDate($l->Date, '%d %F %Y'); ?></strong></div>
                    <a href="/admin/pages/prices/edit/<?= $l->PriceID; ?>"><h4><?= $l->Title; ?></h4></a>
                    <div><?= $l->Description; ?></div>
                </td>
                <td><input name="Line[<?= $l->Alias; ?>]" size="2" value="2"/>
                    <input type="file" name="Price[<?= $l->Alias; ?>]" style="display: inline-block;"/>
                </td>
                <td>
                    <input type="file" name="Download[<?= $l->Alias; ?>]"/>
                    <?php if (isset($app->settings['Download_' . $l->Alias])): ?>
                        <div>
                            <a href="/files/prices/<?= $app->settings['Download_' . $l->Alias]; ?>"><?= $app->settings['Download_' . $l->Alias]; ?></a>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn btn-default"
                       href="/admin/pages/prices/fields/<?= $l->PriceID; ?>">Поля</a><?php if (count($l->getFields()) == 0): ?>
                        <span style="color: red;">*</span><?php endif; ?>
                    <a class="btn btn-danger" href="?del=<?= $l->PriceID; ?>">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div style="text-align: right;"><input class="btn btn-success" type="submit" value="Применить"/></div>
</form>