<?php
/** @var \Service\Orders\Model_Orders_Packs_Pack[] $list */
$list = $vars['list'];
?>
<h1>
    Пакеты
    <small class="pull-right">
        <a class="btn btn-default" href="/admin/orders/pack/add">Добавить пакет покупки</a>
        <a class="btn btn-default" href="/admin/orders/settings">Цены на сервисы</a>
    </small>
</h1>
<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <tr>
        <th>Наименование</th>
        <th>Баллы</th>
        <th>Бонус</th>
        <th>Цена</th>
        <th>Сервисы</th>
        <th>Срок</th>
        <th>Рефер</th>
        <th></th>
    </tr>
    <?php foreach ($list as $pack): ?>
        <tr>
            <td><?= $pack->title; ?></td>
            <td><?= $pack->balance; ?></td>
            <td><?= $pack->bonus; ?></td>
            <td><?= $pack->price; ?></td>
            <td><?= $pack->serviceAll ? 'Все сервисы' : ($pack->serviceCount ?: ''); ?></td>
            <td><?= ($pack->serviceAll || $pack->serviceCount) ? \Lib_Text::Word4NumberNewReturn($pack->serviceMonth,
                    ['месяц', 'месяца', 'месяцев']) : '-'; ?></td>
            <td><?php if ($pack->isReferrer) : ?><span
                        class="glyphicon glyphicon-ok"></span><?php else: ?>-<?php endif; ?></td>
            <td class="text-right">
                <a class="btn btn-primary btn-sm" href="/admin/orders/pack/edit/<?= $pack->packId; ?>">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a class="btn btn-danger btn-sm" href="?del=<?= $pack->packId; ?>">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
