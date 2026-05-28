<h1>
    Настройки кармы
    <small class="pull-right">
        <a class="btn btn-primary" href="javascript:void(0);">Карма</a>
        <a class="btn btn-default" href="/admin/users/penalty">Штрафы</a>
    </small>
</h1>
<form method="post">
    <input type="hidden" name="action" value="save"/>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th class="text-center"><h4>Начисление кармы</h4></th>
            <th class="text-center" style="vertical-align: middle;">При положительной карме</th>
            <th class="text-center" style="vertical-align: middle;">При отрицательной карме</th>
        </tr>
        <?php foreach ($vars['types'] as $type => $title): ?>
            <tr>
                <td><h6><strong><?= $title; ?></strong></h6></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="karma[<?= $type; ?>]"
                               value="<?= $vars['karma']['karma'][$type] ?? ''; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="karma[<?= $type; ?>_negative]"
                               value="<?= $vars['karma']['karma'][$type . '_negative'] ?? ''; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2">
                <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            </td>
        </tr>
    </table>
</form>