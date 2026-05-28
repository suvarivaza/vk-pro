<h3>
    Цены на сервисы
</h3>
<form method="post">

    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Наименование</th>
            <th>Цена</th>
            <th>Лимит групп</th>
            <th>Цена на дополнительную группу</th>
        </tr>
        <?php foreach ($vars['settings'] as $key => $service): ?>
            <tr>
                <td>
                    <?= $service['title']; ?>
                    <input type="hidden" name="<?= $key; ?>[title]" value="<?= $service['title']; ?>"/>
                </td>
                <td>
                    <input class="form-control" name="<?= $key; ?>[price]" value="<?= $service['price']; ?>"/>
                </td>
                <td>
                    <input class="form-control" name="<?= $key; ?>[limit]" value="<?= $service['limit']; ?>"/>
                </td>
                <td>
                    <input class="form-control" name="<?= $key; ?>[group]" value="<?= $service['group']; ?>"/>
                </td>
                <td>
                    <input class="form-control" name="<?= $key; ?>[free]" value="<?= $service['free']; ?>"/>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
</form>