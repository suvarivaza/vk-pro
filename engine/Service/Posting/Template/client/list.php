<?php
/** @var \Service\Posting\Model_Groups_Group $group */
$group = $vars['group'];
/** @var \Service\Posting\Model_Posts_Post $post */
$post = $vars['post'];

if (!$post->datePost) {
    $post->datePost = time();
}

$hour = date('H', $post->datePost);
$minute = date('i', $post->datePost);

$emoji = $vars['emoji'];
?>
    <button class="btn btn-primary btn-lg pull-right" style="color: #ffffff;" onclick="posting.groupSettings();">
        <span class="glyphicon glyphicon-wrench"></span>
    </button>
    <button class="btn btn-info btn-lg pull-right" style="color: #ffffff; margin-right: 10px;"
            onclick="dialog.actionDialog('multiUploadForm', {groupId: <?= $group->groupId; ?>}, '', 'modal-lg')"
            title="Массовая загрузка фото и быстрая настройка публикаций">
        <span class="glyphicon glyphicon-upload"></span>
    </button>
    <ul class="breadcrumb">
        <li>
            <img src="/img/icons/32/icon-post.png" width="30"/>
            <a href="/posting">Автопостинг</a>
        </li>
        <li>
            <img class="img-circle" src="<?= $group->photo; ?>" width="30"/>
            <a href="<?= $group->url; ?>" target="_blank" rel="nofollow"><?= $group->title; ?></a>
        </li>
    </ul>

<?php if (!$group->isActive || ($group->isFree && $group->isFreeCount < 1)): ?>
    <div class="alert alert-danger">Срок действия автопостинга для данной группы истек. Продлите действие слота.</div>

    <div id="i_posting_group_add" class="row c-task-detail c-posting-group-add">
        <div class="col-sm-2">
            <img src="https://vk.com/images/community_50.png" class="img-thumbnail"/>
        </div>
        <div class="col-sm-10">
            <h3>Активировать слот для группы</h3>
        </div>
    </div>
    <div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в автопостинг</h5>
                </div>
                <div class="modal-body" id="i_dialog_group_add_container">
                    <div id="i_dialog_group_add_data"></div>
                    <div id="i_dialog_group_add_error"></div>
                    <div id="i_dialog_group_add_progress" class="progress progress-striped active"
                         style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                             aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button id="i_dialog_group_add_button_cancel" type="button" class="btn btn-default"
                            data-dismiss="modal">Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

    <style>
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

    <div class="text-center">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="alert alert-info">
                    В очереди на публикацию <strong><?= \Lib_Text::Word4NumberNewReturn($vars['counts'][0],
                            ['пост', 'поста', 'постов']); ?></strong>
                </h4>
            </div>
            <div class="col-sm-6">
                <h4 class="alert alert-info">
                    Было опубликованно <strong><?= \Lib_Text::Word4NumberNewReturn($vars['counts'][1],
                            ['пост', 'поста', 'постов']); ?></strong>
                </h4>
            </div>
        </div>

    </div>
    <div style="clear: both;"></div>
    <form method="post" class="form-horizontal" role="form" id="i_post_form">
        <input id="i_post_action" type="hidden" name="action" value="<?= $vars['action']; ?>"/>
        <input id="i_post_uuid" type="hidden" name="uuid" value="<?= $vars['uploader']['uuid']; ?>"/>
        <textarea name="text" id="i_text" style="display: none;"></textarea>
        <div class="page_block">
            <img src="<?= $group->photo; ?>" class="img-circle pull-left" style="width: 28px;">
            <img onclick="$('#i_div_emoji').toggle();" src="/img/icons/32/vk/smile_icon.png" class="pull-right"/>
            <div class="c_div_emoji" id="i_div_emoji" style="display: none;">
                <div class="c_div_emoji_angle" id="i_div_emoji_angle">
                    <span class="glyphicon glyphicon-menu-up"></span>
                </div>
                <div class="c_div_emoji_container" id="i_div_emoji_container">
                    <div class="c_div_emoji_scroll" id="i_div_emoji_scroll">
                        <?php foreach ($emoji as $emojiList): ?>
                            <div><?= $emojiList['title']; ?></div>
                            <?php foreach ($emojiList['texts'] as $text => $code): ?>
                                <a href="javascript:void(0)" onclick="posting.emojiAdd('<?= $code; ?>')">
                                    <img src="/img/emoji/<?= $code; ?>.png"/>
                                </a>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="page_block_add">
                <div class="c_post_field" id="post_field" title="Что у Вас нового?" contenteditable="true"></div>
            </div>
            <div class="submit_post">
                <div class="row form-group">
                    <div class="col-sm-12">
                        <div>Время публикации</div>
                    </div>
                    <div class="col-sm-5">
                        <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                            <input id="i_post_date" name="postDate" type="text" class="datepicker form-control"
                                   placeholder="Дата публикации" value="<?= date('d.m.Y', $post->datePost); ?>">
                        </div>
                    </div>
                    <label class="control-label" style="float: left; margin-top: 5px;">в</label>
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
                <div style="clear: both;"></div>
            </div>
            <div id="i_link_add_container" class="submit_post c_link_add_container"
                 style="display: none; margin-top: 10px;">
                <input class="form-control c_link_add_input" name="url[]" value=""
                       placeholder="Укажите ссылку на внешний ресурс"/>
            </div>
            <div class="submit_post">
                <div id="i_form_attachments_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div id="i_div_attachments"></div>
            </div>

            <div class="c_link_ads submit_post" style="display: none">
                <input type="checkbox" name="ads" id="i_link_ads" style="display: none"/>
                <div class="close pull-right"
                     onclick="$(this).parent().slideUp('slow', function(){$('#i_link_ads').prop('checked', false)});">
                    <span class="glyphicon glyphicon-remove"></span>
                </div>
                <h5 class="c_ads_h5">Это реклама</h5>
            </div>

            <div id="submit_post" class="submit_post">
                <div class="post_buttons">
                    <button type="button" class="btn btn-primary pull-right" onclick="posting.addPost()">Добавить
                    </button>
                    <div class="pull-right" style="margin-right: 10px; padding-top: 2px;">
                        <input id="i_ownerId" type="checkbox" name="signature" style="margin-top: 2px;"/>
                        <label for="i_ownerId" style="margin-top: -2px; display: inline-block;">Подпись</label>
                    </div>

                    <div class="c_div_icon c_tooltip_bottom" data-content="Добавить фото" data-placement="bottom"
                         id="i_div_add_photo" onclick="$('#i_form_photos').modal('show');"><span
                                class="post_button_photo"></span></div>
                    <div class="c_div_icon c_tooltip_bottom" data-content="Добавить видео" data-placement="bottom"
                         id="i_div_add_video" onclick="$('#i_form_video').modal('show');"><span
                                class="post_button_video"></span></div>
                    <div class="c_div_icon c_tooltip_bottom" data-content="Добавить ссылку" data-placement="bottom"
                         id="i_div_add_link" onclick="$('.c_link_add_container').show();"><span
                                class="post_button_link"></span></div>
                    <div class="c_div_icon c_tooltip_bottom" data-content="Добавить опрос" data-placement="bottom"
                         id="i_div_add_poll" onclick="posting.addPoll();"><span class="post_button_poll"></span></div>
                    <div class="c_div_icon c_tooltip_bottom" data-content="Это реклама" data-placement="bottom"
                         id="i_div_ads" onclick="$('#i_link_ads').prop('checked', true); $('.c_link_ads').slideDown();">
                        <span class="post_button_ads"></span></div>
                </div>
            </div>
        </div>
    </form>
    <?php foreach ($vars['list'] as $post): ?>
        <?php STPL::Display('client/list/post', ['post' => $post, 'group' => $group]); ?>
    <?php endforeach; ?>
    <div class="modal fade" id="i_form_video" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="i_form_login_label">Загрузка изображений</h4>
                </div>
                <div class="modal-body" id="i_form_photos_container">
                    <div id="i_form_photos_container_form">
                        <?php \STPL::Display('controls/upload_dialog', $vars['uploader']); ?>
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
    <div id="i_poll_example" data-index="0" style="display: none;">
        <div class="close pull-right" onclick="$(this).parent().slideUp('slow', function(){$(this).remove();});">
            <span class="glyphicon glyphicon-remove"></span>
        </div>
        <div class="submit_post">
            <div class="row form-group">
                <div class="col-sm-12">
                    <label>Тема опроса</label>
                    <input data-index="0" type="text" name="poll[0][title]" class="form-control c_poll_title">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12">
                    <label>Варианты ответа</label>
                    <input data-index="0" type="text" name="poll[0][answers][]" class="form-control c_poll_answer">
                </div>
                <div class="col-sm-12">
                    <label></label>
                    <input data-index="0" type="text" name="poll[0][answers][]" class="form-control c_poll_answer">
                </div>
                <div class="col-sm-12">
                    <label></label>
                    <input data-index="0" type="text" name="poll[0][answers][]" class="form-control"
                           onclick="posting.addAnswer(this);" placeholder="Добавить вариант">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="i_group_settings" tabindex="-1" role="dialog" aria-labelledby="form_login">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="i_form_login_label">Настройки группы</h4>
                </div>
                <div class="modal-body" id="i_group_settings_container">
                    <div id="i_group_settings_data"></div>
                    <div id="i_group_settings_error"></div>
                    <div id="i_group_settings_progress" class="progress progress-striped active" style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                             aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button id="i_group_settings_close" type="button" class="btn btn-danger" data-dismiss="modal">
                        Отмена
                    </button>
                    <button id="i_group_settings_save" type="button" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var dates = <?= json_encode($vars['dates']); ?>;
    </script>
    <script type="text/javascript" language="javascript" src="/js/dialog.min.js" charset="utf-8"></script>
<?php endif; ?>