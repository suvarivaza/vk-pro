<?php
/** @var \Service\Orders\Model_Orders_Order $order */
$order = $vars['order'];

/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<h1>Платеж успешно обработан</h1>
<div class="alert alert-success">
    Вам было начислено <?= \Lib_Text::Word4NumberNewReturn($order->balance, ['балл', 'балла', 'баллов']); ?>

    <?php if ($order->isAuto || $order->isPosting || $order->isGrabber || $order->isSpecial): ?>
        <div>
            <strong>Было активировано:</strong>
            <ul>
                <?php if ($order->isAuto): ?>
                    <li>Автоведение, сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isAutoMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></li>
                <?php endif; ?>
                <?php if ($order->isPosting): ?>
                    <li>Автопостинг, сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isPostingMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></li>
                <?php endif; ?>
                <?php if ($order->isGrabber): ?>
                    <li>Граббер, сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isGrabberMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></li>
                <?php endif; ?>
                <?php if ($order->isSpecial): ?>
                    <li>Спецзадания, сроком на <?= \Lib_Text::Word4NumberNewReturn($order->isSpecialMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>