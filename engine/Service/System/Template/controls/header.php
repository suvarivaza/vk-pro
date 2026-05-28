<?php
/** @var \System\App $app */
$app = $vars['app'];
$photo = null;

if ($app->UserIsAuth()) {
    $photo = $app->User->getPhotos();
}
?>
<header>
    <div class="container">
        <div class="row">
            <div class="col-sm-2 text-center" style="padding: 20px;">
                <a href="/"><img src="/img/logo.new.135.png" width="135"/></a>
            </div>
            <div class="col-sm-8 text-right c-header-center">
                <?php if ($app->UserIsAuth()): ?>
                    <div class="balance">
                        Ваш баланс: <span id="i_header_balance"><?= number_format($app->User->balance, 1, '.',
                                ' '); ?></span> баллов
                    </div>
                    <a href="/orders/buy" class="button-green">
                        <div class="name">Пополнить баланс</div>
                        <div class="icon icon-balance"></div>
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-sm-2">
                <div id="i-login" <?php if (!$app->UserIsAuth()): ?>onclick="$('#i_form_login').modal('show');"
                     <?php else: ?>onclick="$('#i-login-div').toggle(); $('#i-login').toggleClass('active');"<?php endif; ?>>
                    <div style="position: absolute; width: 100%; height: 100%; z-index:10;"></div>
                    <?php if (!$app->UserIsAuth()): ?>
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="c-login-text">Вход</div>
                                </td>
                                <td style="width: 60px;" class="text-right">
                                    <div class="c-login-photo">
                                        <img class="img-circle" style="width: 60px;" src="/img/no-avatar.png"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <span class="c-head-arrow">
                            <img src="/img/icons/32/vk/head_arrow.png">
                        </span>
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="c-login-text">
                                        <?= $app->User->firstName; ?>
                                        <div class="karma">
                                            <div class="loadbar<?php if ($app->User->karma < 0): ?> minus<?php endif; ?>"
                                                 style="width: <?= abs($app->User->karma) > 100 ? 100 : abs($app->User->karma); ?>%;"></div>
                                            <div class="index">Карма <?= number_format($app->User->karma, 1); ?>%</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 60px;" class="text-right">
                                    <div class="c-login-photo">
                                        <?php if (isset($photo['small']['url'])): ?>
                                            <img class="img-circle" style="width: 50px;"
                                                 src="<?= $photo['small']['url']; ?>"/>
                                        <?php else: ?>
                                            <img class="img-circle" style="width: 60px;" src="/img/no-avatar.png"/>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div id="i-login-div">
                            <ul>
                                <?php if ($app->UserIsAuth() && $app->User->userType == \Service\Users\Model_Config::TYPE_ADMIN): ?>
                                    <li><a href="/admin" target="_blank"><img src="/img/icons/32/icon-accounts.png"/>
                                            Админка</a></li>
                                <?php endif; ?>
                                <li><a href="/users/general"><img src="/img/icons/32/icon-profile.png"/> Профиль</a>
                                </li>
                                <li><a href="/faq"><img src="/img/icons/32/icon-help.png"/> Помощь</a></li>
                                <li>
                                    <hr/>
                                </li>
                                <li><a href="/users/exit"><img src="/img/icons/32/icon-exit.png"/> Выход</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>
