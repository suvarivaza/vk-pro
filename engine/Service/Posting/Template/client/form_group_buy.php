<h4 class="text-center">Приобрести автопостинг для группы</h4>
<form class="form-horizontal" method="post" action="/orders/order">
    <div id="i_dialog_group_buy_data">
        <input type="hidden" name="service" value="posting"/>
<!--        <input name="shopId" value="--><?//= shopId; ?><!--" type="hidden"/>-->
<!--        <input name="scid" value="--><?//= scid; ?><!--" type="hidden"/>-->
        <input name="customerNumber" value="<?= $vars['userId']; ?>" type="hidden"/>
        <input id="i-sum" name="sum" value="0" type="hidden"/>

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
    <div id="i_dialog_group_buy_error"></div>
    <button id="i-button-buy" type="submit" class="button-green btn-block" style="display: none;">Купить за <span
                id="i-span-sum"></span> рублей
    </button>
</form>
<div class="clearfix"></div>
<script>
    posting.groupBuy = true;
    var settings = <?= json_encode($vars['settings']['posting'], JSON_UNESCAPED_UNICODE); ?>;

    $('input[name="month"]').change(function () {
        var val = $('input[name="month"]:checked').val();
        var html = '';
        var profit = (settings.groups[0] * settings.months[val]) - settings.groups[val];
        if (profit) {
            html = '<h3 class="alert alert-success text-center">Выгода: ' + profit + ' рублей</h3>';
        }
        $('#i_dialog_group_buy_error').html('<div class="alert alert-info"><h2 class="text-center">' + (settings.groups[val]) + ' рублей</h2>' + html + '</div>');
        $('#i-sum').val(settings.groups[val]);
        $('#i-span-sum').html(settings.groups[val]);
        $('#i-button-buy').show();
    });

    $('#i_posting_button_add').click(function () {
        $('#i_dialog_group_buy').modal();
    });
</script>