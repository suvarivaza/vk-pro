<?php
/** @var \Service\Pages\Model_Prices_Price $price */
?>
<div class="container text-center c-about">
    <div class="row">
        <?php foreach ($vars['prices'] as $price): ?>
            <div class="col-lg-4 c-price-announce" data-href="/<?= $price->Alias; ?>">
                <div class="cont">
                    <h1><?= $price->Title; ?></h1>
                    <div class="c-line"></div>
                    <h3><?= $price->Description; ?></h3>
                    <a href="/<?= $price->Alias; ?>">
                        <span class="c-more">Подробнее</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="h30"></div>
    <h3 class="h3-header"><span class="c-blue">О</span> компании</h3>
    <?= $vars['page']->text; ?>
</div>
<script>
    $('.c-price-announce').click(function () {
        location.href = $(this).data('href');
    });
</script>