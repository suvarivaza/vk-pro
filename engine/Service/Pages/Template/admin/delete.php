<div class="alert alert-danger">Вы действительно хотите удалить "<?= $vars['page']->title; ?>"?</div>
<form method="post" action="">
    <button type="submit" class="btn btn-danger">Удалить</button>
    <button type="button" class="btn btn-success" onclick="history.back();">Отмена</button>
</form>