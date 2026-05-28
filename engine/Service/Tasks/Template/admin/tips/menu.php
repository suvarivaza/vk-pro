<h3>
    Подсказки для пунктов меню
    <small class="pull-right">
        <a class="btn btn-default btn-sm" href="/admin/tasks/tips/fields">Подсказки для полей ввода</a>
        <a class="btn btn-default btn-sm" href="/admin/tasks/tips/likes">Подсказки при добавлении</a>
    </small>
</h3>
<form method="post">

    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Наименование</th>
            <th>Время</th>
            <th>Подсказка</th>
        </tr>
        <tr>
            <td style="width: 150px;">
                Автоведение
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="auto_time"
                           value="<?= $vars['settings']['auto_time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="auto_text"
                       value="<?= $vars['settings']['auto_text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Автопостинг
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="posting_time"
                           value="<?= $vars['settings']['posting_time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="postiong_text"
                       value="<?= $vars['settings']['postiong_text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Граббер
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="grabber_time"
                           value="<?= $vars['settings']['grabber_time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="grabber_text"
                       value="<?= $vars['settings']['grabber_text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Спецзадания
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="special_time"
                           value="<?= $vars['settings']['special_time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="special_text"
                       value="<?= $vars['settings']['special_text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Автобот
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="bot_time"
                           value="<?= $vars['settings']['bot_time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="bot_text" value="<?= $vars['settings']['bot_text']; ?>"/>
            </td>
        </tr>
    </table>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
</form>