<?php
/** @var \Service\Orders\Model_Orders_Packs_Pack $pack */
$pack = $vars['pack'];
$first = $vars['first'];
$gift = $vars['gift'];
?>
<h1><?= $pack->balance; ?> баллов
    <?php if ($first): ?>
        <br/><small class="text-success">+ <?= number_format($pack->balance * ($vars['bonus'] / 100), 0, '', ' '); ?> в
            подарок за первую покупку</small>
    <?php elseif ($pack->bonus): ?>
        <br/><small class="text-success">+ <?= $pack->bonus; ?> баллов в подарок</small>
    <?php endif; ?>
</h1>
<?php if ($gift !== null): ?>
    <h2>Подарок</h2>
<?php else: ?>
    <h2><?= $pack->price; ?> рублей</h2>
<?php endif; ?>

<form action="/orders/order" method="post" onsubmit="return checkPackForm();">
    <input type="hidden" name="packId" value="<?= $pack->packId; ?>">
    <?php if ($pack->serviceAll): ?>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control c-service" name="service1">
                    <option data-image="/img/icons/32/icon-auto.png" value="auto" selected="selected">Автоведение
                        ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['auto']['limits'][$pack->serviceMonth],
                            ['слот', 'слота', 'слотов']); ?> )
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control c-service" name="service2">
                    <option data-image="/img/icons/32/icon-post.png" value="posting" selected="selected">Автопостинг
                        ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['posting']['limits'][$pack->serviceMonth],
                            ['слот', 'слота', 'слотов']); ?> )
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control c-service" name="service3">
                    <option data-image="/img/icons/32/icon-grabber.png" value="grabber" selected="selected">Граббер
                        ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['grabber']['limits'][$pack->serviceMonth],
                            ['слот', 'слота', 'слотов']); ?> )
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control c-service" name="service4">
                    <option data-image="/img/icons/32/icon-special.png" value="special" selected="selected">Спецзадания
                        ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['special']['limits'][$pack->serviceMonth],
                            ['слот', 'слота', 'слотов']); ?> )
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <select class="form-control c-service" name="service5">
                    <option data-image="/img/icons/32/icon-special.png" value="bot" selected="selected">Автобот PRO
                        ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['bot']['limits'][$pack->serviceMonth],
                            ['слот', 'слота', 'слотов']); ?> )
                    </option>
                </select>
            </div>
        </div>
    <?php else: ?>
        <?php for ($i = 0; $i < $pack->serviceCount; $i++): ?>
            <div class="form-group">
                <div class="col-sm-12">
                    <select class="form-control c-service" name="service<?= $i + 1; ?>">
                        <option value="">-- Укажите сервис --</option>
                        <option data-image="/img/icons/32/icon-auto.png" value="auto">Автоведение
                            ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['auto']['limits'][$pack->serviceMonth],
                                ['слот', 'слота', 'слотов']); ?> )
                        </option>
                        <option data-image="/img/icons/32/icon-post.png" value="posting">Автопостинг
                            ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['posting']['limits'][$pack->serviceMonth],
                                ['слот', 'слота', 'слотов']); ?> )
                        </option>
                        <option data-image="/img/icons/32/icon-grabber.png" value="grabber">Граббер
                            ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['grabber']['limits'][$pack->serviceMonth],
                                ['слот', 'слота', 'слотов']); ?> )
                        </option>
                        <option data-image="/img/icons/32/icon-special.png" value="special">Спецзадания
                            ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['special']['limits'][$pack->serviceMonth],
                                ['слот', 'слота', 'слотов']); ?> )
                        </option>
                        <option data-image="/img/icons/32/icon-bot.png" value="bot">Автобот PRO
                            ( <?= \Lib_Text::Word4NumberNewReturn($vars['settings']['bot']['limits'][$pack->serviceMonth],
                                ['слот', 'слота', 'слотов']); ?> )
                        </option>
                    </select>
                </div>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
<!--    <input name="shopId" value="--><?//= shopId; ?><!--" type="hidden"/>-->
<!--    <input name="scid" value="--><?//= scid; ?><!--" type="hidden"/>-->
    <input name="customerNumber" value="<?= $vars['userId']; ?>" type="hidden"/>
    <input name="sum" value="<?= $pack->price; ?>" type="hidden"/>
    <input name="packId" value="<?= $pack->packId; ?>" type="hidden"/>
    <?php if ($gift): ?>
        <input name="giftId" value="<?= $gift->giftId; ?>" type="hidden"/>
        <button type="submit" class="btn btn-primary btn-block">Активировать подарок</button>
    <?php else: ?>
        <button type="submit" class="btn btn-primary btn-block">Купить за <?= $pack->price; ?> рублей</button>
    <?php endif; ?>
</form>

<script type="text/javascript">
    $('.c-service').msDropDown();

    function checkPackForm() {
        var success = true;
        $('.c-service').each(function () {
            var val = $(this).val();
            if (val === '') {
                success = false;
            }
        });
        if (!success) {
            alert('Выберите все доступные сервисы для активации');
        }
        return success;
    };
</script>