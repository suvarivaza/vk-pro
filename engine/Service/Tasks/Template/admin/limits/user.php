<h2>
    Лимиты на пользователя
    <small class="pull-right">
        <a class="btn btn-primary" href="/admin/tasks/limits">Лимиты на выполнения заданий</a>
        <a class="btn btn-default" href="/admin/tasks">Все задания</a>
    </small>
</h2>
<form method="post" class="form-horizontal">
    <input type="hidden" name="action" value="limits"/>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th colspan="3"><h4>Лимиты на вступление в группу</h4></th>
        </tr>
        <tr>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[join][day]"
                           value="<?= $vars['limits']['user']['join']['day'] ?? ''; ?>">
                    <span class="input-group-addon">в сутки</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[join][hour]"
                           value="<?= $vars['limits']['user']['join']['hour'] ?? ''; ?>">
                    <span class="input-group-addon">в час</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[join][interval]"
                           value="<?= $vars['limits']['user']['join']['interval'] ?? ''; ?>">
                    <span class="input-group-addon">в 10 минут</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[join][comment]"
                       value="<?= $vars['limits']['user']['join']['comment'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении 10 минутного лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[join][comment_interval]"
                       value="<?= $vars['limits']['user']['join']['comment_interval'] ?? ''; ?>">
            </td>
        </tr>

        <tr>
            <th colspan="3"><h4>Лимиты на вступление в друзья</h4></th>
        </tr>
        <tr>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[friends][day]"
                           value="<?= $vars['limits']['user']['friends']['day'] ?? ''; ?>">
                    <span class="input-group-addon">в сутки</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[friends][hour]"
                           value="<?= $vars['limits']['user']['friends']['hour'] ?? ''; ?>">
                    <span class="input-group-addon">в час</span>
                </div>
            </td>
            <td>
                <div class="input-group">

                    <input class="form-control" name="user[friends][interval]"
                           value="<?= $vars['limits']['user']['friends']['interval'] ?? ''; ?>">
                    <span class="input-group-addon">в 10 минут</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[friends][comment]"
                       value="<?= $vars['limits']['user']['friends']['comment'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении 10 минутного лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[friends][comment_interval]"
                       value="<?= $vars['limits']['user']['friends']['comment_interval'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <th colspan="3"><h4>Лимиты на лайки по заданиям</h4></th>
        </tr>
        <tr>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[likes][day]"
                           value="<?= $vars['limits']['user']['likes']['day'] ?? ''; ?>">
                    <span class="input-group-addon">в сутки</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[likes][hour]"
                           value="<?= $vars['limits']['user']['likes']['hour'] ?? ''; ?>">
                    <span class="input-group-addon">в час</span>
                </div>
            </td>
            <td>
                <div class="input-group">

                    <input class="form-control" name="user[likes][interval]"
                           value="<?= $vars['limits']['user']['likes']['interval'] ?? ''; ?>">
                    <span class="input-group-addon">в 10 минут</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[likes][comment]"
                       value="<?= $vars['limits']['user']['likes']['comment'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении 10 минутного лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[likes][comment_interval]"
                       value="<?= $vars['limits']['user']['likes']['comment_interval'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <th colspan="3"><h4>Лимиты на репосты по заданиям</h4></th>
        </tr>
        <tr>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[reposts][day]"
                           value="<?= $vars['limits']['user']['reposts']['day'] ?? ''; ?>">
                    <span class="input-group-addon">в сутки</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[reposts][hour]"
                           value="<?= $vars['limits']['user']['reposts']['hour'] ?? ''; ?>">
                    <span class="input-group-addon">в час</span>
                </div>
            </td>
            <td>
                <div class="input-group">

                    <input class="form-control" name="user[reposts][interval]"
                           value="<?= $vars['limits']['user']['reposts']['interval'] ?? ''; ?>">
                    <span class="input-group-addon">в 10 минут</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[reposts][comment]"
                       value="<?= $vars['limits']['user']['reposts']['comment'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении 10 минутного лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[reposts][comment_interval]"
                       value="<?= $vars['limits']['user']['reposts']['comment_interval'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <th colspan="3"><h4>Лимиты на комментарии по заданиям</h4></th>
        </tr>
        <tr>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[comments][day]"
                           value="<?= $vars['limits']['user']['comments']['day'] ?? ''; ?>">
                    <span class="input-group-addon">в сутки</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[comments][hour]"
                           value="<?= $vars['limits']['user']['comments']['hour'] ?? ''; ?>">
                    <span class="input-group-addon">в час</span>
                </div>
            </td>
            <td>
                <div class="input-group">

                    <input class="form-control" name="user[comments][interval]"
                           value="<?= $vars['limits']['user']['comments']['interval'] ?? ''; ?>">
                    <span class="input-group-addon">в 10 минут</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[comments][comment]"
                       value="<?= $vars['limits']['user']['comments']['comment'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении 10 минутного лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[comments][comment_interval]"
                       value="<?= $vars['limits']['user']['comments']['comment_interval'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <th colspan="3"><h4>Лимиты на Опросы по заданиям</h4></th>
        </tr>
        <tr>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[polls][day]"
                           value="<?= $vars['limits']['user']['polls']['day'] ?? ''; ?>">
                    <span class="input-group-addon">в сутки</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="user[polls][hour]"
                           value="<?= $vars['limits']['user']['polls']['hour'] ?? ''; ?>">
                    <span class="input-group-addon">в час</span>
                </div>
            </td>
            <td>
                <div class="input-group">

                    <input class="form-control" name="user[polls][interval]"
                           value="<?= $vars['limits']['user']['polls']['interval'] ?? ''; ?>">
                    <span class="input-group-addon">в 10 минут</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[polls][comment]"
                       value="<?= $vars['limits']['user']['polls']['comment'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Комментарий при достижении 10 минутного лимита
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input id="i-user-join-comment" class="form-control" name="user[polls][comment_interval]"
                       value="<?= $vars['limits']['user']['polls']['comment_interval'] ?? ''; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            </td>
        </tr>

    </table>
</form>