<?php
/** @var \Service\Orders\Model_Orders_Packs_Pack[] $list */
$list = $vars['list'];
$first = $vars['first'];
?>
<?php if (count($vars['gifts'])): $gift = array_shift($vars['gifts']); ?>
    <div class="alert alert-info text-center">
        <h1>
            Вам подарок!
        </h1>
        <button class="button-green btn-gift-activate" style="font-size: 24px;" data-gift-id="<?= $gift->giftId; ?>">
            <span class="fa fa-gift"></span> Активировать
        </button>
    </div>
<?php endif; ?>
<div class="pull-right">
    <button class="button-green c-balance-another">
        <img src="/img/icons/32/icon-money-white.png" width="32"/>
        Другое количество
    </button>
</div>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-money.png" width="30"/>
        <a href="/auto">Купить баллы</a>
    </li>
</ul>
<ul class="c-orders-pack-list">
    <?php foreach ($list as $pack): ?>
        <li class="c-orders-pack">
        <div class="c-orders-pack-item">
            <h3>
                <?= $pack->title; ?>
                <span title="<?php if ($pack->isReferrer): ?>Пакет участвует в партнерской программе и активирует PRO-версию, если у Вас её еще нет.<?php else: ?>Пакет не участвует в партнерской программе и не активирует доступ к PRO-версии<?php endif; ?>"
                      class="fa fa-users pull-right"
                      style="cursor: help; color: <?php if ($pack->isReferrer): ?>green<?php else: ?>#961101;<?php endif; ?>"></span>
            </h3>
            <h4>
                <?= number_format($pack->balance, 0, '', ' '); ?>
                <small><br/>баллов</small>
            </h4>
            <?php if ($first): ?>
                <h5>+ <?= number_format($pack->balance * ($vars['bonus'] / 100), 0, '', ' '); ?> в подарок за первую
                    покупку</h5>
            <?php else: ?>
                <h5><?= $pack->bonus ? ('+ ' . number_format($pack->bonus, 0, '',
                            ' ') . ' в подарок') : '&nbsp;'; ?></h5>
            <?php endif; ?>
            <h4><?= $pack->price; ?><sup><span class="glyphicon glyphicon-ruble"
                                               style="font-size: 10px; color: #808080;"></span></sup></h4>
            <?php if ($pack->serviceCount): ?>
                +<?= \Lib_Text::Word4NumberNewReturn($pack->serviceCount,
                    ['сервис', 'сервиса', 'сервисов']); ?> на <?= \Lib_Text::Word4NumberNewReturn($pack->serviceMonth,
                    ['месяц', 'месяца', 'месяцев']); ?>
            <?php elseif ($pack->serviceAll): ?>
                все сервисы на <?= \Lib_Text::Word4NumberNewReturn($pack->serviceMonth,
                    ['месяц', 'месяца', 'месяцев']); ?>
            <?php else: ?>
                <div>&nbsp;</div>
            <?php endif; ?>
            <button data-pack-id="<?= $pack->packId; ?>" class="btn btn-primary btn-block btn-pack-buy">Купить
                за <?= $pack->price; ?> рублей
            </button>

        </div>
        </li><?php endforeach; ?>
</ul>
<div class="modal fade" id="i_pack_dialog" tabindex="-1" role="dialog" aria-labelledby="i_pack_dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_pack_dialog_title"></h4>
            </div>
            <div class="modal-body">
                <div id="i_pack_dialog_container"></div>
                <div id="i_pack_dialog_progress" class="progress" style="display: none;">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>