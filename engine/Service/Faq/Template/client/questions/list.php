<?php
/** @var Model_Rubrics_Rubric $rubric */
$rubric = $vars['rubric'];
/** @var Model_Questions_Question[] $list */
$list = $vars['list'];

use Service\Faq\Model_Questions_Question;
use Service\Faq\Model_Rubrics_Rubric;

?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-help.png" width="24"/>&nbsp;
        <a href="/faq">Вопрос-ответ</a>
    </li>
    <li><?= $rubric->title; ?></li>
</ul>
<table class="table">
    <?php foreach ($list as $question): ?>
        <tr>
            <td>
                <h4 id="i_a_<?= $question->qId; ?>" style="padding: 10px;"><a href="javascript:void(0)"
                                                                              onclick="$('#i_h5_<?= $question->qId; ?>').slideDown(); $('#i_a_<?= $question->qId; ?>').hide();"><?= $question->question; ?></a>
                </h4>
                <div id="i_h5_<?= $question->qId; ?>" style="background-color: #ffffff; display: none; padding: 10px;">
                    <h4><a href="javascript:void(0)"
                           onclick="$('#i_h5_<?= $question->qId; ?>').slideUp(); $('#i_a_<?= $question->qId; ?>').show();"><?= strip_tags($question->question); ?></a>
                    </h4>
                    <h5><?= $question->answer; ?></h5>
                </div>

            </td>
        </tr>
    <?php endforeach; ?>
</table>