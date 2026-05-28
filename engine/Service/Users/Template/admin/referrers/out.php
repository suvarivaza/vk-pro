<?php
/** @var \Service\Users\Model_Users_Requests_Request[] $list */
$list = $vars['list'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
    <h1>
        Заявки на вывод средств
        <small class="pull-right"></small>
    </h1>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Дата</th>
            <th>Бонусы</th>
            <th>Статус</th>
        </tr>
        <?php foreach ($list as $referrer): ?>
            <tr>
                <td><?= date('d.m.Y', $referrer->dateCreate); ?></td>
                <td><?= $referrer->balanceRef; ?></td>
                <td><?= $vars['status'][$referrer->status]['title']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>