<?php
/** @var Model_Rubrics_Rubric $rubric */
$rubric = $vars['rubric'];
/** @var Model_Questions_Question $question */
$question = $vars['question'];

use Service\Faq\Model_Questions_Question;
use Service\Faq\Model_Rubrics_Rubric;

?>
<ul class="breadcrumb">
    <li><a href="/admin/faq">Вопрос-ответ</a></li>
    <li><a href="/admin/faq/rubrics/<?= $rubric->rubricId; ?>/list"><?= $rubric->title; ?></a></li>
    <li><strong>Добавление вопрос-ответа</strong></li>
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
            Вопрос:
            <textarea id="i-text" name="question" class="tinymce form-control" rows="20"
                      placeholder="Страница"><?= $question->question; ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            Ответ:
            <textarea id="i-text" name="answer" class="tinymce form-control" rows="20"
                      placeholder="Страница"><?= $question->answer; ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-primary" type="submit" name="submit">Сохранить</button>
            <button class="btn btn-danger" type="submit" name="cancel">Отмена</button>
        </div>
    </div>
</form>
<div id="i-div-preview" style="background-color: #eee; ">
    <div id="i-div-preview-data"></div>
    <div class="text-right">
        <button class="btn btn-default" id="i-div-preview-close">Закрыть</button>
    </div>
</div>

<script type="text/javascript">
    $('.btn-preview').click(function () {
        var val = $('#i-text').val();
        $('#i-div-preview-data').html(val);
        $('#i-form-new').hide();
        $('#i-div-preview').show();
        $('#i-admin-content-container').css({'background-color': '#eee'});
        $('#i-admin-content').removeClass('col-sm-10').addClass('col-sm-8');
    });
    $('#i-div-preview-close').click(function () {
        $('#i-div-preview').hide();
        $('#i-form-new').show();
        $('#i-admin-content-container').css({'background-color': '#fff'});
        $('#i-admin-content').removeClass('col-sm-8').addClass('col-sm-10');
    });
    tinymce.init({
        language: "ru",
        selector: "textarea",
        theme: "modern",
        plugins: [
            "advlist autolink lists moxiemanager link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen lineheight",
            "insertdatetime media nonbreaking save contextmenu directionality",
            "emoticons template paste textcolor table colorpicker textpattern imagetools"
        ],
        toolbar1: "undo redo | styleselect fontsizeselect lineheightselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        toolbar2: "link image | print preview media | forecolor backcolor | bold italic",
        content_css: [
            '/css/bootstrap.css',
            '/css/styles.css'
        ],
        link_advtab: true,
        image_advtab: true,
        image_title: true,
        link_class_list: [
            {title: '-- Укажите --', value: ''},
            {title: 'На весь экран при клике', value: 'c_gallery_link'},
            {title: 'Увеличение при клике', value: 'c_fancybox'}
        ],
        relative_urls: false,
        allow_script_urls: true,
        extended_valid_elements: "script[language|type]",
        verify_html: false,
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        }
    });
</script>