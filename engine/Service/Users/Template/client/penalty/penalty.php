<?php
/** @var \Service\Users\Model_Users_Balances_Balance[] $list */
$list = $vars['list'];
?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-penalty.png" width="30"/>
        <a href="/users/penalty/1">История баланса</a>
    </li>
</ul>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation"><a href="/users/penalty">Общее</a></li>
    <li role="presentation"><a href="/users/penalty/tasks/1">Выполнение заданий</a></li>
    <li role="presentation" class="active"><a href="/users/penalty/penalty/1">Штрафы</a></li>
    <li role="presentation"><a href="/users/penalty/compensation/1">Компенсации</a></li>
</ul>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
<?php if ($vars['total'] > 0): ?>
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
    <div class="alert alert-info">
        Штрафов и компенаций нет
    </div>
<?php endif; ?>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
