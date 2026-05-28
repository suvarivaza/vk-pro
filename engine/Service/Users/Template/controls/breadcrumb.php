<ul class="breadcrumb">
    <li><a href="/admin/catalog/"><?= $vars['mainTitle']; ?></a></li>
    <?php $total = count($vars['list']);
    $i = 0;

    foreach ($vars['list'] as $rubric): $i++; ?>
        <li<?php if ($i == $total): ?> class="active"<?php endif; ?>>
            <?php if ($i != $total): ?><a
                    href="/admin/catalog/<?= urlencode($rubric->alias); ?>"><?php endif; ?><?= $rubric->title ?: $vars['title']; ?><?php if ($i != $total) : ?></a><?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>