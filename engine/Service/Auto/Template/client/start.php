<div style="padding-bottom: 70px;">
    <div class="row text-center">
        <div class="col-sm-12">
            <h2 style="font-family: 'Proxima Nova Rg';font-size: 20pt;color: #39739b;border-bottom: 1px solid #01bcff;padding-bottom: 5px;letter-spacing: 1px;">Автоведение</h2>
            <div style="font-family: 'Proxima Nova Rg';font-size: 12pt;">
                Вы можете сэкономить своё время, доверив нашему сервису самостоятельно отслеживать все новые посты в
                вашем сообществе, и автоматически накручивать на них лайки, репосты и комментарии
            </div>
        </div>
    </div>
    <div style="height: 30px;"></div>
    <div class="row text-center">
        <div class="col-sm-4" style="padding-top: 30px;">
            <img src="/img/main/phnes/1.png" style="width: 100%;" />
        </div>
        <div class="col-sm-4">
            <img src="/img/main/phnes/2.png" style="width: 100%;" />
        </div>
        <div class="col-sm-4" style="padding-top: 30px;">
            <img src="/img/main/phnes/3.png" style="width: 100%;" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-center">
        <button class="button-green" id="i_auto_button_add">Приобрести автоведение</button>
        <?php if ($vars['isFree']): ?>
            <div style="margin-top: 20px;">
                <input type="hidden" id="i-isFree" value="true" />
                <button class="btn btn-default btn-sm" id="i_auto_button_free" onclick="$('#i_dialog_group_add').modal('show');">Пробовать бесплатно</button>
            </div>
            <div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в автоведение</h5>
                        </div>
                        <div class="modal-body" id="i_dialog_group_add_container">
                            <div id="i_dialog_group_add_data"></div>
                            <div id="i_dialog_group_add_error"></div>
                            <div id="i_dialog_group_add_progress" class="progress progress-striped active" style="display: none;">
                                <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-only">&nbsp;</span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer">
                            <button id="i_dialog_group_add_button_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="i_dialog_auto_add_label">Приобрести автоведение</h5>
            </div>
            <div class="modal-body" id="i_dialog_auto_add_container">
                <form action="/orders/order" method="post">
                    <input type="hidden" name="service" value="auto" />
                    <input type="hidden" name="group" value="true" />
                    <h4 class="text-center">Приобрести автоведение</h4>
                    <div id="i_dialog_auto_add_data">
                        <div class="form-group">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                            <div class="col-sm-3">
                                <label><input type="radio" name="month" value="<?= $i; ?>"><?= \Lib_Text::Word4NumberNewReturn($vars['settings']['auto']['months'][$i], ['месяц', 'месяца', 'месяцев']); ?></label>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div id="i_dialog_auto_add_error"></div>
                    <div id="i_dialog_auto_add_progress" class="progress progress-striped active" style="display: none;">
                        <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>

                    <div class="clearfix"></div>
<!--                    <input name="shopId"         value="--><?//= shopId; ?><!--"                 type="hidden" />-->
<!--                    <input name="scid"           value="--><?//= scid; ?><!--"                 type="hidden" />-->
                    <input name="customerNumber" value="<?= $vars['userId']; ?>" type="hidden" />
                    <input id="i-sum" name="sum"            value="0"    type="hidden" />
                    <button id="i-button-buy" type="submit" class="button-green btn-block" style="display: none;">Купить за <span id="i-span-sum"></span> рублей</button>
                </form>
            </div>
            <div class="modal-footer">
                <button id="i_dialog_auto_add_button_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var settings = <?= json_encode($vars['settings']['auto'], JSON_UNESCAPED_UNICODE); ?>;
    $('input[name="month"]').change(function(){
        var val = $('input[name="month"]:checked').val();
        var html = '';
        var profit =  (settings.groups[0] * settings.months[val]) - settings.groups[val];
        if (profit)
        {
            html = '<h3 class="alert alert-success text-center">Выгода: '+profit+' рублей</h3>';
        }
        $('#i_dialog_auto_add_error').html('<div class="alert alert-info"><h2 class="text-center">' + settings.groups[val] + ' рублей</h2>'+html+'</div>');
        $('#i-sum').val(settings.groups[val]);
        $('#i-span-sum').html(settings.groups[val]);
        $('#i-button-buy').show();
    });
    $('#i_auto_button_add').click(function(){
        $('#i_dialog_auto_add').modal();
    });
</script>