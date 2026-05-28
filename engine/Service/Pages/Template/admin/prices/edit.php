<?php
/** @var \Service\Pages\Model_Prices_Price $price */
$price = $vars['price'];
$price->Date = $price->Date ?: time();
?>
<h1><?php if ($vars['action'] == 'add') : ?>Добавление<?php else: ?>Редактирование<?php endif; ?> прайса</h1>
<form method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input name="action" type="hidden" value="save"/>
    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6 alert alert-danger">
                <?= implode('<br />', $vars['errors']); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Наименование</label>
        <div class="col-sm-10">
            <input class="form-control" id="i_Title" name="Title" placeholder="Наименование"
                   value="<?= $price->Title; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Наименование на главной</label>
        <div class="col-sm-10">
            <input class="form-control" id="i_TitleMain" name="TitleMain" placeholder="Наименование на главной"
                   value="<?= $price->TitleMain; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Описание</label>
        <div class="col-sm-10">
            <input class="form-control" id="i_Description" name="Description" placeholder="Описание"
                   value="<?= $price->Description; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Дата</label>
        <div class="col-sm-10">
            <input class="form-control datepicker" id="i_Date" name="Date" placeholder="Дата"
                   value="<?= date('d.m.Y', $price->Date); ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Порядок</label>
        <div class="col-sm-10">
            <input class="form-control" id="i_Order" name="Order" placeholder="Порядок" value="<?= $price->Order; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <textarea name="Text" class="tinymce form-control" rows="20"
                      placeholder="Описание"><?= $price->Text; ?></textarea>
        </div>

    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label"></label>
        <div class="col-sm-10">
            <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="history.back();">Отмена</button>
        </div>
    </div>
</form>
<script type="text/javascript">

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
    });
</script>