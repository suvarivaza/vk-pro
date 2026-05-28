<?php
/** @var Model_Rubrics_Rubric $rubric */
$rubric = $vars['rubric'];

use Service\Faq\Model_Rubrics_Rubric;

?>
<ul class="breadcrumb">
    <li><a href="/admin/faq">Рубрики</a></li>
    <?php if ($vars['action'] == 'add'): ?>
        <li><strong>Добавление рубрики</strong></li>
    <?php else: ?>
        <li>Редактирование <strong><?= $rubric->title; ?></strong></li>
    <?php endif; ?>
</ul>
<form id="i-form-new" action="" method="POST" enctype="multipart/form-data" target="_self" class="form-horizontal"
      role="form">
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
            <input type="text" class="form-control form-span" id="inputtitle" name="title" placeholder="Заголовок"
                   value="<?= $rubric ? $rubric->title : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-primary" type="submit" name="submit">Сохранить</button>
            <button class="btn btn-danger" type="button" onclick="history.back();">Отмена</button>
        </div>
    </div>
</form>