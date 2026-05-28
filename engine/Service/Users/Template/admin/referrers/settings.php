<h1>Настройки реферальной системы</h1>
<form class="form-horizontal" method="post">
    <table class="table">
        <tr>
            <th></th>
            <th>Перая покупка</th>
            <th>Вторая и последующие</th>
            <th>Выполнение заданий</th>
        </tr>
        <tr>
            <td>Первый уровень</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="parentId[first]"
                           value="<?= $vars['settings']['percent']['parentId']['first']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="parentId[all]"
                           value="<?= $vars['settings']['percent']['parentId']['all']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="parentId[tasks]"
                           value="<?= $vars['settings']['percent']['parentId']['tasks']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Второй уровень</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="pParentId[first]"
                           value="<?= $vars['settings']['percent']['pParentId']['first']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="pParentId[all]"
                           value="<?= $vars['settings']['percent']['pParentId']['all']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="pParentId[tasks]"
                           value="<?= $vars['settings']['percent']['pParentId']['tasks']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Третий уровень</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="ppParentId[first]"
                           value="<?= $vars['settings']['percent']['ppParentId']['first']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="ppParentId[all]"
                           value="<?= $vars['settings']['percent']['ppParentId']['all']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="ppParentId[tasks]"
                           value="<?= $vars['settings']['percent']['ppParentId']['tasks']; ?>"/>
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <button type="button" class="btn btn-danger" onclick="history.back();">Отмена</button>
            </td>
        </tr>
    </table>
</form>