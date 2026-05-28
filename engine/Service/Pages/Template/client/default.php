<h1><?= $vars['page']['title']; ?></h1>
<h2><?= $vars['page']['desc']; ?></h2>
<button class="main-button" onclick="login.form_login_show();">Войти прямо сейчас</button>

<?= $vars['page']['text']; ?>
<?php if ($vars['page']['alias'] != 'grabber_vkontakte'): ?>
    <div class="main-image text-center">
        <img class="main-image-vk" src="/img/main/vk.png">
        <img src="/img/main/browsers/browser.png" alt="накрутка просмотров vk, раскрутка групп вконтакте">
    </div>
<?php endif; ?>