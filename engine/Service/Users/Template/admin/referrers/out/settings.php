<h1>Настройки вывода средств</h1>
<form class="form-horizontal" method="post">
    <input type="hidden" name="action" value="save">
    <table class="table table-hover table-condensed table-striped">
        <tr>
            <td>
                <h6><strong>Даты выполнения заявок</strong></h6>
            </td>
            <td class="text-right">
                <h6><strong>с</strong></h6>
            </td>
            <td>
                <select class="form-control" name="out[from]">
                    <?php for ($i = 1; $i < 32; $i++): ?>
                        <option value="<?= $i; ?>" <?php if ($i == $vars['settings']['out']['from']): ?> selected="selected"<?php endif; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
            <td class="text-right">
                <h6><strong>по</strong></h6>
            </td>
            <td>
                <select class="form-control" name="out[to]">
                    <?php for ($i = 1; $i < 32; $i++): ?>
                        <option value="<?= $i; ?>" <?php if ($i == $vars['settings']['out']['to']): ?> selected="selected"<?php endif; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><h6><strong>Минимальная сумма на вывод</strong></h6></td>
            <td colspan="4">
                <div class="input-group" style="width: 100%;">
                    <input class="form-control" name="out[min]" value="<?= $vars['settings']['out']['min']; ?>">
                    <span class="input-group-addon">рублей</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <h6><strong>Комиссия за вывод</strong></h6>
            </td>
            <td colspan="4">
                <div class="input-group" style="width: 100%;">
                    <input class="form-control" name="out[fee]" value="<?= $vars['settings']['out']['fee']; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            </td>
        </tr>
    </table>
</form>