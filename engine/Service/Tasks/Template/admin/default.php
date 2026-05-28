<?php
/** @var \Service\Tasks\Model_Tasks_Task[] $list */
$list = $vars['list'];
/* @var \Service\Users\Model_Users_User $user */
?>
<h1>
    <?= $vars['title']; ?>
    <small class="pull-right">
        <a class="btn btn-default" href="/admin/tasks/limits">Лимиты выполнения заданий</a>
    </small>
</h1>

<form method="get" class="form-horizontal">
    <input type="hidden" name="p" value="1"/>
    <div class="form-group">
        <div class="col-sm-2">
            <input type="text" class="form-control form-span" id="i_form-taskId" placeholder="ИД задания" name="taskId"
                   value="<?= $vars['filter']['taskId'] ?: ''; ?>"/>
        </div>
        <div class="col-sm-2">
            <input type="text" class="form-control form-span" id="i_form-userId" placeholder="ИД пользователя"
                   name="userId" value="<?= $vars['filter']['userId'] ?: ''; ?>"/>
        </div>
        <div class="col-sm-2">
            <select class="form-control form-span" id="i_form-type" placeholder="Тип задания" name="type">
                <option value="">-- Укажите --</option>
                <?php foreach (\Service\Tasks\Model_Config::$types as $type => $title) : ?>
                    <option value="<?= $type; ?>" <?php if ($vars['filter']['type'] == $type): ?> selected="selected"<?php endif; ?>><?= $title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-2">
            <input type="text" class="form-control form-span" id="i_form-q" placeholder="Поиск" name="q"
                   value="<?= $vars['filter']['q']; ?>"/>
        </div>
        <div class="col-sm-1">
            <select class="form-control form-span" id="i_form-sort" placeholder="Сортировать" name="sort">
                <option value="taskId" <?php if ($vars['filter']['sort'] == 'taskId'): ?> selected<?php endif; ?> >ИД
                    Задания
                </option>
                <option value="userId" <?php if ($vars['filter']['sort'] == 'userId'): ?> selected<?php endif; ?>>ИД
                    Пользователя
                </option>
                <option value="dateCreate" <?php if ($vars['filter']['sort'] == 'dateCreate'): ?> selected<?php endif; ?>>
                    Дата создания
                </option>
            </select>
        </div>
        <div class="col-sm-1">
            <select class="form-control form-span" id="i_form-dir" placeholder="Направление" name="dir">
                <option value="ASC" <?php if ($vars['filter']['dir'] == 'ASC'): ?> selected<?php endif; ?>>По
                    возрастанию
                </option>
                <option value="DESC" <?php if ($vars['filter']['dir'] == 'DESC'): ?> selected<?php endif; ?>>По
                    убыванию
                </option>
            </select>
        </div>
        <div class="col-sm-2">
            <button class="btn btn-default" type="submit"><span style="font-size: 19px;" class="fa fa-search"></span>
                Найти
            </button>
        </div>
    </div>
</form>

<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => true,
]); ?>

<table class="table">
    <?php foreach ($list as $task): $photo = $task->getPhoto();
        $user = $vars['users'][$task->userId];
        $userPhoto = $user->getPhotos(); ?>
        <tr>
            <td>
                <h5>
                    <a href="javascript:void(0);"
                       onclick="dialog.actionDialog('showTaskForm', {taskId: <?= $task->taskId; ?>}, '', 'modal-lg')"><?= $task->taskId; ?></a>
                    <?php if ($task->targeting): ?>
                        <i class="fa fa-filter"></i>
                    <?php endif; ?>
                    <small>
                        <br/><?= date('d.m.Y H:i', $task->dateCreate); ?>
                        <?php if ($task->isDelDate): ?>
                            <br/><?= date('d.m.Y H:i', $task->isDelDate); ?>
                        <?php endif; ?>
                    </small>
                </h5>
            </td>
            <td style="width: 60px; vertical-align: middle; position: relative;">
                <div style="position: absolute; left: 0; top: 0; border-radius: 10px; background: gray; color: #fff; min-width: 30px; height: 20px; text-align: center;"><?= $user->userId; ?></div>
                <?php if (isset($userPhoto['small']['url'])): ?>
                    <img class="img-circle" style="width: 50px; height: 50px; object-fit: cover;"
                         src="<?= $userPhoto['small']['url']; ?>"/>
                <?php else: ?>
                    <img class="img-circle" style="width: 60px;" src="/img/no-avatar.png"/>
                <?php endif; ?>
            </td>
            <td>
                <h4><?= $user->login; ?> <small><?= $user->email; ?></small></h4>
                <div><?= $user->name; ?></div>
            </td>
            <td style="width: 60px;" class="text-left">
                <?php if (isset($photo['small'])): ?>
                    <img class="pull-left img-thumbnail" src="<?= $photo['small']['url']; ?>" style="width: 48px;"/>
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
                <?php if ($task->isSpecial): ?>
                    <img src="/img/icons/32/icon-special.png"/>
                <?php endif; ?>
            </td>
            <td style="width: 150px;">
                <div class="c-task-progress">
                    <div class="c-task-progress-text">выполнено <?= $task->countReadyBot; ?> / <?= $task->countReady; ?>
                        из <?= $task->count; ?></div>
                    <div class="c-task-progress-percent"
                         style="width: <?= $task->countReady / $task->count * 100; ?>%;"></div>
                    <div class="c-task-progress-percent green"
                         style="width: <?= $task->countReadyBot / $task->count * 100; ?>%;"></div>
                </div>
            </td>
            <td style="width: 150px;" class="text-right">
                <?php if ($task->isDel): ?>
                    <i class="fa fa-trash"></i>
                <?php endif; ?>
                <a class="c-task-action"
                   href="?p=<?= $vars['page']; ?>&toggle=<?= $task->taskId; ?>"><?php if ($task->active): ?><img
                            onmouseover="$(this).prop('src', '/img/icons/32/icon-stop-active.png')"
                            onmouseleave="$(this).prop('src', '/img/icons/32/icon-stop.png')"
                            src="/img/icons/32/icon-stop.png"><?php else: ?><img
                            onmouseover="$(this).prop('src', '/img/icons/32/icon-play-active.png')"
                            onmouseleave="$(this).prop('src', '/img/icons/32/icon-play.png')"
                            src="/img/icons/32/icon-play.png"><?php endif; ?></a>
                <a class="c-task-action" href="?p=<?= $vars['page']; ?>&isDel=<?= $task->taskId; ?>"><img
                            onmouseover="$(this).prop('src', '/img/icons/32/icon-close-active.png')"
                            onmouseleave="$(this).prop('src', '/img/icons/32/icon-close.png')"
                            src="/img/icons/32/icon-close.png"></a>
                <a class="btn btn-danger" href="?p=<?= $vars['page']; ?>&deleteTask=<?= $task->taskId; ?>" onClick="return window.confirm('Вы действительно хотите окончательно удалить это задание из базы данных?');"><span
                            class="glyphicon glyphicon-alert"></span></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => true,
]); ?>
