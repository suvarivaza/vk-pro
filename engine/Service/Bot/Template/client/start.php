<div style="padding-bottom: 70px;">
    <div class="row text-center">
        <div class="col-sm-12">
            <h2 style="font-family: 'Proxima Nova Rg';font-size: 20pt;color: #39739b;border-bottom: 1px solid #01bcff;padding-bottom: 5px;letter-spacing: 1px;">Автобот</h2>
            <div class="alert alert-success">
                <h3 class="text-center">Не хотите тратить время на выполнение заданий?</h3>
                <h5 class="text-center">Включите автобота, и он всё сделает за Вас</h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <h1 class="text-center">FREE</h1>
            <ul class="c-bot-list">
                <li class="icon-check">До <strong>300</strong> баллов в день</li>
                <li class="icon-close"><strong>Нельзя</strong> выбрать типы заданий</li>
                <li class="icon-close">Останавливает работу при минусовой карме и балансе</li>
                <li class="icon-close">Нельзя просмотреть выполненные задания</li>
            </ul>
        </div>
        <div class="col-sm-4" style="padding-top: 50px;">
            <img src="/img/autobot.png" style="max-width: 100%;" />
        </div>
        <div class="col-sm-4">
            <h1 class="text-center">PRO</h1>
            <ul class="c-bot-list pro">
                <li class="icon-check">До <strong>1000</strong> баллов в день</li>
                <li class="icon-check"><strong>Можно</strong> указать типы выполняемых заданий</li>
                <li class="icon-check">Работает при любых балансах и карме</li>
                <li class="icon-check">Ежедневный отчет о проделанной работе</li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 text-center">
            <form method="post">
                <input type="hidden" name="action" value="free" />
                <button class="btn btn-primary btn-lg">Подключить FREE</button>
            </form>
        </div>
        <div class="col-sm-4 col-sm-offset-4 text-center">
            <button class="btn btn-success btn-lg" id="i_auto_button_add">Приобрести PRO</button>
        </div>
    </div>
</div>
<div style="padding: 70px 0;">&nbsp;</div>
<div class="modal fade" id="i_dialog_auto_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_auto_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="i_dialog_auto_add_label">Приобрести автобот PRO</h5>
            </div>
            <div class="modal-body" id="i_dialog_auto_add_container">
                <form action="/orders/order" method="post">
                    <input type="hidden" name="service" value="bot" />
                    <div id="i_dialog_auto_add_data">
                        <div class="form-group">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div class="col-sm-3">
                                    <label><input type="radio" name="month" value="<?= $i; ?>"><?= \Lib_Text::Word4NumberNewReturn($vars['settings']['bot']['months'][$i], ['месяц', 'месяца', 'месяцев']); ?></label>
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
<!--                    <input name="shopId"         value="--><?//= shopId; ?><!--"          type="hidden" />-->
<!--                    <input name="scid"           value="--><?//= scid; ?><!--"            type="hidden" />-->
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
    var settings = <?= json_encode($vars['settings']['bot'], JSON_UNESCAPED_UNICODE); ?>;
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