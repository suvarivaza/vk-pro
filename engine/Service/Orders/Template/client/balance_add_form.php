<?php
$settings = $vars['settings'];
?>
<form id="i_form_pack_select" class="form-horizontal" action="/orders/order" method="post">
    <div class="form-group">
        <div class="col-sm-12">
            <div class="input-group">
                <span class="input-group-addon">Приобрести</span>
                <input class="form-control" name="balance" id="i_balance_add_value"/>
                <span class="input-group-addon">баллов</span>
            </div>
        </div>
    </div>
<!--    <input name="shopId" value="--><?//= shopId; ?><!--" type="hidden"/>-->
<!--    <input name="scid" value="--><?//= scid; ?><!--" type="hidden"/>-->
    <input name="customerNumber" value="<?= $vars['userId']; ?>" type="hidden"/>
    <input id="i-sum" name="sum" value="<?= $pack->price; ?>" type="hidden"/>
    <div id="i_balance_add_alert" class="alert alert-danger" style="display: none;"></div>
    <div id="i_balance_add_info" class="alert alert-info" style="display: none;"></div>
    <button type="submit" class="btn btn-primary btn-block">Купить за <span id="i-span-sum"></span> </button>
</form>
<script>
    function value_change(e) {
        if (e.keyCode == 9)
            return false;

        if (e.keyCode == 13) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        }

        var balance = $('#i_balance_add_value').val();
        if (balance > 2000000) {
            $('#i_balance_add_value').val('2000000');
            balance = 2000000;
        }

        var price = (balance / 10) * <?= $settings['balance']['price']; ?>;
        $('#i-sum').val(price);
        $('#i-span-sum').html('<strong>' + number_format(price, 0, ',', ' ') + '</strong> рублей');
        $('#i_balance_add_info').html('Стоимость: <strong>' + number_format(price, 0, ',', ' ') + '</strong> рублей. <br />Для покупки более <strong>2 000</strong> баллов рекомендуем приобретать пакеты т.к. в этом случае Вы получите дополнительные бонусные баллы.').show();
    }

    $('#i_balance_add_value').click(function (e) {
        value_change(e);
    }).change(function (e) {
        value_change(e);
    }).keyup(function (e) {
        value_change(e);
    }).keydown(function (e) {
        value_change(e);
    });
</script>