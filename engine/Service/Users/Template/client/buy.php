<?php
/** @var \Service\Orders\Model_Orders_Order[] $list */
$list = $vars['list'];
?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-buy.png" width="30"/>
        <a href="/grabber">История покупок</a>
    </li>
</ul>

<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <tr>
        <th>Дата</th>
        <th>Пакет</th>
        <th class="text-right">Стоимость</th>
        <th class="text-center">Баланс</th>
        <th>Сервисы</th>
    </tr>
    <?php foreach ($list as $order): ?>
        <tr>
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
                    <a href="/tasks/bot" target="_self"><img class="c_tooltip"
                                                             data-content="Автобот сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isBot,
                                                                 ['месяц', 'месяца', 'месяцев']); ?>"
                                                             src="/img/icons/32/icon-bot.png"/></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>