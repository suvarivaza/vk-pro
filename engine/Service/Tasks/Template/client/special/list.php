<?php
/** @var \Service\Tasks\Model_Tasks_Task[] $list */
$list = $vars['list'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];

/** @var \Service\Tasks\Model_Specials_Groups_Group $group */
$group = $vars['group'];
?>
    <ul class="breadcrumb">
        <li>
            <img src="/img/icons/32/icon-special.png" width="30"/>
            <a href="/tasks/special">Спецзадания</a>
        </li>
        <li>
            <img class="img-circle" src="<?= $group->photo; ?>" width="30"/>
            <a href="<?= $group->url; ?>" target="_blank" rel="nofollow"><?= $group->title; ?></a>
        </li>
    </ul>

<?php if ($group->dateValid < time()): ?>
    <div class="alert alert-danger">
        Срок действия для группы "<strong><?= $group->title; ?></strong>" истек.
    </div>
    <div id="i_special_group_add" class="row c-task-detail c-special-group-add">
        <div class="col-sm-2">
            <img src="https://vk.com/images/community_50.png" class="img-thumbnail"/>
        </div>
        <div class="col-sm-10">
            <h3>Активировать слот на группу</h3>
        </div>
    </div>
    <div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в Спецзадания</h5>
                </div>
                <div class="modal-body" id="i_dialog_group_add_container">
                    <div id="i_dialog_group_add_data"></div>
                    <div id="i_dialog_group_add_error"></div>
                    <div id="i_dialog_group_add_progress" class="progress progress-striped active"
                         style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                             aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button id="i_dialog_group_add_button_cancel" type="button" class="btn btn-default"
                            data-dismiss="modal">Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

    <?php if ($vars['counts']['total'] < 500): ?>
        <div class="alert alert-danger">
            <h4 class="text-center">Для успешного выполнения спец.заданий необходимо <strong>не менее 300</strong>
                подписчиков в группе.</h4>
            <div class="text-center">
                <a href="/tasks/special/join/<?= $group->groupId; ?>" class="button-green">
                    <div style="background: url(/img/icons/32/icon-special-white.png) no-repeat center center; background-size: contain;"
                         class="icon"></div>
                    <div class="name">Заказать подписчиков</div>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <h3 class="text-center" style="margin-top: 5px;">
                Вы можете<br/>
                <a href="/tasks/special/join/<?= $group->groupId; ?>" class="button-green">
                    <div style="background: url(/img/icons/32/icon-special-white.png) no-repeat center center; background-size: contain;"
                         class="icon"></div>
                    <div class="name">Заказать еще подписчиков</div>
                </a>
            </h3>
        </div>
    <?php endif; ?>
    <div class="alert alert-info">
        <div class="pull-right">
            <?php if ($GLOBALS['isSuperUser']): ?>
                <div>
                    <button onclick="dialog.actionDialog('showUpdateForm')" class="btn button-green c_tooltip"
                            data-delay="100" data-content="Обновляет данные по подписчикам.">Обновить данные
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <h4 style="font-size: 26px; margin-bottom: 0;">Подписчиков: <strong><?= $vars['counts']['total']; ?></strong>,
            из них <strong><?= $vars['counts']['online']; ?></strong> онлайн</h4>
    </div>

    <br/>
    <div class="pull-right">
        <a href="/tasks/special/<?= $group->groupId; ?>/add" class="button-green">
            <div style="background: url(/img/icons/32/icon-special-white.png) no-repeat center center; background-size: contain;"
                 class="icon"></div>
            <div class="name">Создать спецзадание</div>
        </a>
    </div>
    <table style="width: 100%;">
        <tr style="display: table-row;" class="btn-group" data-toggle="buttons">
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'all'): ?> active<?php endif; ?>"
                data-type="all">Все
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'likes'): ?> active<?php endif; ?>"
                data-type="likes">Лайки
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'reposts'): ?> active<?php endif; ?>"
                data-type="reposts">Репосты
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'comments'): ?> active<?php endif; ?>"
                data-type="comments">Комментарии
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'join'): ?> active<?php endif; ?>"
                data-type="join">Подписчики
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'polls'): ?> active<?php endif; ?>"
                data-type="polls">Опросы
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'views'): ?> active<?php endif; ?>"
                data-type="views">Просмотры
            </td>
            <td style="display: table-cell; float: none;"
                class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'video'): ?> active<?php endif; ?>"
                data-type="video">Просмотры видео
            </td>
        </tr>
    </table>
    <?php if (!($list)): ?>
        <div class="alert alert-info">
            <h5>
                <span class="glyphicon glyphicon-info-sign"></span>
                Вы еще не создали ни одного Спецзадания
            </h5>
        </div>
    <?php endif; ?>
    <?php foreach ($list as $task): $photo = $task->getPhoto(); ?>
        <div class="c-task-my-detail">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60px;" class="text-left">
                        <?php if (isset($photo['small'])): ?>
                            <img class="pull-left img-thumbnail" src="<?= $photo['small']['url']; ?>"
                                 style="width: 48px;"/>
                        <?php else: ?>
                            <img class="pull-left" src="/img/icons/icon-android.png" style="width: 32px;"/>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($task->type == 'comments' && $task->commentType == 3): ?>
                            <a class="c-task-comment" data-task-id="<?= $task->taskId; ?>" target="_blank"
                               href="javascript:void();"><?= $vars['types'][$task->type]; ?></a>
                        <?php elseif ($task->type == 'comments'): ?>
                            <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                               href="<?= $task->url; ?>">Написать <?php if ($task->commentType == 1): ?>положительный<?php elseif ($task->commentType == 2): ?>отрицательный<?php endif; ?>
                                комментарий</a>
                        <?php else: ?>
                            <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                               href="<?= $task->url; ?>"><?= $vars['types'][$task->type]; ?></a>
                        <?php endif; ?>
                        <?php if ($task->type == 'polls'): ?>
                            Проголосовать за <strong>"<?= $task->answerTitle; ?>"</strong>
                        <?php endif; ?>
                        <div><strong><?= $task->title; ?></strong></div>
                    </td>
                    <td style="width: 32px;">
                        <img src="/img/icons/32/icon-special.png"/>
                    </td>
                    <td style="width: 140px;">
                        <div class="c-task-progress">
                            <div class="c-task-progress-text">выполнено <?= $task->countReady; ?>
                                из <?= $task->count; ?></div>
                            <div class="c-task-progress-percent"
                                 style="width: <?= $task->countReady / $task->count * 100; ?>%;"></div>
                        </div>
                    </td>
                    <td style="width: 150px;" class="text-right">
                        <a class="c-task-action" href="?toggle=<?= $task->taskId; ?>"><?php if ($task->active): ?><img
                                    onmouseover="$(this).prop('src', '/img/icons/32/icon-stop-active.png')"
                                    onmouseleave="$(this).prop('src', '/img/icons/32/icon-stop.png')"
                                    src="/img/icons/32/icon-stop.png"><?php else: ?><img
                                    onmouseover="$(this).prop('src', '/img/icons/32/icon-play-active.png')"
                                    onmouseleave="$(this).prop('src', '/img/icons/32/icon-play.png')"
                                    src="/img/icons/32/icon-play.png"><?php endif; ?></a>
                        <a class="c-task-action" href="?isDel=<?= $task->taskId; ?>"><img
                                    onmouseover="$(this).prop('src', '/img/icons/32/icon-close-active.png')"
                                    onmouseleave="$(this).prop('src', '/img/icons/32/icon-close.png')"
                                    src="/img/icons/32/icon-close.png"></a>
                    </td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>

    <script>
        $('.c-main-menu-ul-li').click(function () {
            location.href = '/tasks/special/<?= $group->groupId; ?>/' + $(this).data('type') + '/1';
        })
    </script>
<?php endif; ?>