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
<script type="text/javascript">
    var _tmr = window._tmr || (window._tmr = []);
    _tmr.push({id: "3069300", type: "pageView", start: (new Date()).getTime()});
    (function (d, w, id) {
        if (d.getElementById(id)) return;
        var ts = d.createElement("script");
        ts.type = "text/javascript";
        ts.async = true;
        ts.id = id;
        ts.src = "https://top-fwz1.mail.ru/js/code.js";
        var f = function () {
            var s = d.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(ts, s);
        };
        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "topmailru-code");
</script>
<noscript>
    <div>
        <img src="https://top-fwz1.mail.ru/counter?id=3069300;js=na" style="border:0;position:absolute;left:-9999px;"
             alt="Top.Mail.Ru"/>
    </div>
</noscript>
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
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/71322906" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript> <!-- /Yandex.Metrika counter -->

<header class="main-header">
    <nav id="mainNav" data-spy="affix" data-offset-top="5" class="affix-top">
        <div class="container">
            <img class="main-logo" src="/img/logo.new.135.png" width="150"
                 alt="Бесплатная накрутка лайков, друзей и подписчиков в VK"/>
            <div class="pull-right">
                <button class="main-button main-button-top" onclick="login.form_login_show();">Регистрация/Вход</button>
            </div>
            <div class="text-center" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav nav-center">
                    <li>
                        <a class="page-scroll" href="#i-main-protect">О сервисе</a>
                    </li>
                    <li>
                        <a class="page-scroll active" href="/autoposting">Автопостинг</a>
                    </li>
                    <li>
                        <a class="page-scroll active" href="/autovedenie">Автоведение</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="javascript:void(0)"
                           onclick="init.getPage('user_agreement'); return false;">Правила</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <div class="container text-center">
        <h1 class="main-h1">Накрутка лайков и подписчиков вконтакте</h1>
        <h3 class="main-h3">Используя наш сервис можно бесплатно накрутить
            <a href="https://vk-pro.top/nakrutka_laikov_vkontakte" target="_self" title="Накрутка лайков вконтакте">лайки</a>,
            <a href="https://vk-pro.top/nakrutka_repostov_vkontakte" target="_self" title="Накрутка репостов вконтакте">репосты</a>,
            <a href="https://vk-pro.top/nakrutka_kommentariev_vkontakte" target="_self"
               title="Накрутка комментариев вконтакте">комментарии</a>,
            <a href="https://vk-pro.top/nakrutka_podpischikov_vkontakte" target="_self"
               title="Накрутка подписчиков вконтакте">подписчиков</a>,
            <a href="https://vk-pro.top/nakrutka_druzey_vkontakte" target="_self" title="Накрутка друзей вконтакте">друзей</a>
            вконтакте</h3>
        <button class="main-button" onclick="login.form_login_show();">Зарегистрироваться прямо сейчас</button>
        <h6 class="main-h6">Только наш сервис предлагает ряд функций, не имеющих аналогов, а также позволяет безопасно,
            профессионально и качественно:</h6>
        <div class="row text-left">
            <div class="col-sm-2"></div>
            <div class="col-sm-4 main-check">
                <img src="/img/main/check.png" align="left"/>
                раскрутить свою группу или<br/>паблик ВКонтакте с нуля
            </div>
            <div class="col-sm-5 main-check">
                <img src="/img/main/check.png" align="left"/>
                начать монетизировать свою площадку,<br/>получая прибыль с рекламы
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
    <div class="main-image text-center">
        <img class="main-image-vk" src="/img/main/vk.png"/>
        <img src="/img/main/browsers/browser.png" alt="накрутка просмотров vk, раскрутка групп вконтакте"/>
    </div>
</header>
<div class="main-protect" id="i-main-protect">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 text-center">
                <div>
                    <img src="/img/main/quality.png" alt="накрутка друзей, подписчиков вконтакте"/>
                </div>
                <h3 class="main-protect-h3">Непревзойденное<br/>качество подписчиков</h3>
                <div>
                    Вы сможете самостоятельно выбрать необходимое<br/>
                    качество будущих подписчиков, используя<br/>
                    предлагаемые нашим сервисом функции таргетинга
                </div>
            </div>
            <div class="col-sm-6 text-center">
                <div>
                    <img src="/img/main/protect.png" alt="безопасная накрутка лайков, друзей, подписчиков, репостов"/>
                </div>
                <h3 class="main-protect-h3">Полная защита заказчика</h3>
                <div>
                    Забудьте про собак и низкосортные аккаунты в<br/>
                    своих группах и пабликах. Компенсируем<br/>
                    потраченные средства за отписавшихся. Нарушители<br/>
                    будут наказаны и оштрафованы
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main-active">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h2 class="main-h2">Повышение активности<br/>подписчиков</h2>
                <div class="main-text">
                    <a href="/nakrutka_podpischikov_vkontakte" title="Накрутка подписчиков вконтакте">Накручивая
                        подписчиков</a> через наш сервис, вы сможете
                    точечно давать задания этим пользователям. Таким образом,
                    все накрученные в вашей группе лайки, репосты, комментарии,
                    голоса в опросах, будут совершены подписчиками вашего же
                    сообщества.
                </div>
            </div>
            <div class="col-sm-6">
                <img src="/img/main/browsers/browser-active.png" alt="накрутка друзей и подписчиков vk"/>
            </div>
        </div>
    </div>
</div>
<div class="main-view">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <img src="/img/main/browsers/browser-views.png" alt="безопасная раскрутка вконтакте, просмотры"/>
            </div>
            <div class="col-sm-6">
                <h2 class="main-h2">Увеличение<br/>количества просмотров</h2>
                <div class="main-text">
                    Только с помощью сервиса VK-PRO.TOP можно увеличивать
                    количество просмотров поста и охват
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main-join">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h2 class="main-h2">Охват<br/>подписчиков</h2>
                <div class="main-text">
                    И только наш сервис позволяет накручивать Охват подписчиков
                    в группе или паблике. Значение этого параметра напрямую
                    влияет на стоимость рекламы в Вашем сообществе
                </div>
            </div>
            <div class="col-sm-6">
                <img src="/img/main/browsers/browser-join.png"
                     alt="бесплатная и безопасная накрутка друзей, подписчиков - сервис"/>
            </div>
        </div>
    </div>
</div>
<div class="main-video">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <img src="/img/main/browsers/browser-video.png" alt="вывод видео в топ, раскрутка vk"/>
            </div>
            <div class="col-sm-6">
                <h2 class="main-h2">Вывод<br/>видео в топ</h2>
                <div class="main-text">
                    Хотите вывести видеозаписи своего сообщества в топ поиска
                    ВКонтакте? Теперь это возможно!<br/>
                    Сервис VK-PRO.TOP позволяет
                    накручивать просмотры видео ВКонтакте. Таким образом вы без
                    труда сможете вывести видеозаписи вашего сообщества в ТОП,
                    и получать постоянный трафик целевой аудитории
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main-posting" id="i-main-posting">
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <h2 class="main-h2">Автопостинг и Граббер</h2>
                <div class="main-posting-image">
                    <img src="/img/main/posting.png" alt="автопостинг и граббер vk, монетизация групп вконтакте"/>
                </div>
                <div class="main-text">
                    Мы знаем, что поиск и своевременная публикация контента в сообщество отнимает у администратора много
                    сил и времени. Потому, для наших пользователей мы разработали Автопостинг, способный размещать
                    материалы в вашем сообществе за вас, и Граббер, способный наполнять вашу группу контентом из других
                    сообществ в автоматическом режиме
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5"></div>
            <div class="col-sm-2 text-center">
                <h5 class="h5-sources">Источники</h5>
            </div>
            <div class="col-sm-5"></div>
        </div>
        <div class="row text-center">
            <div class="col-sm-2"></div>
            <div class="col-sm-2">
                <img src="/img/main/groups/1.png" alt="раскрутка групп вконтакте"/>
            </div>
            <div class="col-sm-2">
                <img src="/img/main/groups/2.png" alt="раскрутка групп vk"/>
            </div>
            <div class="col-sm-2">
                <img src="/img/main/groups/3.png" alt="раскрутка групп вконтакте"/>
            </div>
            <div class="col-sm-2">
                <img src="/img/main/groups/4.png" alt="раскрутка групп vk"/>
            </div>
            <div class="col-sm-2"></div>
        </div>
        <div class="row text-center">
            <div class="col-sm-2"></div>
            <div class="col-sm-2">
                <h5>Источник 1</h5>
            </div>
            <div class="col-sm-2">
                <h5>Источник 2</h5>
            </div>
            <div class="col-sm-2">
                <h5>Источник 3</h5>
            </div>
            <div class="col-sm-2">
                <h5>Источник 4</h5>
            </div>
            <div class="col-sm-2"></div>
        </div>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 line">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-sm-6 vline">&nbsp;</div>
        </div>
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="main-img-vk-pro"><img src="/img/main/vk-pro.png"/></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 vline">&nbsp;</div>
        </div>
        <div class="text-center">
            <img src="/img/main/groups/your.png"/>
            <h5>Ваше сообщество</h5>
        </div>
    </div>
</div>
<div class="main-auto" id="i-main-auto">
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-12">
                <h2 class="main-h2">Автоведение</h2>
                <div class="main-text">
                    Вы можете сэкономить своё время, доверив нашему сервису самостоятельно отслеживать все новые посты в
                    вашем сообществе, и автоматически накручивать на них лайки, репосты и комментарии
                </div>
            </div>
        </div>
        <div class="h30"></div>
        <div class="row text-center">
            <div class="col-sm-4 phone-padding">
                <img src="/img/main/phnes/1.png" alt="автоведение комментарии vk"/>
            </div>
            <div class="col-sm-4">
                <img src="/img/main/phnes/2.png" alt="автоведение vk"/>
            </div>
            <div class="col-sm-4 phone-padding">
                <img src="/img/main/phnes/3.png" alt="автоведение лайки vk"/>
            </div>
        </div>
    </div>
</div>
<div class="main-posting">
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-12">
                <h2 class="main-h2">Без заданий</h2>
                <div class="main-text">
                    Хотите получать лайки, комментарии, друзей, репосты, подписчиков Вконтакте без заданий?
                    Просто включите нашего Автобота, и он будет выполнять задания за вас, даже когда вы офлайн
                </div>
            </div>
        </div>
        <div class="h30"></div>
        <div class="row text-center">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
                <img src="/img/autobot.png" alt="Автобот ВК без заданий"/>
            </div>
            <div class="col-sm-4">
            </div>
        </div>
    </div>
</div>
<footer>
    <div>
        <a href="#" onclick="init.getPage('privacy_policy'); return false;">Политика конфиденциальности</a>
    </div>
    <div class="vk-pro-reg">
        Бесплатная накрутка лайков, друзей и подписчиков в VK ®  <?= date('Y') ?> vkPRO
    </div>
</footer>
<div class="modal fade" id="i_form_login" tabindex="-1" role="dialog" aria-labelledby="form_login">
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
                                            data-uloginbutton="vkontakte"/>
                                </div>

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
<div class="modal fade" id="page_dialog" tabindex="-1" role="dialog" aria-labelledby="privacy_policy">
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