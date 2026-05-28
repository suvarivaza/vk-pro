<?php
/** @var \Service\Tasks\Model_Abuses_Abuse $abuse */
$list = $vars['list'];
/** @var \Service\Tasks\Model_Tasks_Task[] $tasks */
$tasks = $vars['tasks'];
?>
    <h1>
        Жалобы
        <small class="pull-right">
            <a class="btn btn-default" href="/admin/tasks/abuse/settings">Настройки автобана</a>
        </small>
    </h1>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
    <table class="table">
        <tr>
            <td></td>
            <td>Задание</td>
            <td></td>
            <td>Жалоба</td>
            <td>Действия</td>
        </tr>
        <?php foreach ($tasks as $task): $photo = $task->getPhoto();
            $author = $task->getUser(); ?>
            <tr>
                <td style="width: 60px;">
                    <?php if (isset($photo['small'])): ?>
                        <img class="pull-left img-thumbnail" src="<?= $photo['small']['url']; ?>" style="width: 48px;"/>
                    <?php else: ?>
                        <img class="pull-left" src="/img/icons/icon-android.png" style="width: 32px;"/>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($task->type == 'comments' && $task->commentType == 3): ?>
                        <a class="c-task-comment" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="/tasks/all?taskId=<?= $task->taskId; ?>"><?= $vars['types'][$task->type]; ?></a>
                    <?php elseif ($task->type == 'comments'): ?>
                        <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="/tasks/all?taskId=<?= $task->taskId; ?>">Написать <?php if ($task->commentType == 1): ?>положительный<?php elseif ($task->commentType == 2): ?>отрицательный<?php endif; ?>
                            комментарий</a>
                    <?php else: ?>
                        <a class="c-task-current" data-task-id="<?= $task->taskId; ?>" target="_blank"
                           href="/tasks/all?taskId=<?= $task->taskId; ?>"><?= $vars['types'][$task->type]; ?></a>
                    <?php endif; ?>
                    <?php if ($task->type == 'polls'): ?>
                        Проголосовать за <strong>"<?= $task->answerTitle; ?>"</strong>
                    <?php endif; ?>
                    <div><strong><?= $task->title; ?></strong></div>
                    <h5><?= $author->name; ?></h5>
                </td>
                <td style="width: 32px;">
                    <?php if ($task->isSpecial): ?>
                        <img title="Спецзадание" src="/img/icons/32/icon-special.png" width="32" height="32"/>
                    <?php endif; ?>
                </td>
                <td>
                    <?php foreach ($list[$task->taskId] as $abuse): $user = $abuse->getUser(); ?>
                        <h4>
                            <?= $user->name; ?>
                        </h4>
                        <div>
                            <?php if ($abuse->reason > 0): ?>
                                <?= $vars['reasons'][$abuse->reason]['title']; ?>
                            <?php endif; ?>
                            <?= $abuse->comment; ?>
                        </div>
                    <?php endforeach; ?>
                </td>
                <td>
                    <div><a class="btn btn-success btn-block" href="?isDone=<?= $task->taskId; ?>">Задание проверено</a>
                    </div>
                    <div><a class="btn btn-warning btn-block" href="?del=<?= $task->taskId; ?>">Удалить задание</a>
                    </div>
                    <div><a class="btn btn-danger btn-block" href="?ban=<?= $task->taskId; ?>">Забанить автора</a></div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>