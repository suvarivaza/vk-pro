<?php
/** @var \Service\Users\Model_Users_Balances_Balance[] $list */
$list = $vars['list'];

$bonusData = isset($vars['bonus']['apply']) ? $vars['bonus']['apply'] : $vars['bonus'];
?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-bonus.png" width="30">
        <a href="/auto">Бонусы</a>
    </li>
</ul>

<h5>Ежедневный бонус <strong><?= $bonusData['day']; ?> баллов</strong>:</h5>
<?php

foreach ($bonusData['min'] as $type => $limit): ?>
    <?php
    $limit == 0 ? $width = 0 : $width = $vars['statistic'][$type] / $limit * 100;
    ?>
    <div class="karma" style="width: 12%;">
        <div class="loadbar" style="width: <?= $width; ?>%;"></div>
        <div class="index"><?= $vars['types'][$type]; ?> <?= $vars['statistic'][$type] . '/' . $limit; ?></div>
    </div>
<?php endforeach; ?>
<div>Вы можете ежедневно получать бонус в виде баллов, за выполнение перечня заданий в указанном количестве.</div>
<div>Пользователь, не получивший ежедневный бонус, теряет часть кармы от максимального значения. Не переживайте, карма
    не опустится ниже нуля за пропуски. Таким образом, мы просто выделяем активных пользователей для создателей заданий.
</div>
<br/>

<h5>Еженедельный бонус <strong><?= $bonusData['week']; ?> баллов</strong>:</h5>
<?php for ($i = 1; $i < 8; $i++): ?>
    <div class="karma" style="width: 12%;">
        <div class="loadbar" style="width: <?= $vars['week'][$i]['active'] ? 100 : 0; ?>%;"></div>
        <div class="index"><?= $vars['week'][$i]['title']; ?></div>
    </div>
<?php endfor; ?>

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