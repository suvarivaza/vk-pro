<?php
$vars['bonus'] = $vars['bonus']['apply'] ?? $vars['bonus'];
?>

<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-bonus.png" width="30">
        <a href="/admin/users/bonus">Бонусы</a>
    </li>
</ul>
<form method="post">
    <input type="hidden" name="action" value="save">
    <table class="table table-hover table-condensed table-striped">
        <tr>
            <td><h6><strong>Бонус при регистрации</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][register]"
                           value="<?= $vars['bonus']['register']; ?>">
                    <span class="input-group-addon">баллов</span>
                </div>
            </td>
        </tr>
        <tr>
            <td><h6><strong>Первая покупка</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][buy]" value="<?= $vars['bonus']['buy']; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td><h6><strong>Ежедневный бонус за задание</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][day_one]" value="<?= $vars['bonus']['day_one']; ?>">
                    <span class="input-group-addon">баллов</span>
                </div>
            </td>
        </tr>
        <tr>
            <td><h6><strong>Ежедневный бонус</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][day]" value="<?= $vars['bonus']['day']; ?>">
                    <span class="input-group-addon">баллов</span>
                </div>
            </td>
        </tr>
        <tr>
            <td><h6><strong>Еженедельный бонус</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][week]" value="<?= $vars['bonus']['week']; ?>">
                    <span class="input-group-addon">баллов</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><h4><strong>Минимальное количество выполняемых заданий</strong></h4></td>
        </tr>

        <tr>
            <td><h6><strong>Лайки</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][likes]"
                           value="<?= $vars['bonus']['min']['likes']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Репосты</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][reposts]"
                           value="<?= $vars['bonus']['min']['reposts']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Комментарии</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][comments]"
                           value="<?= $vars['bonus']['min']['comments']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Пописка на группу</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][join]"
                           value="<?= $vars['bonus']['min']['join']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Заявки в друзья</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][friends]"
                           value="<?= $vars['bonus']['min']['friends']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Просмотры</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][views]"
                           value="<?= $vars['bonus']['min']['views']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Видео</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][video]"
                           value="<?= $vars['bonus']['min']['video']; ?>">
                    <span class="input-group-addon">штук</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><h6><strong>Голосования</strong></h6></td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="bonus[apply][min][polls]"
                           value="<?= $vars['bonus']['min']['polls']; ?>">
                    <span class="input-group-addon">штук</span>
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