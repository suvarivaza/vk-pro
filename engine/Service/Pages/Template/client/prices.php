<?php
/** @var \Service\Pages\Model_Prices_Price[] $prices */
$prices = $vars['prices'];
/** @var \Service\Pages\Model_Prices_Price $price */
$price = $vars['price'];
/** @var \System\App $app */
$app = $vars['app'];
?>
<div class="text-center">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($prices as $l): ?>
            <li role="presentation"
                class="<?php if ($l->PriceID == $price->PriceID): ?>active<?php else: ?>hidden-print<?php endif; ?>">
                <?php if ($l->PriceID == $price->PriceID): ?>
                    <h1 class="h1-beton"><?= $l->TitleMain; ?></h1>
                <?php else: ?>
                    <a href="/<?= $l->Alias; ?>" role="tab">
                        <?= $l->TitleMain; ?>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="<?= $price->Alias; ?>">
            <table class="c-table-price" cellspacing="5">
                <tr>
                    <?php $i = 0;

                    foreach ($price->getFields() as $field): if ($i++ > 1) {
                        break;
                    } ?>
                        <th><?= $field->Title; ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($vars['list'][$price->Alias] as $l): if (!$l[0] && !$l[1]) {
                        continue;
                    } ?>
                    <tr itemscope itemtype="http://schema.org/Product">
                        <?php $i = 0;

                        foreach ($price->getFields() as $field): if ($i++ > 1) {
                            break;
                        } ?>
                            <td<?php if ($i == 2): ?> itemprop="offers" itemscope itemtype="http://schema.org/Offer"<?php endif; ?>
                                    align="<?= $field->Align; ?>">
                                <div<?php if ($i == 1): ?> itemprop="name"<?php elseif ($i == 2): ?> itemprop="price"<?php endif; ?>>
                                    <?= $l[$field->Column]; ?>
                                </div>
                                <?php if ($i == 1): ?>
                                    <div class="hidden" itemprop="description"><?= $l[$field->Column]; ?></div>
                                <?php elseif ($i == 2): ?>
                                    <div class="hidden" itemprop="priceCurrency">RUB</div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="row">
                <div class="col-lg-6 text-left hidden-xs">
                    <span class="c-price-date">Прайс от <?= date('d.m.Y', $price->Date); ?>г.</span>
                </div>
                <div class="col-xs-12 col-lg-6 text-right hidden-print">
                    <a class="c-price-link-download"
                       href="/files/prices/<?= $app->settings['Download_' . $price->Alias]; ?>"><span
                                class="glyphicon glyphicon-download-alt"></span>Сохранить прайс</a>
                    <div class="visible-xs"></div>
                    <a class="c-price-link-print" onclick="window.print();" href="javascript::void(0)"><span
                                class="glyphicon glyphicon-print"></span>Распечатать прайс</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden-print">
    <?= $price->Text; ?>
</div>

<?php if ($vars['print']): ?>
    <script>
        $(document).ready(function () {
            window.print();
        });
    </script>
<?php endif; ?>
