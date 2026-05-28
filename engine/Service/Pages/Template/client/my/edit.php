<?php
/** @var \Service\Pages\Model_Pages_Page $page */
$page = $vars['page'];
?>
<ul class="breadcrumb">
    <?php $total = count($vars['chain']);
    $i = 0;

    foreach ($vars['chain'] as $data): $i++; ?>
        <li<?php if ($i == $total): ?> class="active"<?php endif; ?>>
            <?php if ($i != $total): ?><a href="<?= $data['url']; ?>"><?php endif; ?><?php if (isset($data['bold'])): ?>
                <strong><?php endif; ?><?= $data['title']; ?><?php if (isset($data['bold'])): ?></strong><?php endif; ?><?php if ($i != $total): ?>
            </a><?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<form action="" method="POST" enctype="multipart/form-data" target="_self" class="form-horizontal" role="form">
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
            <input type="text" class="form-control" id="inputtitle" name="title" placeholder="Заголовок"
                   value="<?= $page ? $page->title : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" class="form-control" id="inputbrief" name="describe" placeholder="Описание"
                   value="<?= $page ? $page->describe : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" class="form-control" id="inputbrief" name="keywords" placeholder="Ключевые слова"
                   value="<?= $page ? $page->keywords : ''; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <?php $photos = [];

            if ($page->photo) {
                $photos[] = $page->photo;
            }
            $list = [];

            if (is_array($photos) && count($photos)) {
                foreach ($photos as $id => $photo) {
                    $list[] = [
                        'key' => $photo,
                        'url' => '/images/articles/big/' . $photo,
                        'url_preview' => '/images/articles/small/' . $photo,
                    ];
                }
            } ?>
            <?php $vars['uploader']['list'] = $list;
            $vars['uploader']['max_count'] = 1;
            \STPL::Display('controls/upload', $vars['uploader']); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <textarea name="text" class="tinymce form-control" rows="20"
                      placeholder="Страница"><?= $page ? $page->text : ''; ?></textarea>
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
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            trigger: 'focus'
        });
    });
    tinymce.init({
        language: "ru",
        selector: "textarea",
        theme: "modern",
        plugins: [
            "advlist autolink lists moxiemanager link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern imagetools"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "print preview media | forecolor backcolor emoticons",
        //image_advtab: true,
        templates: [
            {title: 'Test template 1', content: 'Test 1'},
            {title: 'Test template 2', content: 'Test 2'}
        ],
        relative_urls: false
    });
</script>