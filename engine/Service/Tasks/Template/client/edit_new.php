<?php
/** @var \Service\Tasks\Model_Tasks_Task $task */
$task = $vars['task'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];

/** @var \System\App $app */
$app = $vars['app'];

?>

<form id="i_form_task_add" action="" method="POST" enctype="multipart/form-data" target="_self" class="form-horizontal"
      role="form">
    <input type="hidden" id="i_form_task_add_action" name="action" value="<?= $vars['action']; ?>"/>
    <input type="hidden" id="i_form_task_add_taskId" name="taskId" value="<?= $task->taskId; ?>"/>
<!--    <input type="hidden" id="ownerType" name="ownerType" value="">-->
    <div class="form-group">
        <div class="btn-group btn-group-justified" data-toggle="buttons">
            <label class="btn btn-primary<?php if ($task->type == 'likes' || $task->type == ''): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="likes" id="likes"
                       autocomplete="off"<?php if ($task->type == 'likes' || $task->type == ''): ?> checked<?php endif; ?>>
                Лайки
            </label>
            <label class="btn btn-primary<?php if ($task->type == 'reposts'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="reposts" id="reposts"
                       autocomplete="off"<?php if ($task->type == 'reposts'): ?> checked<?php endif; ?>> Репосты
            </label>
            <label style="width: 13%;"
                   class="btn btn-primary<?php if ($task->type == 'join'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="join" id="join"
                       autocomplete="off"<?php if ($task->type == 'join'): ?> checked<?php endif; ?>> Подписчики
            </label>
            <label class="btn btn-primary<?php if ($task->type == 'friends'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="friends" id="friend"
                       autocomplete="off"<?php if ($task->type == 'friends'): ?> checked<?php endif; ?>> Друзья
            </label>
            <label style="width: 13%;"
                   class="btn btn-primary<?php if ($task->type == 'comments'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="comments" id="comments"
                       autocomplete="off"<?php if ($task->type == 'comments'): ?> checked<?php endif; ?>> Комментарии
            </label>
            <label class="btn btn-primary<?php if ($task->type == 'polls'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="polls" id="polls"
                       autocomplete="off"<?php if ($task->type == 'polls'): ?> checked<?php endif; ?>> Опросы
            </label>
            <label style="width: 17%;"
                   class="btn btn-primary<?php if ($task->type == 'views'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="views" id="views"
                       autocomplete="off"<?php if ($task->type == 'views'): ?> checked<?php endif; ?>> Просмотры постов
            </label>
            <label style="width: 17%;"
                   class="btn btn-primary<?php if ($task->type == 'video'): ?> active<?php endif; ?> c_type">
                <input type="radio" name="type" value="video" id="video"
                       autocomplete="off"<?php if ($task->type == 'video'): ?> checked<?php endif; ?>> Просмотры видео
            </label>
        </div>
    </div>

    <div id="i_div_vkTypes">
        <div class="form-group">
            <select class="form-control" name="vkType">
                <?php foreach ($vars['vkTypes'] as $vkType => $vkTitle): ?>
                    <option id="i_vkType_<?= $vkType; ?>"
                            value="<?= $vkType; ?>"<?php if ($task->vkType == $vkType || (!$task->vkType && $vkType == 'post')): ?> selected="selected"<?php endif; ?>><?= $vkTitle; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php foreach ($vars['types'] as $type): ?>
        <div class="c-tip-type" id="i-tip-type-<?= $type; ?>"
             <?php if ($task->type != $type): ?>style="display: none;"<?php endif; ?>>
            <?= str_replace('%uid%', $app->User->uid, $app->settings['tip_' . $type]); ?>
        </div>
        <?php foreach ($vars['vkTypes'] as $vkType => $title): ?>
            <div class="c-tip-vkType" id="i-tip-vkType-<?= $type; ?>-<?= $vkType; ?>" style="display: none;">
                <?= isset($app->settings['tip_' . $type . '_' . $vkType]) ? str_replace('%uid%', $app->User->uid,
                    $app->settings['tip_' . $type . '_' . $vkType]) : ''; ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-9">
                <div class="alert alert-danger">
                    <?= implode('<br />', $vars['errors']); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div id="i_div_task_url" class="form-group">
        <label for="i_task_url" class="col-sm-5 control-label text-right">Ссылка:</label>
        <div class="col-sm-4">
            <input type="hidden" class="form-control" id="i_task_url_last" value=""/>
            <input type="text" class="form-control" id="i_task_url" name="url" placeholder=""
                   value="<?= $task ? $task->url : ''; ?>"/>
        </div>
        <div class="col-sm-1">
            <?php if ($app->settings['url-text']): ?>
                <label class="control-label c_tooltip" data-delay="<?= $app->settings['url-time']; ?>"
                       data-content="<?= $app->settings['url-text']; ?>"><img src="/img/icons/32/info.png"
                                                                              style="width: 14px; height: 14px;"/></label>
            <?php endif; ?>
        </div>
    </div>
    <div id="i_div_task_url_result">
        <div id="i_div_task_url_result_text" class="col-sm-offset-5 col-sm-4"></div>
    </div>
    <div id="i_div_comments" <?php if ($task->type != 'comments'): ?> style="display: none;"<?php endif; ?>>
        <div class="form-group">
            <label for="i_commentType" class="col-sm-5 control-label text-right">хочу</label>
            <div class="col-sm-4">
                <select id="i_commentType" name="commentType" class="form-control">
                    <option value="0" <?php if ($task->commentType == 0): ?> selected="selected"<?php endif; ?>>любые
                    </option>
                    <option value="1" <?php if ($task->commentType == 1): ?> selected="selected"<?php endif; ?>>
                        положительные
                    </option>
                    <option value="2" <?php if ($task->commentType == 2): ?> selected="selected"<?php endif; ?>>
                        отрицательные
                    </option>
                    <option value="3" <?php if ($task->commentType == 3): ?> selected="selected"<?php endif; ?>>
                        заданные
                    </option>
                </select>
            </div>
            <label for="i_commentType" class="col-sm-3 control-label" style="text-align: left;">комментарии</label>
        </div>
        <div class="form-group">
            <div class="col-sm-9"
                 id="i_div_comments_list" <?php if ($task->commentType != 3): ?> style="display: none;"<?php endif; ?>>
                <?php $comments = $task->getComments(); ?>
                <?php foreach ($comments as $comment): ?>
                    <input class="form-control c_input_comments" name="comments[]"
                           placeholder="Введите сюда текст комментария" value="<?= $comment; ?>"/>
                <?php endforeach; ?>
                <input class="form-control c_input_comments" name="comments[]"
                       placeholder="Введите сюда текст комментария" value=""/>
            </div>
        </div>
    </div>
    <div id="i_div_minKarma" class="form-group">
        <label for="i_task_minKarma" class="col-sm-5 control-label text-right">Пользователям с уровнем кармы:</label>
        <div class="col-sm-4">
            <select id="i_task_minKarma" name="minKarma"
                    class="form-control<?php if ($task->minKarma > 0): ?> alert-info<?php endif; ?>">
                <option value="0" <?php if ($task->minKarma == 0): ?> selected="selected"<?php endif; ?>>Любой</option>
                <option value="25" <?php if ($task->minKarma == 25): ?> selected="selected"<?php endif; ?>>Выше 25%
                </option>
                <option value="50" <?php if ($task->minKarma == 50): ?> selected="selected"<?php endif; ?>>Выше 50%
                </option>
                <option value="75" <?php if ($task->minKarma == 75): ?> selected="selected"<?php endif; ?>>Выше 75%
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
                           class="btn btn-default<?php if ($task->targeting): ?> active<?php endif; ?>">
                        <input name="targeting" type="checkbox" autocomplete="off">Таргетинг и настройка
                    </label>
                </div>
            </div>
        </div>
        <div id="i_div_targeting" <?php if (!$task->targeting): ?>style="display: none;"<?php endif; ?>>
            <div class="form-group">
                <label for="i_sex" class="col-sm-5 control-label text-right">Хочу, что бы задание выполняли:</label>
                <div class="col-sm-4">
                    <select id="i_sex" name="sex"
                            class="form-control<?php if ($task->sex > 0): ?> alert-info<?php endif; ?>">
                        <option value="0" <?php if ($task->sex == 0): ?> selected="selected"<?php endif; ?>>и парни и
                            девушки
                        </option>
                        <option value="2" <?php if ($task->sex == 2): ?> selected="selected"<?php endif; ?>>только
                            парни
                        </option>
                        <option value="1" <?php if ($task->sex == 1): ?> selected="selected"<?php endif; ?>>только
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
                <label for="i_ageFrom" class="col-sm-5 control-label text-right">Возраст:</label>
                <div class="col-sm-2">
                    <select id="i_ageFrom" name="ageFrom"
                            class="form-control<?php if ($task->ageFrom > 0): ?> alert-info<?php endif; ?>">
                        <option value="0">любой</option>
                        <?php for ($i = 14; $i <= 80; $i++): ?>
                            <option value="<?= $i; ?>"<?php if ($task->ageFrom == $i): ?> selected="selected"<?php endif; ?>>
                                от <?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select id="i_ageTo" name="ageTo"
                            class="form-control<?php if ($task->ageTo > 0): ?> alert-info<?php endif; ?>">
                        <option value="0">любой</option>
                        <?php for ($i = 14; $i <= 80; $i++): ?>
                            <option value="<?= $i; ?>"<?php if ($task->ageTo == $i): ?> selected="selected"<?php endif; ?>>
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
                <label for="i_city" class="col-sm-5 control-label text-right">Город:</label>
                <div class="col-sm-4">
                    <select id="i_city" name="city"
                            class="form-control<?php if ($task->cityId > 0 || $task->countryId > 0): ?> alert-info<?php endif; ?>">
                        <option value="0">любой</option>
                        <?php foreach ($vars['cities'] as $city): if ($city->cityId != $user->cityId) {
                        continue;
                    } ?>
                            <option data-type="city"
                                    value="<?= $user->cityId; ?>"<?php if ($task->cityId == $user->cityId): ?> selected="selected"<?php endif; ?>>
                                <strong><?= $user->city; ?></strong></option>
                        <?php endforeach; ?>
                        <?php foreach ($vars['countries'] as $country): ?>
                            <option data-type="country"
                                    value="<?= $country->countryId; ?>"<?php if ($task->countryId == $country->countryId): ?> selected="selected"<?php endif; ?>><?= $country->title; ?></option>
                        <?php endforeach; ?>
                        <?php foreach ($vars['cities'] as $city): if ($city->cityId == $user->cityId) {
                        continue;
                    } ?>
                            <option data-type="city"
                                    value="<?= $city->cityId; ?>"<?php if ($task->cityId == $city->cityId): ?> selected="selected"<?php endif; ?>><?= $city->title; ?></option>
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
                            class="form-control<?php if ($task->relation > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->relation == 0): ?> selected="selected"<?php endif; ?>>любой
                        </option>
                        <option value="1"<?php if ($task->relation == 1): ?> selected="selected"<?php endif; ?>>не
                            женат/не замужем
                        </option>
                        <option value="2"<?php if ($task->relation == 2): ?> selected="selected"<?php endif; ?>>есть
                            друг/есть подруга
                        </option>
                        <option value="3"<?php if ($task->relation == 3): ?> selected="selected"<?php endif; ?>>
                            помолвлен/помолвлена
                        </option>
                        <option value="4"<?php if ($task->relation == 4): ?> selected="selected"<?php endif; ?>>
                            женат/замужем
                        </option>
                        <option value="5"<?php if ($task->relation == 5): ?> selected="selected"<?php endif; ?>>всё
                            сложно
                        </option>
                        <option value="6"<?php if ($task->relation == 6): ?> selected="selected"<?php endif; ?>>в
                            активном поиске
                        </option>
                        <option value="7"<?php if ($task->relation == 7): ?> selected="selected"<?php endif; ?>>
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
                            class="form-control<?php if ($task->avatarCount > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->avatarCount == 0): ?> selected="selected"<?php endif; ?>>
                            любое
                        </option>
                        <option value="1"<?php if ($task->avatarCount == 1): ?> selected="selected"<?php endif; ?>>не
                            менее 1
                        </option>
                        <option value="2"<?php if ($task->avatarCount == 2): ?> selected="selected"<?php endif; ?>>не
                            менее 2
                        </option>
                        <option value="5"<?php if ($task->avatarCount == 5): ?> selected="selected"<?php endif; ?>>не
                            менее 5
                        </option>
                        <option value="10"<?php if ($task->avatarCount == 10): ?> selected="selected"<?php endif; ?>>не
                            менее 10
                        </option>
                        <option value="20"<?php if ($task->avatarCount == 20): ?> selected="selected"<?php endif; ?>>не
                            менее 20
                        </option>
                        <option value="50"<?php if ($task->avatarCount == 50): ?> selected="selected"<?php endif; ?>>не
                            менее 50
                        </option>
                        <option value="100"<?php if ($task->avatarCount == 100): ?> selected="selected"<?php endif; ?>>
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
                            class="form-control<?php if ($task->filled > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->filled == 0): ?> selected="selected"<?php endif; ?>>Любая
                        </option>
                        <option value="1"<?php if ($task->filled == 1): ?> selected="selected"<?php endif; ?>>Не менее 1
                            раздела
                        </option>
                        <option value="2"<?php if ($task->filled == 2): ?> selected="selected"<?php endif; ?>>Не менее 2
                            разделов
                        </option>
                        <option value="3"<?php if ($task->filled == 3): ?> selected="selected"<?php endif; ?>>Не менее 3
                            разделов
                        </option>
                        <option value="4"<?php if ($task->filled == 4): ?> selected="selected"<?php endif; ?>>Не менее 4
                            разделов
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
                            class="form-control<?php if ($task->pageAge > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->pageAge == 0): ?> selected="selected"<?php endif; ?>>Любой
                        </option>
                        <option value="1"<?php if ($task->pageAge == 1): ?> selected="selected"<?php endif; ?>>Не менее
                            3 месяцев
                        </option>
                        <option value="2"<?php if ($task->pageAge == 2): ?> selected="selected"<?php endif; ?>>Не менее
                            полугода
                        </option>
                        <option value="3"<?php if ($task->pageAge == 3): ?> selected="selected"<?php endif; ?>>Не менее
                            1 года
                        </option>
                        <option value="4"<?php if ($task->pageAge == 4): ?> selected="selected"<?php endif; ?>>Не менее
                            2 лет
                        </option>
                        <option value="5"<?php if ($task->pageAge == 5): ?> selected="selected"<?php endif; ?>>Не менее
                            3 лет
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
                            class="form-control<?php if ($task->followersCount > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->followersCount == 0): ?> selected="selected"<?php endif; ?>>
                            Любое
                        </option>
                        <option value="10"<?php if ($task->followersCount == 10): ?> selected="selected"<?php endif; ?>>
                            Не менее 10
                        </option>
                        <option value="50"<?php if ($task->followersCount == 50): ?> selected="selected"<?php endif; ?>>
                            Не менее 50
                        </option>
                        <option value="100"<?php if ($task->followersCount == 100): ?> selected="selected"<?php endif; ?>>
                            Не менее 100
                        </option>
                        <option value="200"<?php if ($task->followersCount == 200): ?> selected="selected"<?php endif; ?>>
                            Не менее 200
                        </option>
                        <option value="500"<?php if ($task->followersCount == 500): ?> selected="selected"<?php endif; ?>>
                            Не менее 500
                        </option>
                        <option value="1000"<?php if ($task->followersCount == 1000): ?> selected="selected"<?php endif; ?>>
                            Не менее 1 000
                        </option>
                        <option value="5000"<?php if ($task->followersCount == 5000): ?> selected="selected"<?php endif; ?>>
                            Не менее 5 000
                        </option>
                        <option value="10000"<?php if ($task->followersCount == 10000): ?> selected="selected"<?php endif; ?>>
                            Не менее 10 000
                        </option>
                        <option value="20000"<?php if ($task->followersCount == 20000): ?> selected="selected"<?php endif; ?>>
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
                            class="form-control<?php if ($task->interestingPage > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->interestingPage == 0): ?> selected="selected"<?php endif; ?>>
                            Любое
                        </option>
                        <option value="5"<?php if ($task->interestingPage == 5): ?> selected="selected"<?php endif; ?>>
                            Не более 5
                        </option>
                        <option value="10"<?php if ($task->interestingPage == 10): ?> selected="selected"<?php endif; ?>>
                            Не более 10
                        </option>
                        <option value="20"<?php if ($task->interestingPage == 20): ?> selected="selected"<?php endif; ?>>
                            Не более 20
                        </option>
                        <option value="50"<?php if ($task->interestingPage == 50): ?> selected="selected"<?php endif; ?>>
                            Не более 50
                        </option>
                        <option value="100"<?php if ($task->interestingPage == 100): ?> selected="selected"<?php endif; ?>>
                            Не более 100
                        </option>
                        <option value="200"<?php if ($task->interestingPage == 200): ?> selected="selected"<?php endif; ?>>
                            Не более 200
                        </option>
                        <option value="500"<?php if ($task->interestingPage == 500): ?> selected="selected"<?php endif; ?>>
                            Не более 500
                        </option>
                        <option value="1000"<?php if ($task->interestingPage == 1000): ?> selected="selected"<?php endif; ?>>
                            Не более 1 000
                        </option>
                        <option value="2000"<?php if ($task->interestingPage == 2000): ?> selected="selected"<?php endif; ?>>
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
                            class="form-control<?php if ($task->frequencyPost > 0): ?> alert-info<?php endif; ?>">
                        <option value="0"<?php if ($task->frequencyPost == 0): ?> selected="selected"<?php endif; ?>>
                            Любая
                        </option>
                        <option value="1"<?php if ($task->frequencyPost == 1): ?> selected="selected"<?php endif; ?>>Не
                            более 1 поста в день
                        </option>
                        <option value="2"<?php if ($task->frequencyPost == 2): ?> selected="selected"<?php endif; ?>>Не
                            более 2 постов в день
                        </option>
                        <option value="5"<?php if ($task->frequencyPost == 5): ?> selected="selected"<?php endif; ?>>Не
                            более 5 постов в день
                        </option>
                        <option value="10"<?php if ($task->frequencyPost == 10): ?> selected="selected"<?php endif; ?>>
                            Не более 10 постов в день
                        </option>
                        <option value="20"<?php if ($task->frequencyPost == 20): ?> selected="selected"<?php endif; ?>>
                            Не более 20 постов в день
                        </option>
                        <option value="50"<?php if ($task->frequencyPost == 50): ?> selected="selected"<?php endif; ?>>
                            Не более 50 постов в день
                        </option>
                        <option value="100"<?php if ($task->frequencyPost == 100): ?> selected="selected"<?php endif; ?>>
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
                <label class="btn btn-default<?php if ($task->prior): ?> active<?php endif; ?>">
                    <input name="prior" type="checkbox" id="i_check_prior" autocomplete="off"
                           <?php if ($task->prior): ?>checked<?php endif; ?>>Моё задание в начале очереди
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
        <label for="i_task_url" class="col-sm-5 control-label text-right">Количество:</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="i_task_count" name="count" placeholder=""
                   value="<?= $task->count ?: 5; ?>"/>
            <div class="alert alert-info" id="i_count_recomended" style="display: none;"></div>
        </div>
    </div>
    <div id="i_div_sum" class="form-group">
        <label for="i_task_url" class="col-sm-5 control-label text-right">Стоимость:</label>
        <div class="col-sm-4">
            <input class="form-control" id="i_task_sum" disabled="disabled" value="0 баллов">
        </div>
    </div>
    <div class="form-group">
        <div id="i_div_sum_result" class="col-sm-4 col-sm-offset-5 alert alert-error" style="display: none;">
            На балансе недостаточно средств для начала задания.
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-5">
            <button id="i_button_submit" type="button" class="button-green"
                    style="display: block; width: 100%; max-width: none;">
                <div class="icon"
                     style="background: url(/img/icon-create.png) no-repeat center center; background-size: contain;"></div>
                <div class="name">Создать задание</div>
            </button>
        </div>
    </div>

</form>

<div style="height: 100px;"></div>
<script>
    task_edit.prices = <?= json_encode($vars['prices']); ?>;
    task_edit.percents = <?= json_encode($vars['percents']); ?>;
    task_edit.percentsVals = <?= json_encode($vars['percentsVals']); ?>;
    task_edit.userCityId = <?= intval($user->cityId); ?>;

    $(document).ready(function () {
        task_edit.vkTypeChange();
        $('select[name="vkType"]').change(function () {
            task_edit.vkTypeChange();
        });
    });

    $('#i_button_submit').click(function () {

        if (task_edit.type == 'join' && !task_edit.accept) {
            if (task_edit.members_count < task_edit.limits.counts[0]) {
                var count = $('#i_task_count').val();
                if (parseInt(count) > parseInt(task_edit.limits.limits[0])) {
                    $('#i_dialog_task_accept').modal();
                    return false;
                }
            }
        }
        $('#i_form_task_add_action').val('task_check');
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i_form_task_add').serialize(),
            beforeSend: function () {
                $('#i_dialog_task_check_error').removeClass('alert alert-danger').html('');
                $('#i_dialog_task_check_progress').show();
                $('#i_dialog_task_check').modal();
            },
            success: function (data) {
                console.log(data)
                if (data.success) {
                    $('#i_form_task_add_action').val('<?= $vars['action']; ?>');
                    $('#i_form_task_add').submit();
                } else {
                    $('#i_dialog_task_check_progress').hide();
                    $('#i_dialog_task_check_data').html(data.html);
                }
            },
            error: function () {
                $('#i_dialog_task_check_error').addClass('alert alert-danger').html('Ошибка при добавлении задания');
            }
        });
    });
    $('#i_task_minKarma').change(function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'countUsers',
                karma: $('#i_task_minKarma').val(),
                sex: $('#i_sex').val(),
                ageFrom: $('#i_ageFrom').val(),
                ageTo: $('#i_ageTo').val(),
                city: $('#i_city').val(),
                relation: $('#i_relation').val(),
                avatarCount: $('#i_avatarCount').val(),
                filled: $('#i_filled').val(),
                pageAge: $('#i_pageAge').val(),
                followersCount: $('#i_followersCount').val(),
                interestingPage: $('#i_interestingPage').val(),
                frequencyPost: $('#i_frequencyPost').val()
            },
            success: function (data) {

                $('#i_count_recomended').hide();

                if (data.success) {

                    $('#i_count_recomended').html("Рекомендуемое количество: <strong>не более " + data.userCount + "</strong>").show()
                        .removeClass("alert-danger")
                        .removeClass("alert-warning")
                        .removeClass("alert-info")
                        .removeClass("alert-success");

                    if (data.userCount < 10) {
                        $('#i_count_recomended').addClass('alert-danger');
                    }
                    if (data.userCount > 10 && data.userCount < 30) {
                        $('#i_count_recomended').addClass('alert-warning');
                    }
                    if (data.userCount > 30 && data.userCount < 100) {
                        $('#i_count_recomended').addClass('alert-info');
                    }
                    if (data.userCount > 100) {
                        $('#i_count_recomended').addClass('alert-success');
                    }
                }
            }
        });
    });
    $('#i_div_targeting select').change(function () {

        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'countUsers',
                karma: $('#i_task_minKarma').val(),
                sex: $('#i_sex').val(),
                ageFrom: $('#i_ageFrom').val(),
                ageTo: $('#i_ageTo').val(),
                city: $('#i_city').val(),
                relation: $('#i_relation').val(),
                avatarCount: $('#i_avatarCount').val(),
                filled: $('#i_filled').val(),
                pageAge: $('#i_pageAge').val(),
                followersCount: $('#i_followersCount').val(),
                interestingPage: $('#i_interestingPage').val(),
                frequencyPost: $('#i_frequencyPost').val()
            },
            success: function (data) {
                $('#i_count_recomended').hide();
                if (data.success) {
                    $('#i_count_recomended').html("Рекомендуемое количество: <strong>не более " + data.userCount + "</strong>").show()
                        .removeClass("alert-danger")
                        .removeClass("alert-warning")
                        .removeClass("alert-info")
                        .removeClass("alert-success");


                    if (data.userCount < 10) {
                        $('#i_count_recomended').addClass('alert-danger');
                    }
                    if (data.userCount > 10 && data.userCount < 30) {
                        $('#i_count_recomended').addClass('alert-warning');
                    }
                    if (data.userCount > 30 && data.userCount < 100) {
                        $('#i_count_recomended').addClass('alert-info');
                    }
                    if (data.userCount > 100) {
                        $('#i_count_recomended').addClass('alert-success');
                    }
                }
            }
        });
    });
</script>
<div class="modal fade" id="i_dialog_task_check" tabindex="-1" role="dialog" aria-labelledby="i_dialog_task_check">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_task_check_label">Добавление задания</h5>
            </div>
            <div class="modal-body" id="i_dialog_task_check_container">
                <div id="i_dialog_task_check_data"></div>
                <div id="i_dialog_task_check_error"></div>
                <div id="i_dialog_task_check_progress" class="progress progress-striped active" style="display: none;">
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

<div class="modal fade" id="i_dialog_task_accept" tabindex="-1" role="dialog" aria-labelledby="i_dialog_task_accept">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_task_accept_label">Добавление задания</h5>
            </div>
            <div class="modal-body" id="i_dialog_task_accept_container">
                <div id="i_dialog_task_accept_data">
                    <div class="alert alert-info text-center">
                        <h4>Внимание!</h4>
                        <h5>В Вашей группе слишком мало подписчиков.</h5>
                        <h5>Скорость накрутки будет снижена, с целью не привлекать лишнее внимание администрации
                            Вконтакте к Вашему сообществу, и <strong>не допустить</strong> списания подписчиков и/или
                            блокировки сообщества.</h5>
                        <h5>Подписчики будут поступать в Ваше сообщество с сервиса равномерно.</h5>
                    </div>
                    <div class="text-center">
                        <button id="i_button_task_accept" class="btn btn-primary">Спасибо, всё понятно</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#i_button_task_accept').click(function () {
        task_edit.accept = true;
        $('#i_dialog_task_accept').modal('hide');
        $('#i_button_submit').trigger('click');
    });
</script>