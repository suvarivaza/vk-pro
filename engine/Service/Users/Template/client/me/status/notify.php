<h1 class="title">Уведомления о смене статуса</h1>
<?php if ($vars['success']): ?>
    <div class="success">
        <div class="alert-icon"></div>
        Подписка успешно сохранена.
    </div>
<?php endif; ?>
<form method="post">
    <ul style="list-style: none;">
        <?php foreach ($vars['statuses'] as $id => $status): if (!isset($status['mail']) || !$status['mail']) {
    continue;
} ?>
            <li>
                <input id="i_checked_<?= $id; ?>" type="checkbox" name="status[<?= $id; ?>]"
                       value="<?= $id; ?>" <?php if (isset($status['selected'])): ?> checked="checked"<?php endif; ?> />
                <label for="i_checked_<?= $id; ?>"><?= $status['title']; ?></label>
            </li>
        <?php endforeach; ?>
    </ul>
    <input type="submit" value="Сохранить"/>
    <a href="/users/general">вернуться</a>
</form>