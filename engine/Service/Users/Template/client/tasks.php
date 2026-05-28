<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
/** @var \Service\Tasks\Model_Tasks_Task[] $list */
$list = $vars['list'];
?>
    <ul class="breadcrumb">
        <li>
            <img src="/img/icons/32/icon-tasks.png" width="30"/>
            <a href="/grabber">История заданий</a>
        </li>
    </ul>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
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
                    <div><small><?= date('d.m.Y H:i', $task->dateCreate); ?></small></div>
                    <?php if ($task->type == 'comments' && $task->commentType == 3): ?>
                        <a class="c-task-comment" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="javascript:void();"><?= $vars['titles'][$task->type]['title']; ?></a>
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
                             style="width: <?= ($task->countReady > $task->count) ? 100 : ($task->countReady / $task->count * 100); ?>%;"></div>
                    </div>
                    <?php if ($task->sum > 0): ?>
                        <div class="c-task-progress-text text-center"
                             style="width: 140px; margin: 0; line-height: normal;">
                            баллов: <?= $task->price * $task->countReady; ?>/<?= $task->sum; ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>