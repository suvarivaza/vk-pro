<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<?php if ($user->ban): ?>
    <div class="alert alert-danger">
        <h3>Ваша страничка Вконтакте была заблокирована.</h3>
        <p>Вероятно, вы проигнорировали предлагаемые нашим сервисом лимиты при совершении действий Вконтакте, и были
            заблокированы за подозрительную активность.</p>
        <p>Справедливо понимать, что никому не нужны заблокированные пользователи в подписчиках или участниках
            группы.</p>
        <p> Так же, лайки, репосты и комментарии от “собак” выглядят не привлекательно.</p>
        <p>Пожалуйста, соблюдайте предлагаемые нашим сервисом лимиты в будущем и не допускайте блокировки вашей страницы
            Вконтакте.</p>
    </div>
<?php elseif ($user->bad > 0): ?>
    <div class="alert alert-danger">
        <h3>Ваша страничка Вконтакте не проходит проверку качества.</h3>
        <p>Требования качетва, которые Вы не проходите:</p>
        <ul>
            <?php if ($user->bad & \Service\Users\Model_Config::BAD_AVATAR): ?>
                <li>Наличие аватара</li>
            <?php endif; ?>
            <?php if ($user->bad & \Service\Users\Model_Config::BAD_POSTS): ?>
                <li>Не менее 5-ти постов на стене</li>
            <?php endif; ?>
            <?php if ($user->bad & \Service\Users\Model_Config::BAD_FOLLOWERS): ?>
                <li>Не менее 3-х друзей</li>
            <?php endif; ?>
            <?php if ($user->bad & \Service\Users\Model_Config::BAD_AVATAR_COUNT): ?>
                <li>Не менее 5 фото</li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" class="form-horizontal">
    <input type="hidden" name="action" value="unban"/>
    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-4">
            <?php if (count($vars['errors'])): ?>
                <div class="alert alert-danger">
                    <?= implode('<br />', $vars['errors']); ?>
                </div>
            <?php else: ?>
                <button type="submit"
                        class="btn btn-success btn-block"><?= $user->bad ? 'Перепроверить' : 'Меня разбанили'; ?></button>
            <?php endif; ?>

        </div>
    </div>
</form>