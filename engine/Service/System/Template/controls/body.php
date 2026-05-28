<?php
/** @var \System\App $app */
$app = $vars['app'];
$settings = \Service\News\Model_Config::GetSettings();
?>
<div class="container" style="margin-top: 30px;">
    <div class="row" style="position: relative;">
        <div class="col-sm-2">
            <a class="button-green button-green-task-add" href="/tasks/add" style="display: block">
                <div class="icon-task-add"></div>
                <div class="name" style="display: block;">Создать задание</div>
            </a>
            <ul class="left-menu">
                <li class="icon-my-work<?php if ($app->page == 'tasks-my'): ?> active<?php endif; ?>">
                    <a href="/tasks/my/all/1">Мои задания</a>
                </li>
                <li class="icon-buy<?php if ($app->page == 'buy'): ?> active<?php endif; ?>">
                    <a href="/orders/buy">Купить баллы</a>
                </li>
                <li class="icon-work<?php if ($app->page == 'tasks-work'): ?> active<?php endif; ?>">
                    <a href="/tasks/all">Все задания</a>
                </li>
            </ul>
            <hr>
            <ul class="left-menu">
                <li class="icon-auto<?php if ($app->page == 'auto'): ?> active<?php endif; ?>">
                    <a href="/auto">
                        Автоведение
                        <?php if (isset($app->notifications['auto'])): $texts = [];

                            foreach ($app->notifications['auto'] as $notification) {
                                $texts[] = $notification->title;
                            } ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['auto_time']; ?>"
                                 data-content="<?= \Lib_Html::ChangeTags(\Lib_Html::ChangeQuotes(implode('<br />',
                                     $texts))); ?>" src="/img/icons/32/info_active.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php else: ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['auto_time']; ?>"
                                 data-content="<?= $app->settings['auto_text']; ?>" src="/img/icons/32/info.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="icon-posting<?php if ($app->page == 'posting'): ?> active<?php endif; ?>">
                    <a href="/posting">
                        Автопостинг
                        <?php if (isset($app->notifications['posting'])): $texts = [];

                            foreach ($app->notifications['posting'] as $notification) {
                                $texts[] = $notification->title;
                            } ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['posting_time']; ?>"
                                 data-content="<?= \Lib_Html::ChangeTags(\Lib_Html::ChangeQuotes(implode('<br />',
                                     $texts))); ?>" src="/img/icons/32/info_active.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php else: ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['posting_time']; ?>"
                                 data-content="<?= $app->settings['postiong_text']; ?>" src="/img/icons/32/info.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="icon-grabber<?php if ($app->page == 'grabber'): ?> active<?php endif; ?>">
                    <a href="/grabber">
                        Граббер
                        <?php if (isset($app->notifications['grabber'])): $texts = [];

                            foreach ($app->notifications['grabber'] as $notification) {
                                $texts[] = $notification->title;
                            } ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['grabber_time']; ?>"
                                 data-content="<?= \Lib_Html::ChangeTags(\Lib_Html::ChangeQuotes(implode('<br />',
                                     $texts))); ?>" src="/img/icons/32/info_active.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php else: ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['grabber_time']; ?>"
                                 data-content="<?= $app->settings['grabber_text']; ?>" src="/img/icons/32/info.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="icon-special<?php if ($app->page == 'special'): ?> active<?php endif; ?>">
                    <a href="/tasks/special">
                        Спецзадания
                        <?php if (isset($app->notifications['special'])): $texts = [];

                            foreach ($app->notifications['special'] as $notification) {
                                $texts[] = $notification->title;
                            } ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['special_time']; ?>"
                                 data-content="<?= \Lib_Html::ChangeTags(\Lib_Html::ChangeQuotes(implode('<br />',
                                     $texts))); ?>" src="/img/icons/32/info_active.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php else: ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['special_time']; ?>"
                                 data-content="<?= $app->settings['special_text']; ?>" src="/img/icons/32/info.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="icon-bot<?php if ($app->page == 'bot'): ?> active<?php endif; ?>">
                    <a href="/bot">
                        Автобот
                        <?php if (isset($app->notifications['bot'])): $texts = [];

                            foreach ($app->notifications['bot'] as $notification) {
                                $texts[] = $notification->title;
                            } ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['bot_time']; ?>"
                                 data-content="<?= \Lib_Html::ChangeTags(\Lib_Html::ChangeQuotes(implode('<br />',
                                     $texts))); ?>" src="/img/icons/32/info_active.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php else: ?>
                            <img class="c_tooltip" data-delay="<?= $app->settings['bot_time']; ?>"
                                 data-content="<?= $app->settings['bot_text']; ?>" src="/img/icons/32/info.png"
                                 style="float: right; width: 14px; height: 14px; margin-right: 5px;"/>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-sm-8">
            <?= $vars['html']; ?>
        </div>
        <div style="position: absolute; top: 0; right: -30px; padding: 0; width: 200px;" class="col-sm-2">
            <?php if ($app->UserIsAuth() && $app->User->karma < 0): ?>
                <a id="i_button_karma_clear" class="button-green" style="width: 100%; margin-bottom: 10px;">
                    <img src="/img/icons/32/icon-karma-white.png" width="32">
                    <div class="name">Очистка кармы</div>
                </a>
                <div class="modal fade" id="i_karma_clear" tabindex="-1" role="dialog" aria-labelledby="i_karma_clear">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title" id="i_karma_clear_label">Очистка кармы</h5>
                            </div>
                            <div class="modal-body" id="i_dialog_group_add_container">
                                <div id="i_karma_clear_data">
                                    <p>Уровень вашей кармы ушёл в отрицательное значение, т.к. вы нарушили правила
                                        сервиса, отменили совершенные ранее действия, либо ваша страница Вконтакте была
                                        заблокирована за подозрительную активность.</p>
                                    <p>Пока уровень вашей кармы находится в отрицательном значении:</p>
                                    <ul>
                                        <li>Вы видите меньше доступных заданий, т.к. пользователи вам меньше доверяют
                                            или не доверяют вообще.
                                        </li>
                                        <li>Вы получаете в два раза меньше баллов за выполнение заданий.</li>
                                        <li>Уровень вашей кармы при выполнении заданий растет в два раза медленнее.</li>
                                        <li>Вам не доступно получение ежедневного и еженедельного бонусов, за выполнение
                                            заданий.
                                        </li>
                                        <li>Вам не доступны ежедневный и еженедельный бонусы.</li>
                                    </ul>
                                    <p>Выходом из сложившейся ситуации будет – поднимать уровень кармы вручную, путём
                                        выполнения доступных задания, не нарушая правил сервиса.
                                        Либо, воспользоваться функцией – Очистка кармы, и пообещать, более не нарушать
                                        правила сервиса.
                                    </p>
                                    <p>
                                        Это
                                        ваше <?= $app->User->karmaMinus == 1 ? 'первое' : ($app->User->karmaMinus == 2 ? 'второе' : 'третье и более'); ?>
                                        серьезное нарушение правил.
                                        Очистив карму в день, когда вас поймали на нарушениях правил, вы не лишитесь
                                        возможности получить ежедневный, а так же, еженедельный бонусы.
                                        Так же, уровень вашей кармы после очистки будет составлять 50%.
                                    </p>
                                    <p>Стоимость очистки кармы для вас: <?= $app->User->getKarmaPrice(); ?> рублей.</p>
                                </div>
                                <div id="i_karma_clear_error"></div>
                                <div id="i_karma_clear_progress" class="progress progress-striped active"
                                     style="display: none;">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                         aria-valuemax="100" style="width: 100%">
                                        <span class="sr-only">&nbsp;</span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="modal-footer">
                                <form class="form-horizontal" action="/orders/order" method="post">
<!--                                    <input name="shopId" value="--><?//= shopId; ?><!--" type="hidden"/>-->
<!--                                    <input name="scid" value="--><?//= scid; ?><!--" type="hidden"/>-->
                                    <input name="customerNumber" value="<?= $app->User->userId; ?>" type="hidden"/>
                                    <input name="sum" value="<?= $app->User->getKarmaPrice(); ?>" type="hidden"/>
                                    <input name="type" value="karmaMinus" type="hidden"/>
                                    <button id="i_karma_clear_cancel" type="button" class="btn btn-default"
                                            data-dismiss="modal">Отмена
                                    </button>
                                    <button id="i_karma_clear_save" type="submit" class="btn btn-success">Очистить
                                        карму
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div>

                <?php if ($app->page == 'users' or $app->page == 'faq'): ?>
                    <?= \STPL::FetchAction($app, 'Users/Widget_Menu', []); ?>
                <?php endif; ?>

            </div>
            <div id="i_div_messages_container" class="messages_container"></div>
            <?php if ($settings['show']): ?>
                <?php echo \STPL::FetchAction($vars['app'], 'News/Widget_Menu', []); ?>
            <?php endif; ?>
        </div>
    </div>
</div>