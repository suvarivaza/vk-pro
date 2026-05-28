<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
$photo = $user->getPhotos();
?>
<p>access_token: <?= $user->access_token; ?></p>
<p>valid_access_token: <?= $vars['valid_access_token']; ?></p>
<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <tr>
        <td rowspan="2" style="width: 210px;">
            <img src="<?= $photo['big']['url']; ?>" class="img-thumbnail"/>
        </td>
        <td>
            <h4>
                <?= $user->login; ?>
                <small><?= $user->name; ?></small>
                <div><a href="<?= $user->identity; ?>" target="_blank">Профиль в VK</a></div>
            </h4>
            <h5><?= $user->email; ?></h5>
            <div class="karma" style="cursor: pointer;"
                 onclick="$('#i_modal_karma_userId').val(<?= $user->userId; ?>); $('#i_modal_karma').modal();">
                <div style="width: <?= abs($user->karma) > 100 ? 100 : abs($user->karma); ?>%;"
                     class="loadbar<?php if ($user->karma < 0): ?> minus<?php endif; ?>"></div>
                <div class="index">Карма <?= number_format($user->karma, 1); ?>%</div>
            </div>

        </td>
        <td>
            Дата регистрации: <?= date('d.m.Y H:i:s', $user->dateCreate); ?>
            <br/>
            Дата последнего входа: <?= date('d.m.Y H:i:s', $user->lastLogin); ?>
            <?php if ($user->bad): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php if ($user->bad & \Service\Users\Model_Config::BAD_AVATAR): ?>
                            <li>Отсутствует аватар</li>
                        <?php endif; ?>
                        <?php if ($user->bad & \Service\Users\Model_Config::BAD_AVATAR_COUNT): ?>
                            <li>Менее 5 фотографий</li>
                        <?php endif; ?>
                        <?php if ($user->bad & \Service\Users\Model_Config::BAD_FOLLOWERS): ?>
                            <li>Менее 5 друзей или подписчиков</li>
                        <?php endif; ?>
                        <?php if ($user->bad & \Service\Users\Model_Config::BAD_POSTS): ?>
                            <li>Постов на стене менее 5</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <input type="hidden" id="i_user_detail_userId" name="userId" value="<?= $user->userId ?>">
            <select class="form-control" name="userType" id="i_user_detail_userType">
                <option
                    <?php if ($user->userType === \Service\Users\Model_Config::TYPE_USER): ?>selected="selected"<?php endif ?>
                    value="<?= \Service\Users\Model_Config::TYPE_USER ?>">Пользователь</option>
                <option
                    <?php if ($user->userType === \Service\Users\Model_Config::TYPE_ADMIN): ?>selected="selected"<?php endif ?>
                    value="<?= \Service\Users\Model_Config::TYPE_ADMIN ?>">Администратор</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <table class="<?= DEFAULT_TABLE_CLASS; ?>">
                <tr>
                    <td>Пол</td>
                    <td><?= $user->sex; ?></td>
                </tr>
            </table>
        </td>
        <td></td>
    </tr>
</table>

<div class="row">
    <div class="col-sm-12">
        <button onclick="$('#i_modal_balance_userId').val(76); $('#i_modal_balance').modal();" class="btn btn-primary">
            Баллы
        </button>
        <button class="btn btn-primary" onclick="admin_user.showForm(<?= $user->userId; ?>, 'getPacks')">Пакет</button>
        <button class="btn btn-primary" onclick="admin_user.showForm(<?= $user->userId; ?>, 'getAuto')">Автоведение
        </button>
        <button class="btn btn-primary" onclick="admin_user.showForm(<?= $user->userId; ?>, 'getPosting')">Автопостинг
        </button>
        <button class="btn btn-primary" onclick="admin_user.showForm(<?= $user->userId; ?>, 'getGrabber')">Граббер
        </button>
        <button class="btn btn-primary" onclick="admin_user.showForm(<?= $user->userId; ?>, 'getSpecial')">Спецзадания
        </button>
        <button class="btn btn-primary" onclick="admin_user.showForm(<?= $user->userId; ?>, 'getBot')">Автобот</button>
    </div>
</div>
<?php if (count($vars['orders'])): ?>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Пакет</th>
            <th class="text-right" style="width: 100px;">Стоимость</th>
            <th class="text-center">Баланс</th>
            <th>Сервисы</th>
        </tr>
        </thead>
        <?php foreach ($vars['orders'] as $order): ?>
            <tr>
                <td><?= $order->orderId; ?></td>
                <td><?= date('d.m.Y', $order->dateCreate); ?></td>
                <td>
                    <?php if ($order->type == 'karmaMinus'): ?>
                        Очистка кармы
                    <?php else: ?>
                        <?= $order->packId ? $vars['packs'][$order->packId]->title : ''; ?>
                    <?php endif; ?>
                </td>
                <td class="text-right"><?= $order->price; ?></td>
                <td class="text-center"><?= $order->balance; ?></td>
                <td>
                    <?php if ($order->isAuto): ?>
                        <a href="/auto" target="_self"><img class="c_tooltip"
                                                            data-content="Автоведение сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isAutoMonth,
                                                                ['месяц', 'месяца', 'месяцев']); ?>"
                                                            src="/img/icons/32/icon-auto.png"/></a>
                    <?php endif; ?>
                    <?php if ($order->isPosting): ?>
                        <a href="/posting" target="_self"><img class="c_tooltip"
                                                               data-content="Автопостинг сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isPostingMonth,
                                                                   ['месяц', 'месяца', 'месяцев']); ?>"
                                                               src="/img/icons/32/icon-post.png"/></a>
                    <?php endif; ?>
                    <?php if ($order->isGrabber): ?>
                        <a href="/grabber" target="_self"><img class="c_tooltip"
                                                               data-content="Граббер сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isGrabberMonth,
                                                                   ['месяц', 'месяца', 'месяцев']); ?>"
                                                               src="/img/icons/32/icon-grabber.png"/></a>
                    <?php endif; ?>
                    <?php if ($order->isSpecial): ?>
                        <a href="/tasks/special" target="_self"><img class="c_tooltip"
                                                                     data-content="Спецзадания сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isSpecialMonth,
                                                                         ['месяц', 'месяца', 'месяцев']); ?>"
                                                                     src="/img/icons/32/icon-special.png"/></a>
                    <?php endif; ?>
                    <?php if ($order->isBot): ?>
                        <a href="/bot" target="_self"><img class="c_tooltip"
                                                           data-content="Автобот сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isSpecialMonth,
                                                               ['месяц', 'месяца', 'месяцев']); ?>"
                                                           src="/img/icons/32/icon-bot.png"/></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>
<?php endif; ?>
