<?php if ($vars['visible']): ?>
    <?php if ($vars['page'] == 'articles' || $vars['page'] == 'news'): ?>
        <div>
            <a class="btn btn-success"
               href="/my/news/add<?php if ($vars['page'] == 'articles'): ?>?isArticle=true<?php elseif ($vars['page'] == 'news'): ?>?isNew=true<?php endif; ?>"><?= $vars['add']; ?></a>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if ($vars['list']): ?>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
    <table class="table">
        <?php foreach ($vars['list'] as $page): ?>
            <tr>
                <td>
                    <h4><a href="/<?= $page['alias']; ?>"><?= $page['title'] ?: 'Редактировать'; ?></a></h4>
                    <div><?= $page['brief']; ?></div>
                    <div>
                        <a class="btn btn-primary" href="/news/my/edit/<?= $page['alias']; ?>">Редактировать</a>
                        <?php if (!$page['restricted']): ?><a class="btn btn-danger"
                                                           href="/my/news/delete/<?= urlencode($page['alias']); ?>?backUrl=<?= urlencode($vars['backUrl']); ?>">
                                удалить</a><?php endif; ?>
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