<h1>
    Настройки штрафов
    <small class="pull-right">
        <a class="btn btn-default" href="/admin/users/karma">Карма</a>
        <a class="btn btn-primary" href="javascript:void(0)">Штрафы</a>
    </small>
</h1>
<form method="post">
    <input type="hidden" name="action" value="save"/>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th colspan="2" class="text-center"><h4>Начисление шрафов</h4></th>
        </tr>
        <?php foreach ($vars['types'] as $type => $title): ?>
            <tr>
                <td><h6><strong><?= $title; ?></strong></h6></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="penatly[<?= $type; ?>]"
                               value="<?= $vars['penatly']['penatly'][$type] ?? ''; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="2" class="text-center"><h4>Списание кармы за прогулы</h4></th>
        </tr>
        <tr>
            <td>
                <h6>
                    <strong>
                        За пропущенный день
                    </strong>
                </h6>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="penalty_day"
                           value="<?= $vars['penatly']['penalty_day'] ?? ''; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            </td>
        </tr>
    </table>
</form>