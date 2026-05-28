<?php
/** @var \Service\Tasks\Model_Tasks_Task[] $list */
$list = $vars['list'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];

//getTrace();
?>
<?php if (!$user->uid): ?>
    <div class="c-main-menu">
        <div class="text">
            <h1>Задания</h1>
            <p>Для выполнения заданий необходимо привязать аккаунт ВКонтакте:</p>
            <div id="uLogin_eb7899e9" data-uloginid="eb7899e9"></div>
        </div>
    </div>
<?php else: ?>
    <table style="width: 100%;">
        <tr data-toggle="buttons">
            <td class="c-main-menu-ul-li <?php if ($vars['type'] == 'all'): ?> active<?php endif; ?>" data-type="all">
                Все
            </td>
            <td class="c-main-menu-ul-li <?php if ($vars['type'] == 'likes'): ?> active<?php endif; ?>"
                data-type="likes">Лайки
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'reposts'): ?> active<?php endif; ?>"
                data-type="reposts">Репосты
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'comments'): ?> active<?php endif; ?>"
                data-type="comments">Комментарии
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'join'): ?> active<?php endif; ?>" data-type="join">
                Подписчики
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'friends'): ?> active<?php endif; ?>"
                data-type="friends">Друзья
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'polls'): ?> active<?php endif; ?>"
                data-type="polls">Опросы
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'views'): ?> active<?php endif; ?>"
                data-type="views">Просмотры
            </td>
            <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'video'): ?> active<?php endif; ?>"
                data-type="video">Просмотры видео
            </td>
        </tr>
    </table>
    <?php if ($user->bonus == 1 && isset($vars['bonus'])): ?>
        <div class="alert alert-info">
            <h6 class="text-center">
                Поздравляем! Вы зарегистрировались на сервисе vk-Pro.top! Выполните бонусное задание и<br/>получите
                бонус за регистрацию <strong><?= $vars['bonus']['register']; ?> баллов!</strong>
            </h6>
            <div class="text-center">
                <a href="/tasks/bonus" class="button-green">Выполнить задание и получить бонус</a>
            </div>
        </div>
    <?php elseif ($user->bonus == 2 && isset($vars['bonus'])): ?>
        <div class="alert alert-info">
            <h6 class="text-center">
                Выполните ежедневное бонусное задание и<br/>получите бонус <strong><?= $vars['bonus']['day_one']; ?>
                    баллов!</strong>
            </h6>
            <div class="text-center">
                <a href="/tasks/bonus" class="button-green">Выполнить задание и получить бонус</a>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!$user->isBot): ?>
        <div class="alert alert-success">
            <h5 class="text-center">Хотите получить баллы бесплатно и не тратить время на выполнение заданий?</h5>
            <div class="text-center">
                <a href="/bot" class="button-green" id="i-button-bot">Включить автоматическое выполнение</a>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($vars['type'] == 'reposts'): ?>
        <div class="alert alert-info">
            <img src="/img/icons/32/icon-abuse.png"/>&nbsp;&nbsp;&nbsp;&nbsp;
            Репост можно удалить через сутки после публикации, вы не будете за это оштрафованы
        </div>
    <?php endif; ?>
    <?php

    if (!empty($vars['badTokenErrorText'])): ?>
        <div class="alert alert-danger">
            <img src="/img/icons/32/icon-abuse.png"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <?= $vars['badTokenErrorText'] ?>
        </div>
    <?php endif; ?>
    <div id="i-div-tasks-list">
        <?php
        if ($user->bad > 0): ?>
            <div class="alert alert-danger">
                <h4>Ваша страничка Вконтакте не проходит проверку качества.</h4>
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
                        <li>Не менее 5 <strong>общедоступных</strong> фото</li>
                    <?php endif; ?>
                </ul>
                <br/>
                <strong>Вам доступны только задания на просмотры и голосования</strong>
            </div>

            <form method="post" class="form-horizontal">
                <input type="hidden" name="action" value="unban"/>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <?php if (is_array($vars['errors']) and count($vars['errors'])): ?>
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
        <?php endif; ?>
        <?php if (!count($list) && !$user->bad): ?>
            <br/>
            <div class="alert alert-success">
                <div class="row">
                    <div class="col-sm-1">
                        <img src="/img/icons/32/icon-check.png" style="margin-top: 20px;"/>
                    </div>
                    <div class="col-sm-11">
                        <h2>Поздравляем!</h2><h5>Вы выполнили все задания данного типа.</h5><h5>Для выполнения новых
                            заданий зайдите на сайт позже.</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-success" target="_self" href="/tasks/views">Перейти к заданиям на
                                    просмотры</a>
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn-success" target="_self" href="/tasks/video">Перейти к заданиям на
                                    просмотры видео</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php foreach ($list as $task): ?>
            <?php \STPL::Display('client/task', [
                'karmaParams' => $vars['karmaParams'],
                'bonus_balance' => $vars['bonus_balance'] ?? null,
                'task' => $task,
                'user' => $user,
                'prices' => $vars['prices'],
                'titles' => $vars['titles'],
                'types' => $vars['types'],
            ]); ?>
        <?php endforeach; ?>
        <?php if (isset($vars['errors']) and is_array($vars['errors']) and count($vars['errors'])): ?>
            <div class="c-task-detail alert alert-danger">
                <div class=""><?= implode('<br />', $vars['errors']); ?></div>
            </div>
        <?php endif; ?>
    </div>
    <script>
        var focus = 1;
        $('.c-main-menu-ul-li').click(function () {
            location.href = '/tasks/' + $(this).data('type');
        });
    </script>
<?php endif; ?>
<?php if (!$user->isBot): ?>
    <div class="modal fade" id="i_form_bot" tabindex="-1" role="dialog" aria-labelledby="i_form_bot">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title" id="i_form_bot_label">Включение автоматического выполнения заданий</h5>
                </div>
                <div class="modal-body" id="i_form_bot_container">
                    <div id="i_form_bot_data"></div>
                    <div id="i_form_bot_progress" class="progress progress-striped active" style="display: none;">
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
<?php endif; ?>
<div class="modal fade" id="i_form_abuse" tabindex="-1" role="dialog" aria-labelledby="i_form_abuse">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_form_abuse_label">Пожалуйста, сообщите причину, по которой задание должно
                    быть заблокированно</h5>
            </div>
            <div class="modal-body" id="i_form_abuse_container">
                <form method="post" class="form-horizontal" role="form" id="i_form_abuse_form">
                    <input type="hidden" name="action" value="abuse"/>
                    <input type="hidden" id="i_form_abuse_taskId"/>
                    <h4>Причина</h4>
                    <?php foreach (\Service\Tasks\Model_Config::$reasons as $id => $reason): ?>
                        <div>
                            <label for="i_form_abuse_reason_<?= $id; ?>">
                                <input type="radio" name="reason" value="<?= $id; ?>"
                                       id="i_form_abuse_reason_<?= $id; ?>"/>
                                <?= $reason['title']; ?>
                                <?php if ($id == 5): ?>
                                    <br/>
                                    Вы можете <strong style="color: red;">скрыть</strong> для себя задания <strong
                                            style="color: red;">18+</strong> в личном кабинете. <a style="color: red;"
                                                                                                   href="/users/general">Перейти...</a>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <div id="i_form_abuse_div_comment" style="display: none;">
                        <textarea class="form-control" placeholder="Укажите причину" id="i_form_abuse_comment"
                                  name="comment"></textarea>
                    </div>
                </form>
                <div id="i_form_abuse_data"></div>
                <div id="i_form_abuse_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_form_abuse_button_cancel" type="button" class="btn btn-default" data-dismiss="modal">
                    Отмена
                </button>
                <button id="i_form_abuse_button_abuse" type="button" class="btn btn-danger">Пожаловаться</button>
            </div>
        </div>
    </div>
</div>

<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => true,
]); ?>

<script>
    $('input[name="reason"]').click(function () {
        var val = $(this).val();
        if (val == 0) {
            $('#i_form_abuse_div_comment').show();
        } else {
            $('#i_form_abuse_div_comment').hide();
        }
    });
    $('#i_form_abuse').on('show.bs.modal', function () {
        $('#i_form_abuse_button_abuse').show();
        $('#i_form_abuse_button_cancel').html('Отмена');
    });
    $('#i_form_abuse_button_abuse').click(function () {
        $('#i_form_abuse_button_abuse').show();
        var val = $('input[name="reason"]:checked').val();
        if (val == undefined) {
            $('#i_form_abuse_data').html('<div class="alert alert-danger">Укажите причину</div>');
            return;
        }
        if (val == 0 && $('#i_form_abuse_comment').val() == '') {
            $('#i_form_abuse_data').html('<div class="alert alert-danger">Укажите причину в комментарии</div>');
            return;
        }

        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: "abuse",
                taskId: $('#i_form_abuse_taskId').val(),
                reason: val,
                comment: $('#i_form_abuse_comment').val()
            },
            beforeSend: function () {
                $('#i_form_abuse_data').html('');
                $('#i_form_abuse_progress').show();
            },
            complete: function () {
                $('#i_form_abuse_progress').hide();
            },
            success: function (data) {
                if (data.success) {
                    $('#i_form_abuse_data').html('<div class="alert alert-success">Ваша жалоба принята. Спасибо.</div>');
                    $('#i_form_abuse_button_abuse').hide();
                    $('#i_form_abuse_button_cancel').html('Закрыть');
                    $('#i-div-task-' + $('#i_form_abuse_taskId').val()).remove();
                    if(data.html){
                        $('#i-div-tasks-list').append(data.html)
                    }
                } else {
                    $('#i_form_abuse_data').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                    $('#i_form_abuse_button_abuse').hide();
                }
            },
            error: function () {
                $('#i_form_abuse_data').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            }
        });
    });
</script>
