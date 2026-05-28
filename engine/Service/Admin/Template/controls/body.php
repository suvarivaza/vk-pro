<?php
/** @var App $app */
$app = $vars['app'];

use System\App;

?>
<style>
    .left-menu li a {
        padding: 15px 0 15px 15px
    }
</style>

<div id="i-admin-content-container" class="container" style="margin-top: 30px; background-color: #ffffff;">
    <div class="row">
        <div class="col-sm-2">
            <ul class="left-menu">
                <?php foreach ($app->menu as $menu): ?>
                    <li class="<?php if ($menu['active']): ?> active<?php endif; ?>">
                        <a href="<?= $menu['href']; ?>"><?= $menu['title']; ?></a>
                        <?php if (isset($menu['menu'])): ?>
                            <ul class="left-sub-menu">
                                <?php foreach ($menu['menu'] as $item): ?>
                                    <li class="<?php if ($item['active']): ?> active<?php endif; ?>">
                                        <a href="<?= $item['href']; ?>"><?= $item['title']; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="i-admin-content" class="col-sm-10">
            <?= $vars['html']; ?>
        </div>
    </div>
</div>