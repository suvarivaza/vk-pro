<h2>
    Автоведение
    <small class="pull-right">
        <a class="btn btn-default" href="/admin/auto/start">Главная страница</a>
        <a class="btn btn-default" href="/admin/auto/short">Краткое описание</a>
    </small>
</h2>

<form method="post">
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th class="text-center" colspan="5"><h4>Стоимость автоведения</h4></th>
        </tr>
        <tr>
            <td><h5>Срок</h5></td>
            <td><h5>1 месяц</h5></td>
            <td><h5>2 месяца</h5></td>
            <td><h5>3 месяца</h5></td>
            <td><h5>6 месяцев</h5></td>
        </tr>
        <tr>
            <td><h5>Стоимость</h5></td>
            <td>
                <input class="form-control" name="price[1]" value="<?= $vars['config']['price'][1]; ?>">
            </td>
            <td>
                <input class="form-control" name="price[2]" value="<?= $vars['config']['price'][2]; ?>">
            </td>
            <td>
                <input class="form-control" name="price[3]" value="<?= $vars['config']['price'][3]; ?>">
            </td>
            <td>
                <input class="form-control" name="price[6]" value="<?= $vars['config']['price'][6]; ?>">
            </td>
        </tr>
    </table>
    <button class="btn btn-primary btn-lg">Сохранить</button>
</form>