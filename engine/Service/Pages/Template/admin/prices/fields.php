<script type="text/javascript">
    var fields = {
        count: 0,
        addField: function () {
            $('#add_field').before('<tr><td><input type="hidden" name="Column[' + fields.count + ']" value="' + fields.count + '" /><input name="Title[' + fields.count + ']" /><select name="Align[' + fields.count + ']"><option value="left">По левому краю</option><option value="center">По центру</option><option value="right">По правому краю</option></select></td></tr>');
            fields.count++;
        },
        removeField: function () {
            $('#add_field').prev().remove();
            fields.count--;
        }
    };
</script>
<h1>Состав прайса "<?= $vars['price']->Title; ?>"</h1>
<?php if ($vars['errors']): ?>
    <div class="alert <?php if ($vars['success']): ?> alert-success<?php else: ?> alert-danger<?php endif; ?>">
        <?= implode('<br />', $vars['errors']); ?>
    </div>
<?php endif; ?>
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="fields"/>
    <table class="table" cellpadding="3" cellspacing="0" border="0">
        <?php $id = 0;

        foreach ($vars['data'] as $l): ?>
            <tr>
                <td>
                    <input type="hidden" name="Column[<?= $id; ?>]" value="<?= $id; ?>"/>
                    <input name="Title[<?= $id; ?>]" value="<?= $l->Title; ?>"/>
                    <select name="Align[<?= $id; ?>]">
                        <option value="left"<?php if ($l->Align == 'left'): ?> selected="selected"<?php endif; ?>>По
                            левому краю
                        </option>
                        <option value="center"<?php if ($l->Align == 'center'): ?> selected="selected"<?php endif; ?>>По
                            центру
                        </option>
                        <option value="right"<?php if ($l->Align == 'right'): ?> selected="selected"<?php endif; ?>>По
                            правому краю
                        </option>
                    </select>
                </td>
            </tr>
            <?php $id++; endforeach; ?>
        <tr id="add_field">
            <td colspan="2" style="cursor: pointer;">
                <script type="text/javascript">
                    fields.count = <?= $id; ?>;
                </script>
                <a class="btn btn-info btn-sm" href="javascript: return false;" onclick="fields.addField()">Добавить
                    столбец</a>
                <a class="btn btn-danger btn-sm" href="javascript: return false;" onclick="fields.removeField()">Убрать
                    столбец</a>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
                <a href="/admin/pages/prices">К списку прайсов</a>
            </td>
        </tr>
    </table>
</form>