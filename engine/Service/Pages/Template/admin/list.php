<div>
    <a class="btn btn-success" href="/admin/pages/add"><?= $vars['add']; ?></a>
</div>

<?php if ($vars['list']): ?>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
    <table class="table">
        <?php foreach ($vars['list'] as $page): ?>
            <tr>
                <td style="vertical-align: top; width: 170px;">
                    <a href="/<?= $page['alias']; ?>">
                        <?php if ($page['photo']): ?>
                            <img class="img_news" src="/images/articles/small/<?= $page['photo']; ?>">
                        <?php endif; ?>
                    </a>
                </td>
                <td>
                    <h4>
                        <a href="/<?= $page['alias']; ?>"><?= $page['title'] ?: 'Редактировать'; ?></a>
                        <small class="pull-right">
                            <a class="btn btn-primary" href="/admin/pages/edit/<?= $page['alias']; ?>">Редактировать</a>
                            <?php if (!$page['restricted']): ?><a class="btn btn-danger"
                                                               href="/admin/pages/delete/<?= urlencode($page['alias']); ?>?backUrl=<?= urlencode($vars['backUrl']); ?>">
                                    удалить</a><?php endif; ?>
                        </small>
                    </h4>
                    <div><?= $page['brief']; ?></div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
<?php endif; ?>
