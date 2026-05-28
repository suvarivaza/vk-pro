<h1 class="title">Смена пароля</h1>

<?php if (count($vars['errors'])): ?>
    <div class="errors">
        <div class="alert-icon"></div>
        <?= implode('<br />', $vars['errors']); ?>
    </div>
<?php endif; ?>
<form method="post" action="" class="form-horizontal">
    <input type="hidden" name="action" value="new"/>
    <table class="table">
        <tr>
            <td>Пароль<span class="redtext">*</span>:</td>
            <td><input class="form-control" type="password" name="password"/></td>
        </tr>
        <tr>
            <td>Подтверждение пароля<span class="redtext">*</span>:</td>
            <td><input class="form-control" type="password" name="passwordConfirm"/></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center">
                <input class="button-green" type="submit" value="Сменить"/>
            </td>
        </tr>
    </table>
</form>