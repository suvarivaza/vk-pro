<nav class="navbar navbar-default navbar-static-top c_menu">
    <ul class="nav navbar-nav">
        <?php foreach ($vars['app']->menu as $item): ?>
            <li class="<?= $item['class'] ?? ''; ?><?php if ($item['active']): ?> active<?php endif; ?>"><a
                    href="<?= $item['href']; ?>"><?= $item['title']; ?></a></li><?php if (!isset($item['class'])): ?>
                <li class="nav-border"><a>&nbsp;</a></li><?php endif; ?><?php endforeach; ?>
    </ul>
</nav>