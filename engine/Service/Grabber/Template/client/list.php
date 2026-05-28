<?php
/** @var \Service\Posting\Model_Groups_Group $group */
$group = $vars['group'];
/** @var \Service\Grabber\Model_Sources_Source[] $list */
$list = $vars['list'];
?>
<button class="btn btn-primary btn-lg pull-right" style="color: #ffffff;" onclick="grabber.groupSettings();">
    <span class="glyphicon glyphicon-wrench"></span>
</button>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-grabber.png" width="30"/>
        <a href="/grabber">Граббер</a>
    </li>
    <li>
        <img class="img-circle" src="<?= $group->photo; ?>" width="30"/>
        <a href="<?= $group->url; ?>" target="_blank" rel="nofollow"><?= $group->title; ?></a>
    </li>
</ul>

<?php if ($group->dateValid < time()): ?>
    <div class="alert alert-danger">
        Срок действия для группы "<strong><?= $group->title; ?></strong>" истек.
        Посты не будут собираться и публиковаться, пока группа не будет активированна.
    </div>

    <div id="i_grabber_group_add" class="row c-task-detail c-grabber-group-add">
        <div class="col-sm-2">
            <img src="https://vk.com/images/community_100.png" class="img-thumbnail"/>
        </div>
        <div class="col-sm-8">
            <h3>Купить или активировать слот</h3>
        </div>
    </div>
<?php endif; ?>

<?php if (!count($list)): ?>
    <div class="alert alert-warning">
        <span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
        Добавьте источников для группы
    </div>
<?php endif; ?>

<div class="text-right">
    <button type="button" class="btn btn-primary" style="color: #ffffff;" onclick="grabber.sourcesAdd();">Добавить
        источник
    </button>
</div>

<?php foreach ($list as $source): ?>

    <div class="c-task-detail">
        <table style="width: 100%;">
            <tr>
                <td style="width: 56px;">
                    <img class="img-thumbnail" src="<?= $source->photo; ?>" style="width: 50px; height: 50px;"/>
                </td>
                <td>
                    <h5><a href="<?= $source->url; ?>" target="_blank"><?= $source->title; ?></a></h5>
                </td>
                <td style="width: 100px;">
                    <span class="badge c_tooltip_top"
                          data-content="Ждут размещения / Размещено"><?= $source->count; ?>/<?= $source->countAll; ?></span>
                </td>
                <td class="text-right">
                    <button class="btn btn-default c-source-settings c_tooltip_top" data-content="Настройки источника"
                            data-source-id="<?= $source->sourceId; ?>"><span class="glyphicon glyphicon-cog"></span>
                    </button>
                    <button class="btn btn-warning c-source-refresh c_tooltip_top" data-content="Очистить очередь"
                            data-source-id="<?= $source->sourceId; ?>"><span class="glyphicon glyphicon-refresh"></span>
                    </button>
                    <button class="btn btn-danger c-source-remove c_tooltip_top" data-content="Удалить источник"
                            data-source-id="<?= $source->sourceId; ?>"><span class="glyphicon glyphicon-trash"></span>
                    </button>
                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>


<?php foreach ($vars['posts'] as $post): ?>

    <?php STPL::Display('client/list/post', ['post' => $post, 'group' => $group]); ?>
<?php endforeach; ?>
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
                <div id="i_form_photos_container_form">

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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
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
                <button id="i_group_settings_close" type="button" class="btn btn-danger" data-dismiss="modal">Отмена
                </button>
                <button id="i_group_settings_save" type="button" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="i_source_settings" tabindex="-1" role="dialog" aria-labelledby="form_login">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_form_login_label">Источник</h4>
            </div>
            <div class="modal-body" id="i_source_settings_container">
                <div id="i_source_settings_data"></div>
                <div id="i_source_settings_error"></div>
                <div id="i_source_settings_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_source_settings_close" type="button" class="btn btn-danger" data-dismiss="modal">Отмена
                </button>
                <button id="i_source_settings_save" type="button" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в Граббер</h5>
            </div>
            <div class="modal-body" id="i_dialog_group_add_container">
                <div id="i_dialog_group_add_data"></div>
                <div id="i_dialog_group_add_error"></div>
                <div id="i_dialog_group_add_progress" class="progress progress-striped active" style="display: none;">
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