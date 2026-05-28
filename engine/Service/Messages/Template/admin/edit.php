<?php
$message = $vars['message'];
?>
<ul class="breadcrumb">
    <li><a href="/admin/messages/">Сообщения</a></li>
    <li><strong><?= $vars['action'] == 'add' ? 'Добавить' : 'Редактировать'; ?> сообщение</strong></li>
</ul>

<form action="" method="POST" enctype="multipart/form-data" target="_self" class="form-horizontal" role="form">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-4 bg-danger">
                <?= implode('<br />', $vars['errors']); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <div class="col-sm-12">
            <textarea name="text" class="tinymce form-control" rows="20"
                      placeholder="Текст сообщения"><?= $message->text; ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-default" type="submit" name="submit">Сохранить</button>
            <button class="btn btn-danger" type="button" name="cancel" onclick="history.back();">Отмена</button>
        </div>
    </div>
</form>