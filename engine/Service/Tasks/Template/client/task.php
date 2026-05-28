<?php
$task = $vars['task'];
$user = $vars['user'];
$date = new DateTime();
$date->setTimestamp($task->dateCreate);

?>
<?php if ($task !== null): $photo = $task->getPhoto(); ?>
    <div class="c-task-detail" id="i-div-task-<?= $task->taskId; ?>">
        <table style="width: 100%;">
            <tr>

                <td style="width: 60px;">
                    <?php
                    if (isset($photo['small'])): ?>
                        <img class="pull-left img-thumbnail" src="<?= $photo['small']['url']; ?>"
                             style="width: 48px;height: 48px; object-fit: cover;"/>
                    <?php else: ?>
                        <img class="pull-left" src="/img/icons/icon-android.png" style="width: 32px;"/>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="c-task-current text-muted">ID: <?= $task->taskId; ?></div>
                    <?php if ($task->type == 'comments' && $task->commentType == 3): ?>
                        <a class="c-task-comment" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="javascript:void();"><?= $vars['types'][$task->type]; ?></a>
                    <?php elseif ($task->type == 'comments'): ?>
                        <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="/tasks/go?taskId=<?= $task->taskId; ?>">Написать <?php if ($task->commentType == 1): ?>положительный<?php elseif ($task->commentType == 2): ?>отрицательный<?php endif; ?>
                            комментарий</a>
                    <?php else: ?>
                        <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="/tasks/go?taskId=<?= $task->taskId; ?>">
                            <?= isset($vars['titles'][$task->type]['vkTypes']) ? $vars['titles'][$task->type]['vkTypes'][$task->vkType] : $vars['titles'][$task->type]['title']; ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($task->type == 'video'): ?>
                        <strong>5</strong> сек.
                    <?php elseif ($task->type == 'views'): ?>
                        <strong>5</strong> сек.
                    <?php endif; ?>
                    <?php if ($task->type == 'polls'): ?>
                        Проголосовать за <strong>"<?= $task->answerTitle; ?>"</strong>
                    <?php endif; ?>
                    <div>
                        <strong><?= $task->title; ?></strong>
                    </div>


                    <?php if (($task->isDel or !$task->active) and $task->reason) { ?>
                        <div class="c-task-current">Причина остановки задания: <?= $task->reason; ?></div>
                    <?php } ?>

                    <?php if ($GLOBALS['isSuperUser']) { ?>
                        <?php
                        echo $task->active ? 'Статус: Активно' : 'Статус : На паузе';

                        if ($task->isDel and $task->isDelDate) echo '<br>Задание удалено: ' . date('Y-m-d H:i:s', $task->isDelDate);

                        ?>

                        <div class="c-task-current text-muted"><?= $date->format('Y-m-d') ?> <?= $task->countReady; ?> из <?= $task->count; ?></div>
                        <div class="c-task-current text-muted">Инфо: type: <?= $task->type; ?> vkType: <?= $task->vkType; ?> ownerId: <?= $task->ownerId; ?> itemId: <?= $task->itemId; ?> commentId: <?= $task->commentId; ?> reason: <?= $task->reason; ?></div>
                        <?php if ($task->dateLast) { ?>
                            <div class="c-task-current text-muted"> Последнее выполнение: <?= date('Y-m-d H:i:s', $task->dateLast); ?></div>
                        <?php } ?>
                    <?php } ?>

                </td>
                <td style="width: 32px;">
                    <?php if ($task->isSpecial): ?>
                        <img src="/img/icons/32/icon-special.png"
                             width="32"
                             height="32"
                             data-toggle="popover"
                             data-original-title="Спецзадание"
                             data-content="Персональное задание для Вас с большей оплатой."
                        />
                    <?php endif; ?>
                </td>
                <td style="width: 140px;">
                    <div class="c-task-progress">
                        <div class="c-task-progress-text">
                            <?php if (!isset($vars['bonus_balance'])): ?>
                                +<?= \Lib_Text::Word4NumberNew($vars['prices']['price_' . $task->type . '_sell' . ($user->karma < 0 ? '_negative' : '') . ($user->karma >= 75 ? '_positive' : '')],
                                    ['балл', 'балла', 'баллов']); ?>
                            <?php else: ?>
                                +<?= \Lib_Text::Word4NumberNew($vars['bonus_balance'], ['балл', 'балла', 'баллов']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="c-task-progress-text text-center"
                         style="width: 140px; margin: 0; line-height: normal; position: relative;">
                        +<?= floatval($vars['karmaParams']['karma'][$task->type . ($user->karma < 0 ? '_negative' : '')]); ?>
                        к карме
                    </div>
                </td>
                <td style="width: 150px;" class="text-right">
                    <a class="c-task-action c-task-abuse"
                       onclick="$('#i_form_abuse_taskId').val($(this).data('taskId')); $('#i_form_abuse').modal();"
                       data-task-id="<?= $task->taskId; ?>"
                       href="javascript:void()"
                       data-toggle="popover"
                       data-original-title_="Пожаловаться"
                       data-content="Пожаловаться на задание."
                    >
                        <img onmouseover="$(this).prop('src', '/img/icons/32/icon-abuse-active.png')"
                             onmouseleave="$(this).prop('src', '/img/icons/32/icon-abuse.png')"
                             src="/img/icons/32/icon-abuse.png">
                    </a>
                    <a class="c-task-action"
                       href="?idDel=<?= $task->taskId; ?>"
                       data-toggle="popover"
                       data-content="Не показывать мне это задание."
                    >
                        <img onmouseover="$(this).prop('src', '/img/icons/32/icon-close-active.png')"
                             onmouseleave="$(this).prop('src', '/img/icons/32/icon-close.png')"
                             src="/img/icons/32/icon-close.png">
                    </a>
                    <?php if ($task->type == 'comments' && $task->commentType == 3): ?>
                        <a class="c-task-action c-task-comment"
                           data-task-id="<?= $task->taskId; ?>"
                           target="_blank"
                           href="javascript:void();"
                           data-toggle="popover"
                           data-original-title_="Выполнить задание"
                           data-content="Приступить к выполнению задания."
                        >
                            <img
                                    onmouseover="$(this).prop('src', '/img/icons/32/icon-check-active.png')"
                                    onmouseleave="$(this).prop('src', '/img/icons/32/icon-check.png')"
                                    src="/img/icons/32/icon-check.png">
                        </a>
                    <?php else: ?>
                        <a class="c-task-action c-task-current"
                           data-task-id="<?= $task->taskId; ?>"
                           target="_blank"
                           href="/tasks/go?taskId=<?= $task->taskId; ?>"
                           data-toggle="popover"
                           data-original-title_="Выполнить задание"
                           data-content="Приступить к выполнению задания."
                        >
                            <img onmouseover="$(this).prop('src', '/img/icons/32/icon-check-active.png')"
                                 onmouseleave="$(this).prop('src', '/img/icons/32/icon-check.png')"
                                 src="/img/icons/32/icon-check.png">
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" id="i_td_error_<?= $task->taskId; ?>" style="display: none;"></td>
            </tr>
        </table>
        <div class="alert alert-danger" id="i-div-task-detail-<?= $task->taskId; ?>"
             style="display: none; margin-bottom: 0; margin-top: 10px;"></div>
    </div>
<?php elseif (count($vars['errors'])): ?>
    <div class="c-task-detail alert alert-danger">
        <div class=""><?= implode('<br />', $vars['errors']); ?></div>
    </div>
<?php endif; ?>
<script>
    $('#i-div-task-<?= $task->taskId; ?> .c-task-current').click(function (e) {
        ws.taskId = $(this).data('taskId');
        e.stopPropagation();
    });
</script>
<script>
    // Включаем popover
    jQuery(function () {
        jQuery('[data-toggle="popover"]').popover({trigger: 'hover', placement: 'top'});
    });
</script>