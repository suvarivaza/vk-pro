<?php
/** @var Model_Rubrics_Rubric[] $list */
$list = $vars['list'];

use Service\Faq\Model_Rubrics_Rubric;

?>
<h1>
    Рубрики
    <small class="pull-right">
        <a class="btn btn-primary" href="/admin/faq/rubrics/add">Добавить</a>
    </small>
</h1>
<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <?php foreach ($list as $rubric): ?>
        <tr>
            <td>
                <h2>
                    <a href="/admin/faq/rubrics/<?= $rubric->rubricId; ?>/list"><?= $rubric->title; ?></a>
                    <small class="pull-right">
                        <a class="btn btn-success"
                           href="/admin/faq/rubrics/edit/<?= $rubric->rubricId; ?>">Редатировать</a>
                        <a class="btn btn-danger" href="?isDel=<?= $rubric->rubricId; ?>">Удалить</a>
                    </small>
                </h2>
            </td>
        </tr>
    <?php endforeach; ?>
</table>