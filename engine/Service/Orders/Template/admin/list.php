<?php
/** @var \Service\Orders\Model_Orders_Order $order */
?>
    <h1>
        Покупки
    </h1>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
    <table class="table">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>Фото</th>
            <th>Пользователь</th>
            <th>Дата</th>
            <th>Пакет</th>
            <th class="text-right" style="width: 100px;">Стоимость</th>
            <th class="text-center">Баланс</th>
            <th>Сервисы</th>
        </tr>
        </thead>
        <?php foreach ($vars['list'] as $order): $photo = $order->getUser()->getPhotos(); ?>
            <tr>
                <td><?= $order->orderId; ?></td>
                <td>
                    <strong><?= $order->userId; ?></strong>
                </td>
                <td style="width: 60px; vertical-align: middle;">
                    <?php if (isset($photo['small']['url'])): ?>
                        <img class="img-circle" style="width: 50px;" src="<?= $photo['small']['url']; ?>"/>
                    <?php else: ?>
                        <img class="img-circle" style="width: 60px;" src="/img/no-avatar.png"/>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $order->getUser()->name; ?> (<?= $order->getUser()->login; ?>)
                    <div><?= $order->getUser()->email; ?></div>
                </td>

                <td><?= \Lib_TimeStamp::createFromTimestamp($order->dateCreate)->format(); ?></td>
                <td>
                    <?php if ($order->type == 'karmaMinus'): ?>
                        Очистка кармы
                    <?php elseif ($order->packId > 0): ?>
                        <?= $order->packId ? $vars['packs'][$order->packId]->title : ''; ?>
                    <?php else: ?>
                        <?php if ($order->isAuto): ?>
                            Автоведение<br/>1 слот на
                            <?= \Lib_Text::Word4NumberNewReturn($order->isAutoMonth, ['месяц', 'месяца', 'месяцев']); ?>
                        <?php endif; ?>
                        <?php if ($order->isPosting): ?>
                            Автопостинг<br/>1 слот на
                            <?= \Lib_Text::Word4NumberNewReturn($order->isPostingMonth,
                                ['месяц', 'месяца', 'месяцев']); ?>
                        <?php endif; ?>
                        <?php if ($order->isGrabber): ?>
                            Граббер<br/>1 слот на
                            <?= \Lib_Text::Word4NumberNewReturn($order->isGrabberMonth,
                                ['месяц', 'месяца', 'месяцев']); ?>
                        <?php endif; ?>
                        <?php if ($order->isSpecial): ?>
                            Спецзадания<br/>1 слот на
                            <?= \Lib_Text::Word4NumberNewReturn($order->isSpecialMonth,
                                ['месяц', 'месяца', 'месяцев']); ?>
                        <?php endif; ?>
                        <?php if ($order->isBot): ?>
                            Автобот<br/>1 слот на
                            <?= \Lib_Text::Word4NumberNewReturn($order->isBotMonth, ['месяц', 'месяца', 'месяцев']); ?>
                        <?php endif; ?>
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
                                                           data-content="Автобот сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isBotMonth,
                                                               ['месяц', 'месяца', 'месяцев']); ?>"
                                                           src="/img/icons/32/icon-bot.png"/></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>