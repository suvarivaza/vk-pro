<?php
/** @var Model_News_New[] $list */
use Service\News\Model_News_New;

$list = $vars['list'];
?>
<ul class="c-news-announce">
    <?php foreach ($list as $new): ?>
        <li>
            <div class="c-news-announce-date"><?= date('d.m.Y', $new->dateUpdate); ?></div>
            <div class="c-news-announce-desc"><a href="/news/<?= urlencode($new->alias); ?>"><?= $new->title; ?></a>
            </div>
        </li>
    <?php endforeach; ?>
    <li>
        <a href="/news">Все новости</a>
    </li>
</ul>