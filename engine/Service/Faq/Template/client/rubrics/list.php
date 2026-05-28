<?php
/** @var Model_Rubrics_Rubric[] $list */
use Service\Faq\Model_Rubrics_Rubric;

$list = $vars['list'];
?>
<div class="pull-right">
    <button class="button-green pull-right" onclick="$('#i_dialog_question').modal('show');">
        Задать вопрос
        <img src="/img/icons/32/icon-help-white.png" width="32"/>
    </button>
</div>

<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-help.png" width="24"/>&nbsp;
        <a href="/faq">Вопрос-ответ</a>
    </li>
</ul>

<div class="row">
    <?php $i = 1;

    foreach ($list
    as $rubric):
    $i++; ?>
    <?php if ($i == 2):
    $i = 0; ?></div>
<div class="row"><?php endif; ?>
    <div class="col-sm-6">
        <a href="/faq/list/<?= $rubric->rubricId; ?>">
            <h4>
                <img src="<?= $rubric->icon; ?>"/>
                <?= $rubric->title; ?>
            </h4>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="modal fade" id="i_dialog_question" tabindex="-1" role="dialog" aria-labelledby="i_dialog_question">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="i_dialog_question_label">Задать вопрос</h4>
            </div>
            <div class="modal-body" id="i_dialog_question_container">
                <div class="alert alert-info">Ответ придет Вам в личный кабинет в разделе "Техическая поддержка"</div>
                <div class="clearfix"></div>
                <form id="i_dialog_question_form" class="form-horizontal">
                    <input type="hidden" name="action" value="add"/>
                    <div class="form-group" style="position: relative;">
                        <div class="col-sm-12">
                            <select name="rubricId" id="i_dialog_question_rubricId" class="form-control form-span"
                                    placeholder="Укажите рубрику">
                                <?php foreach ($list as $rubric): ?>
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