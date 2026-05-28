<?php
/** @var App $app */
use System\App;

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
                <img src="/img/logo.135.png"/>
            </div>
            <div class="col-sm-8 text-right c-header-center">
            </div>
            <div class="col-sm-2">
                <div id="i-login">
                    <div id="i-login-full"></div>
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
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="c-login-text">
                                        <?= $app->User->firstName; ?>
                                        <div class="karma">
                                            <div class="loadbar<?php if ($app->User->karma < 0): ?> minus<?php endif; ?>"
                                                 style="width: <?= abs($app->User->karma) > 100 ? 100 : abs($app->User->karma); ?>%;"></div>
                                            <div class="index">Карма <?= number_format(abs($app->User->karma), 1); ?>%
                                            </div>
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
                                <li><a href="/"><img src="/img/icons/32/icon-profile.png"/> Профиль</a></li>
                                <li><a href="/users/exit"><img src="/img/icons/32/icon-exit.png"/> Выход</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>
