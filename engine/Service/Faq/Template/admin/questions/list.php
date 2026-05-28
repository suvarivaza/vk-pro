<?php
/** @var Model_Rubrics_Rubric $rubric */
$rubric = $vars['rubric'];
/** @var Model_Questions_Question[] $list */
$list = $vars['list'];

use Service\Faq\Model_Questions_Question;
use Service\Faq\Model_Rubrics_Rubric;

?>
<div class="pull-right">
    <?php if ($rubric->rubricId): ?>
        <a class="btn btn-primary" href="/admin/faq/rubrics/<?= $rubric->rubricId; ?>/add">Добавить вопрос-ответ</a>
    <?php endif; ?>
</div>
<ul class="breadcrumb">
    <li><a href="/admin/faq">Вопрос-ответ</a></li>
    <li><?= $rubric->title; ?></li>
</ul>
<table class="<?= DEFAULT_TABLE_CLASS; ?>">
    <?php foreach ($list as $question): ?>
        <tr>
            <td>
                <div class="pull-right">
                    <a class="btn btn-success"
                       href="/admin/faq/rubrics/<?= $question->rubricId; ?>/edit/<?= $question->qId; ?>">Редактировать</a>
                </div>
                <h4><?= strip_tags($question->question); ?></h4>
                <h5><?= Lib_Text::Truncate(strip_tags($question->answer)); ?></h5>
            </td>
        </tr>
    <?php endforeach; ?>
</table>