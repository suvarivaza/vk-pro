<?php
/** @var \Service\Users\Model_Users_User[] $list */
$list = $vars['list'];
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation"><a href="/users/referrer">О рефералах</a></li>
    <li role="presentation" class="active"><a href="/users/referrer/1/1">Мои рефералы</a></li>
    <li role="presentation"><a href="/users/referrer/bonus/1">Реферальные бонусы</a></li>
    <li role="presentation"><a href="/users/referrer/balance">Реферальные баллы</a></li>
</ul>
<div class="tab-content" style="background: #ffffff; border: 1px solid #ddd; border-top: 0; padding: 10px;">
    <div id="general" class="tab-pane fade">
    </div>
    <div id="i-my-referrers" class="tab-pane fade active in">
        <div class="btn-group btn-group-justified btn-group-lg" role="group">
            <a type="button" class="btn btn-primary<?php if ($vars['level'] == 1): ?> active<?php endif; ?>"
               href="/users/referrer/1/1">Первый уровень (<?= $vars['limits'][1]; ?>)</a>
            <a type="button" class="btn btn-primary<?php if ($vars['level'] == 2): ?> active<?php endif; ?>"
               href="/users/referrer/2/1">Второй уровень (<?= $vars['limits'][2]; ?>)</a>
        </div>
        <?php echo STPL::PagesLink([
            'pageslink' => $vars['pageslink'],
            'showtitle' => false,
        ]); ?>
        <table class="table">
            <?php foreach ($vars['list'] as $user): $photo = $user->getPhotos(); ?>
                <tr>
                    <td style="width: 60px; vertical-align: middle;">
                        <?php if (isset($photo['small']['url'])): ?>
                            <img class="img-circle" style="width: 50px;" src="<?= $photo['small']['url']; ?>"/>
                        <?php else: ?>
                            <img class="img-circle" style="width: 60px;" src="/img/no-avatar.png"/>
                        <?php endif; ?>
                    </td>
                    <td>
                        <h4>
                            <small class="pull-right">
                                <div class="karma" style="cursor: pointer;">
                                    <div style="width: <?= abs($user->karma); ?>%;"
                                         class="loadbar<?php if ($user->karma < 0): ?> minus<?php endif; ?>"></div>
                                    <div class="index">Карма <?= number_format($user->karma, 1); ?>%</div>
                                </div>
                            </small>
                            <?= $user->name; ?>
                        </h4>
                        <div>
                            <span id="i_user_balance_<?= $user->userId; ?>"><?= number_format($user->balance, 1, '.',
                                    ' '); ?></span> баллов
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php echo STPL::PagesLink([
            'pageslink' => $vars['pageslink'],
            'showtitle' => false,
        ]); ?>
    </div>
    <div id="i-referrer-bonus" class="tab-pane fade">
        Реферальные бонусы
    </div>
    <div id="i-balanceRef" class="tab-pane fade">
        Вывод средств
    </div>
</div>