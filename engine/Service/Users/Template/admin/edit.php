<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<h1><?php if ($vars['action'] == 'add'): ?>Добавление<?php else: ?>Редактирование<?php endif; ?>
    пользователя <?= $user->userId ? ('<br />' . $user->name) : ''; ?></h1>
<form class="form-horizontal" role="form" method="post">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input type="hidden" name="uuid" value="<?= $vars['uploader']['uuid']; ?>"/>
    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-6 alert alert-danger">
                <?= implode('<br />', $vars['errors']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Е-mail:</label>
        <div class="col-sm-6">
            <input class="form-control" name="email" value="<?= $user->email; ?>" placeholder="Email"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Пароль:</label>
        <div class="col-sm-6">
            <input class="form-control" type="password" name="password" placeholder="Пароль"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Подтверждение пароля:</label>
        <div class="col-sm-6">
            <input class="form-control" type="password" name="passwordConfirm" placeholder="Подтверждение пароля"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Фамилия:</label>
        <div class="col-sm-6">
            <input class="form-control" name="lastName" value="<?= $user->lastName; ?>" placeholder="Фамилия"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Имя:</label>
        <div class="col-sm-6">
            <input class="form-control" name="firstName" value="<?= $user->firstName; ?>" placeholder="Имя"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Отчество:</label>
        <div class="col-sm-6">
            <input class="form-control" name="secondName" value="<?= $user->secondName; ?>" placeholder="Отчество"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">День рождения:</label>
        <div class="col-sm-6">
            <input class="form-control datepicker" name="year" value="<?= $user->year; ?>" placeholder="День рождения"/>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Номер телефона:</label>
        <div class="col-sm-6">
            <input class="form-control c_phone" name="phone" value="<?= $user->phone; ?>" placeholder="Номер телефона"/>
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label">Фото:</label>
        <div class="col-sm-6">
            <?php $photos = $user->getPhotos();
            $list = [];

            if (is_array($photos) && count($photos)) {
                foreach ($photos as $id => $photo) {
                    $list[] = [
                        'key' => $photo['path'],
                        'url' => '/images/users/big/' . $photo['path'],
                        'url_preview' => '/images/users/small/' . $photo['path'],
                    ];
                }
            } ?>
            <?php $vars['uploader']['list'] = $list;
            $vars['uploader']['max_count'] = 1;
            \STPL::Display('controls/upload', $vars['uploader']); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label"></label>
        <div class="col-sm-6">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="button" class="btn btn-danger" onclick="history.back();">Отмена</button>
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
        selector: ".tinymce",
        theme: "modern",
        plugins: [
            "advlist autolink lists moxiemanager link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern imagetools"
        ],
        toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "print preview media | forecolor backcolor emoticons",
        content_css: [
            '/css/bootstrap.min.css',
            //'/css/styles.css',
            '/css/tinymce.css'
        ]
    });

    $('#i_add_param').click(function () {
        var div = $(this).parent().parent().prev();
        var insert = $(div).clone();
        $(insert).css({display: 'block'});
        $(div).before($(insert));
    });
    $('.c_phone').mask("+7 (999) 999-99-99");
</script>