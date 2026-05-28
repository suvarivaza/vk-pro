<?php
$settings = $vars['settings'];
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<div class="alert alert-info">
    Ваш баланс: <strong><?= $vars['user']->balanceRef; ?></strong> руб.
    <div>
        Заявка на вывод средств будет исполнена c <strong><?= date('d.m.Y', $vars['fromDate']); ?></strong> по
        <strong><?= date('d.m.Y', $vars['toDate']); ?></strong>
        <br/>на кошелек <strong>+<?= $user->qiwi_prefix; ?> <?= $user->qiwi; ?></strong>
    </div>
</div>
<form id="i-form-balanceRef-out" class="form-horizontal">
    <input type="hidden" name="action" value="out">
    <div class="form-group">
        <div class="col-sm-12">
            <div class="input-group">
                <span class="input-group-addon">Вывести</span>
                <input class="form-control" name="balanceRef" id="i-balanceRef-value" type="number">
                <span class="input-group-addon">рублей</span>
            </div>
        </div>
    </div>
</form>
<div id="i-balanceRef-info" class="alert alert-info"></div>
<script>
    function value_change(e) {
        if (e.keyCode == 9)
            return false;

        if (e.keyCode == 13) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        }

        var balance = parseFloat($('#i-balanceRef-value').val());
        if (balance > 0) {
            var fee = balance * (<?= $settings['out']['fee']; ?> / 100);
            var price = balance + fee;
            $('#i-balanceRef-info').html('<div>Комиссия: <strong>' + number_format(fee) + '</strong> рублей</div>ИТОГО: <strong>' + number_format(price) + '</strong> рублей').show();
        }
    }

    $('#i-balanceRef-value').click(function (e) {
        value_change(e);
    }).change(function (e) {
        value_change(e);
    }).keyup(function (e) {
        value_change(e);
    }).keydown(function (e) {
        value_change(e);
    });
</script>