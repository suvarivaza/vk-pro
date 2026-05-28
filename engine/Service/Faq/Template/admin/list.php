<?php
/** @var Model_Questions_Question[] $questions */
use Service\Faq\Model_Questions_Question;

$list = $vars['list'];
$rubrics = $vars['rubrics'];
?>

<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-help.png" width="30">
        <a href="/admin/faq/my">Техническая поддержка</a>
    </li>
    <li>
        Все вопросы
    </li>
</ul>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
<table class="table">
    <?php foreach ($list as $rubricId => $questions): ?>
        <tr>
            <td>
                <h3><?= $rubrics[$rubricId]->title; ?></h3>
            </td>
        </tr>
        <?php foreach ($questions as $question): ?>
            <tr>
                <td>
                    <h4 id="i_a_<?= $question->qId; ?>" style="padding: 10px;"><a href="javascript:void(0)"
                                                                                  onclick="$('#i_h5_<?= $question->qId; ?>').slideDown(); $('#i_a_<?= $question->qId; ?>').hide();"><?= $question->question; ?></a>
                    </h4>
                    <div id="i_h5_<?= $question->qId; ?>"
                         style="background-color: #ffffff; display: none; padding: 10px;">
                        <h4><a href="javascript:void(0)"
                               onclick="$('#i_h5_<?= $question->qId; ?>').slideUp(); $('#i_a_<?= $question->qId; ?>').show();"><?= strip_tags($question->question); ?></a>
                        </h4>
                        <h5><?= $question->answer; ?></h5>
                        <?php $chat = $question->getChat(); ?>
                        <?php foreach ($chat as $text): ?>
                            <div class="faq_container <?= $text['type']; ?>">
                                <h5><?= $text['title']; ?>
                                    <small><?= Lib_TimeStamp::createFromTimestamp($text['date'])->format('d F Y'); ?></small>
                                </h5>
                                <div class="faq_container_text"><?= Lib_Html::ChangeBR($text['text']); ?></div>
                            </div>
                            <div class="clearfix"></div>
                        <?php endforeach; ?>

                        <form class="form-horizontal">
                            <input type="hidden" name="action" value="edit"/>
                            <input type="hidden" name="qId" value="<?= $question->qId; ?>"/>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea name="question" class="form-control form-span"
                                              placeholder="Текст для ответа" rows="8"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <button class="c-button-answer btn btn-primary" type="button">Ответить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
<div class="modal fade" id="i_dialog_question" tabindex="-1" role="dialog" aria-labelledby="i_dialog_question">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="i_dialog_question_label">Задать вопрос</h4>
            </div>
            <div class="modal-body" id="i_dialog_question_container">
                <form id="i_dialog_question_form" class="form-horizontal">
                    <input type="hidden" name="action" value="add"/>
                    <div class="form-group" style="position: relative;">
                        <div class="col-sm-12">
                            <select name="rubricId" id="i_dialog_question_rubricId" class="form-control form-span"
                                    placeholder="Укажите рубрику">
                                <?php foreach ($rubrics as $rubric): ?>
                                    <option value="<?= $rubric->rubricId; ?>"><?= $rubric->title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <textarea name="question" class="form-control form-span" placeholder="Текст вопроса"
                                      rows="12"></textarea>
                        </div>
                    </div>
                </form>
                <div id="i_dialog_question_error"></div>
                <div id="i_dialog_question_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="i_dialog_question_dome" type="button" class="btn btn-primary">Задать</button>
                <button id="i_dialog_question_cancel" type="button" class="btn btn-default" data-dismiss="modal">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>