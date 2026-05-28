<h1>Настройки новостей</h1>
<form method="post" class="form-horizontal">
    <div class="form-group">
        <label class="control-label col-sm-6" style="text-align: left;">
            <input type="checkbox" name="settings[show]"
                   value="true" <?php if ($vars['settings']['show']): ?> checked="checked"<?php endif; ?> />
            Отображать новости
        </label>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            <button type="submit" class="button-green">Сохранить</button>
        </div>
    </div>
</form>