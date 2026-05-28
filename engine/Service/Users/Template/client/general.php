<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<table class="table">
    <tr>
        <th colspan="2">
            <h3>Профиль пользователя</h3>
        </th>
    </tr>
    <tr>
        <td>Логин</td>
        <td><?= $user->login; ?></td>
    </tr>
    <tr>
        <td>E-mail</td>
        <td><?= $user->email; ?></td>
    </tr>
    <tr>
        <td>Пароль</td>
        <td>
            <button onclick="$('#i_pass_form').toggle();" class="btn btn-default">Изменить</button>
        </td>
    </tr>
    <tr>
        <td>Показывать задания возрастного ограничения</td>
        <td>
            <form method="post">
                <input type="hidden" name="action" value="age_limits"/>
                <div class="input-group">
                    <select class="form-control" name="age_limits">
                        <option value="0"<?php if ($user->age_limits == 0): ?> selected="selected"<?php endif; ?>>-- Укажите
                            желаемый фильтр --
                        </option>
                        <option value="1"<?php if ($user->age_limits == 1): ?> selected="selected"<?php endif; ?>>0+</option>
                        <option value="2"<?php if ($user->age_limits == 2): ?> selected="selected"<?php endif; ?>>16+</option>
                        <option value="3"<?php if ($user->age_limits == 3): ?> selected="selected"<?php endif; ?>>18+</option>
                    </select>
                    <div class="input-group-btn">
                        <button class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        </td>
    </tr>
    <tr id="i_pass_form" <?php if ($vars['action'] != 'password'): ?>style="display: none;"<?php endif; ?>>
        <td></td>
        <td>
            <form class="form-horizontal" method="post">
                <input type="hidden" name="action" value="password"/>
                <?php if ($vars['action'] == 'password' && count($vars['errors'])): ?>
                    <div class="alert alert-danger"><?= implode('<br />', $vars['errors']); ?></div>
                <?php endif; ?>
                <?php if ($vars['action'] == 'password' && $vars['success']): ?>
                    <div class="alert alert-success">Пароль успешно изменен</div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="control-label col-sm-3">Старый пароль</label>
                    <div class="col-sm-6">
                        <input class="form-control" name="passwordOld" value="" type="password"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Новый пароль</label>
                    <div class="col-sm-6">
                        <input class="form-control" name="passwordNew" value="" type="password"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Подтверждение</label>
                    <div class="col-sm-6">
                        <input class="form-control" name="passwordConfirm" value="" type="password"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">&nbsp;</label>
                    <div class="col-sm-6">
                        <button class="button-green">Изменить</button>
                    </div>
                </div>
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <h3>Данные профиля ВК</h3>
        </td>
    </tr>
    <tr>
        <td>Имя в ВК</td>
        <td><?= $user->name; ?></td>
    </tr>
    <tr>
        <td>Профиль ВК</td>
        <td><a target="_blank" href="https://vk.com/id<?= $user->uid; ?>">https://vk.com/id<?= $user->uid; ?></td>
    </tr>
    <tr>
        <td colspan="2">
            <?php if ($vars['success'] === true && $vars['action'] == 'user'): ?>
                <div class="alert alert-success">
                    <h2>Настройки успешно сохранены</h2>
                </div>
            <?php endif; ?>
            <form method="post" class="form-horizontal">
                <input type="hidden" name="action" value="user"/>
                <div class="form-group">
                    <label class="col-sm-12 control-label" style="text-align: left;">Укажите токен</label>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <input name="access_token" class="form-control" id="i_user_token"
                                   value="<?= $user->access_token; ?>"/>
                            <a class="input-group-addon btn btn-primary" href="<?= VK_TOKEN_URL; ?>" target="_blank">
                                Получить токен
                            </a>
                        </div>
                    </div>
                </div>
                <?php if (!$user->access_token): ?>
                    <div class="alert alert-info">
                        После нажатия кнопки "Получить токен", скопируйте целиком ссылку из открывшейся вкладки,
                        вставьте её в поле выше, и нажмите "Сохранить".
                        Не переживайте из-за находящегося во вкладке предупреждения, токен нужен только для полноценной
                        работы всех функций сервиса.
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        </td>
    </tr>
</table>