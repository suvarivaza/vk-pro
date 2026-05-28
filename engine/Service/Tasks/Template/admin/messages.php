<h2>
    Лимиты на выполнения заданий
    <small class="pull-right">
        <a class="btn btn-primary" href="/admin/tasks/limits/user">Лимиты на пользователя</a>
        <a class="btn btn-default" href="/admin/tasks">Все задания</a>
    </small>
</h2>
<form method="post" class="form-horizontal">
    <input type="hidden" name="action" value="limits"/>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Лимиты на вступление в группу/заявки в друзья</th>
        </tr>
        <?php for ($i = 0; $i < 7; $i++): ?>
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">Для групп до </span>
                        <input class="form-control" name="groups[counts][<?= $i; ?>]"
                               value="<?= $vars['limits']['groups']['counts'][$i] ?? ''; ?>">
                        <span class="input-group-addon">подписчиков</span>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">Не более</span>
                        <input class="form-control" name="groups[limits][<?= $i; ?>]"
                               value="<?= $vars['limits']['groups']['limits'][$i] ?? ''; ?>">
                        <span class="input-group-addon">в сутки</span>
                    </div>
                </td>
            </tr>
        <?php endfor; ?>
        <tr>
            <td colspan="3">
                <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            </td>
        </tr>
    </table>
</form>