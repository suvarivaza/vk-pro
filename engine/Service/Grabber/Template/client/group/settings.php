<?php
/** @var \Service\Grabber\Model_Groups_Group $group */
$group = $vars['group'];
?>
<ul id="i-group-settings-nav" class="nav nav-pills nav-justified">
    <li role="presentation" class="active">
        <a href="javascript:void(0);" id="i_group_settings_general_link"
           data-div="i_group_settings_general">Основные</a>
    </li>
    <li role="presentation">
        <a href="javascript:void(0);" id="i_group_settings_adv_link" data-div="i_group_settings_adv">Дополнительно</a>
    </li>
    <li role="presentation">
        <a href="javascript:void(0);" id="i_group_settings_watermark_link" data-div="i_group_settings_watermark">Водяной
            знак</a>
    </li>
</ul>
<br/>
<form class="form-horizontal" method="post" id="i_group_settings_form">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input type="hidden" name="groupId" value="<?= $group->groupId; ?>"/>
    <div id="i_group_settings_general" class="c_group_settings_div">
        <div class="form-group">
            <div class="col-sm-12">
                <textarea class="form-control form-span" id="i_group_hashtags"
                          placeholder="Хэштеги или подпись к постам" name="hashtags"
                          5><?= $group->hashtags; ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-6">
                Местоположение подписи
            </label>
            <div class="col-sm-6">
                <select class="form-control" name="hashtagsPos">
                    <option value="0"<?php if ($group->hashtagsPos == 0): ?> selected="selected"<?php endif; ?>>В конце
                        поста
                    </option>
                    <option value="1"<?php if ($group->hashtagsPos == 1): ?> selected="selected"<?php endif; ?>>В начале
                        поста
                    </option>
                </select>
            </div>
        </div>
        <div id="i-group-timeLimits">
            <div class="form-group">
                <label class="control-label col-sm-4">
                    <input type="checkbox" name="timeLimit" id="i-group-timeLimit"
                           value="1" <?php if ($group->timeLimit): ?> checked="checked"<?php endif; ?> />
                    Не публиковать
                </label>
                <div class="col-sm-4">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%;">
                                <select id="i-group-timeHourFrom" name="timeHourFrom" class="form-control">
                                    <?php for ($i = 0; $i < 24; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                        <option value="<?= $val; ?>"<?php if ($group->timeHourFrom == $val): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td>:</td>
                            <td style="width: 50%;">
                                <select id="i-group-timeMinuteFrom" name="timeMinuteFrom" class="form-control">
                                    <?php for ($i = 0; $i < 60; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                        <option value="<?= $val; ?>"<?php if ($group->timeMinuteFrom == $val): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-4">
                    <table style="width: 100%;">
                        <tr>
                            <td>по</td>
                            <td style="width: 50%;">
                                <select id="i-group-timeHourTo" name="timeHourTo" class="form-control">
                                    <?php for ($i = 0; $i < 24; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                        <option value="<?= $val; ?>"<?php if ($group->timeHourTo == $val): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td>:</td>
                            <td style="width: 50%;">
                                <select id="i-group-timeMinuteTo" name="timeMinuteTo" class="form-control">
                                    <?php for ($i = 0; $i < 60; $i++): $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                        <option value="<?= $val; ?>"<?php if ($group->timeMinuteTo == $val): ?> selected="selected"<?php endif; ?>><?= $val; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-8">
                Интервал между постами
            </label>
            <div class="col-sm-4">
                <select class="form-control" name="interval">
                    <option value="15"<?php if ($group->interval == 15): ?> selected="selected"<?php endif; ?>>15
                        минут
                    </option>
                    <option value="30"<?php if ($group->interval == 30): ?> selected="selected"<?php endif; ?>>30
                        минут
                    </option>
                    <option value="45"<?php if ($group->interval == 45): ?> selected="selected"<?php endif; ?>>45
                        минут
                    </option>
                    <option value="60"<?php if ($group->interval == 60): ?> selected="selected"<?php endif; ?>>час
                    </option>
                    <option value="90"<?php if ($group->interval == 90): ?> selected="selected"<?php endif; ?>>1 час 30
                        минут
                    </option>
                    <option value="120"<?php if ($group->interval == 120): ?> selected="selected"<?php endif; ?>>2
                        часа
                    </option>
                    <option value="180"<?php if ($group->interval == 180): ?> selected="selected"<?php endif; ?>>2 часа
                        30 минут
                    </option>
                    <option value="240"<?php if ($group->interval == 240): ?> selected="selected"<?php endif; ?>>3
                        часа
                    </option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-10 text-right">
                Публиковать случайным образом из очереди
            </div>
            <div class="col-sm-2">
                <div class="material-switch">
                    <input id="i-specialId" type="checkbox" name="random"
                           value="1"<?php if ($group->random): ?> checked="checked"<?php endif; ?> />
                    <label for="i-specialId" class="label-primary">
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-8">
                Максимальное количество отложенных
            </label>
            <div class="col-sm-4">
                <select class="form-control" name="maxLength">
                    <option value="25"<?php if ($group->maxLength == 25): ?> selected="selected"<?php endif; ?>>25
                    </option>
                    <option value="50"<?php if ($group->maxLength == 50): ?> selected="selected"<?php endif; ?>>50
                    </option>
                    <option value="75"<?php if ($group->maxLength == 75): ?> selected="selected"<?php endif; ?>>75
                    </option>
                    <option value="100"<?php if ($group->maxLength == 100): ?> selected="selected"<?php endif; ?>>100
                    </option>
                    <option value="150"<?php if ($group->maxLength == 150): ?> selected="selected"<?php endif; ?>>150
                    </option>
                    <option value="200"<?php if ($group->maxLength == 200): ?> selected="selected"<?php endif; ?>>200
                    </option>
                    <option value="250"<?php if ($group->maxLength == 250): ?> selected="selected"<?php endif; ?>>250
                    </option>
                </select>
            </div>
        </div>
    </div>
    <div id="i_group_settings_adv" class="c_group_settings_div" style="display: none;">
        <div class="form-group">
            <label class="control-label col-sm-7" style="text-align: left;">
                <input type="checkbox" name="photoInGroup"/>
                Загружать фото в альбом группы
            </label>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-7" style="text-align: left;">
                <input type="checkbox" name="adsLimit"
                       value="1" <?php if ($group->adsLimit): ?> checked="checked"<?php endif; ?> />
                Отложить постинг после рекламного поста
            </label>
            <div class="col-sm-5">
                <input class="form-control" name="adsInterval" value="<?= $group->adsInterval; ?>"/>
            </div>
        </div>
    </div>
    <input type="hidden" id="i-watermarkPos" name="watermarkPos" value="<?= $group->watermarkPos; ?>"/>
    <input type="hidden" id="i-watermarkOpacity" name="watermarkOpacity" value="<?= $group->watermarkOpacity; ?>"/>
    <input type="hidden" id="i-watermarkMaxSize" name="watermarkMaxSize" value="<?= $group->watermarkMaxSize; ?>"/>

    <input type="hidden" id="i-watermarkText" name="watermarkText" value="<?= $group->watermarkText; ?>"/>
    <input type="hidden" id="i-watermarkSize" name="watermarkSize" value="<?= $group->watermarkSize; ?>"/>
    <input type="hidden" id="i-watermarkFont" name="watermarkFont" value="<?= $group->watermarkFont; ?>"/>
    <input type="hidden" id="i-watermarkColor" name="watermarkColor" value="<?= $group->watermarkColor; ?>"/>
    <input type="hidden" id="i-watermarkTextPos" name="watermarkTextPos" value="<?= $group->watermarkTextPos; ?>"/>
    <input type="hidden" id="i-watermarkTextOpacity" name="watermarkTextOpacity"
           value="<?= $group->watermarkTextOpacity; ?>"/>

    <input type="checkbox" id="i-isWatermark-2" name="isWatermark[]" value="2"
           style="display: none;" <?php if ($group->isWatermark & 2): ?> checked="checked"<?php endif; ?> />
    <input type="checkbox" id="i-isWatermark-4" name="isWatermark[]" value="4"
           style="display: none;" <?php if ($group->isWatermark & 4): ?> checked="checked"<?php endif; ?> />
</form>
<div id="i_group_settings_watermark" class="c_group_settings_div" style="display: none;">
    <div class="c_group_photo">
        <div id="i_div_watermark"
             style="max-width: <?= $group->watermarkMaxSize; ?>%;<?php if (!($group->isWatermark & 2)): ?> display: none;<?php endif; ?>">
            <img src="<?= '/img/grabber/watermark/' . $group->watermark; ?>" style="width: 100%;"/>
        </div>
        <div id="i_div_watermark_text" style="
                color: <?= $group->watermarkColor; ?>;
                font-family: '<?= $group->watermarkFont; ?>';
                font-size: <?= $group->watermarkSize; ?>px;
                opacity: <?= 1 - $group->watermarkTextOpacity; ?>;
        <?php if (!($group->isWatermark & 4)): ?> display: none;<?php endif; ?>"
        ><?= $group->watermarkText; ?></div>
        <img id="i-examples-img" src="/img/grabber/examples/1.jpg" width="100%"/>
    </div>
    <div class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-6" style="text-align: left;">
                <input type="checkbox" name="isWatermark" id="i-isWatermark2"
                       value="2"<?php if ($group->isWatermark & 2): ?> checked="checked"<?php endif; ?> />
                Наложить картинку
            </label>
            <label class="control-label col-sm-6" style="text-align: left;">
                <input type="checkbox" name="isWatermark" id="i-isWatermark4"
                       value="4"<?php if ($group->isWatermark & 4): ?> checked="checked"<?php endif; ?> />
                Наложить текст
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6" id="i-watermarkImage">
            <form class="form-horizontal" method="post" id="i_settings_watermark_form" enctype="multipart/form-data">
                <div class="c-watermark-image">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="file" name="watermark" id="i_settings_watermark_file" style="display: none;"/>
                            <button type="button" onclick="$('#i_settings_watermark_file').trigger('click');"
                                    class="btn btn-primary btn-block" id="i_settings_watermark_submit">Загрузить
                            </button>
                        </div>
                    </div>
                    <div id="i_settings_watermark_result"></div>
                </div>
            </form>
            <div class="c-watermark-image">
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <select class="form-control form-span c-watermark-param"
                                    placeholder="Позиция водяного знака" id="i-watermark-param-watermarkPos"
                                    name="watermarkPos">
                                <option value="0"<?php if ($group->watermarkPos == 0): ?> selected="selected"<?php endif; ?>>
                                    По центру
                                </option>
                                <option value="1"<?php if ($group->watermarkPos == 1): ?> selected="selected"<?php endif; ?>>
                                    Нижний правый угол
                                </option>
                                <option value="2"<?php if ($group->watermarkPos == 2): ?> selected="selected"<?php endif; ?>>
                                    Нижний левый угол
                                </option>
                                <option value="3"<?php if ($group->watermarkPos == 3): ?> selected="selected"<?php endif; ?>>
                                    Верхний левый угол
                                </option>
                                <option value="4"<?php if ($group->watermarkPos == 4): ?> selected="selected"<?php endif; ?>>
                                    Верхний правый угол
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <select class="form-control form-span c-watermark-param" placeholder="Уровень прозрачности"
                                    id="i-watermark-param-watermarkOpacity" name="watermarkOpacity">
                                <option value="0"<?php if ($group->watermarkOpacity == 0): ?> selected="selected"<?php endif; ?>>
                                    Не прозрачный
                                </option>
                                <option value="0.1"<?php if ($group->watermarkOpacity == 0.1): ?> selected="selected"<?php endif; ?>>
                                    90%
                                </option>
                                <option value="0.2"<?php if ($group->watermarkOpacity == 0.2): ?> selected="selected"<?php endif; ?>>
                                    80%
                                </option>
                                <option value="0.3"<?php if ($group->watermarkOpacity == 0.3): ?> selected="selected"<?php endif; ?>>
                                    70%
                                </option>
                                <option value="0.4"<?php if ($group->watermarkOpacity == 0.4): ?> selected="selected"<?php endif; ?>>
                                    60%
                                </option>
                                <option value="0.5"<?php if ($group->watermarkOpacity == 0.5): ?> selected="selected"<?php endif; ?>>
                                    50%
                                </option>
                                <option value="0.6"<?php if ($group->watermarkOpacity == 0.6): ?> selected="selected"<?php endif; ?>>
                                    40%
                                </option>
                                <option value="0.7"<?php if ($group->watermarkOpacity == 0.7): ?> selected="selected"<?php endif; ?>>
                                    30%
                                </option>
                                <option value="0.8"<?php if ($group->watermarkOpacity == 0.8): ?> selected="selected"<?php endif; ?>>
                                    20%
                                </option>
                                <option value="0.9"<?php if ($group->watermarkOpacity == 0.9): ?> selected="selected"<?php endif; ?>>
                                    10%
                                </option>
                                <option value="1"<?php if ($group->watermarkOpacity == 1): ?> selected="selected"<?php endif; ?>>
                                    Прозрачный
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <select class="form-control form-span c-watermark-param" placeholder="Максимальная ширина"
                                    id="i-watermark-param-watermarkMaxSize" name="watermarkMaxSize">
                                <option value="10"<?php if ($group->watermarkMaxSize == 10): ?> selected="selected"<?php endif; ?>>
                                    10%
                                </option>
                                <option value="20"<?php if ($group->watermarkMaxSize == 20): ?> selected="selected"<?php endif; ?>>
                                    20%
                                </option>
                                <option value="30"<?php if ($group->watermarkMaxSize == 30): ?> selected="selected"<?php endif; ?>>
                                    30%
                                </option>
                                <option value="40"<?php if ($group->watermarkMaxSize == 40): ?> selected="selected"<?php endif; ?>>
                                    40%
                                </option>
                                <option value="50"<?php if ($group->watermarkMaxSize == 50): ?> selected="selected"<?php endif; ?>>
                                    50%
                                </option>
                                <option value="60"<?php if ($group->watermarkMaxSize == 60): ?> selected="selected"<?php endif; ?>>
                                    60%
                                </option>
                                <option value="70"<?php if ($group->watermarkMaxSize == 70): ?> selected="selected"<?php endif; ?>>
                                    70%
                                </option>
                                <option value="80"<?php if ($group->watermarkMaxSize == 80): ?> selected="selected"<?php endif; ?>>
                                    80%
                                </option>
                                <option value="90"<?php if ($group->watermarkMaxSize == 90): ?> selected="selected"<?php endif; ?>>
                                    90%
                                </option>
                                <option value="100"<?php if ($group->watermarkMaxSize == 100): ?> selected="selected"<?php endif; ?>>
                                    100%
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-sm-6" id="i-watermarkText">
            <div class="form-horizontal">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input class="form-control form-span c-watermark-param" placeholder="Укажите текст"
                               id="i-watermark-param-watermarkText" name="watermarkText"
                               value="<?= $group->watermarkText; ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div id="cp2" class="input-group">
                            <input class="form-control c-watermark-param" id="i-watermark-param-watermarkColor"
                                   name="watermarkColor" value="<?= $group->watermarkColor ?: '#ffffff;'; ?>"/>
                            <span class="input-group-addon"><i></i></span>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <select class="form-control form-span c-watermark-param" id="i-watermark-param-watermarkFont"
                                name="watermarkFont" placeholder="Шрифт">
                            <?php foreach ($vars['fonts'] as $font) : ?>
                                <option style="font-family: <?= $font; ?>; font-size: 14px;"
                                        value="<?= $font; ?>"<?php if ($font == $group->watermarkFont): ?> selected="selected"<?php endif; ?>><?= $font; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <select class="form-control form-span c-watermark-param" id="i-watermark-param-watermarkSize"
                                name="watermarkSize" placeholder="Размер шрифта">
                            <?php foreach ($vars['sizes'] as $size): ?>
                                <option value="<?= $size; ?>"<?php if ($size == $group->watermarkSize || (!$group->watermarkSize && $size == 32)): ?> selected="selected"<?php endif; ?>><?= $size; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <select class="form-control form-span c-watermark-param" placeholder="Позиция текста"
                                id="i-watermark-param-watermarkTextPos" name="watermarkTextPos">
                            <option value="0"<?php if ($group->watermarkTextPos == 0): ?> selected="selected"<?php endif; ?>>
                                По центру
                            </option>
                            <option value="1"<?php if ($group->watermarkTextPos == 1): ?> selected="selected"<?php endif; ?>>
                                Нижний правый угол
                            </option>
                            <option value="2"<?php if ($group->watermarkTextPos == 2): ?> selected="selected"<?php endif; ?>>
                                Нижний левый угол
                            </option>
                            <option value="3"<?php if ($group->watermarkTextPos == 3): ?> selected="selected"<?php endif; ?>>
                                Верхний левый угол
                            </option>
                            <option value="4"<?php if ($group->watermarkTextPos == 4): ?> selected="selected"<?php endif; ?>>
                                Верхний правый угол
                            </option>
                            <option value="5"<?php if ($group->watermarkTextPos == 5): ?> selected="selected"<?php endif; ?>>
                                Верх центр
                            </option>
                            <option value="6"<?php if ($group->watermarkTextPos == 6): ?> selected="selected"<?php endif; ?>>
                                Лево центр
                            </option>
                            <option value="7"<?php if ($group->watermarkTextPos == 7): ?> selected="selected"<?php endif; ?>>
                                Право центр
                            </option>
                            <option value="8"<?php if ($group->watermarkTextPos == 8): ?> selected="selected"<?php endif; ?>>
                                Низ центр
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <select class="form-control form-span c-watermark-param" placeholder="Уровень прозрачности"
                                id="i-watermark-param-watermarkTextOpacity" name="watermarkTextOpacity">
                            <option value="0"<?php if ($group->watermarkTextOpacity == 0): ?> selected="selected"<?php endif; ?>>
                                Не прозрачный
                            </option>
                            <option value="0.1"<?php if ($group->watermarkTextOpacity == 0.1): ?> selected="selected"<?php endif; ?>>
                                90%
                            </option>
                            <option value="0.2"<?php if ($group->watermarkTextOpacity == 0.2): ?> selected="selected"<?php endif; ?>>
                                80%
                            </option>
                            <option value="0.3"<?php if ($group->watermarkTextOpacity == 0.3): ?> selected="selected"<?php endif; ?>>
                                70%
                            </option>
                            <option value="0.4"<?php if ($group->watermarkTextOpacity == 0.4): ?> selected="selected"<?php endif; ?>>
                                60%
                            </option>
                            <option value="0.5"<?php if ($group->watermarkTextOpacity == 0.5): ?> selected="selected"<?php endif; ?>>
                                50%
                            </option>
                            <option value="0.6"<?php if ($group->watermarkTextOpacity == 0.6): ?> selected="selected"<?php endif; ?>>
                                40%
                            </option>
                            <option value="0.7"<?php if ($group->watermarkTextOpacity == 0.7): ?> selected="selected"<?php endif; ?>>
                                30%
                            </option>
                            <option value="0.8"<?php if ($group->watermarkTextOpacity == 0.8): ?> selected="selected"<?php endif; ?>>
                                20%
                            </option>
                            <option value="0.9"<?php if ($group->watermarkTextOpacity == 0.9): ?> selected="selected"<?php endif; ?>>
                                10%
                            </option>
                            <option value="1"<?php if ($group->watermarkTextOpacity == 1): ?> selected="selected"<?php endif; ?>>
                                Прозрачный
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if ($group->watermarkOpacity): ?>
    $('#i_div_watermark').css({opacity: <?= 1 - $group->watermarkOpacity; ?>});
    <?php endif; ?>

    $('#i_div_watermark').css({'max-width': '<?= $group->watermarkMaxSize; ?>%'});

    <?php if ($group->watermarkPos == 0): ?>

    var w = $('#i_div_watermark').width();
    var h = $('#i_div_watermark').height();
    $('#i_div_watermark').css({
        position: 'absolute',
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        'margin-top': '-' + (h / 2) + 'px',
        'margin-left': '-' + (w / 2) + 'px'
    });

    <?php elseif ($group->watermarkPos == 1): ?>
    $('#i_div_watermark').css({
        position: 'absolute',
        top: 'auto',
        left: 'auto',
        right: 0,
        bottom: 0,
        'margin-top': 0,
        'margin-left': 0
    });
    <?php elseif ($group->watermarkPos == 2): ?>
    $('#i_div_watermark').css({
        position: 'absolute',
        top: 'auto',
        left: 0,
        right: 'auto',
        bottom: 0,
        'margin-top': 0,
        'margin-left': 0
    });
    <?php elseif ($group->watermarkPos == 3): ?>
    $('#i_div_watermark').css({
        position: 'absolute',
        top: 0,
        left: 0,
        right: 'auto',
        bottom: 'auto',
        'margin-top': 0,
        'margin-left': 0
    });
    <?php elseif ($group->watermarkPos == 4): ?>
    $('#i_div_watermark').css({
        position: 'absolute',
        top: 0,
        left: 'auto',
        right: 0,
        bottom: 'auto',
        'margin-top': 0,
        'margin-left': 0
    });
    <?php endif; ?>

    $('#i-group-settings-nav > li > a').click(function () {
        $('#i-group-settings-nav > li').removeClass('active');
        $(this).parent().addClass('active');
        $('.c_group_settings_div').hide();
        $('#' + $(this).data('div')).show();
    });
    grabber_watermark.init();
    refreshLabels();

    $(function () {
        $('#cp2').colorpicker({
            format: 'hex'
        }).on('changeColor', function (e, a) {
            $('#i-watermarkColor').val(e.color);
            grabber_watermark.setWatermarkImage();
        });
    });

</script>