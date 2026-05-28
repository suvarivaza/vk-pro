<?php
/**
 * @var \Service\Users\Model_Users_User
 */
?>
<h1>
    Рефералы
    <span class="pull-right">
        <a class="btn btn-default" href="/admin/users/referrers/settings">
            <span class="glyphicon glyphicon-user"></span>
            Настройки реферальной программы
        </a>
    </span>
</h1>
<?php if (count($vars['errors'])): ?>
    <div class="bg-danger text-danger" style="padding: 5px 10px;">
        <?= implode('<br />', $vars['errors']); ?>
    </div>
<?php endif; ?>
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
                        <div class="karma" style="cursor: pointer;"
                             onclick="$('#i_modal_karma_userId').val(<?= $user->userId; ?>); $('#i_modal_karma').modal();">
                            <div style="width: <?= $user->karma; ?>%;"
                                 class="loadbar<?php if ($user->karma < 0): ?> minus<?php endif; ?>"></div>
                            <div class="index">Карма <?= number_format($user->karma, 1); ?>%</div>
                        </div>
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
