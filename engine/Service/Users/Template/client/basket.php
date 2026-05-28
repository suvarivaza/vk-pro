<?php
/** @var \Service\Users\Model_Baskets_Basket $basket */
$basket = $vars['basket'];

$parts = $vars['parts'];

?>
<h1>Корзина</h1>
<?php if (count($parts)): ?>
    <div class="row">
        <div class="col-sm-9 c-basket-items">
            <?php foreach ($parts as $part): ?>
                <div class="row c-basket-item">
                    <div class="col-sm-2">
                        <img class="img-thumbnail" style="width: 100%;" src="<?= $part['photo']['url']; ?>"/>
                    </div>
                    <div class="col-sm-5">
                        <div class="title"><?= $part['title']; ?> <?= $part['number']; ?></div>
                        <?php foreach ($part['options'] as $name => $val): ?>
                            <div><?= $name; ?> <strong><?= $val; ?></strong></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                              <span class="input-group-btn">
                                <button data-part-id="<?= $part['partId']; ?>"
                                        class="btn btn-default c-btn-part-count-minus" type="button"><span
                                            class="glyphicon glyphicon-minus"></span></button>
                              </span>
                            <input id="i_part_count_<?= $part['partId']; ?>" type="text"
                                   class="form-control text-center" aria-label="Количество"
                                   name="count[<?= $part['partId']; ?>]" value="<?= $part['count']; ?>">
                            <span class="input-group-btn">
                                <button data-part-id="<?= $part['partId']; ?>"
                                        class="btn btn-default c-btn-part-count-plus" type="button"><span
                                            class="glyphicon glyphicon-plus"></span></button>
                              </span>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button data-part-id="<?= $part['partId']; ?>" class="btn btn-danger c-btn-part-remove"><span
                                    class="glyphicon glyphicon-remove"></span></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-sm-3">
            Итого:
            <h2 id="i-basket-detail-sum"><?= number_format($basket->sum, 0, ',', ' '); ?><span
                        class="glyphicon glyphicon-ruble"></span></h2>
            <button class="btn btn-primary btn-lg btn-block">Оформить заказ</button>
        </div>

    </div>
<?php else: ?>
    <div class="alert alert-info">Корзина пуста.</div>
<?php endif; ?>
