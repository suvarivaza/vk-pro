<ul class="breadcrumb">
    <li>Спецзадания</li>
</ul>

<form action="" method="POST" enctype="multipart/form-data" target="_self" class="form-horizontal" role="form">
    <input type="hidden" name="action" value="save"/>

    <div class="form-group">
        <div class="col-sm-12">
            <textarea name="text" class="tinymce form-control" rows="20"
                      placeholder="Главная спецзаданий"><?= $vars['text']; ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <button class="btn btn-default" type="submit" name="submit">Сохранить</button>
            <button class="btn btn-danger" type="submit" name="cancel">Отмена</button>
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