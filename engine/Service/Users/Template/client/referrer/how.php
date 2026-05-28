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
<style>
    .c-bot-list li.icon-close, .c-bot-list li.icon-check {
        background-position: top left;
        background-position-y: 10px;
    }

    .c-bot-list h5 {
        margin-top: 0;
        padding-top: 0;
    }

    .c-bot-list li.icon-close h5 {
        color: #ff3838;
        font-weight: bold;
    }

    .c-bot-list li.icon-check h5 {
        color: #00a764;
        font-weight: bold;
    }

    h1 {
        font-size: 14px;
        color: #4f7299;
        font-weight: bold;
    }
</style>
<div class="tab-content" style="background: #ffffff; border: 1px solid #ddd; border-top: 0; padding: 10px;">
    <div id="general" class="tab-pane fade active in">
        <div class="row">
            <div class="form-group">
                <label class="col-sm-12 control-label" style="text-align: left;">
                    Ваша реферальная ссылка
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

        <h1>Как правильно пользоваться партнёрской программой и своей реферальной ссылкой?</h1>
        <ul class="c-bot-list">
            <li class="icon-check">
                <h5>Как и где правильно оставлять реферальную ссылку:</h5>
                <p>Если вы оставляете реферальную ссылку, она должна иметь контекстуальный смысл.</p>
                <p>Пример: Вы являетесь участником форума, где ведутся дискуссии о тех или иных методах продвижения
                    Вконтакте. Один из участников форума спрашивает, где и как можно безопасно накрутить живых
                    пользователей в группу Вконтакте. Вы можете ответить ему, что есть сервис, который позволяет это
                    сделать безопасно и быстро, и прикрепляете свою реферальную ссылку. Такая ссылка будет иметь
                    контекстуальный смысл, подходить по теме вопроса, и будет работать в сотни раз эффективнее, нежели
                    спамовая. У администрации же ресурса, на котором оставлена ссылка, не будет причин подозревать вас в
                    распространении смапа и блокировать доступ, т.к. всё сделано корректно.</p>
                <p>Ещё одним хорошим местом агрегации ссылкой рефералов, будут разного рода опросы о сервисах по
                    накрутке и продвижению Вконтакте. А так же, ресурсы, где собираются отзывы пользователей о сервисах.
                    В таких местах, реферальная ссылка так же, будет уместна. Но, не забывайте про контекстуальный вес
                    ссылки. Не оставляйте голую ссылку. Прикрепляйте к ней свой осмысленный отзыв о сервисе.</p>
            </li>
        </ul>
        <ul class="c-bot-list">
            <li class="icon-close">
                <h5>Ни в коем случае, не распространяйте свою реферальную ссылку методом спама.</h5>
                <p>Во первых, это не эффективно.</p>
                <p>Во вторых, в абсолютном большинстве случаев, ссылка, распространяемая методом спама, будет скорее
                    раздражать ваших потенциальных рефералов, нежели привлекать пройти по ней, и зарегистрироваться на
                    сервисе.</p>
                <p>В третьих, скорее всего, администрация интернет ресурсов, на которых вы размещали ссылку методом
                    спама, заблокирует вам доступ к этим ресурсам, а все оставленные вами копии реферальной ссылки будут
                    удалены.</p>
            </li>
        </ul>

    </div>
</div>