<?php
/** @var Model_Autos_Templates_Template $template */
$template = $vars['template'];
$app = $vars['app'];
$user = $vars['user'];

use Service\Auto\Model_Autos_Templates_Template;

?>
<form action="" id="i-form-template-add" method="POST" enctype="multipart/form-data" target="_self"
      class="form-horizontal" role="form">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input type="hidden" name="groupId" value="<?= $vars['groupId']; ?>"/>
    <input type="hidden" name="templateId" value="<?= $template->templateId; ?>"/>
    <div class="form-group">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-primary<?php if ($template->type == 'likes' || $template->type == ''): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="likes" id="likes"
                       autocomplete="off"<?php if ($template->type == 'likes' || $template->type == ''): ?> checked<?php endif; ?>>
                Лайки
            </label>
            <label class="btn btn-primary<?php if ($template->type == 'reposts'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="reposts" id="reposts"
                       autocomplete="off"<?php if ($template->type == 'reposts'): ?> checked<?php endif; ?>> Репосты
            </label>
            <label class="btn btn-primary<?php if ($template->type == 'polls'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="polls" id="polls"
                       autocomplete="off"<?php if ($template->type == 'polls'): ?> checked<?php endif; ?>> Голосования
            </label>
            <label class="btn btn-primary<?php if ($template->type == 'comments'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="comments" id="comments"
                       autocomplete="off"<?php if ($template->type == 'comments'): ?> checked<?php endif; ?>>
                Комментарии
            </label>
            <label class="btn btn-primary<?php if ($template->type == 'views'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="views" id="views"
                       autocomplete="off"<?php if ($template->type == 'views'): ?> checked<?php endif; ?>> Просмотры
                постов
            </label>
            <label class="btn btn-primary<?php if ($template->type == 'video'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="video" id="video"
                       autocomplete="off"<?php if ($template->type == 'video'): ?> checked<?php endif; ?>> Просмотры
                видео
            </label>
        </div>
    </div>

    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-9">
                <div class="alert alert-danger">
                    <?= implode('<br />', $vars['errors']); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div id="i_div_comments" <?php if ($template->type != 'comments'): ?> style="display: none;"<?php endif; ?>>
        <div class="form-group">
            <label for="i_commentType" class="col-sm-5 control-label text-right">хочу</label>
            <div class="col-sm-4">
                <select id="i_commentType" name="commentType" class="form-control">
                    <option value="0" <?php if ($template->commentType == 0): ?> selected="selected"<?php endif; ?>>
                        любые
                    </option>
                    <option value="1" <?php if ($template->commentType == 1): ?> selected="selected"<?php endif; ?>>
                        положительные
                    </option>
                    <option value="2" <?php if ($template->commentType == 2): ?> selected="selected"<?php endif; ?>>
                        отрицательные
                    </option>
                    <option value="3" <?php if ($template->commentType == 3): ?> selected="selected"<?php endif; ?>>
                        заданные
                    </option>
                </select>
            </div>
            <label for="i_commentType" class="col-sm-3 control-label" style="text-align: left;">комментарии</label>
        </div>
        <div class="form-group">
            <div class="col-sm-9"
                 id="i_div_comments_list" <?php if ($template->commentType != 3): ?> style="display: none;"<?php endif; ?>>
                <?php $comments = $template->getComments(); ?>
                <?php foreach ($comments as $comment): ?>
                    <input class="form-control c_input_comments" name="comments[]"
                           placeholder="Введите сюда текст комментария" value="<?= $comment; ?>"/>
                <?php endforeach; ?>
                <input class="form-control c_input_comments" name="comments[]"
                       placeholder="Введите сюда текст комментария" value=""/>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-5 text-right">
            Спецзадание
            <span><img src="/img/icons/32/icon-special.png" width="20"/></span>
        </div>
        <div class="col-sm-2">
            <div class="material-switch">
                <input id="i-specialId" type="checkbox" name="specialId"
                       value="<?= $vars['special'] ? $vars['special']->groupId : 0; ?>"<?php if (!$vars['special']): ?> disabled="disabled"<?php endif; ?> <?php if ($template->specialId > 0): ?> checked="checked"<?php endif; ?> />
                <label for="i-specialId" class="label-primary">
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['specialId-text']): ?>
                <label class="c_tooltip" data-delay="<?= $app->settings['specialId-time']; ?>"
                       data-content="<?= $app->settings['specialId-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                    style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
        <div class="col-sm-4">&nbsp;</div>
        <div>&nbsp;</div>
        <?php if (!$vars['special']): ?>
            <div class="col-sm-6 col-sm-offset-3">
                <div class="alert alert-info">Данная группа не добавлена в спецзадания</div>
            </div>
        <?php endif; ?>
    </div>
    <div id="i_div_minKarma" class="form-group">
        <label for="i_task_minKarma" class="col-sm-5 control-label text-right">Пользователям с уровнем кармы:</label>
        <div class="col-sm-4">
            <select id="i_task_minKarma" name="minKarma"
                    class="form-control<?php if ($template->minKarma > 0): ?> alert-info<?php endif; ?>">
                <option value="0" <?php if ($template->minKarma == 0): ?> selected="selected"<?php endif; ?>>Любой
                </option>
                <option value="25" <?php if ($template->minKarma == 25): ?> selected="selected"<?php endif; ?>>Выше
                    25%
                </option>
                <option value="50" <?php if ($template->minKarma == 50): ?> selected="selected"<?php endif; ?>>Выше
                    50%
                </option>
                <option value="75" <?php if ($template->minKarma == 75): ?> selected="selected"<?php endif; ?>>Выше
                    75%
                </option>
            </select>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['minKarma-text']): ?>
                <label class="control-label c_tooltip" data-delay="<?= $app->settings['minKarma-time']; ?>"
                       data-content="<?= $app->settings['minKarma-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                   style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div id="i_div_targeting_container">
        <div id="i_div_targeting_button" class="form-group">
            <div class="col-sm-4 col-sm-offset-5 text-right">
                <div class="btn-group" data-toggle="buttons">
                    <label onclick="$('#i_div_targeting').toggle();"
                           class="btn btn-default<?php if ($template->targeting): ?> active<?php endif; ?>">
                        <input name="targeting" type="checkbox"
                               autocomplete="off" <?php if ($template->targeting): ?> checked="checked"<?php endif; ?>>Таргетинг
                        и настройка
                    </label>
                </div>
            </div>
        </div>
        <div id="i_div_targeting" <?php if (!$template->targeting): ?>style="display: none;"<?php endif; ?>>
            <div class="form-group">
                <label for="i_sex" class="col-sm-5 control-label text-right">Хочу, что бы задание выполняли:</label>
                <div class="col-sm-4">
                    <select id="i_sex" name="sex"
                            class="form-control<?php if ($template->sex > 0): ?> alert-info<?php endif; ?>">
                        <option value="0" <?php if ($template->sex == 0): ?> selected="selected"<?php endif; ?>>и парни
                            и девушки
                        </option>
                        <option value="2" <?php if ($template->sex == 2): ?> selected="selected"<?php endif; ?>>только
                            парни
                        </option>
                        <option value="1" <?php if ($template->sex == 1): ?> selected="selected"<?php endif; ?>>только
                            девушки
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['sex-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['sex-time']; ?>"
                               data-content="<?= $app->settings['sex-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                      style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_task_minKarma" class="col-sm-5 control-label text-right">Возраст:</label>
                <div class="col-sm-2">
                    <select name="ageFrom"
                            class="form-control<?php if ($template->ageFrom > 0): ?> alert-info<?php endif; ?>">
                        <option value="0">любой</option>
                        <?php for ($i = 14; $i <= 80; $i++): ?>
                            <option value="<?= $i; ?>"<?php if ($template->ageFrom == $i): ?> selected="selected"<?php endif; ?>>
                                от <?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="ageTo"
                            class="form-control<?php if ($template->ageTo > 0): ?> alert-info<?php endif; ?>">
                        <option value="0">любой</option>
                        <?php for ($i = 14; $i <= 80; $i++): ?>
                            <option value="<?= $i; ?>"<?php if ($template->ageTo == $i): ?> selected="selected"<?php endif; ?>>
                                до <?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['age-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['age-time']; ?>"
                               data-content="<?= $app->settings['age-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                      style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label text-right">Город:</label>
                <div class="col-sm-4">
                    <select name="city"
                            class="form-control<?php if ($template->cityId > 0 || $template->countryId > 0): ?> alert-info<?php endif; ?>">
                        <option value="0">любой</option>
                        <option data-type="city"
                                value="<?= $user->cityId; ?>"<?php if ($template->cityId == $user->cityId): ?> selected="selected"<?php endif; ?>>
                            <strong><?= $user->city; ?></strong></option>
                        <?php foreach ($vars['countries'] as $country): ?>
                            <option data-type="country"
                                    value="<?= $country->countryId; ?>"<?php if ($template->countryId == $country->countryId): ?> selected="selected"<?php endif; ?>><?= $country->title; ?></option>
                        <?php endforeach; ?>
                        <?php foreach ($vars['cities'] as $city): if ($city->cityId == $user->cityId) {
    continue;
} ?>
                            <option data-type="city"
                                    value="<?= $city->cityId; ?>"<?php if ($template->cityId == $city->cityId): ?> selected="selected"<?php endif; ?>><?= $city->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['city-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['city-time']; ?>"
                               data-content="<?= $app->settings['city-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                       style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_relation" class="col-sm-5 control-label text-right">Семеное положение:</label>
                <div class="col-sm-4">
                    <select id="i_relation" name="relation"
                            class="form-control<?php if ($template->relation > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->relation == 0): ?> selected="selected"<?php endif; ?>>
                            любой
                        </option>
                        <option value="1"<?php if ($template->relation == 1): ?> selected="selected"<?php endif; ?>>не
                            женат/не замужем
                        </option>
                        <option value="2"<?php if ($template->relation == 2): ?> selected="selected"<?php endif; ?>>есть
                            друг/есть подруга
                        </option>
                        <option value="3"<?php if ($template->relation == 3): ?> selected="selected"<?php endif; ?>>
                            помолвлен/помолвлена
                        </option>
                        <option value="4"<?php if ($template->relation == 4): ?> selected="selected"<?php endif; ?>>
                            женат/замужем
                        </option>
                        <option value="5"<?php if ($template->relation == 5): ?> selected="selected"<?php endif; ?>>всё
                            сложно
                        </option>
                        <option value="6"<?php if ($template->relation == 6): ?> selected="selected"<?php endif; ?>>в
                            активном поиске
                        </option>
                        <option value="7"<?php if ($template->relation == 7): ?> selected="selected"<?php endif; ?>>
                            влюблён/влюблена
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['relation-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['relation-time']; ?>"
                               data-content="<?= $app->settings['relation-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                           style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_avatarCount" class="col-sm-5 control-label text-right">Количество аватарок:</label>
                <div class="col-sm-4">
                    <select id="i_avatarCount" name="avatarCount"
                            class="form-control<?php if ($template->avatarCount > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->avatarCount == 0): ?> selected="selected"<?php endif; ?>>
                            любое
                        </option>
                        <option value="1"<?php if ($template->avatarCount == 1): ?> selected="selected"<?php endif; ?>>
                            не менее 1
                        </option>
                        <option value="2"<?php if ($template->avatarCount == 2): ?> selected="selected"<?php endif; ?>>
                            не менее 2
                        </option>
                        <option value="5"<?php if ($template->avatarCount == 5): ?> selected="selected"<?php endif; ?>>
                            не менее 5
                        </option>
                        <option value="10"<?php if ($template->avatarCount == 10): ?> selected="selected"<?php endif; ?>>
                            не менее 10
                        </option>
                        <option value="20"<?php if ($template->avatarCount == 20): ?> selected="selected"<?php endif; ?>>
                            не менее 20
                        </option>
                        <option value="50"<?php if ($template->avatarCount == 50): ?> selected="selected"<?php endif; ?>>
                            не менее 50
                        </option>
                        <option value="100"<?php if ($template->avatarCount == 100): ?> selected="selected"<?php endif; ?>>
                            не менее 100
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['avatarCount-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['avatarCount-time']; ?>"
                               data-content="<?= $app->settings['avatarCount-text']; ?>"><img
                                    src="/img/icons/32/info.png" style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_filled" class="col-sm-5 control-label text-right">Заполненность странички:</label>
                <div class="col-sm-4">
                    <select id="i_filled" name="filled"
                            class="form-control<?php if ($template->filled > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->filled == 0): ?> selected="selected"<?php endif; ?>>
                            Любая
                        </option>
                        <option value="1"<?php if ($template->filled == 1): ?> selected="selected"<?php endif; ?>>Не
                            менее 1 раздела
                        </option>
                        <option value="2"<?php if ($template->filled == 2): ?> selected="selected"<?php endif; ?>>Не
                            менее 2 разделов
                        </option>
                        <option value="3"<?php if ($template->filled == 3): ?> selected="selected"<?php endif; ?>>Не
                            менее 3 разделов
                        </option>
                        <option value="4"<?php if ($template->filled == 4): ?> selected="selected"<?php endif; ?>>Не
                            менее 4 разделов
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['filled-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['filled-time']; ?>"
                               data-content="<?= $app->settings['filled-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                         style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_pageAge" class="col-sm-5 control-label text-right">Возраст странички :</label>
                <div class="col-sm-4">
                    <select id="i_pageAge" name="pageAge"
                            class="form-control<?php if ($template->pageAge > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->pageAge == 0): ?> selected="selected"<?php endif; ?>>
                            Любой
                        </option>
                        <option value="1"<?php if ($template->pageAge == 1): ?> selected="selected"<?php endif; ?>>Не
                            менее 3 месяцев
                        </option>
                        <option value="2"<?php if ($template->pageAge == 2): ?> selected="selected"<?php endif; ?>>Не
                            менее полугода
                        </option>
                        <option value="3"<?php if ($template->pageAge == 3): ?> selected="selected"<?php endif; ?>>Не
                            менее 1 года
                        </option>
                        <option value="4"<?php if ($template->pageAge == 4): ?> selected="selected"<?php endif; ?>>Не
                            менее 2 лет
                        </option>
                        <option value="5"<?php if ($template->pageAge == 5): ?> selected="selected"<?php endif; ?>>Не
                            менее 3 лет
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['pageAge-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['pageAge-time']; ?>"
                               data-content="<?= $app->settings['pageAge-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                          style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_followersCount" class="col-sm-5 control-label text-right">Количество друзей и
                    подписчиков:</label>
                <div class="col-sm-4">
                    <select id="i_followersCount" name="followersCount"
                            class="form-control<?php if ($template->followersCount > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->followersCount == 0): ?> selected="selected"<?php endif; ?>>
                            Любое
                        </option>
                        <option value="10"<?php if ($template->followersCount == 10): ?> selected="selected"<?php endif; ?>>
                            Не менее 10
                        </option>
                        <option value="50"<?php if ($template->followersCount == 50): ?> selected="selected"<?php endif; ?>>
                            Не менее 50
                        </option>
                        <option value="100"<?php if ($template->followersCount == 100): ?> selected="selected"<?php endif; ?>>
                            Не менее 100
                        </option>
                        <option value="200"<?php if ($template->followersCount == 200): ?> selected="selected"<?php endif; ?>>
                            Не менее 200
                        </option>
                        <option value="500"<?php if ($template->followersCount == 500): ?> selected="selected"<?php endif; ?>>
                            Не менее 500
                        </option>
                        <option value="1000"<?php if ($template->followersCount == 1000): ?> selected="selected"<?php endif; ?>>
                            Не менее 1 000
                        </option>
                        <option value="5000"<?php if ($template->followersCount == 5000): ?> selected="selected"<?php endif; ?>>
                            Не менее 5 000
                        </option>
                        <option value="10000"<?php if ($template->followersCount == 10000): ?> selected="selected"<?php endif; ?>>
                            Не менее 10 000
                        </option>
                        <option value="20000"<?php if ($template->followersCount == 20000): ?> selected="selected"<?php endif; ?>>
                            Не менее 20 000
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['followersCount-text']): ?>
                        <label class="control-label c_tooltip"
                               data-delay="<?= $app->settings['followersCount-time']; ?>"
                               data-content="<?= $app->settings['followersCount-text']; ?>"><img
                                    src="/img/icons/32/info.png" style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_interestingPage" class="col-sm-5 control-label text-right">Количество интересных
                    страниц:</label>
                <div class="col-sm-4">
                    <select id="i_interestingPage" name="interestingPage"
                            class="form-control<?php if ($template->interestingPage > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->interestingPage == 0): ?> selected="selected"<?php endif; ?>>
                            Любое
                        </option>
                        <option value="5"<?php if ($template->interestingPage == 5): ?> selected="selected"<?php endif; ?>>
                            Не более 5
                        </option>
                        <option value="10"<?php if ($template->interestingPage == 10): ?> selected="selected"<?php endif; ?>>
                            Не более 10
                        </option>
                        <option value="20"<?php if ($template->interestingPage == 20): ?> selected="selected"<?php endif; ?>>
                            Не более 20
                        </option>
                        <option value="50"<?php if ($template->interestingPage == 50): ?> selected="selected"<?php endif; ?>>
                            Не более 50
                        </option>
                        <option value="100"<?php if ($template->interestingPage == 100): ?> selected="selected"<?php endif; ?>>
                            Не более 100
                        </option>
                        <option value="200"<?php if ($template->interestingPage == 200): ?> selected="selected"<?php endif; ?>>
                            Не более 200
                        </option>
                        <option value="500"<?php if ($template->interestingPage == 500): ?> selected="selected"<?php endif; ?>>
                            Не более 500
                        </option>
                        <option value="1000"<?php if ($template->interestingPage == 1000): ?> selected="selected"<?php endif; ?>>
                            Не более 1 000
                        </option>
                        <option value="2000"<?php if ($template->interestingPage == 2000): ?> selected="selected"<?php endif; ?>>
                            Не более 2 000
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['interestingPage-text']): ?>
                        <label class="control-label c_tooltip"
                               data-delay="<?= $app->settings['interestingPage-time']; ?>"
                               data-content="<?= $app->settings['interestingPage-text']; ?>"><img
                                    src="/img/icons/32/info.png" style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="i_frequencyPost" class="col-sm-5 control-label text-right">Частота постов на стене:</label>
                <div class="col-sm-4">
                    <select id="i_frequencyPost" name="frequencyPost"
                            class="form-control<?php if ($template->frequencyPost > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($template->frequencyPost == 0): ?> selected="selected"<?php endif; ?>>
                            Любая
                        </option>
                        <option value="1"<?php if ($template->frequencyPost == 1): ?> selected="selected"<?php endif; ?>>
                            Не более 1 поста в день
                        </option>
                        <option value="2"<?php if ($template->frequencyPost == 2): ?> selected="selected"<?php endif; ?>>
                            Не более 2 постов в день
                        </option>
                        <option value="5"<?php if ($template->frequencyPost == 5): ?> selected="selected"<?php endif; ?>>
                            Не более 5 постов в день
                        </option>
                        <option value="10"<?php if ($template->frequencyPost == 10): ?> selected="selected"<?php endif; ?>>
                            Не более 10 постов в день
                        </option>
                        <option value="20"<?php if ($template->frequencyPost == 20): ?> selected="selected"<?php endif; ?>>
                            Не более 20 постов в день
                        </option>
                        <option value="50"<?php if ($template->frequencyPost == 50): ?> selected="selected"<?php endif; ?>>
                            Не более 50 постов в день
                        </option>
                        <option value="100"<?php if ($template->frequencyPost == 100): ?> selected="selected"<?php endif; ?>>
                            Не более 100 постов в день
                        </option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <?php if ($app->settings['frequencyPost-text']): ?>
                        <label class="control-label c_tooltip" data-delay="<?= $app->settings['frequencyPost-time']; ?>"
                               data-content="<?= $app->settings['frequencyPost-text']; ?>"><img
                                    src="/img/icons/32/info.png" style="width: 14px; height: 14px;"/></label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-5 text-right">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-default<?php if ($template->prior): ?> active<?php endif; ?>">
                    <input name="prior" type="checkbox" id="i_check_prior" autocomplete="off"
                           <?php if ($template->prior): ?>checked="checked"<?php endif; ?>>Моё задание в начале очереди
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['prior-text']): ?>
                <label class="control-label c_tooltip" data-delay="<?= $app->settings['prior-time']; ?>"
                       data-content="<?= $app->settings['prior-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3 text-right">
            <h4>Создавать задания на посты</h4>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-5 text-right">
            <strong>Только от имени сообщества:</strong>
        </div>
        <div class="col-sm-1">
            <div class="material-switch">
                <input id="i_task_fromGroupOnly" type="checkbox" name="fromGroupOnly"
                       value="1" <?php if ($template->fromGroupOnly): ?> checked="checked"<?php endif; ?> />
                <label for="i_task_fromGroupOnly" class="label-primary">
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <?php if ($app->settings['fromGroupOnly-text']): ?>
                <label class="c_tooltip" data-delay="<?= $app->settings['fromGroupOnly-time']; ?>"
                       data-content="<?= $app->settings['fromGroupOnly-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                        style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label text-right">Только содержащие:</label>
        <div class="col-sm-4">
            <label class="control-label text-right">
                <input type="checkbox" id="i_attachmentType_main" class="c-check-attachmentType" name="attachmentType[]"
                       value="0" <?php if (!$template->attachmentType): ?> checked="checked"<?php endif; ?> />
                Все
            </label>
            <br/>
            <label class="control-label text-right">
                <input type="checkbox" class="c-check-attachmentType" name="attachmentType[]"
                       value="2" <?php if (!$template->attachmentType || $template->attachmentType & 2): ?> checked="checked"<?php endif; ?> />
                <span class="c-vk-icon" style="background-position: 0 -110px;">&nbsp;</span>
                Аудиозапись
            </label>
            <br/>
            <label class="control-label text-right">
                <input type="checkbox" class="c-check-attachmentType" name="attachmentType[]"
                       value="4" <?php if (!$template->attachmentType || $template->attachmentType & 4): ?> checked="checked"<?php endif; ?> />
                <span class="c-vk-icon" style="background-position: 0 -90px;">&nbsp;</span>
                Видеозапись
            </label>
            <br/>
            <label class="control-label text-right">
                <input type="checkbox" class="c-check-attachmentType" name="attachmentType[]"
                       value="8" <?php if (!$template->attachmentType || $template->attachmentType & 8): ?> checked="checked"<?php endif; ?> />
                <span class="c-vk-icon" style="background-position: 0 -135px;">&nbsp;</span>
                Документ
            </label>
            <br/>
            <label class="control-label text-right">
                <input type="checkbox" class="c-check-attachmentType" name="attachmentType[]"
                       value="16" <?php if (!$template->attachmentType || $template->attachmentType & 16): ?> checked="checked"<?php endif; ?> />
                <span class="c-vk-icon" style="background-position: 0 -70px;">&nbsp;</span>
                Изображение
            </label>
            <br/>
            <label class="control-label text-right">
                <input type="checkbox" class="c-check-attachmentType" name="attachmentType[]"
                       value="32" <?php if (!$template->attachmentType || $template->attachmentType & 32): ?> checked="checked"<?php endif; ?> />
                <span class="c-vk-icon" style="background-position: 0 -202px;">&nbsp;</span>
                Опрос
            </label>
            <br/>
            <label class="control-label text-right">
                <input type="checkbox" class="c-check-attachmentType" name="attachmentType[]"
                       value="64" <?php if (!$template->attachmentType || $template->attachmentType & 64): ?> checked="checked"<?php endif; ?> />
                <span class="c-vk-icon" style="background-position: 0 -245px;">&nbsp;</span>
                Текст
            </label>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['attachmentType-text']): ?>
                <label class="c_tooltip" data-delay="<?= $app->settings['attachmentType-time']; ?>"
                       data-content="<?= $app->settings['attachmentType-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                         style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <div class="col-sm-5 text-right">
            Исключать рекламу
            <span class="c-vk-icon" style="background-position: 0 -310px;">&nbsp;</span>
        </div>
        <div class="col-sm-2">
            <div class="material-switch">
                <input id="i-adsOut" type="checkbox" name="adsOut"
                       value="1" <?php if ($template->adsOut): ?> checked="checked"<?php endif; ?> />
                <label for="i-adsOut" class="label-primary">
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <?php if ($app->settings['adsOut-text']): ?>
                <label class="c_tooltip" data-delay="<?= $app->settings['adsOut-time']; ?>"
                       data-content="<?= $app->settings['adsOut-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                 style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3 text-right">
            <h4>Бюджет, лимиты и расписание</h4>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label text-right">Количество выполнений на пост:</label>
        <div class="col-sm-2">
            <input class="form-control" name="countFrom" value="<?= $template->countFrom; ?>"/>
            <label class="control-label"
                   style="position: absolute;top: -7px;left: 20px;background: #ffffff;font-weight: bold;padding: 0 5px">От</label>
        </div>
        <div class="col-sm-2">
            <input class="form-control" name="countTo" value="<?= $template->countTo; ?>"/>
            <label class="control-label"
                   style="position: absolute;top: -7px;left: 20px;background: #ffffff;font-weight: bold;padding: 0 5px">До</label>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['counts-text']): ?>
                <label class="control-label c_tooltip" data-delay="<?= $app->settings['counts-time']; ?>"
                       data-content="<?= $app->settings['counts-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                 style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div id="i_div_period" class="form-group">
        <label for="i_task_url" class="col-sm-5 control-label text-right">Собирать посты:</label>
        <div class="col-sm-4">
            <select class="form-control" name="weekDay">
                <option value="0">-- Периодичность --</option>
                <option value="1"<?php if ($template->weekDay == 1): ?> selected="selected"<?php endif; ?>>Понедельник
                </option>
                <option value="2"<?php if ($template->weekDay == 2): ?> selected="selected"<?php endif; ?>>Вторник
                </option>
                <option value="3"<?php if ($template->weekDay == 3): ?> selected="selected"<?php endif; ?>>Среда
                </option>
                <option value="4"<?php if ($template->weekDay == 4): ?> selected="selected"<?php endif; ?>>Четверг
                </option>
                <option value="5"<?php if ($template->weekDay == 5): ?> selected="selected"<?php endif; ?>>Пятница
                </option>
                <option value="6"<?php if ($template->weekDay == 6): ?> selected="selected"<?php endif; ?>>Суббота
                </option>
                <option value="7"<?php if ($template->weekDay == 7): ?> selected="selected"<?php endif; ?>>Воскресенье
                </option>
                <option value="8"<?php if ($template->weekDay == 8): ?> selected="selected"<?php endif; ?>>Единоразово
                </option>
                <option value="9"<?php if ($template->weekDay == 9): ?> selected="selected"<?php endif; ?>>Постоянно
                </option>
            </select>
        </div>
        <div class="col-sm-3">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <select id="i_hour" name="hour" class="form-control">
                            <?php for ($i = 0; $i < 24; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                <option value="<?= $val; ?>"<?php if ($val == $template->hourFrom): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td>по</td>
                    <td style="width: 50%;">
                        <select id="i_minute" name="minute" class="form-control">
                            <?php for ($i = 0; $i < 24; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                <option value="<?= $val; ?>"<?php if ($val == $template->hourTo): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label text-right">Лимит баллов для шаблона:</label>
        <div class="col-sm-3">
            <input class="form-control" name="balanceLimit" value="<?= $template->balanceLimit; ?>"/>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['balanceLimit-text']): ?>
                <label class="control-label c_tooltip" data-delay="<?= $app->settings['balanceLimit-time']; ?>"
                       data-content="<?= $app->settings['balanceLimit-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                       style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div id="i_div_sum" class="form-group">
        <label for="i_task_url" class="col-sm-5 control-label text-right">Цена одного выполнения:</label>
        <div class="col-sm-4">
            <input class="form-control" id="i_task_price" disabled="disabled" value="<?= $template->price; ?> баллов">
        </div>
    </div>
    <div class="form-group">
        <hr/>
    </div>
    <div id="i_div_sum" class="form-group">
        <label for="i_task_url" class="col-sm-5 control-label text-right">Название шаблона:</label>
        <div class="col-sm-4">
            <input class="form-control" name="title" value="<?= $template->title; ?>">
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['title-text']): ?>
                <label class="control-label c_tooltip" data-delay="<?= $app->settings['title-time']; ?>"
                       data-content="<?= $app->settings['title-text']; ?>"><img src="/img/icons/32/info.png"
                                                                                style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div id="i-templateAdd-result"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-5">
            <button type="button" class="btn btn-primary btn-lg"
                    onclick="auto.templateAdd()"><?php if ($template->templateId > 0): ?>Сохранить<?php else: ?>Добавить<?php endif; ?></button>
        </div>

    </div>
</form>

<script>
    $('#i-specialId').click(function () {
        $('#i_task_minKarma > option').each(function () {
            $(this).prop('selected', false);
        });
        if ($(this).prop('checked')) {
            $('#i_task_minKarma > option[value=0]').prop('disabled', true);
            $('#i_task_minKarma > option[value=25]').prop('disabled', true);
            $('#i_task_minKarma > option[value=50]').prop('selected', true);
        } else {
            $('#i_task_minKarma > option[value=0]').prop('disabled', false);
            $('#i_task_minKarma > option[value=25]').prop('disabled', false);
        }
    });

    task_edit.prices = <?= json_encode($vars['prices']); ?>;
    task_edit.percents = <?= json_encode($vars['percents']); ?>;
    task_edit.percentsVals = <?= json_encode($vars['percentsVals']); ?>;
    task_edit.userCityId = <?= intval($user->cityId); ?>;
    task_edit.init();
    task_edit.calculateSum();
    $('.c_tooltip').popover({
        placement: 'right',
        trigger: 'hover',
        html: true
    });
    $('.c-check-attachmentType').click(function () {
        var id = $(this).attr('id');
        if (id == 'i_attachmentType_main') {
            $('.c-check-attachmentType').prop('checked', $(this).prop('checked'));
        } else {
            $('#i_attachmentType_main').prop('checked', false);
        }
    });
</script>