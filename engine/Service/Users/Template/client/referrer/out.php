<?php
/** @var \Service\Users\Model_Users_Requests_Request[] $list */
$list = $vars['list'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation"><a href="/users/referrer">О рефералах</a></li>
    <li role="presentation"><a href="/users/referrer/1/1">Мои рефералы</a></li>
    <li role="presentation"><a href="/users/referrer/bonus/1">Реферальные бонусы</a></li>
</ul>
<div class="tab-content" style="background: #ffffff; border: 1px solid #ddd; border-top: 0; padding: 10px;">
    <div id="general" class="tab-pane fade"></div>
    <div id="i-my-referrers" class="tab-pane fade"></div>
    <div id="i-referrer-bonus" class="tab-pane fade">
    </div>
    <div id="i-balanceRef" class="tab-pane fade active in">
        <div class="pull-right alert alert-info">
            Ваш баланс:
            <strong><?= number_format($user->balanceRef, 2, ',', ' '); ?></strong>
            <span class="glyphicon glyphicon-rub"></span>
            <button id="i-button-balanceRef-out" class="btn btn-primary btn-sm">Вывод средств</button>
        </div>
        <?php if (!$user->balanceRef): ?>
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
                    <th class="text-right">К получению, руб</th>
                    <th class="text-right">Комиссия, руб</th>
                    <th class="text-right">ИТОГО, руб</th>
                    <th>Статус</th>
                </tr>
                <?php foreach ($list as $referrer): ?>
                    <tr>
                        <td><?= date('d.m.Y', $referrer->dateCreate); ?></td>
                        <td class="text-right"><?= $referrer->balanceRef; ?></td>
                        <td class="text-right"><?= $referrer->balanceFee; ?></td>
                        <td class="text-right"><?= $referrer->balanceTotal; ?></td>
                        <td><?= $vars['status'][$referrer->status]['title']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo STPL::PagesLink([
                'pageslink' => $vars['pageslink'],
                'showtitle' => false,
            ]); ?>
        <?php endif; ?>
    </div>
</div>
<div class="modal fade" id="i-dialog-balanceRef" tabindex="-1" role="dialog" aria-labelledby="i-dialog-balanceRef">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_pack_dialog_title">Заявка на вывод средств</h4>
            </div>
            <div class="modal-body">
                <div id="i-dialog-balanceRef-container"></div>
                <div id="i-dialog-balanceRef-data"></div>
                <div id="i-dialog-balanceRef-progress" class="progress" style="display: none;">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="i-dialog-balanceRef-save" class="btn btn-primary">Отправить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>