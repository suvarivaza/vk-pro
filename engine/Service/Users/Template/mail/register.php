<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<h1><?= $user->firstName; ?>, добро пожаловать!</h1>

<div>
    Вы зарегистрировались на сайте <a href="https://'.DOMAIN.'">'.DOMAIN.'</a>.
</div>

<div>
    Ваш пароль: <strong><?= $vars['password']; ?></strong>
</div>

<div>
    Для подтверждения почтового ящика перейдите по ссылке:
    <a href="https://<?= DOMAIN; ?>/users/confirm?token=<?= urlencode($user->token); ?>">Перейти</a>
</div>