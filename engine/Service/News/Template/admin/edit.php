<?php
/** @var Model_News_New $new */
$new = $vars['new'];

use Service\News\Model_News_New;

?>
<ul class="breadcrumb">
    <li><a href="/admin/news/list/1">Новости</a></li>
    <li><strong>Добавление новости</strong></li>
</ul>

<form id="i-form-new" action="" method="POST" enctype="multipart/form-data" target="_self" class="form-horizontal"
      role="form">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input type="hidden" name="uuid" value="<?= $vars['uploader']['uuid']; ?>"/>
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
                   value="<?= $new ? $new->title : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" class="form-control form-span" id="inputbrief" name="describe" placeholder="Описание"
                   value="<?= $new ? $new->desc : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" class="form-control form-span" id="inputbrief" name="keywords"
                   placeholder="Ключевые слова" value="<?= $new ? $new->keywords : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <?php $photos = [];
            $photos[] = $new->getPhoto();

            if (is_array($photos) && count($photos)) {
                foreach ($photos as $id => $photo) {
                    if (isset($photo['path'])) {
                        $list[] = [
                            'key' => $photo['path'],
                            'url' => '/img/news/big/' . $photo['path'],
                            'url_preview' => '/img/news/small/' . $photo['path'],
                        ];
                    }
                }
            } ?>
            <?php $vars['uploader']['list'] = $list;
            $vars['uploader']['max_count'] = 1;
            STPL::Display('controls/upload', $vars['uploader']); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <textarea id="i-text" name="text" class="tinymce form-control" rows="20"
                      placeholder="Страница"><?= $new ? $new->text : ''; ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-primary" type="submit" name="submit">Сохранить</button>
            <button class="btn btn-default btn-preview" type="button" name="submit">Предпросмотр</button>
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