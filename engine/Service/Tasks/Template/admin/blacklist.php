<h1>Список слов</h1>
<form method="post">
    <textarea class="form-control" name="data" rows="30"><?= $vars['data']; ?></textarea>
    <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
    <button type="button" class="btn btn-default" onclick="history.back();">Отмена</button>
</form>