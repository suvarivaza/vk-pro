<?php
/** @var \Service\Users\Model_Users_Balances_Balance[] $list */
$list = $vars['list'];
?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-penalty.png" width="30"/>
        <a href="/grabber">История баланса</a>
    </li>
</ul>

<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <tr>
        <th>Дата</th>
        <th>Баллы</th>
        <th>До</th>
        <th>После</th>
        <th>Комментарий</th>
    </tr>
    <?php foreach ($list as $balance): ?>
        <tr>
            <td>
                <?= date('d.m.Y H:i:s', $balance->dateCreate); ?>
            </td>
            <td>
                <?= $balance->balance; ?>
            </td>
            <td>
                <?= $balance->balanceFrom; ?>
            </td>
            <td>
                <?= $balance->balanceTo; ?>
            </td>
            <td>
                <?= $balance->comment; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>