<?php
/** @var \Service\Users\Model_Users_User $user */
/** @var \Service\Users\Model_Users_Balances_Balance $balance */
$user = $vars['user'];

?>
<h1 class="title">
    Баланс
    <?php if (isset($vars['rep']) && $vars['rep']): ?>
        пользователя <strong><?= $user->login; ?></strong>
    <?php endif; ?>
</h1>

<div class="c_balance_div">
    <table style="width: 100%;">
        <tr>
            <td>Баланс</td>
            <td style="text-align: right"><?= number_format($user->balance, 2, ',', ' '); ?></td>
        </tr>
        <tr>
            <td>Баланс бухгалтерский</td>
            <td style="text-align: right"><?= number_format($user->balanceSheet, 2, ',', ' '); ?></td>
        </tr>
        <tr>
            <td>Заказов в работе</td>
            <td style="text-align: right"><?= number_format($vars['items']['sumWork'], 2, ',', ' '); ?></td>
        </tr>
        <tr>
            <td>Заказов к выдаче</td>
            <td style="text-align: right"><?= number_format($vars['items']['sumReady'], 2, ',', ' '); ?></td>
        </tr>
        <tr>
            <td>Заказов на модерации</td>
            <td style="text-align: right"><?= number_format($vars['items']['sumModerate'], 2, ',', ' '); ?></td>
        </tr>
        <tr>
            <td>Заказов в стоп-листе</td>
            <td style="text-align: right"><?= number_format($vars['items']['sumStop'], 2, ',', ' '); ?></td>
        </tr>
    </table>
</div>
<div class="c_balance_div">
    <table>
        <?php foreach ($vars['months'] as $month => $sum): ?>
            <tr>
                <td><?= \Lib_TimeStamp::createFromTimestamp($month)->format('Fi'); ?></td>
                <td><?= number_format($sum, 2, ',', ' '); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<br/><br/>
<form method="get">
    <input class="input c_datePicker" name="dateFrom" value="<?= $vars['dateFrom']; ?>"/>
    <input class="input c_datePicker" name="dateTo" value="<?= $vars['dateTo']; ?>"/>
    <input type="submit" value="Применить"/>
</form>
<table class="result-table">
    <tr class="head">
        <th>Дата</th>
        <th>Остаток</th>
        <th>Приход</th>
        <th>Расход</th>
        <th>Баланс</th>
        <th>Комментарий</th>
    </tr>
    <?php foreach ($vars['list'] as $balance): $items = $balance->getItems(); ?>
        <tr>
            <td><?= \Lib_TimeStamp::createFromTimestamp($balance->date)->format(); ?></td>
            <td><?= $balance->sumFrom ? number_format($balance->sumFrom, 2, ',', ' ') : ''; ?></td>
            <td><?php if (!$balance->operation) {
    echo '<strong style="color: MediumSeaGreen;">' . number_format($balance->sum, 2, ',',
                            ' ') . '</strong>';
} ?></td>
            <td>
                <?php if ($balance->operation) {
    echo '<strong style="color: #ff0000;">- ' . number_format($balance->sum, 2, ',', ' ') . '</strong>';
} ?>
                <?php if (count($items)): ?>
                    <a href="javascript:void(0)"
                       onclick="$(this).parent().parent().parent().next().toggle()">Подробнее</a>
                <?php endif; ?>
            </td>
            <td><?= $balance->sumTo ? number_format($balance->sumTo, 2, ',', ' ') : ''; ?></td>
            <td><?= $balance->document ?: $balance->comment; ?></td>
        </tr>
        <?php if (count($items)) : ?>
            <tbody style="display: none;">
            <tr>
                <th>Номер заказа</th>
                <th>Производитель</th>
                <th>Номер</th>
                <th>Наименование</th>
                <th>Цена</th>
                <th>Количество</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item->itemId; ?></td>
                    <td><?= $item->brand; ?></td>
                    <td><?= $item->number; ?></td>
                    <td><?= $item->title; ?></td>
                    <td><?= $item->priceSell; ?></td>
                    <td><?= $item->count; ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" style="background-color: #ffffff;">
                    <hr/>
                </td>
            </tr>
            </tbody>
        <?php endif; ?>
    <?php endforeach; ?>
</table>

<script type="text/javascript">
    $(".c_datePicker").datepicker({
        dateFormat: "dd.mm.yy"
    });
</script>