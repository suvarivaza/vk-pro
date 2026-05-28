<?php
/** @var \Service\Tasks\Model_Tasks_Task $task */
$task = $vars['task'];
$targeting = $vars['targeting'];
?>
<div style="font-size: 14pt;">
    <div class="row">
        <div class="col-sm-4">
            Тип задания:
        </div>
        <div class="col-sm-8">
            <strong><?= $vars['types'][$task->type]; ?></strong>
            <?= $vars['vkTypes'][$task->vkType]; ?>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-4">
            Адрес задания:
        </div>
        <div class="col-sm-8">
            <a href="<?= $task->url; ?>" target="_blank"><?= $task->url; ?></a>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-4">
            Для пользователей с уровнем кармы, от:
        </div>
        <div class="col-sm-8">
            <strong><?= $task->minKarma; ?></strong>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-4">
            Таргетинг
        </div>
        <div class="col-sm-8">
            <strong><?= $task->targeting ? 'Есть' : 'Нет'; ?></strong>
        </div>
    </div>
    <?php if ($task->targeting): ?>
        <?php if ($task->sex): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Пол</div>
                <div class="col-sm-8"><?= $task->sex ? $targeting['sex'][$task->sex] : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->ageFrom || $task->ageTo): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Возраст</div>
                <div class="col-sm-8">
                    <?php if ($task->ageFrom || $task->ageTo): ?>
                        <strong>
                            <?= $task->ageFrom ? ('от ' . $task->ageFrom) : ''; ?>
                            <?= $task->ageTo ? ('до ' . $task->ageTo) : ''; ?>
                        </strong>
                    <?php else: ?>
                        Все
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($task->cityId): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Город</div>
                <div class="col-sm-8"><?= $task->cityId; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->relation): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Семеное положение</div>
                <div class="col-sm-8"><?= $task->relation ? ('<strong>' . $targeting['relation'][$task->relation] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->avatarCount): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Количество аватарок</div>
                <div class="col-sm-8"><?= $task->avatarCount ? ('<strong>' . $targeting['avatarCount'][$task->avatarCount] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->filled): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Заполненность странички</div>
                <div class="col-sm-8"><?= $task->filled ? ('<strong>' . $targeting['filled'][$task->filled] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->pageAge): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Возраст странички</div>
                <div class="col-sm-8"><?= $task->pageAge ? ('<strong>' . $targeting['pageAge'][$task->pageAge] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->followersCount): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Количество друзей и подписчиков</div>
                <div class="col-sm-8"><?= $task->followersCount ? ('<strong>' . $targeting['followersCount'][$task->followersCount] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->interestingPage): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Количество интересных страниц</div>
                <div class="col-sm-8"><?= $task->interestingPage ? ('<strong>' . $targeting['interestingPage'][$task->interestingPage] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($task->frequencyPost): ?>
            <hr/>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-1">Частота постов на стене</div>
                <div class="col-sm-8"><?= $task->frequencyPost ? ('<strong>' . $targeting['frequencyPost'][$task->frequencyPost] . '</strong>') : 'Все'; ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <hr/>
    <div class="row">
        <div class="col-sm-4">Задание в начале очереди</div>
        <div class="col-sm-8"><?= $task->prior ? '<strong>Да</strong>' : 'Нет'; ?></div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-4">Количество (Бот/Готово/Всего)</div>
        <div class="col-sm-8"><?= $task->countReadyBot; ?>/<?= $task->countReady; ?>/<?= $task->count; ?></div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-4">Цена одного выполнения</div>
        <div class="col-sm-8"><?= $task->price; ?></div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-4">Стоимость задания</div>
        <div class="col-sm-8"><?= $task->sum; ?></div>
    </div>
</div>