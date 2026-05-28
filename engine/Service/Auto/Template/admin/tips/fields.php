<h3>
    Подсказки для полей ввода
</h3>
<form method="post">

    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Наименование</th>
            <th>Время</th>
            <th>Подсказка</th>
        </tr>
        <tr>
            <td style="width: 200px;">
                Пользователям с уровнем кармы:
            </td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="minKarma-time" value="<?= $vars['tips']['minKarma-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="minKarma-text" value="<?= $vars['tips']['minKarma-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">
                Хочу, что бы задание выполняли(пол):
            </td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="sex-time" value="<?= $vars['tips']['sex-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="sex-text" value="<?= $vars['tips']['sex-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">
                Возраст:
            </td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="age-time" value="<?= $vars['tips']['age-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="age-text" value="<?= $vars['tips']['age-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">
                Город:
            </td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="city-time" value="<?= $vars['tips']['city-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="city-text" value="<?= $vars['tips']['city-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Семеное положение:</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="relation-time" value="<?= $vars['tips']['relation-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="relation-text" value="<?= $vars['tips']['relation-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Количество аватарок:</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="avatarCount-time" value="<?= $vars['tips']['avatarCount-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="avatarCount-text" value="<?= $vars['tips']['avatarCount-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Заполненность странички:</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="filled-time" value="<?= $vars['tips']['filled-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="filled-text" value="<?= $vars['tips']['filled-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Возраст странички :</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="pageAge-time" value="<?= $vars['tips']['pageAge-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="pageAge-text" value="<?= $vars['tips']['pageAge-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Количество друзей и подписчиков:</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="followersCount-time" value="<?= $vars['tips']['followersCount-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="followersCount-text" value="<?= $vars['tips']['followersCount-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Количество интересных страниц:</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="interestingPage-time" value="<?= $vars['tips']['interestingPage-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="interestingPage-text" value="<?= $vars['tips']['interestingPage-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Частота постов на стене:</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="frequencyPost-time" value="<?= $vars['tips']['frequencyPost-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="frequencyPost-text" value="<?= $vars['tips']['frequencyPost-text']; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 200px;">Моё задание в начале очереди</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="prior-time" value="<?= $vars['tips']['prior-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="prior-text" value="<?= $vars['tips']['prior-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Только от имени сообщества</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="fromGroupOnly-time" value="<?= $vars['tips']['fromGroupOnly-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="fromGroupOnly-text" value="<?= $vars['tips']['fromGroupOnly-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Только содержащие</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="attachmentType-time" value="<?= $vars['tips']['attachmentType-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="attachmentType-text" value="<?= $vars['tips']['attachmentType-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Исключать рекламу</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="adsOut-time" value="<?= $vars['tips']['adsOut-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="adsOut-text" value="<?= $vars['tips']['adsOut-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Спецзадание</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="specialId-time" value="<?= $vars['tips']['specialId-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="specialId-text" value="<?= $vars['tips']['specialId-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Количество выполнений на пост</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="counts-time" value="<?= $vars['tips']['counts-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="counts-text" value="<?= $vars['tips']['counts-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Лимит баллов для шаблона</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="balanceLimit-time" value="<?= $vars['tips']['balanceLimit-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="balanceLimit-text" value="<?= $vars['tips']['balanceLimit-text']; ?>" />
            </td>
        </tr>

        <tr>
            <td style="width: 200px;">Название шаблона</td>
            <td style="width: 200px;">
                <div class="input-group">
                    <input class="form-control" type="text" name="title-time" value="<?= $vars['tips']['title-time']; ?>" />
                    <span class="input-group-addon">msec</span>
                </div>
            </td>
            <td>
                <input class="form-control" type="text" name="title-text" value="<?= $vars['tips']['title-text']; ?>" />
            </td>
        </tr>
    </table>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
</form>