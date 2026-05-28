<div style="padding-bottom: 70px;">
    <div class="row text-center">
        <div class="col-sm-12">
            <h2 style="font-family: 'Proxima Nova Rg';font-size: 20pt;color: #39739b;border-bottom: 1px solid #01bcff;padding-bottom: 5px;letter-spacing: 1px;">
                Граббер</h2>
            <div class="main-text">
                Мы знаем, что поиск и своевременная публикация контента в сообщество отнимает у администратора много
                сил и времени. Потому, для наших пользователей мы разработали Граббер, способный наполнять вашу группу
                контентом из других
                сообществ в автоматическом режиме
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5"></div>
        <div class="col-sm-2 text-center">
            <h5 style="font-family: 'Proxima Nova Rg';font-weight: bold;color: #5e81a8;font-size: 14pt;margin-top: 40px;margin-bottom: 20px;">
                Источники</h5>
        </div>
        <div class="col-sm-5"></div>
    </div>
    <div class="row text-center">
        <div class="col-sm-2"></div>
        <div class="col-sm-2">
            <img src="/img/main/groups/1.png">
        </div>
        <div class="col-sm-2">
            <img src="/img/main/groups/2.png">
        </div>
        <div class="col-sm-2">
            <img src="/img/main/groups/3.png">
        </div>
        <div class="col-sm-2">
            <img src="/img/main/groups/4.png">
        </div>
        <div class="col-sm-2"></div>
    </div>
    <div class="row text-center">
        <div class="col-sm-2"></div>
        <div class="col-sm-2">
            <h5>Источник 1</h5>
        </div>
        <div class="col-sm-2">
            <h5>Источник 2</h5>
        </div>
        <div class="col-sm-2">
            <h5>Источник 3</h5>
        </div>
        <div class="col-sm-2">
            <h5>Источник 4</h5>
        </div>
        <div class="col-sm-2"></div>
    </div>
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6" style="border-bottom: 1px solid #9bb2cc;">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-sm-6" style="border-right: 1px solid #9bb2cc;height: 40px;">&nbsp;</div>
    </div>
    <div class="row text-center">
        <div class="col-sm-12">
            <div class="main-img-vk-pro"><img src="/img/main/vk-pro.png"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6" style="border-right: 1px solid #9bb2cc;height: 40px;">&nbsp;</div>
    </div>
    <div class="text-center">
        <img src="/img/main/groups/your.png">
        <h5>Ваше сообщество</h5>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-center">
        <button class="button-green" id="i_auto_button_add">Приобрести граббер</button>
        <?php if ($vars['isFree']): ?>
            <div style="margin-top: 20px;">
                <input type="hidden" id="i-isFree" value="true"/>
                <button class="btn btn-default btn-sm" id="i_auto_button_free"
                        onclick="$('#i_dialog_group_add').modal('show');">Пробовать бесплатно
                </button>
            </div>
            <div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog"
                 aria-labelledby="i_dialog_group_add">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в граббер</h5>
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
        <?php endif; ?>
    </div>
</div>
<div style="padding: 70px 0;">&nbsp;</div>
<div class="modal fade" id="i_dialog_auto_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_auto_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_auto_add_label">Приобрести граббер</h5>
            </div>
            <div class="modal-body" id="i_dialog_auto_add_container">
                <form action="/orders/order" method="post">
                    <input type="hidden" name="service" value="grabber"/>
                    <input type="hidden" name="group" value="true"/>
                    <h4 class="text-center">Приобрести граббер</h4>
                    <div id="i_dialog_auto_add_data">
                        <div class="form-group">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div class="col-sm-3">
                                    <label><input type="radio" name="month"
                                                  value="<?= $i; ?>"><?= \Lib_Text::Word4NumberNewReturn($vars['settings']['posting']['months'][$i],
                                            ['месяц', 'месяца', 'месяцев']); ?></label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div id="i_dialog_auto_add_error"></div>
                    <div id="i_dialog_auto_add_progress" class="progress progress-striped active"
                         style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                             aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
<!--                    <input name="shopId" value="--><?//= shopId; ?><!--" type="hidden"/>-->
<!--                    <input name="scid" value="--><?//= scid; ?><!--" type="hidden"/>-->
                    <input name="customerNumber" value="<?= $vars['userId']; ?>" type="hidden"/>
                    <input id="i-sum" name="sum" value="0" type="hidden"/>
                    <button id="i-button-buy" type="submit" class="button-green btn-block" style="display: none;">Купить
                        за <span id="i-span-sum"></span> рублей
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button id="i_dialog_auto_add_button_cancel" type="button" class="btn btn-default" data-dismiss="modal">
                    Отмена
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var settings = <?= json_encode($vars['settings']['grabber'], JSON_UNESCAPED_UNICODE); ?>;
    $('input[name="month"]').change(function () {
        var val = $('input[name="month"]:checked').val();

        var html = '';
        var profit = (settings.groups[0] * settings.months[val]) - settings.groups[val];
        if (profit) {
            html = '<h3 class="alert alert-success text-center">Выгода: ' + profit + ' рублей</h3>';
        }
        $('#i_dialog_auto_add_error').html('<div class="alert alert-info"><h2 class="text-center">' + settings.groups[val] + ' рублей</h2>' + html + '</div>');

        $('#i-sum').val(settings.groups[val]);
        $('#i-span-sum').html(settings.groups[val]);
        $('#i-button-buy').show();
    });
    $('#i_auto_button_add').click(function () {
        $('#i_dialog_auto_add').modal();
    });
</script>