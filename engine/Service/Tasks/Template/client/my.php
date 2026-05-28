<?php
/** @var \Service\Tasks\Model_Tasks_Task[] $list */
$list = $vars['list'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>


<table style="width: 100%;">
    <tr data-toggle="buttons">
        <td class="c-main-menu-ul-li <?php if ($vars['type'] == 'all'): ?> active<?php endif; ?>" data-type="all">Все
        </td>
        <td class="c-main-menu-ul-li <?php if ($vars['type'] == 'likes'): ?> active<?php endif; ?>" data-type="likes">
            Лайки
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
        <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'polls'): ?> active<?php endif; ?>" data-type="polls">
            Опросы
        </td>
        <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'views'): ?> active<?php endif; ?>" data-type="views">
            Просмотры
        </td>
        <td class="c-main-menu-ul-li<?php if ($vars['type'] == 'video'): ?> active<?php endif; ?>" data-type="video">
            Просмотры видео
        </td>
    </tr>
</table>

<?php foreach ($list as $task): $photo = $task->getPhoto(); ?>
    <div class="c-task-my-detail">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60px;" class="text-left">
                    <?php if (isset($photo['small'])): ?>
                        <img class="pull-left img-thumbnail" src="<?= $photo['small']['url']; ?>" style="width: 48px;"/>
                    <?php else: ?>
                        <img class="pull-left" src="/img/icons/icon-android.png" style="width: 32px;"/>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="c-task-current text-muted" >ID: <?= $task->taskId; ?></div>
                    <div class="c-task-current text-muted" >Статус: <?= $task->active ? 'Активно' : 'На паузе'; ?></div>

                    <?php if ($task->type == 'comments' && $task->commentType == 3): ?>
                        <a class="c-task-comment" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="<?= $task->url; ?>"><?= $vars['titles'][$task->type]['title']; ?></a>
                    <?php elseif ($task->type == 'comments'): ?>
                        <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="<?= $task->url; ?>">Написать <?php if ($task->commentType == 1): ?>положительный<?php elseif ($task->commentType == 2): ?>отрицательный<?php endif; ?>
                            комментарий</a>
                    <?php else: ?>
                        <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="<?= $task->url; ?>">
                            <?= isset($vars['titles'][$task->type]['vkTypes']) ? $vars['titles'][$task->type]['vkTypes'][$task->vkType] : $vars['titles'][$task->type]['title']; ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($task->type == 'polls'): ?>
                        Проголосовать за <strong>"<?= $task->answerTitle; ?>"</strong>
                    <?php endif; ?>
                    <div><strong><?= $task->title; ?></strong></div>
                    <?php if (($task->isDel or !$task->active) and $task->reason) { ?>
                        <div class="c-task-current">Причина остановки задания: <?= $task->reason; ?></div>
                    <?php } ?>
                    <?php if($GLOBALS['isSuperUser']){?>
                        <div class="c-task-current text-muted">Инфо: ownerId: <?= $task->ownerId; ?> itemId: <?= $task->itemId; ?></div>
                    <?php } ?>
                </td>
                <td style="width: 32px;">

                </td>
                <td style="width: 140px;">
                    <?php if ($task->isTemplate): ?>
                        <div class="c-task-progress-text text-center"
                             style="width: 140px; margin: 0; line-height: normal; margin-top: -10px;">
                            <img src="/img/icons/32/icon-auto.png" height="14"/>
                            автоведение
                        </div>
                    <?php endif; ?>
                    <div class="c-task-progress">
                        <div class="c-task-progress-text">выполнено <?= $task->countReady; ?>
                            из <?= $task->count; ?></div>
                        <div class="c-task-progress-percent"
                             style="width: <?= $task->countReady / $task->count * 100; ?>%;"></div>
                    </div>
                    <?php if ($task->sum > 0): ?>
                        <div class="c-task-progress-text text-center"
                             style="width: 140px; margin: 0; line-height: normal;">
                            баллов: <?= $task->price * $task->countReady; ?>/<?= $task->sum; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td style="width: 150px;" class="text-right">
                    <?php if ($GLOBALS['isSuperUser']) { ?>
                        <a class="c-task-action"
                           href="/tasks/my/edit/<?= $task->taskId; ?>"
                           data-toggle="popover"
                           data-content="Редактировать задание."
                        >
                            <img src="/img/icons/32/icon-edit.png">
                        </a>
                    <?php } ?>
                    <a class="c-task-action"
                       data-toggle="popover"
                       data-content="<?php if ($task->active): ?>Поставить на паузу<?php else: ?>Запустить задание<?php endif; ?>"
                       href="?toggle=<?= $task->taskId; ?>"><?php if ($task->active): ?>
                            <img onmouseover="$(this).prop('src', '/img/icons/32/icon-stop-active.png')"
                                onmouseleave="$(this).prop('src', '/img/icons/32/icon-stop.png')"
                                src="/img/icons/32/icon-stop.png"><?php else: ?>
                            <img onmouseover="$(this).prop('src', '/img/icons/32/icon-play-active.png')"
                                onmouseleave="$(this).prop('src', '/img/icons/32/icon-play.png')"
                                src="/img/icons/32/icon-play.png"><?php endif; ?></a>
                    <a class="c-task-action"
                       href="?isDel=<?= $task->taskId; ?>"
                       data-toggle="popover"
                       data-content="Удалить задание.">
                        <img onmouseover="$(this).prop('src', '/img/icons/32/icon-close-active.png')"
                             onmouseleave="$(this).prop('src', '/img/icons/32/icon-close.png')"
                             src="/img/icons/32/icon-close.png">
                    </a>

                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>

<script>
    $('.c-main-menu-ul-li').click(function () {
        location.href = '/tasks/my/' + $(this).data('type') + '/1';
    })
</script>
<script>
    // Включаем popover
    jQuery(function () {
        jQuery('[data-toggle="popover"]').popover({trigger: 'hover', placement: 'top'});
    });
</script>