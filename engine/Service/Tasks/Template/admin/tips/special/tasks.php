<h3>
    Подсказки при добавлении спецзадания
    <small class="pull-right">
        <a class="btn btn-default btn-sm" href="/admin/tasks/tips/special/fields">Подсказки для полей ввода
            спецзадания</a>
    </small>
</h3>
<div class="col-sm-12">
    <div class="btn-group" data-toggle="buttons">
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'likes'): ?> active<?php endif; ?>"
               data-type="likes">Лайки</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'reposts'): ?> active<?php endif; ?>"
               data-type="reposts">Репосты</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'comments'): ?> active<?php endif; ?>"
               data-type="comments">Комментарии</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'join'): ?> active<?php endif; ?>"
               data-type="join">Подписки</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'friends'): ?> active<?php endif; ?>"
               data-type="friends">Друзья</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'polls'): ?> active<?php endif; ?>"
               data-type="polls">Опросы</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'views'): ?> active<?php endif; ?>"
               data-type="views">Просмотры</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'video'): ?> active<?php endif; ?>"
               data-type="video">Просмотры видео</label>
        <label class="c-main-menu-ul-li btn btn-primary<?php if ($vars['type'] == 'token'): ?> active<?php endif; ?>"
               data-type="token">Получение токена</label>
    </div>
</div>

<script>
    var focus = 1;
    $('.c-main-menu-ul-li').click(function () {
        location.href = '/admin/tasks/tips/special/' + $(this).data('type');
    });
</script>

<form method="post">
    <?php if (in_array($vars['type'], ['likes', 'reposts', 'comments'])): ?>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control" id="i_vkType" name="vkType">
                    <?php foreach ($vars['vkTypes'] as $vkType => $vkTitle): if ($vars['type'] == 'comments' && $vkType == 'comment') {
    continue;
} ?>
                        <option value="<?= $vkType; ?>"><?= $vkTitle; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (in_array($vars['type'], ['likes', 'reposts', 'comments'])): ?>
        <?php foreach ($vars['vkTypes'] as $vkType => $vkTitle): if ($vars['type'] == 'comments' && $vkType == 'comment') {
    continue;
} ?>
            <div class="form-group c-form-tinymce" id="i_tip_<?= $vars['type']; ?>_<?= $vkType; ?>">
                <div class="col-sm-12">
                    <textarea name="<?= $vars['type']; ?>_<?= $vkType; ?>"
                              class="tinymce form-control"><?= $vars['texts'][$vars['type'] . '_' . $vkType]; ?></textarea>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="form-group">
            <div class="col-sm-12">
                <textarea id="i_tip_<?= $vars['type']; ?>_<?= $vkType; ?>" name="<?= $vars['type']; ?>"
                          class="tinymce form-control"><?= $vars['text']; ?></textarea>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    function vkTypeChange(vkType) {
        if (!vkType)
            vkType = $('#i_vkType > option:selected').val();

        $('.c-form-tinymce').hide();
        $('#i_tip_<?= $vars['type']; ?>_' + vkType).show();
    }

    $(document).ready(function () {
        var vkType = location.hash.substr(1);
        vkTypeChange(vkType);
        $('#i_vkType option').each(function () {
            $(this).prop('selected', false);
            if ($(this).val() == vkType) {
                $(this).prop('selected', true);
            }
        });

        $('#i_vkType').change(function () {
            vkTypeChange($('#i_vkType > option:selected').val());
        });
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
    });
</script>