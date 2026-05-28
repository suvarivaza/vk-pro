<?php
/** @var \Service\Users\Model_Users_Balances_Balance[] $list */
$list = $vars['list'];
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation"><a href="/users/referrer">О рефералах</a></li>
    <li role="presentation"><a href="/users/referrer/1/1">Мои рефералы</a></li>
    <li role="presentation"><a href="/users/referrer/bonus/1">Реферальные бонусы</a></li>
    <li role="presentation" class="active"><a href="/users/referrer/balance">Реферальные баллы</a></li>
</ul>
<?php if (count($list)): ?>
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
<?php else: ?>
    <div class="alert alert-info">Реферальных баллов нет.</div>
<?php endif; ?>
