<?php
/** @var \Service\Orders\Model_Orders_Packs_Pack $pack */
$first = $vars['first'];
?>

<input type="hidden" name="action" value="packActivate"/>
<input type="hidden" name="userId" value="<?= $vars['userId']; ?>"/>

<div class="form-group">
    <div class="col-sm-12">
        <select class="form-control" name="packId">
            <?php foreach ($vars['list'] as $pack): ?>
                <option value="<?= $pack->packId; ?>">
                    <strong><?= $pack->title; ?></strong>
                    | <?= number_format($pack->balance, 0, '', ' '); ?> баллов |
                    <?php if ($first): ?>
                        + <?= number_format($pack->balance * ($vars['bonus'] / 100), 0, '',
                            ' '); ?> в подарок за первую покупку
                    <?php else: ?>
                        <?= $pack->bonus ? ('+ ' . number_format($pack->bonus, 0, '',
                                ' ') . ' в подарок') : '&nbsp;'; ?>
                    <?php endif; ?>
                    <?php if ($pack->serviceCount): ?>
                        | +<?= \Lib_Text::Word4NumberNewReturn($pack->serviceCount, [
                            'сервис',
                            'сервиса',
                            'сервисов',
                        ]); ?> на <?= \Lib_Text::Word4NumberNewReturn($pack->serviceMonth,
                            ['месяц', 'месяца', 'месяцев']); ?>
                    <?php elseif ($pack->serviceAll): ?>
                        | все сервисы на <?= \Lib_Text::Word4NumberNewReturn($pack->serviceMonth,
                            ['месяц', 'месяца', 'месяцев']); ?>
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<script type="text/javascript">
    $('#i_modal_activate_label').html('Активировать пакет');
</script>