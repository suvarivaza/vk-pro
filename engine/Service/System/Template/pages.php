<?php
/** @var \System\App $app */
$app = $vars['app'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?= $app->Title->Head; ?>
    <!-- Global Site Tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-107713177-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-107713177-1');
    </script>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-6581045320745946",
            enable_page_level_ads: true
        });
    </script>
</head>
<body>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-69611031-2"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-69611031-2');
</script>


<!-- Rating@Mail.ru counter -->
<!--<script type="text/javascript">-->
<!--    var _tmr = window._tmr || (window._tmr = []);-->
<!--    _tmr.push({id: "3069300", type: "pageView", start: (new Date()).getTime()});-->
<!--    (function (d, w, id) {-->
<!--        if (d.getElementById(id)) return;-->
<!--        var ts = d.createElement("script");-->
<!--        ts.type = "text/javascript";-->
<!--        ts.async = true;-->
<!--        ts.id = id;-->
<!--        ts.src = "https://top-fwz1.mail.ru/js/code.js";-->
<!--        var f = function () {-->
<!--            var s = d.getElementsByTagName("script")[0];-->
<!--            s.parentNode.insertBefore(ts, s);-->
<!--        };-->
<!--        if (w.opera == "[object Opera]") {-->
<!--            d.addEventListener("DOMContentLoaded", f, false);-->
<!--        } else {-->
<!--            f();-->
<!--        }-->
<!--    })(document, window, "topmailru-code");-->
<!--</script>-->
<!--<noscript>-->
<!--    <div>-->
<!--        <img src="https://top-fwz1.mail.ru/counter?id=3069300;js=na" style="border:0;position:absolute;left:-9999px;"-->
<!--             alt="Top.Mail.Ru"/>-->
<!--    </div>-->
<!--</noscript>-->
<!-- //Rating@Mail.ru counter -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript"> (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter71322906 = new Ya.Metrika({
                    id: 71322906,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true,
                    trackHash: true
                });
            } catch (e) {
            }
        });
        var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () {
            n.parentNode.insertBefore(s, n);
        };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";
        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks"); </script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/71322906" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript> <!-- /Yandex.Metrika counter -->
<header class="main-header">
    <nav id="mainNav" data-offset-top="5" class="affix-top">
        <div class="container">
            <a href="/" target="_self" title="Главная"><img class="main-logo" src="/img/logo.new.135.png" width="150"
                                                            alt="Бесплатная накрутка лайков, друзей и подписчиков в VK"/></a>
            <div class="pull-right">
                <button class="main-button main-button-top" onclick="login.form_login_show();">Регистрация/Вход</button>
            </div>
            <div class="text-center" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav nav-center">
                    <li>
                        <a class="page-scroll" href="/#i-main-protect">О сервисе</a>
                    </li>
                    <li>
                        <a class="page-scroll active" href="/autoposting">Автопостинг</a>
                    </li>
                    <li>
                        <a class="page-scroll active" href="/autovedenie">Автоведение</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="/grabber_vkontakte">Граббер</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="/nakrutka_vkontakte_bez_zadaniy">Автобот</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>
<div class="container" id="i-main-container">
    <?= $vars['html']; ?>
</div>
<?php if (\STPL::IsTemplate('pages/' . $app->page)) {
    \STPL::Display('pages/' . $app->page);
} ?>
<footer>
    <div>
        <a href="#" onclick="init.getPage('privacy_policy'); return false;">Политика конфиденциальности</a>
    </div>
    <div class="vk-pro-reg">
        Бесплатная накрутка лайков, друзей и подписчиков в VK ®  <?= date('Y') ?> vkPRO
    </div>
</footer>

<div class="modal fade" id="i_form_login" tabindex="-1" role="dialog" aria-labelledby="form_login"
     style="display: none">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #1d759b;">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal" aria-label="Close"
                        style="padding: 0 7px; font-size: 18px;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="i_form_login_label">
                    <img src="/img/logo.new.135.png" style="width: 30%;"/>
                </h4>
            </div>
            <div class="modal-body" id="i_form_login_container" style="background-color: #ffffff;">
                <form method="post" class="form-horizontal" role="form" id="i_form_login_form">
                    <input type="hidden" name="action" value="login"/>
                    <div id="i_login_container" style="display: none;">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="i_form_login_email" name="email" class="form-control form-span"
                                       placeholder="Логин или е-майл" type="text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="i_form_login_password" name="password" class="form-control form-span"
                                       value="" placeholder="Пароль" type="password"/>
                            </div>
                        </div>
                        <a class="pull-right" href="/users/remind" style="color: #808080; margin-top: -10px;">Забыли
                            пароль?</a>
                        <div class="form-group">
                            <div class="col-xs-12">

                            </div>
                        </div>
                    </div>
                    <div id="i_ulogin_container">
                        <div class="form-group text-center">
                            <div class="col-sm-12">
                                <h4>Что-бы начать работу с сайтом войдите, нажав кнопку:</h4>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 text-center">
                                <div id="uLogin_eb7899e9" data-uloginid="eb7899e9" data-ulogin="display=buttons"><img
                                            src="/img/vk-login.jpg" style="max-width: 100%; cursor: pointer;"
                                            data-uloginbutton="vkontakte"/></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 text-center">
                                Или
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 text-center">
                                <button type="button"
                                        onclick="$('#i_login_container').slideDown();$('#i_form_login_bottom').show();$('#i_ulogin_container').hide();"
                                        class="main-button main-button-top">Войти по логину и паролю
                                </button>
                            </div>
                        </div>
                        <div>
                            Нажимая кнопку «Войти через ВКонтакте», вы принимаете условия <a target="_blank"
                                                                                             href="/html?page=user_agreement">Пользовательского
                                соглашения</a>
                        </div>
                    </div>


                </form>
                <div id="i_form_login_data"></div>
                <div id="i_form_login_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
            </div>
            <div id="i_form_login_bottom" class="modal-footer" style="background-color: #ffffff; display: none;">
                <div>
                    Нажимая кнопку «Войти», вы принимаете условия <a target="_blank" href="/html?page=user_agreement">Пользовательского
                        соглашения</a>
                </div>
                <button id="i_form_login_button_login" type="button" class="button-green btn-block"
                        style="font-size: 18px;">Войти
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="page_dialog" tabindex="-1" role="dialog" aria-labelledby="privacy_policy"
     style="display: none">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="page_dialog_label"></h5>
            </div>
            <div class="modal-body" id="privacy_policy_container">
                <div id="page_dialog_data"></div>
                <div id="page_dialog_error"></div>
                <div id="page_dialog_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="page_dialog_cancel" type="button" class="btn btn-default" data-dismiss="modal">Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
<?php if ($app->UserIsAuth()): ?>
    <script>
        login.loggedin = true;
    </script>
<?php endif; ?>
</body>
</html>