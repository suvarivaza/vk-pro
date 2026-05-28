<?php
/** @var Model_Autos_Templates_Template[] $templates */
use Service\Auto\Model_Autos_Templates_Template;

$templates = $vars['list'];
?>
<?php if (!count($templates)): ?>
    <div class="c-task-my-detail">
        <?php if ($vars['isArchive']): ?>
            <div class="alert alert-info">
                <h5>
                    <span class="glyphicon glyphicon-info-sign"></span>
                    Архив шаблонов пуст.
                </h5>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h5>
                    <span class="glyphicon glyphicon-info-sign"></span>
                    Вы еще не добавили ни одного шаблона
                </h5>
            </div>
            <div class="text-center">
                <button class="button-green" onclick="$('#i-link-template-add').trigger('click');">Добавить</button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php foreach ($templates as $template): ?>
    <div class="c-task-my-detail" id="i_template_detail_<?= $template->templateId; ?>">
        <div class="row">
            <div class="col-sm-4">
                <strong>
                    <?= $template->title; ?>
                    <img src="/img/icons/auto/icon-edit.png"/>
                </strong>
            </div>
            <div class="col-sm-4">
                Тип задания:
                <strong><?= $vars['types'][$template->type]; ?></strong>
                <?php if ($template->specialId > 0): ?>
                    <img style="margin-top: -5px;" src="/img/icons/32/icon-special.png" width="16"/>
                <?php endif; ?>
            </div>
            <div class="col-sm-4">
                Статус:
                <?php if ($template->isArchive): ?>
                    <span style="color: #d59824;">В архиве</span>
                <?php elseif ($template->isActive): ?>
                    <span style="color: #00ab66;">Запущено</span>
                <?php else: ?>
                    <span style="color: #d59824;">Приостановленно</span>
                <?php endif; ?>
            </div>
        </div>
        <hr/>
        <div class="pull-right"><?= $template->balanceRemain; ?> / <?= $template->balanceLimit; ?></div>
        <a class="c-template-settings-link" href="javascript:void(0);" onclick="$(this).toggleClass('active')">
            Показать настройки шаблона
            <img src="/img/icons/auto/icon-settings.png"/>
        </a>
        <div class="c-div-settings-detail">
            <hr/>
            <div class="c-div-settings-group">
                <div class="c-div-settings-group-title" onclick="$(this).toggleClass('active')">Создавать задания на
                    посты
                </div>
                <div class="c-div-settings-group-container">
                    <div class="c-div-settings-group-item">
                        <?php if ($template->fromGroupOnly): ?>
                            <strong>Только от имени группы</strong>
                        <?php else: ?>
                            <strong>На все посты на стене группы</strong>
                        <?php endif; ?>
                    </div>
                    <div class="c-div-settings-group-item">
                        <strong>Содержащие:</strong>
                        <?php if ($template->attachmentType & 2): ?>&nbsp;<span class="c-vk-icon c_tooltip_top"
                                                                                data-content="<div class='text-center'>Аудиозапись</div>"
                                                                                style="background-position: 0 -113px;">&nbsp;</span><?php endif; ?>
                        <?php if ($template->attachmentType & 4): ?>&nbsp;<span class="c-vk-icon c_tooltip_top"
                                                                                data-content="<div class='text-center'>Видеозапись</div>"
                                                                                style="background-position: 0 -90px;">&nbsp;</span><?php endif; ?>
                        <?php if ($template->attachmentType & 8): ?>&nbsp;<span class="c-vk-icon c_tooltip_top"
                                                                                data-content="<div class='text-center'>Документ</div>"
                                                                                style="background-position: 0 -135px;">&nbsp;</span><?php endif; ?>
                        <?php if ($template->attachmentType & 16): ?>&nbsp;<span class="c-vk-icon c_tooltip_top"
                                                                                 data-content="<div class='text-center'>Изображение</div>"
                                                                                 style="background-position: 0 -70px;">&nbsp;</span><?php endif; ?>
                        <?php if ($template->attachmentType & 32): ?>&nbsp;<span class="c-vk-icon c_tooltip_top"
                                                                                 data-content="<div class='text-center'>Опрос</div>"
                                                                                 style="background-position: 0 -202px;">&nbsp;</span><?php endif; ?>
                        <?php if ($template->attachmentType & 64): ?>&nbsp;<span class="c-vk-icon c_tooltip_top"
                                                                                 data-content="<div class='text-center'>Текст</div>"
                                                                                 style="background-position: 0 -245px;">&nbsp;</span><?php endif; ?>
                    </div>
                    <?php if ($template->adsOut): ?>
                        <div class="c-div-settings-group-item">
                            <strong>Исключать рекламу</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <hr/>
            <div class="c-div-settings-group">
                <div class="c-div-settings-group-title" onclick="$(this).toggleClass('active')">Бюджет, лимиты и
                    расписание
                </div>
                <div class="c-div-settings-group-container">
                    <div class="c-div-settings-group-item">
                        Количество выполнений на пост: от <strong><?= $template->countFrom; ?></strong> до
                        <strong><?= $template->countTo; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Собирать посты:
                        <strong>
                            <?= $vars['weekDay'][$template->weekDay]; ?>
                            <?php if ($template->hourFrom == 0 && $template->hourTo == 0): ?>
                                весь день
                            <?php else: ?>
                                с <?= $template->hourFrom; ?> до <?= $template->hourTo; ?>
                            <?php endif; ?>
                        </strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Лимит баллов для шаблона: <strong><?= $template->balanceLimit ?: 'Без лимита'; ?></strong>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="c-div-settings-group">
                <div class="c-div-settings-group-title" onclick="$(this).toggleClass('active')">Таргетинг</div>
                <div class="c-div-settings-group-container">
                    <div class="c-div-settings-group-item">
                        Пользователям с уровнем кармы: <strong><?= $template->minKarma; ?>%</strong>
                    </div>
                    <?php if ($template->prior) : ?>
                        <div class="c-div-settings-group-item">
                            <strong>Моё задание в начале очереди</strong>
                        </div>
                    <?php endif; ?>
                    <div class="c-div-settings-group-item">
                        Хочу, что бы задание выполняли:
                        <strong><?= $vars['targeting']['sex'][$template->sex]; ?></strong>
                    </div>

                    <div class="c-div-settings-group-item">
                        Возраст:
                        <strong>
                            <?php if ($template->ageFrom == 0 && $template->ageTo == 0): ?>
                                Любой
                            <?php else: ?>
                                <?= $template->ageFrom ? ('от ' . $template->ageFrom) : ''; ?>
                                <?= $template->ageTo ? ('до ' . $template->ageTo) : ''; ?>
                            <?php endif; ?>
                        </strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Город:
                        <strong>
                            <?php if ($template->cityId == 0 && $template->countryId == 0): ?>
                                Любой
                            <?php elseif ($template->cityId > 0): ?>
                                <?= $vars['cities'][$template->cityId]->title; ?>
                            <?php else: ?>
                                <?= $vars['countries'][$template->countryId]->title; ?>
                            <?php endif; ?>
                        </strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Семеное положение: <strong><?= $vars['targeting']['relation'][$template->relation]; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Количество аватарок:
                        <strong><?= $vars['targeting']['avatarCount'][$template->avatarCount]; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Заполненность странички:
                        <strong><?= $vars['targeting']['filled'][$template->filled]; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Возраст странички: <strong><?= $vars['targeting']['pageAge'][$template->pageAge]; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Количество друзей и подписчиков:
                        <strong><?= $vars['targeting']['followersCount'][$template->followersCount]; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Количество интересных страниц:
                        <strong><?= $vars['targeting']['interestingPage'][$template->interestingPage]; ?></strong>
                    </div>
                    <div class="c-div-settings-group-item">
                        Частота постов на стене:
                        <strong><?= $vars['targeting']['frequencyPost'][$template->frequencyPost]; ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-sm-3">
                <?php if (!$template->isArchive): ?>
                    <button class="btn <?php if ($template->isActive): ?>btn-warning<?php else: ?>btn-success<?php endif; ?> btn-block"
                            onclick="auto.templateToggle(<?= $template->templateId; ?>)">
                        <?php if ($template->isActive): ?>
                            <img src="/img/icons/auto/icon-pause-white.png"/>
                            Приостановить
                        <?php else: ?>
                            <img src="/img/icons/auto/icon-play.png"/>
                            Запустить
                        <?php endif; ?>
                    </button>
                <?php endif; ?>
            </div>
            <div class="col-sm-3">
                <button class="btn btn-primary btn-block"
                        onclick="auto.getTemplateEdit(<?= $template->templateId; ?>);">
                    <img src="/img/icons/auto/icon-edit-white.png"/>
                    Редактировать
                </button>
            </div>
            <div class="col-sm-3 col-sm-offset-3">
                <button class="btn <?php if ($template->isArchive): ?>btn-success<?php else: ?>btn-danger<?php endif; ?> btn-block"
                        onclick="auto.templateToArchive(<?= $template->templateId; ?>)">
                    <?php if ($template->isArchive): ?>
                        <img src="/img/icons/auto/icon-play.png"/>
                    <?php else: ?>
                        <img src="/img/icons/auto/icon-trash-white.png"/>
                    <?php endif; ?>
                    <?php if ($template->isArchive): ?>
                        Из архива
                    <?php else: ?>
                        В архив
                    <?php endif; ?>
                </button>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<script>
    $('.c_tooltip_top').popover({
        placement: 'top',
        trigger: 'hover',
        html: true
    });
</script>