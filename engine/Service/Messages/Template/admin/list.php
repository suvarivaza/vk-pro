<?php
/** @var \Service\Messages\Model_Messages_Message[] $list */
$list = $vars['list'];
?>
<h1>
    Сообщения
    <small class="pull-right">
        <a href="/admin/messages/config" class="btn btn-default">Системные сообщения</a>
        <a href="/admin/messages/add" class="btn btn-primary">Добавить</a>
    </small>
</h1>
<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <tr>
        <th>Дата</th>
        <th>Сообщение</th>
    </tr>
    <?php foreach ($list as $message): ?>
        <tr>
            <td><?= date('d.m.Y', $message->dateCreate); ?></td>
            <td><?= $message->text; ?></td>
        </tr>
    <?php endforeach; ?>
</table>