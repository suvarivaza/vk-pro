<h3>
    Подсказки для полей ввода
    <small class="pull-right">
        <a class="btn btn-default btn-sm" href="/admin/tasks/tips/menu">Подсказки для пунктов меню</a>
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
                Ссылка:
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="url-time"
                           value="<?= $vars['settings']['url-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="url-text" value="<?= $vars['settings']['url-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Пользователям с уровнем кармы:
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="minKarma-time"
                           value="<?= $vars['settings']['minKarma-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="minKarma-text"
                       value="<?= $vars['settings']['minKarma-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Хочу, что бы задание выполняли(пол):
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="sex-time"
                           value="<?= $vars['settings']['sex-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="sex-text" value="<?= $vars['settings']['sex-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Возраст:
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="age-time"
                           value="<?= $vars['settings']['age-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="age-text" value="<?= $vars['settings']['age-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">
                Город:
            </td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="city-time"
                           value="<?= $vars['settings']['city-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="city-text"
                       value="<?= $vars['settings']['city-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Семеное положение:</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="relation-time"
                           value="<?= $vars['settings']['relation-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="relation-text"
                       value="<?= $vars['settings']['relation-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Количество аватарок:</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="avatarCount-time"
                           value="<?= $vars['settings']['avatarCount-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="avatarCount-text"
                       value="<?= $vars['settings']['avatarCount-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Заполненность странички:</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="filled-time"
                           value="<?= $vars['settings']['filled-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="filled-text"
                       value="<?= $vars['settings']['filled-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Возраст странички :</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="pageAge-time"
                           value="<?= $vars['settings']['pageAge-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="pageAge-text"
                       value="<?= $vars['settings']['pageAge-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Количество друзей и подписчиков:</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="followersCount-time"
                           value="<?= $vars['settings']['followersCount-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="followersCount-text"
                       value="<?= $vars['settings']['followersCount-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Количество интересных страниц:</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="interestingPage-time"
                           value="<?= $vars['settings']['interestingPage-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="interestingPage-text"
                       value="<?= $vars['settings']['interestingPage-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Частота постов на стене:</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="frequencyPost-time"
                           value="<?= $vars['settings']['frequencyPost-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="frequencyPost-text"
                       value="<?= $vars['settings']['frequencyPost-text']; ?>"/>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;">Моё задание в начале очереди</td>
            <td style="width: 150px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="prior-time"
                           value="<?= $vars['settings']['prior-time']; ?>"/>
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="prior-text"
                       value="<?= $vars['settings']['prior-text']; ?>"/>
            </td>
        </tr>
    </table>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
</form>