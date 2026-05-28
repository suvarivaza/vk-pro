<ul class="breadcrumb">
    <li><strong>Статьи</strong></li>
</ul>

<?php if ($vars['list']): ?>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
    <table class="table">
        <?php foreach ($vars['list'] as $page): ?>
            <tr>
                <?php if ($vars['page'] == 'news'): ?>
                    <td class="news_date">
                        <p><?= \Lib_TimeStamp::createFromTimestamp($page['date'])->format('d.m'); ?></p></td>
                <?php endif; ?>
                <td style="vertical-align: top; width: 170px;">
                    <a href="/<?= $page['alias']; ?>">
                        <?php if ($page['photo']): ?>
                            <img class="img_news" src="/images/articles/small/<?= $page['photo']; ?>">
                        <?php endif; ?>
                    </a>
                </td>
                <td style="vertical-align: top;">
                    <h5 style="margin-top: 0;"><a href="/<?= $page['alias']; ?>"><?= $page['title']; ?></a></h5>
                    <h5><?= $page['brief']; ?></h5>
                    <div>
                        <a href="/<?= $page['alias']; ?>">Подробнее</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
<?php endif; ?>