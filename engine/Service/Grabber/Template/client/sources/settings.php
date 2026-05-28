<?php
/** @var \Service\Grabber\Model_Sources_Source $source */
$source = $vars['source'];
?>
<ul id="i-settings-nav" class="nav nav-pills nav-justified">
    <li role="presentation" class="active">
        <a href="javascript:void(0);" data-div="i_settings_general">Основные</a>
    </li>
    <li role="presentation">
        <a href="javascript:void(0);" data-div="i_settings_content">Контент</a>
    </li>
    <li role="presentation">
        <a href="javascript:void(0);" data-div="i_settings_filter">Фильтры</a>
    </li>
    <li role="presentation">
        <a href="javascript:void(0);" data-div="i_settings_feat">Дополнительно</a>
    </li>
</ul>
<br/>
<form class="form-horizontal" method="post" id="i_settings_form">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input type="hidden" name="sourceId" value="<?= $source->sourceId; ?>"/>
    <div class="c_settings_div" id="i_settings_general">
        <div class="form-group">
            <div class="col-sm-12">
                <input class="form-control form-span" id="i_source_url" placeholder="Адрес группы" name="url"
                       value="<?= $source->url; ?>"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <input class="form-control form-span" id="i_source_blacklist" placeholder="Стоп-слова, через запятую"
                       name="blacklist" value="<?= $source->blacklist; ?>"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delText"<?php if ($source->delText): ?> checked="checked"<?php endif; ?> /> Удалять
                    весь текст у постов
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delHashtags"<?php if ($source->delHashtags): ?> checked="checked"<?php endif; ?> />
                    Удалять все хэш-теги из текста
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delLinks"<?php if ($source->delLinks): ?> checked="checked"<?php endif; ?> /> Удалять
                    все ссылки из текста
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delVKLinks"<?php if ($source->delVKLinks): ?> checked="checked"<?php endif; ?> />
                    Удалять все ВК-ссылки из текста
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delEmoji"<?php if ($source->delEmoji): ?> checked="checked"<?php endif; ?> /> Удалять
                    все смайлы из текста
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delVideo"<?php if ($source->delVideo): ?> checked="checked"<?php endif; ?> /> Удалять
                    все видео из поста
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="delPoll"<?php if ($source->delPoll): ?> checked="checked"<?php endif; ?> /> Удалять все
                    голосования из поста
                </label>
            </div>
        </div>
    </div>
    <div class="c_settings_div" id="i_settings_content" style="display: none;">
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notPhoto"<?php if ($source->notPhoto): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты с картинками
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notVideo"<?php if ($source->notVideo): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты с видео
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox" name="notMusic" checked="checked" disabled="disabled"/> Не копировать посты с
                    музыкой
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notDoc"<?php if ($source->notDoc): ?> checked="checked"<?php endif; ?> /> Не копировать
                    посты с вложениями
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notGif"<?php if ($source->notGif): ?> checked="checked"<?php endif; ?> /> Не копировать
                    посты с GIFками
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="withPhoto"<?php if ($source->withPhoto): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты без картинок
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="withVideo"<?php if ($source->withVideo): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты без видео
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="withDoc"<?php if ($source->withDoc): ?> checked="checked"<?php endif; ?> /> Не
                    копировать без вложеных документов
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="withGif"<?php if ($source->withGif): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты без GIFок
                </label>
            </div>
        </div>
    </div>
    <div class="c_settings_div" id="i_settings_filter" style="display: none;">
        <div class="form-group">
            <div class="col-sm-12">
                <input class="form-control form-span" id="i_source_filter"
                       placeholder="Фразы поиска (через запятую, максимум: 10)" name="filter"
                       value="<?= $source->filter; ?>"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notAdv"<?php if ($source->notAdv): ?> checked="checked"<?php endif; ?> /> Не копировать
                    рекламные посты
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notFixed"<?php if ($source->notFixed): ?> checked="checked"<?php endif; ?> /> Не
                    копировать закрепленный пост
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notLink"<?php if ($source->notLink): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты с ссылками
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notVKLink"<?php if ($source->notVKLink): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты с ВК-ссылками
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notTextOnly"<?php if ($source->notTextOnly): ?> checked="checked"<?php endif; ?> /> Не
                    копировать посты только с текстом
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notPhotoOnly"<?php if ($source->notPhotoOnly): ?> checked="checked"<?php endif; ?> />
                    Не копировать посты только с фото
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="notFromGroup"<?php if ($source->notFromGroup): ?> checked="checked"<?php endif; ?> />
                    Не копировать посты от имени группы
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="withFromGroup"<?php if ($source->withFromGroup): ?> checked="checked"<?php endif; ?> />
                    Копировать посты только от имени группы
                </label>
            </div>
        </div>
    </div>
    <div class="c_settings_div" id="i_settings_feat" style="display: none;">
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label">
                    <input type="checkbox"
                           name="addCopyright"<?php if ($source->addCopyright): ?> checked="checked"<?php endif; ?> />
                    Добавлять ссылку на автора
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <input class="form-control form-span" id="i_source_copyrightTitle"
                       placeholder="Подпись к ссылке на автора" name="copyrightTitle"
                       value="<?= $source->copyrightTitle; ?>"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control" name="copyrightType">
                    <?php foreach (\Service\Grabber\Model_Config::$copyrightType as $copyrightType): ?>
                        <option value="<?= $copyrightType['id']; ?>"<?php if ($source->copyrightType == $copyrightType['id']): ?> selected="selected"<?php endif; ?>><?= $copyrightType['title']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control" name="copyrightPosition">
                    <?php foreach (\Service\Grabber\Model_Config::$copyrightPosition as $copyrightPosition): ?>
                        <option value="<?= $copyrightPosition['id']; ?>"<?php if ($source->copyrightPosition == $copyrightPosition['id']): ?> selected="selected"<?php endif; ?>><?= $copyrightPosition['title']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <input class="form-control form-span" id="i_source_maxLength" placeholder="Максимальная длина текста"
                       name="maxLength" value="<?= $source->maxLength; ?>"/>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $('#i-settings-nav > li > a').click(function () {
        $('#i-settings-nav > li').removeClass('active');
        $(this).parent().addClass('active');
        $('.c_settings_div').hide();
        $('#' + $(this).data('div')).show();
    });
</script>