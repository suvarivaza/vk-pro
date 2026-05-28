<?php
/** @var \Service\Posting\Model_Posts_Post $post */
$post = $vars['post'];

if (!$post->datePost) {
    $post->datePost = time();
}

$hour = date('H', $post->datePost);
$minute = date('i', $post->datePost);
?>
<style>
    textarea.form-control {
        height: 100%;
    }

    .c_div_icon {
        position: absolute;
        bottom: 0;
        right: 150px;
        font-size: 20px;
        color: #337ab7;
    }

    .c_div_icon:hover {
        color: #455672 !important;
        cursor: pointer;
    }

    #i_div_add_photo {
        right: 120px;
    }

    #i_div_add_video {
        right: 90px;
    }

    #i_div_add_link {
        right: 60px;
    }

    #i_div_add_emoji {
        right: 30px;
    }

    .uploader_file {
        width: 50px;
        height: 50px;
    }

    .c_video_add {
        position: absolute;
        top: 5px;
        right: 20px;
        font-size: 24px;
    }

    .play_button {
        display: none;
    }

    .c_video_preview:hover .play_button {
        display: block;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-top: -40px;
        margin-left: -20px;
    }
</style>
<h1><?php if ($vars['action'] == 'add'): ?>Добавление<?php else: ?>Редактирование<?php endif; ?> поста</h1>
<form method="post" class="form-horizontal" role="form">
    <input id="i_post_action" type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input id="i_post_uuid" type="hidden" name="uuid" value="<?= $vars['uploader']['uuid']; ?>"/>
    <div class="form-group">
        <label class="col-sm-12 control-label" style="text-align: left;">Время публикации</label>
        <div class="col-sm-5">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
                <input id="i_post_date" type="text" class="datepicker form-control" placeholder="Дата публикации"
                       value="<?= date('d.m.Y', $post->datePost); ?>">
            </div>
        </div>
        <label class="control-label" style="float: left;">в</label>
        <div class="col-sm-4">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <select id="i_hour" name="hour" class="form-control">
                            <?php for ($i = 0; $i < 24; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                <option value="<?= $val; ?>"<?php if ($val == $hour): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td>:</td>
                    <td style="width: 50%;">
                        <select id="i_minute" name="minute" class="form-control">
                            <?php for ($i = 0; $i < 60; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                <option value="<?= $val; ?>"<?php if ($val == $minute): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12" style="position: relative;">
            <div style="position: absolute; height: auto; z-index: -1;" id="i_div_text"></div>
            <div class="c_div_icon c_tooltip_bottom" data-content="Добавить фото" data-placement="bottom"
                 id="i_div_add_photo" onclick="$('#i_form_photos').modal('show');">
                <span class="glyphicon glyphicon-picture"></span>
            </div>
            <div class="c_div_icon c_tooltip_bottom" data-content="Добавить видео" data-placement="bottom"
                 id="i_div_add_video" onclick="$('#i_form_video').modal('show');">
                <span class="glyphicon glyphicon-film"></span>
            </div>
            <div class="c_div_icon c_tooltip_bottom" data-content="Добавить ссылку" data-placement="bottom"
                 id="i_div_add_link" onclick="$('.c_link_add_container').show();">
                <span class="glyphicon glyphicon-link"></span>
            </div>

            <div class="c_div_icon c_tooltip_bottom" data-content="Добавить смайлы" data-placement="bottom"
                 id="i_div_add_emoji">
                <span class="glyphicon glyphicon-thumbs-up"></span>
            </div>
            <textarea class="form-control form-span" id="i_textarea_text" placeholder="Введите текст поста"
                      style="min-height: 80px;"><?= $post->text; ?></textarea>
        </div>
        <div id="i_link_add_container" class="col-sm-12 c_link_add_container" style="display: none; margin-top: 10px;">
            <input class="form-control c_link_add_input" name="url[]" value=""
                   placeholder="Укажите ссылку на внешний ресурс"/>
        </div>
        <div class="col-sm-12">
            <div id="i_form_attachments_progress" class="progress progress-striped active" style="display: none;">
                <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                     style="width: 100%">
                    <span class="sr-only">&nbsp;</span>
                </div>
            </div>
            <div id="i_div_attachments"></div>
        </div>
        <div class="col-sm-12">
            <button type="button" class="btn btn-success" onclick="post_edit.formSubmit();">Сохранить</button>
        </div>
    </div>
</form>

<div class="modal fade" id="i_form_video" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_form_login_label">Добавление Видео из ВК</h4>
            </div>
            <div class="modal-body" id="i_form_video_container">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Вставьте ссылку на видео" id="i_video_url">
                    <span class="input-group-addon btn btn-primary" id="i_button_video_url">
                        Подгрузить
                    </span>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Укажите текст для поиска видео"
                           id="i_video_search_query">
                    <span class="input-group-addon btn btn-primary" id="i_button_video_search">
                        Искать
                    </span>
                </div>
                <div id="i_form_video_data"></div>
                <div id="i_form_video_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_form_video_close" type="button" class="btn btn-success" data-dismiss="modal">Готово
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="i_form_photos" tabindex="-1" role="dialog" aria-labelledby="form_login">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_form_login_label">Загрузка изображений</h4>
            </div>
            <div class="modal-body" id="i_form_photos_container">
                <div class="row">
                    <div class="col-sm-6">
                        <?php $vars['uploader']['list'] = [];
                        $vars['uploader']['max_count'] = 10;
                        \STPL::Display('controls/upload', $vars['uploader']); ?>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" placeholder="Введите URL: http://example.com/image.jpg"
                               class="modal-url-image input-sm form-control">
                        <div class="clearfix">
                            <a class="btn-add-url-input noselect" href="javascript:void(0);">
                                + Добавить еще
                            </a>
                            <a class="btn btn-sm btn-success btn-upload-url pull-right">Загрузить</a>
                        </div>
                    </div>
                </div>
                <div id="i_form_photos_data"></div>
                <div id="i_form_photos_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_form_photos_close" type="button" class="btn btn-success" data-dismiss="modal">Готово
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="i_form_progress" tabindex="-1" role="dialog" aria-labelledby="form_login">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="i_form_login_label">Подождите, идет сохранение поста</h4>
            </div>
            <div class="modal-body" id="i_form_progress_container">
                <div id="i_form_progress_progress" class="progress progress-striped active">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>