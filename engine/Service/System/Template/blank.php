<?php
/** @var \System\App $app */
$app = $vars['app'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?= $app->Title->Head; ?>
    <!-- Global Site Tag (gtag.js) - Google Analytics -->
<!--    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-107713177-1"></script>-->
<!--    <script>-->
<!--        window.dataLayer = window.dataLayer || [];-->
<!---->
<!--        function gtag() {-->
<!--            dataLayer.push(arguments);-->
<!--        }-->
<!---->
<!--        gtag('js', new Date());-->
<!---->
<!--        gtag('config', 'UA-107713177-1');-->
<!--    </script>-->
<!--    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
<!--    <script>-->
<!--        (adsbygoogle = window.adsbygoogle || []).push({-->
<!--            google_ad_client: "ca-pub-6581045320745946",-->
<!--            enable_page_level_ads: true-->
<!--        });-->
<!--    </script>-->
</head>
<body>
<!-- Global site tag (gtag.js) - Google Analytics -->
<!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-69611031-2"></script>-->
<!--<script>-->
<!--    window.dataLayer = window.dataLayer || [];-->
<!---->
<!--    function gtag() {-->
<!--        dataLayer.push(arguments);-->
<!--    }-->
<!---->
<!--    gtag('js', new Date());-->
<!---->
<!--    gtag('config', 'UA-69611031-2');-->
<!--</script>-->

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
<?php STPL::Display('controls/header', $vars); ?>
<?php STPL::Display('controls/body', $vars); ?>
<?php STPL::Display('controls/footer', $vars); ?>
</body>
<div class="modal fade" id="i_dialog_container" tabindex="-1" role="dialog" aria-labelledby="i_dialog_container">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_dialog_container_label"></h4>
            </div>
            <div class="modal-body" id="i_dialog_container_container">

            </div>
            <div class="modal-footer">
                <button id="_dialog_container_cancel" type="button" class="btn btn-default" data-dismiss="modal">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="i_dialog" tabindex="-1" role="dialog" aria-labelledby="i_dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="i_dialog_label"></h3>
            </div>
            <div class="modal-body" id="i_dialog_container">
                <div id="i_dialog_data"></div>
                <div id="i_dialog_error"></div>
                <div id="i_dialog_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
</html>