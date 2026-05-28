<h1 class="title">Настройки</h1>

<form method="post">
    <table>
        <?php foreach ($vars['config']['settings'] as $id => $settings): ?>
            <tr>
                <td>
                    <?= $settings['title']; ?>
                </td>
                <td>
                    <select name="<?= $id; ?>">
                        <?php foreach ($settings['options'] as $oid => $val): ?>
                            <option value="<?= $oid; ?>" <?php if (isset($vars['settings'][$id]) && $vars['settings'][$id] == $oid): ?> selected="selected"<?php endif; ?>><?= $val['title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <button class="button-green" type="submit">
        Сохранить
    </button>
</form>