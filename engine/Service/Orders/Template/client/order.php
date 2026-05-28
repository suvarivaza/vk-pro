<?php
if (isset($vars['errors']) and count($vars['errors'])): ?>
    <div class="alert alert-danger"><?= implode('<br />', $vars['errors']); ?></div>
<?php else: ?>
    <?php /** @var \Service\Orders\Model_Orders_Order $order */
    $order = $vars['order']; ?>
    <?php
    /** @var \Service\Orders\Model_Orders_Order $order */
    $order = $vars['order'];
    /** @var \Service\Users\Model_Users_User $user */
    $user = $vars['user'];
    /** @var \Service\Orders\Model_Orders_Packs_Pack $pack */
    $pack = $vars['pack'];
    ?>
    <h2>Подтверждение платежа на сумму <?= $order->price; ?> рублей</h2>
    <?php if ($pack && $vars['first']): ?>
        <div class="alert alert-info">
            <h5>Вам доступен <strong>Бонус Первой Покупки</strong>, он позволяет получить двойное количество баллов,
                имеющихся в пакете.</h5>
            <h5>Этот бонус доступен только <strong>один раз</strong> и только на первую покупку.</h5>
            <h4>Вы выбрали пакет <strong><?= $pack->title; ?></strong>.</h4>
            <h4>Стандартное количество баллов в пакете – <strong><?= number_format($pack->balance, 0, '',
                        ' '); ?></strong>.</h4>
            <h4>С Бонусом Первой Покупки, Вы получите <strong><?= number_format($pack->balance + $pack->balance, 0, '',
                        ' '); ?></strong> баллов.</h4>
            <div class="text-center">
                <a class="button-green" href="/orders/buy">Выбрать другой пакет</a>
            </div>
        </div>
    <?php endif; ?>
    <div class="alert alert-success">
        <?php if ($order->type == 'karmaMinus'): ?>
            Ваша карма будет очищена.
        <?php else: ?>
            <h3 class="text-center">Вам будет начислено <strong><?= number_format($order->balance, 0, '',
                        ' '); ?></strong> <?= \Lib_Text::Word4NumberClear($order->balance,
                    ['балл', 'балла', 'баллов']); ?></h3>

            <?php if ($order->isAuto || $order->isPosting || $order->isGrabber || $order->isSpecial || $order->isBot): ?>
                <?php if ($order->isAuto): ?>
                    <h3 class="text-center"><strong>Автоведение</strong>, сроком
                        на <?= \Lib_Text::Word4NumberNewReturn($order->isAutoMonth, ['месяц', 'месяца', 'месяцев']); ?>
                    </h3>
                <?php endif; ?>
                <?php if ($order->isPosting): ?>
                    <h3 class="text-center"><strong>Автопостинг</strong>, сроком
                        на <?= \Lib_Text::Word4NumberNewReturn($order->isPostingMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></h3>
                <?php endif; ?>
                <?php if ($order->isGrabber): ?>
                    <h3 class="text-center"><strong>Граббер</strong>, сроком
                        на <?= \Lib_Text::Word4NumberNewReturn($order->isGrabberMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></h3>
                <?php endif; ?>
                <?php if ($order->isSpecial): ?>
                    <h3 class="text-center"><strong>Спецзадания</strong>, сроком
                        на <?= \Lib_Text::Word4NumberNewReturn($order->isSpecialMonth,
                            ['месяц', 'месяца', 'месяцев']); ?></h3>
                <?php endif; ?>
                <?php if ($order->isBot): ?>
                    <h3 class="text-center"><strong>Автобот PRO</strong>, сроком
                        на <?= \Lib_Text::Word4NumberNewReturn($order->isBotMonth, ['месяц', 'месяца', 'месяцев']); ?>
                    </h3>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php //echo '<pre>'; print_r($order); ?>
<h3>Варианты оплаты</h3>
<div class="row">

    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="yoo_money" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить через YooMoney."
                    data-original-title="Оплата YooMoney"
            >
                <img src="/img/icons/yandex_kassa.png" style="width: 100%; margin-top: -10px;"/>
            </button>
        </form>
    </div>

    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="bank_card" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить банковской картой."
                    data-original-title="Оплата картами"
            >
                <img src="/img/icons/visa.png" style="width: 100%;"/>
            </button>
        </form>
    </div>

    <!--
    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="qiwi" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить через Qiwi."
                    data-original-title="Оплата Qiwi"
            >
                <img src="/img/icons/qiwi.png" style="width: 100%;"/>
            </button>
        </form>
    </div>
    -->

<!--    <div class="col-sm-3">-->
<!--        <form id="kassa-form" action="/orders/pay" method="POST">-->
<!--            <input name="payData[typePay]" value="webmoney" type="hidden"/>-->
<!--            <input name="payData[orderTitle]" value="--><?//= $vars['title'] ?><!--" type="hidden"/>-->
<!--            <input name="payData[sum]" value="--><?//= $order->price; ?><!--" type="hidden"/>-->
<!--            <input name="payData[userId]" value="--><?//= $user->userId; ?><!--" type="hidden"/>-->
<!---->
<!--            --><?php //foreach ($vars['arr'] as $name => $val) : ?>
<!--                <input type="hidden" name="payData[--><?//= $name; ?><!--]" value="--><?//= $val; ?><!--"/>-->
<!--            --><?php //endforeach; ?>
<!---->
<!--            <button class="btn btn-default"-->
<!--                    type="submit"-->
<!--                    style="height: 90px;"-->
<!--                    data-toggle="popover"-->
<!--                    data-placement="top"-->
<!--                    data-trigger="hover"-->
<!--                    data-content="Выберите если Вы хотите оплатить через WebMoney."-->
<!--                    data-original-title="Оплата WebMoney"-->
<!--            >-->
<!--                <img src="/img/icons/WebMoney.jpg" style="width: 100%;"/>-->
<!--            </button>-->
<!--        </form>-->
<!--    </div>-->

</div>

<div class="row" style="margin-top:10px">
    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="sberbank" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить через Сбербанк Онлайн."
                    data-original-title="Оплата Сбербанк Онлайн"
            >
                <img src="/img/icons/sberbank-onlayn.png" style="width: 100%;"/>
            </button>
        </form>
    </div>

    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="alfabank" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить через Альфа банк."
                    data-original-title="Оплата Альфа банк"
            >
                <img src="/img/icons/alphaclik.png" style="width: 100%;"/>
            </button>
        </form>
    </div>

    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="tinkoff_bank" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить через Тинькофф банк."
                    data-original-title="Оплата Тинькофф банк"
            >
                <img src="/img/icons/tinkoff-bank.png" style="width: 100%;"/>
            </button>
        </form>
    </div>

<!--    <div class="col-sm-3">-->
<!--        <form id="kassa-form" action="/orders/pay" method="POST">-->
<!--            <input name="payData[typePay]" value="apple_pay" type="hidden"/>-->
<!--            <input name="payData[orderTitle]" value="--><?//= $vars['title'] ?><!--" type="hidden"/>-->
<!--            <input name="payData[sum]" value="--><?//= $order->price; ?><!--" type="hidden"/>-->
<!--            <input name="payData[userId]" value="--><?//= $user->userId; ?><!--" type="hidden"/>-->
<!---->
<!--            --><?php //foreach ($vars['arr'] as $name => $val) : ?>
<!--                <input type="hidden" name="payData[--><?//= $name; ?><!--]" value="--><?//= $val; ?><!--"/>-->
<!--            --><?php //endforeach; ?>
<!---->
<!--            <button class="btn btn-default"-->
<!--                    type="submit"-->
<!--                    style="height: 90px;"-->
<!--                    data-toggle="popover"-->
<!--                    data-placement="top"-->
<!--                    data-trigger="hover"-->
<!--                    data-content="Выберите если Вы хотите оплатить через Apple Pay."-->
<!--                    data-original-title="Оплата Apple Pay"-->
<!--            >-->
<!--                <img src="/img/icons/apple-pay.png" style="width: 100%;"/>-->
<!--            </button>-->
<!--        </form>-->
<!--    </div>-->
</div>

<div class="row" style="margin-top:10px">

<!--    <div class="col-sm-3">-->
<!--        <form id="kassa-form" action="/orders/pay" method="POST">-->
<!--            <input name="payData[typePay]" value="google_pay" type="hidden"/>-->
<!--            <input name="payData[orderTitle]" value="--><?//= $vars['title'] ?><!--" type="hidden"/>-->
<!--            <input name="payData[sum]" value="--><?//= $order->price; ?><!--" type="hidden"/>-->
<!--            <input name="payData[userId]" value="--><?//= $user->userId; ?><!--" type="hidden"/>-->
<!---->
<!--            --><?php //foreach ($vars['arr'] as $name => $val) : ?>
<!--                <input type="hidden" name="payData[--><?//= $name; ?><!--]" value="--><?//= $val; ?><!--"/>-->
<!--            --><?php //endforeach; ?>
<!---->
<!--            <button class="btn btn-default"-->
<!--                    type="submit"-->
<!--                    style="height: 90px;"-->
<!--                    data-toggle="popover"-->
<!--                    data-placement="top"-->
<!--                    data-trigger="hover"-->
<!--                    data-content="Выберите если Вы хотите оплатить через Google Pay."-->
<!--                    data-original-title="Оплата Google Pay"-->
<!--            >-->
<!--                <img src="/img/icons/googpay.png" style="width: 100%;"/>-->
<!--            </button>-->
<!--        </form>-->
<!--    </div>-->

    <!--
    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="b2b_sberbank" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить со счета организации через Сбербанк Онлайн Бизнес."
                    data-original-title="Сбербанк Онлайн Бизнес"
            >
                <img src="/img/icons/sb_business.png" style="width: 100%;"/>
            </button>
        </form>
    </div>
    -->

    <!--
    <div class="col-sm-3">
        <form id="kassa-form" action="/orders/pay" method="POST">
            <input name="payData[typePay]" value="mobile_balance" type="hidden"/>
            <input name="payData[orderTitle]" value="<?= $vars['title'] ?>" type="hidden"/>
            <input name="payData[sum]" value="<?= $order->price; ?>" type="hidden"/>
            <input name="payData[userId]" value="<?= $user->userId; ?>" type="hidden"/>

            <?php foreach ($vars['arr'] as $name => $val) : ?>
                <input type="hidden" name="payData[<?= $name; ?>]" value="<?= $val; ?>"/>
            <?php endforeach; ?>

            <button class="btn btn-default"
                    type="submit"
                    style="height: 90px;"
                    data-toggle="popover"
                    data-placement="top"
                    data-trigger="hover"
                    data-content="Выберите если Вы хотите оплатить с баланса Вашего мобильного телефона."
                    data-original-title="Оплата с баланса телефона"
            >
                <img src="/img/icons/phone.jpg" style="width: 100%;"/>
            </button>
        </form>
    </div>
    -->


    <?php if ($GLOBALS['isSuperUser']): ?>
        <div class="col-sm-3">
            <p>isSuperUser</p>
            <form action="https://sprypay.ru/sppi/" method="POST" accept-charset="utf-8">
                <input type="hidden" name="spShopId" value="<?= spShopId; ?>">
                <input type="hidden" name="spShopPaymentId" value="<?= $order->orderId; ?>">
                <input type="hidden" name="spCurrency" value="rur">
                <input type="hidden" name="spPurpose" value="Оплата VK-PRO.TOP <?= $order->type; ?>">
                <input type="hidden" name="spAmount" value="<?= $order->price; ?>">
                <input type="hidden" name="spSuccessUrl"
                       value="https://<?= DOMAIN; ?>/orders/success?orderId=<?= $order->orderId; ?>">
                <input type="hidden" name="spSuccessMethod" value="0">
                <input type="hidden" name="spFailUrl"
                       value="https://<?= DOMAIN; ?>/orders/error?orderId=<?= $order->orderId; ?>">
                <input type="hidden" name="spFailMethod" value="0">
                <input type="hidden" name="lang" value="ru">
                <button type="submit" class="btn btn-default" style="height: 90px;">
                    <img src="/img/icons/Bitcoin_Logo.png" style="width: 100%; margin-top: 10px;"/>
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="col-sm-3">
            <form action="" method="post">
                <?php foreach ($vars['arr'] as $name => $val): ?>
                    <input type="hidden" name="<?= $name; ?>" value="<?= $val; ?>"/>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-default" disabled="disabled" style="height: 90px;">
                    <img src="/img/icons/Bitcoin_Logo.png" style="width: 100%; margin-top: 10px;"/>
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    // Включаем popover
    jQuery(function () {
        jQuery('[data-toggle="popover"]').popover({trigger: 'hover', placement: 'top'});
    });
</script>