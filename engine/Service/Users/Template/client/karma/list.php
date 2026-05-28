<?php
/** @var \Service\Users\Model_Users_Karma_Karma[] $list */
$list = $vars['list'];
?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-karma.png" width="30"/>
        <a href="/users/karma">Карма</a>
    </li>
</ul>

<ul class="nav nav-tabs nav-justified">
    <li role="presentation"><a href="/users/karma">Общее</a></li>
    <li role="presentation" class="active"><a href="/users/karma/list">История кармы</a></li>
</ul>

<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <tr>
        <th>Дата</th>
        <th>Карма</th>
        <th>До</th>
        <th>После</th>
        <th>Комментарий</th>
    </tr>
    <?php foreach ($list as $karma): ?>
        <tr>
            <td>
                <?= date('d.m.Y H:i:s', $karma->dateCreate); ?>
            </td>
            <td>
                <?= $karma->karma; ?>
            </td>
            <td>
                <?= $karma->karmaFrom; ?>
            </td>
            <td>
                <?= $karma->karmaTo; ?>
            </td>
            <td>
                <?= $karma->comment; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>