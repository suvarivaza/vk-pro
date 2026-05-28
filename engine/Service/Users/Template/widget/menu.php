<?php if (!$vars['show']) {
    return;
} ?>
<div class="c_widget_container">
    <div class="c_widget_content">
        <ul class="list-group">
            <?php foreach ($vars['list'] as $alias => $item): ?>
                <li style="padding: 0;" class="list-group-item<?php if ($alias == $vars['page']): ?> active<?php endif; ?>">
                    <a class="c-user-menu-link" href="<?= $item['href']; ?>">
                        <?php if (isset($item['icon'])): ?>
                            <img src="<?php if ($alias == $vars['page']): ?><?= $item['icon_active']; ?><?php else: ?><?= $item['icon']; ?><?php endif; ?>"style="height: 16px;"/>
                        <?php endif; ?>
                        <?= $item['title']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
