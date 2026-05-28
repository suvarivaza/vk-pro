<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation" class="active"><a href="/users/referrer">О рефералах</a></li>
    <li role="presentation"><a href="/users/referrer/1/1">Мои рефералы</a></li>
    <li role="presentation"><a href="/users/referrer/bonus/1">Реферальные бонусы</a></li>
    <li role="presentation"><a href="/users/referrer/balance">Реферальные баллы</a></li>
</ul>
<div class="tab-content" style="background: #ffffff; border: 1px solid #ddd; border-top: 0; padding: 10px;">
    <div id="general" class="tab-pane fade active in">
        <div class="row">
            <div class="form-group">
                <label class="col-sm-12 control-label" style="text-align: left;">
                    Ваша реферальная ссылка
                    <a href="/users/referrer/how" class="c-referrer-how c_tooltip" data-delay="100"
                       data-content="Как правильно пользоваться партнёрской программой и своей реферальной ссылкой?">?</a>
                </label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <input name="access_token" class="form-control" id="i_user_referrerUrl"
                               value="<?= \Lib_Html::ChangeQuotes('https://' . DOMAIN . '/?referrerUrl=' . $user->referrerUrl); ?>"/>
                        <span class="input-group-addon btn btn-primary" id="i_user_referrerUrl_button">
                    Копировать
                </span>
                    </div>

                </div>
            </div>
        </div>
        <script>
            $('#i_user_referrerUrl_button').click(function () {
                $('#i_user_referrerUrl').select();
                try {
                    var successful = document.execCommand('copy');
                    if (!successful) {
                        alert('Ваш браузер не поддерживает копирование. Скопируйте ссылку в ручную, либо обновите браузер');
                    }
                } catch (err) {
                    alert('Ваш браузер не поддерживает копирование. Скопируйте ссылку в ручную, либо обновите браузер');
                }
            });
        </script>
        <?php STPL::Display('client/referrer/general'); ?>
    </div>
</div>