<?php
/** @var \Service\Users\Model_Users_Referrers_Referrer[] $list */
$list = $vars['list'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation"><a href="/users/referrer">О рефералах</a></li>
    <li role="presentation"><a href="/users/referrer/1/1">Мои рефералы</a></li>
    <li role="presentation" class="active"><a href="/users/referrer/bonus/1">Реферальные бонусы</a></li>
    <li role="presentation"><a href="/users/referrer/balance">Реферальные баллы</a></li>
</ul>
<div class="tab-content" style="background: #ffffff; border: 1px solid #ddd; border-top: 0; padding: 10px;">
    <div id="general" class="tab-pane fade"></div>
    <div id="i-my-referrers" class="tab-pane fade"></div>
    <div id="i-referrer-bonus" class="tab-pane fade active in">
        <div class="pull-right alert alert-info">Ваш баланс: <strong><?= number_format($user->balanceRef, 2, ',',
                    ' '); ?></strong> бонусов
        </div>
        <?php if (!$vars['total']): ?>
            <div class="alert alert-danger">
                У вас еще нет реферальных бонусов!
            </div>
        <?php else: ?>
            <?php echo STPL::PagesLink([
                'pageslink' => $vars['pageslink'],
                'showtitle' => false,
            ]); ?>
            <table class="<?= DEFAULT_TABLE_CLASS; ?>">
                <tr>
                    <th>Дата</th>
                    <th>Бонусов начислено</th>
                    <th>Бонусов было</th>
                    <th>Бонусов стало</th>
                    <th>Комментарий</th>
                </tr>
                <?php foreach ($list as $referrer): ?>
                    <tr>
                        <td><?= date('d.m.Y', $referrer->dateCreate); ?></td>
                        <td><?= $referrer->balanceRef; ?></td>
                        <td><?= $referrer->balanceRefFrom; ?></td>
                        <td><?= $referrer->balanceRefTo; ?></td>
                        <td><?= $referrer->comment; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo STPL::PagesLink([
                'pageslink' => $vars['pageslink'],
                'showtitle' => false,
            ]); ?>
        <?php endif; ?>
    </div>
    <div id="i-balanceRef" class="tab-pane fade">
        Вывод средств
    </div>
</div>